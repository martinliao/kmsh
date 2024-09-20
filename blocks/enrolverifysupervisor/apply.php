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
$userenrolments = optional_param_array('userenrolments', array(), PARAM_RAW);

require_login();

$params = array();
$context = context_system::instance();
$url = new moodle_url('/blocks/enrolverifysupervisor/apply.php', $params);

if (!empty($userenrolments)) {
    $enrol = enrol_get_plugin('supervisor');
    if (optional_param('cancel', false, PARAM_BOOL)) {
        $enrol->enrol_apply_cancel($userenrolments);
    }
    redirect($url);
}
$PAGE->set_context($context);
$PAGE->set_url($url);
$PAGE->set_pagetype('my-index-externalverify');
$PAGE->set_pagelayout('standard');
// Create the breadcrumb.
$PAGE->navbar->add(get_string('myhome'), new moodle_url('/my'));
$PAGE->navbar->add(get_string('pluginname', 'block_enrolverifysupervisor'));
$PAGE->navbar->add(get_string('applylist', 'block_enrolverifysupervisor'));
$PAGE->set_heading(get_string('applylist', 'block_enrolverifysupervisor'));
$PAGE->set_title(get_string('applylist', 'block_enrolverifysupervisor'));

$table = new blocks_enrolverifysupervisor_apply_table($USER->id);
$table->define_baseurl($url);
$table->collapsible(false);
$renderer = $PAGE->get_renderer('block_enrolverifysupervisor');
$renderer->manage_page($table, $url, false);
echo $OUTPUT->footer();