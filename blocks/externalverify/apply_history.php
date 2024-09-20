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
$filters = array();
$filters['course']  = optional_param('searchcourse', '', PARAM_TEXT);
$filters['status']  = optional_param('status', '', PARAM_INT);
$download = optional_param('download', '', PARAM_ALPHA);

require_login();
$context = context_user::instance($USER->id);
$url = new moodle_url('/blocks/externalverify/apply_history.php', $filters);

$PAGE->set_context($context);
$PAGE->set_url($url);
$PAGE->set_pagetype('my-index-externalverify');
$PAGE->set_pagelayout('standard');

$title = get_string('applyhistory', 'block_externalverify');
// Create the breadcrumb.
$PAGE->navbar->add(get_string('myhome'), new moodle_url('/my'));
$PAGE->navbar->add(get_string('pluginname', 'block_externalverify'));
$PAGE->navbar->add($title);

$filterform = html_writer::start_tag('form', array('id' => 'searchnavbar', 'action' => $url, 'method' => 'post'));
$filterform .= html_writer::start_tag('fieldset', array('class' => 'coursesearchbox invisiblefieldset'));    
$filterform .= html_writer::tag('label', get_string('course', 'moodle').': ', array('for' => 'searchcoursename'));
$filterform .= html_writer::empty_tag('input', array('type' => 'text', 'id' => 'searchcourse', 'size' => '20', 'name' => 'searchcourse', 'value' => s($filters['course'])));
$verify_status = array('1'=>get_string('agree', 'block_externalverify'),'2'=>get_string('reject', 'block_externalverify'),'3'=>get_string('cancel', 'block_externalverify'));
$filterform .= html_writer::select($verify_status, 'status', $filters['status']);
$filterform .= html_writer::empty_tag('input', array('type' => 'submit', 'value' => get_string('search','admin')));
$filterform .= html_writer::end_tag('fieldset');
$filterform .= html_writer::end_tag('form');

$table = new blocks_externalverify_applyhistory_table($USER->id, $filters);
$table->define_baseurl($url);
$table->collapsible(false);//disable Field hide
$table->is_downloading($download, fullname($USER).get_string('filename', 'block_externalverify', date('Ymd', time())));

if (!$table->is_downloading()) {
    $PAGE->set_heading($title);
    $PAGE->set_title($title);

    echo $OUTPUT->header();
    echo $OUTPUT->heading($title);
    echo $filterform;
}

$renderer = $PAGE->get_renderer('block_externalverify');
$renderer->apply_history_page($table, $url, $filterform);

if (!$table->is_downloading()) {
    echo $OUTPUT->footer();
}