<?php
/**
 * plugin infomation
 *
 * @package    block_enrolverifycreator
 * @copyright  2017 Mary Chen {@link http://www.click-ap.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once('../../config.php');
require_once($CFG->dirroot.'/blocks/enrolverifycreator/locallib.php');
require_once($CFG->dirroot.'/blocks/enrolverifycreator/manage_table.php');
require_once($CFG->dirroot.'/blocks/enrolverifycreator/renderer.php');

$id = optional_param('id', null, PARAM_INT);
$userenrolments = optional_param_array('userenrolments', array(), PARAM_RAW);
$filters = array();
$filters['user']    = optional_param('searchuser', '', PARAM_TEXT);
$filters['course']  = optional_param('searchcourse', '', PARAM_TEXT);

require_login();
$params = array();
$context = context_system::instance();
$pageheading = get_string('confirmusers', 'block_enrolverifycreator');
$url = new moodle_url('/blocks/enrolverifycreator/manage.php', $params);

$PAGE->set_context($context);
$PAGE->set_url($url);
$PAGE->set_pagetype('my-index-externalverify');
$PAGE->set_pagelayout('standard');
// Create the breadcrumb.
$PAGE->navbar->add(get_string('myhome'), new moodle_url('/my'));
$PAGE->navbar->add(get_string('pluginname', 'block_enrolverifycreator'));
$PAGE->navbar->add(get_string('confirmusers', 'block_enrolverifycreator'));
$PAGE->set_heading($pageheading);
$PAGE->set_title(get_string('confirmusers', 'block_enrolverifycreator'));

$checkusers = implode(',', $userenrolments);
$rejectform = new block_enrolverifycreator_reject_request_form($url, array('userenrolments'=>$checkusers));
 
if (!empty($userenrolments)) {
    $enrol = enrol_get_plugin('creator');
    if (optional_param('confirm', false, PARAM_BOOL)) {
        $enrol->enrol_apply_approved($userenrolments);
    } else if (optional_param('reject', false, PARAM_BOOL)) {
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
    $enrol = enrol_get_plugin('creator');
    $enrol->enrol_apply_reject($data);    
}

$filterform = html_writer::start_tag('form', array('id' => 'searchnavbar', 'action' => $url, 'method' => 'post'));
$filterform .= html_writer::start_tag('fieldset', array('class' => 'coursesearchbox invisiblefieldset'));    
$filterform .= html_writer::tag('label', get_string('lastname').': ', array('for' => 'searchuser'));
$filterform .= html_writer::empty_tag('input', array('type' => 'text', 'id' => 'searchuser', 'size' => '20', 'name' => 'searchuser', 'value' => s($filters['user'])));
$filterform .= html_writer::tag('label', get_string('course', 'moodle').': ', array('for' => 'searchcoursename'));
$filterform .= html_writer::empty_tag('input', array('type' => 'text', 'id' => 'searchcourse', 'size' => '20', 'name' => 'searchcourse', 'value' => s($filters['course'])));
$filterform .= html_writer::empty_tag('input', array('type' => 'submit', 'value' => get_string('search','admin')));
$filterform .= html_writer::end_tag('fieldset');
$filterform .= html_writer::end_tag('form');

$table = new blocks_enrolverifycreator_manage_table($USER->id, $filters);
$table->define_baseurl($url);
$table->collapsible(false);//disable Field hid
$renderer = $PAGE->get_renderer('block_enrolverifycreator');
$renderer->manage_page($table, $url, true, $filterform);

echo $OUTPUT->footer();