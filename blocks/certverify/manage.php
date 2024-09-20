<?php
/**
 * 
 * @package    block_certverify
 * @author     Elaine Chen(CLICK-AP)
 * @copyright  CLICK-AP {@link https://www.click-ap.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once('../../config.php');
require_once($CFG->dirroot.'/blocks/certverify/locallib.php');
require_once($CFG->dirroot.'/blocks/certverify/manage_table.php');

$filters = array();

$id = optional_param('id', null, PARAM_INT);
$applys = optional_param_array('applyids', array(), PARAM_RAW);
$filters['user'] = optional_param('searchuser', '', PARAM_TEXT);
$filters['keyword']   = optional_param('keyword', '', PARAM_TEXT);

require_login();

$context = context_user::instance($USER->id);
$pageheading = get_string('confirmusers', 'block_certverify');
$url = new moodle_url('/blocks/certverify/manage.php', $filters);

$PAGE->set_context($context);
$PAGE->set_url($url);
$PAGE->set_pagetype('my-index-certverify');
$PAGE->set_pagelayout('standard');
// Create the breadcrumb.
$PAGE->navbar->add(get_string('myhome'), new moodle_url('/my'));
$PAGE->navbar->add(get_string('pluginname', 'block_certverify'));
$PAGE->navbar->add($pageheading);

$PAGE->set_heading($pageheading);
$PAGE->set_title($pageheading);

$checkusers = implode(',', $applys);
$rejectform = new block_certverify_request_reject_form($url, array('applyids'=>$checkusers));

if (!empty($applys)) {
    if (optional_param('confirm', false, PARAM_BOOL)) {
        block_certverify_batch_verify($applys, 1);
    } else if (optional_param('reject', false, PARAM_BOOL)) {
        $PAGE->navbar->add(get_string('reject_reason', 'block_certverify'));
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
    block_certverify_batch_verify($data, 2);
}

$filterform = html_writer::start_tag('form', array('id' => 'searchnavbar', 'action' => $url, 'method' => 'post'));
$filterform .= html_writer::start_tag('fieldset', array('class' => 'coursesearchbox invisiblefieldset'));    
$filterform .= html_writer::tag('label', get_string('user', 'moodle').': ', array('for' => 'searchuser'));
$filterform .= html_writer::empty_tag('input', array('type' => 'text', 'id' => 'searchuser', 'size' => '20', 'name' => 'searchuser', 'value' => s($filters['user'])));
$filterform .= html_writer::tag('label', get_string('keyword', 'block_certverify').': ', array('for' => 'searchkeyword'));
$filterform .= html_writer::empty_tag('input', array('type' => 'text', 'id' => 'keyword', 'size' => '20', 'name' => 'keyword', 'value' => s($filters['keyword'])));
$filterform .= html_writer::empty_tag('input', array('type' => 'submit', 'value' => get_string('search','admin')));
$filterform .= html_writer::end_tag('fieldset');
$filterform .= html_writer::end_tag('form');

$table = new block_certverify_manage_table($USER->username, $filters);
$table->define_baseurl($url);
$table->collapsible(false);//disable Field hide

$renderer = $PAGE->get_renderer('block_certverify');
$renderer->manage_page($table, $url, true, $filterform);

echo $OUTPUT->footer();