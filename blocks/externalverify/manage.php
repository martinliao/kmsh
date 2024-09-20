<?php
/**
 * plugin infomation
 * 
 * @package    block
 * @subpackage externalverify
 * @author     Elaine Chen(CLICK-AP)
 * @copyright  CLICK-AP {@link https://www.click-ap.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once('../../config.php');
require_once($CFG->dirroot.'/blocks/externalverify/locallib.php');
require_once($CFG->dirroot.'/blocks/externalverify/manage_table.php');

$id = optional_param('id', null, PARAM_INT);
$stage = optional_param('stage', null, PARAM_INT);
$applys = optional_param_array('applyids', array(), PARAM_RAW);
$filters = array();
$filters['user']    = optional_param('searchuser', '', PARAM_TEXT);
$filters['course']  = optional_param('searchcourse', '', PARAM_TEXT);

require_login();

$params = array();
if(!empty($stage)){
    $params['stage'] = $stage;
}
$context = context_user::instance($USER->id);
$pageheading = get_string('confirmusers', 'block_externalverify');
if(!empty($stage)){
    $pageheading = get_string('confirmusers_manager', 'block_externalverify');
}
$url = new moodle_url('/blocks/externalverify/manage.php', $params);

$PAGE->set_context($context);
$PAGE->set_url($url);
$PAGE->set_pagetype('my-index-externalverify');
$PAGE->set_pagelayout('standard');
// Create the breadcrumb.
$PAGE->navbar->add(get_string('myhome'), new moodle_url('/my'));
$PAGE->navbar->add(get_string('pluginname', 'block_externalverify'));
$PAGE->navbar->add($pageheading);

$PAGE->set_heading($pageheading);
$PAGE->set_title($pageheading);

$checkusers = implode(',', $applys);
$rejectform = new block_externalverify_request_reject_form($url, array('applyids'=>$checkusers, 'stage'=>$stage));

if (!empty($applys)) {
    if (optional_param('confirm', false, PARAM_BOOL)) {
        block_external_verify_course_batch($applys, 1, $stage);
    } else if (optional_param('reject', false, PARAM_BOOL)) {
        $PAGE->navbar->add(get_string('reject-reason', 'block_externalverify'));
        echo $OUTPUT->header($rejectform->focus());
        $rejectform->display();
        echo $OUTPUT->footer();
        exit;
    }
    redirect($url);
}
if ($rejectform->is_cancelled()){
    redirect($url);
}else if ($data = $rejectform->get_data()) {
    block_external_verify_course_batch($data, 2, $stage);
}

$filterform = html_writer::start_tag('form', array('id' => 'searchnavbar', 'action' => $url, 'method' => 'post'));
$filterform .= html_writer::start_tag('fieldset', array('class' => 'coursesearchbox invisiblefieldset'));    
$filterform .= html_writer::tag('label', get_string('lastname', 'moodle').': ', array('for' => 'searchuser'));
$filterform .= html_writer::empty_tag('input', array('type' => 'text', 'id' => 'searchuser', 'size' => '20', 'name' => 'searchuser', 'value' => s($filters['user'])));
$filterform .= html_writer::tag('label', get_string('course', 'moodle').': ', array('for' => 'searchcoursename'));
$filterform .= html_writer::empty_tag('input', array('type' => 'text', 'id' => 'searchcourse', 'size' => '20', 'name' => 'searchcourse', 'value' => s($filters['course'])));
$filterform .= html_writer::empty_tag('input', array('type' => 'submit', 'value' => get_string('search','admin')));
$filterform .= html_writer::end_tag('fieldset');
$filterform .= html_writer::end_tag('form');

$table = new blocks_externalverify_manage_table($USER->username, $filters, $stage);
$table->define_baseurl($url);
$table->collapsible(false);//disable Field hide

$renderer = $PAGE->get_renderer('block_externalverify');
$renderer->manage_page($table, $url, true, $filterform, $stage);

echo $OUTPUT->footer();