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
$filters['user']    = optional_param('searchuser', '', PARAM_TEXT);
$filters['course']  = optional_param('searchcourse', '', PARAM_TEXT);
$filters['status']  = optional_param('status', '', PARAM_INT);

require_login();
$context = context_user::instance($USER->id);
$params = array();
$url = new moodle_url('/blocks/externalverify/manage_history.php', $params);

$PAGE->set_context($context);
$PAGE->set_url($url);
$PAGE->set_pagetype('my-index-externalverify');
$PAGE->set_pagelayout('standard');
//$PAGE->navbar->add(get_string('applylist', 'block_externalverify'));
// Create the breadcrumb.
$PAGE->navbar->add(get_string('myhome'), new moodle_url('/my'));
$PAGE->navbar->add(get_string('pluginname', 'block_externalverify'));
$PAGE->navbar->add(get_string('verifyhistory', 'block_externalverify'));

$PAGE->set_heading(get_string('verifyhistory', 'block_externalverify'));
$PAGE->set_title(get_string('verifyhistory', 'block_externalverify'));

$filterform = html_writer::start_tag('form', array('id' => 'searchnavbar', 'action' => $url, 'method' => 'post'));
$filterform .= html_writer::start_tag('fieldset', array('class' => 'coursesearchbox invisiblefieldset'));    
$filterform .= html_writer::tag('label', get_string('lastname', 'moodle').': ', array('for' => 'searchuser'));
$filterform .= html_writer::empty_tag('input', array('type' => 'text', 'id' => 'searchuser', 'size' => '20', 'name' => 'searchuser', 'value' => s($filters['user'])));
$filterform .= html_writer::tag('label', get_string('course', 'moodle').': ', array('for' => 'searchcoursename'));
$filterform .= html_writer::empty_tag('input', array('type' => 'text', 'id' => 'searchcourse', 'size' => '20', 'name' => 'searchcourse', 'value' => s($filters['course'])));
//$verify_status = array('1'=>get_string('agree', 'block_externalverify'),'2'=>get_string('reject', 'block_externalverify'),'3'=>get_string('cancel', 'block_externalverify'));
$verify_status = array('1'=>get_string('agree', 'block_externalverify'),'2'=>get_string('reject', 'block_externalverify'));
$filterform .= html_writer::select($verify_status, 'status', $filters['status']);
$filterform .= html_writer::empty_tag('input', array('type' => 'submit', 'value' => get_string('search','admin')));
$filterform .= html_writer::end_tag('fieldset');
$filterform .= html_writer::end_tag('form');

$table = new blocks_externalverify_verifyhistory_table($USER->id, $filters);
$table->define_baseurl($url);
$table->collapsible(false);//disable Field hide
$renderer = $PAGE->get_renderer('block_externalverify');
$renderer->verify_history_page($table, $url, $filterform, $filters);

echo $OUTPUT->footer();