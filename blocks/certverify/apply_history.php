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

$id = optional_param('id', null, PARAM_INT);
$filters = array();
$filters['status']  = optional_param('status', '', PARAM_INT);
$download = optional_param('download', '', PARAM_ALPHA);

require_login();

$context = context_user::instance($USER->id);
$url = new moodle_url('/blocks/certverify/apply_history.php', $filters);

$PAGE->set_context($context);
$PAGE->set_url($url);
$PAGE->set_pagetype('my-index-certverify');
$PAGE->set_pagelayout('standard');

$title = get_string('applyhistory', 'block_certverify');
// Create the breadcrumb.
$PAGE->navbar->add(get_string('myhome'), new moodle_url('/my'));
$PAGE->navbar->add(get_string('pluginname', 'block_certverify'));
$PAGE->navbar->add($title);

$filterform = html_writer::start_tag('form', array('id' => 'searchnavbar', 'action' => $url, 'method' => 'post'));
$filterform .= html_writer::start_tag('fieldset', array('class' => 'coursesearchbox invisiblefieldset'));    
$verify_status = array('1'=>get_string('agree', 'block_certverify'),'2'=>get_string('reject', 'block_certverify'),'3'=>get_string('cancel', 'block_certverify'));
$filterform .= html_writer::select($verify_status, 'status', $filters['status']);
$filterform .= html_writer::empty_tag('input', array('type' => 'submit', 'value' => get_string('search','admin')));
$filterform .= html_writer::end_tag('fieldset');
$filterform .= html_writer::end_tag('form');

$table = new block_certverify_apply_history_table($USER->id, $filters);
$table->define_baseurl($url);
$table->collapsible(false);
$table->is_downloading($download, fullname($USER).get_string('filename', 'block_certverify', date('Ymd', time())));

if (!$table->is_downloading()) {
    $PAGE->set_heading($title);
    $PAGE->set_title($title);

    echo $OUTPUT->header();
    echo $OUTPUT->heading($title);
    echo $filterform;
}

$renderer = $PAGE->get_renderer('block_certverify');
$renderer->apply_history_page($table, $url, $filterform);

if (!$table->is_downloading()) {
    echo $OUTPUT->footer();
}