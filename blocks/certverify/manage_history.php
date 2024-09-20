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
$filters['user']   = optional_param('searchuser', '', PARAM_TEXT);
$filters['keyword']   = optional_param('keyword', '', PARAM_TEXT);
$filters['status'] = optional_param('status', '', PARAM_INT);

require_login();

$context = context_user::instance($USER->id);
$url = new moodle_url('/blocks/certverify/manage_history.php', $filters);

$PAGE->set_context($context);
$PAGE->set_url($url);
$PAGE->set_pagetype('my-index-certverify');
$PAGE->set_pagelayout('standard');
//$PAGE->navbar->add(get_string('applylist', 'block_certverify'));
// Create the breadcrumb.
$PAGE->navbar->add(get_string('myhome'), new moodle_url('/my'));
$PAGE->navbar->add(get_string('pluginname', 'block_certverify'));
$PAGE->navbar->add(get_string('verifyhistory', 'block_certverify'));

$PAGE->set_heading(get_string('verifyhistory', 'block_certverify'));
$PAGE->set_title(get_string('verifyhistory', 'block_certverify'));

$filterform = html_writer::start_tag('form', array('id' => 'searchnavbar', 'action' => $url, 'method' => 'post'));
$filterform .= html_writer::start_tag('fieldset', array('class' => 'coursesearchbox invisiblefieldset'));    
$filterform .= html_writer::tag('label', get_string('user', 'moodle').': ', array('for' => 'searchuser'));
$filterform .= html_writer::empty_tag('input', array('type' => 'text', 'id' => 'searchuser', 'size' => '20', 'name' => 'searchuser', 'value' => s($filters['user'])));
$filterform .= html_writer::tag('label', get_string('keyword', 'block_certverify').': ', array('for' => 'searchkeyword'));
$filterform .= html_writer::empty_tag('input', array('type' => 'text', 'id' => 'keyword', 'size' => '20', 'name' => 'keyword', 'value' => s($filters['keyword'])));
$verify_status = array('1'=>get_string('agree', 'block_certverify'),'2'=>get_string('reject', 'block_certverify'));
$filterform .= html_writer::select($verify_status, 'status', $filters['status']);
$filterform .= html_writer::empty_tag('input', array('type' => 'submit', 'value' => get_string('search','admin')));
$filterform .= html_writer::end_tag('fieldset');
$filterform .= html_writer::end_tag('form');

$table = new block_certverify_verify_history_table($USER->id, $filters);
$table->define_baseurl($url);
$table->collapsible(false);//disable Field hide
$renderer = $PAGE->get_renderer('block_certverify');
$renderer->verify_history_page($table, $url, $filterform, $filters);

echo $OUTPUT->footer();