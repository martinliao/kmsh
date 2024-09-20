<?php
/**
 * plugin infomation
 *
 * @package    block_enrolverifysupervisor
 * @copyright  2020 CLICK-AP  {@link https://www.click-ap.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once('../../config.php');
require_once($CFG->dirroot.'/blocks/enrolverifysupervisor/locallib.php');
require_once($CFG->dirroot.'/blocks/enrolverifysupervisor/manage_table.php');

$id = optional_param('id', null, PARAM_INT);
$filters = array();
$filters['course']  = optional_param('searchcourse', '', PARAM_TEXT);
$filters['status']  = optional_param('status', '', PARAM_INT);

require_login();
$context = context_system::instance();
$params = array();
$url = new moodle_url('/blocks/enrolverifysupervisor/apply_history.php', $params);

$PAGE->set_context($context);
$PAGE->set_url($url);
$PAGE->set_pagetype('my-index-externalverify');
$PAGE->set_pagelayout('standard');
// Create the breadcrumb.
$PAGE->navbar->add(get_string('myhome'), new moodle_url('/my'));
$PAGE->navbar->add(get_string('pluginname', 'block_enrolverifysupervisor'));
$PAGE->navbar->add(get_string('applyhistory', 'block_enrolverifysupervisor'));
$PAGE->set_heading(get_string('applyhistory', 'block_enrolverifysupervisor'));
$PAGE->set_title(get_string('applyhistory', 'block_enrolverifysupervisor'));

$filterform = html_writer::start_tag('form', array('id' => 'searchnavbar', 'action' => $url, 'method' => 'post'));
$filterform .= html_writer::start_tag('fieldset', array('class' => 'coursesearchbox invisiblefieldset'));    
$filterform .= html_writer::tag('label', get_string('course', 'moodle').': ', array('for' => 'searchcoursename'));
$filterform .= html_writer::empty_tag('input', array('type' => 'text', 'id' => 'searchcourse', 'size' => '20', 'name' => 'searchcourse', 'value' => s($filters['course'])));
$verify_status = array('1'=>get_string('agree', 'block_enrolverifysupervisor'),'2'=>get_string('reject', 'block_enrolverifysupervisor'),'3'=>get_string('cancel', 'block_enrolverifysupervisor'));
$filterform .= html_writer::select($verify_status, 'status', $filters['status']);
$filterform .= html_writer::empty_tag('input', array('type' => 'submit', 'value' => get_string('search','admin')));
$filterform .= html_writer::end_tag('fieldset');
$filterform .= html_writer::end_tag('form');

$table = new blocks_enrolverifysupervisor_applyhistory_table($USER->id, $filters);
$table->define_baseurl($url);
$table->collapsible(false);//disable Field hide
$renderer = $PAGE->get_renderer('block_enrolverifysupervisor');
$renderer->apply_history_page($table, $url, $filterform);

echo $OUTPUT->footer();