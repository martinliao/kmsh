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
$applyids = optional_param_array('applyids', array(), PARAM_RAW);

require_login();

$context = context_user::instance($USER->id);
$url = new moodle_url('/blocks/externalverify/apply.php');
$PAGE->set_context($context);
$PAGE->set_url($url);
$PAGE->set_pagetype('my-index-externalverify');
$PAGE->set_pagelayout('standard');
// Create the breadcrumb.
$PAGE->navbar->add(get_string('myhome'), new moodle_url('/my'));
$PAGE->navbar->add(get_string('pluginname', 'block_externalverify'));
$PAGE->navbar->add(get_string('applylist', 'block_externalverify'));

if (!empty($applyids)) {
    if (optional_param('cancel', false, PARAM_BOOL)) {
        foreach($applyids as $applyid){
            $cancel = new stdClass();
            $cancel->id = $applyid;
            $cancel->status = 3;
            $cancel->timemodified = time();
            $DB->update_record('course_external', $cancel);
        }
    }
    redirect($url);
}
$PAGE->set_heading(get_string('applylist', 'block_externalverify'));
$PAGE->set_title(get_string('applylist', 'block_externalverify'));

$table = new blocks_externalverify_apply_table($USER->id);
$table->define_baseurl($url);
$table->collapsible(false);//disable Field hide
$renderer = $PAGE->get_renderer('block_externalverify');
$renderer->manage_page($table, $url, false);

echo $OUTPUT->footer();