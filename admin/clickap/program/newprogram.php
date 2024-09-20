<?php
/**
 * Version details.
 *
 * @package    clickap
 * @subpackage program
 * @copyright  2018 Click-AP <elaine@click-ap.com>
 * @license    http://www.click-ap.com/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(dirname(dirname(__FILE__)))) . '/config.php');
require_once($CFG->dirroot . '/admin/clickap/program/lib.php');
require_once($CFG->dirroot . '/admin/clickap/program/edit_form.php');
require_once($CFG->dirroot . '/admin/clickap/program/award/lib.php');

$type = required_param('type', PARAM_INT);

require_login();

$title = get_string('create', 'clickap_program');
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('admin');
$PAGE->set_url('/admin/clickap/program/newprogram.php', array('type' => $type));
$PAGE->set_heading($title);
$PAGE->set_title($title);

require_capability('clickap/programs:createprogram', $PAGE->context);

//$PAGE->requires->js('/admin/clickap/program/backpack.js');
//$PAGE->requires->js_init_call('check_site_access', null, false);
$program = new stdClass();
$imageoptions = program_image_options();
$program = file_prepare_standard_filemanager($program, 'medal', $imageoptions, context_system::instance(), 'program', 'medal', 0);
//$program = file_prepare_standard_filemanager($program, 'banner', $imageoptions, context_system::instance(), 'program', 'banner', 0);
$program = file_prepare_standard_filemanager($program, 'award', $imageoptions, context_system::instance(), 'program', 'award', 0);
$form = new program_edit_details_form($PAGE->url, array('action' => 'new', 'program' => $program));

if ($form->is_cancelled()) {
    redirect(new moodle_url('/admin/clickap/program/index.php', array('type' => $type)));
} else if ($data = $form->get_data()) {
    // Creating new program here.
    $now = time();
    //$fordb = new stdClass();    
    //$fordb->id = null;
    //$data->name = $data->name;
    //$data->description = $data->description;
    $data->timecreated = $now;
    $data->timemodified = $now;
    $data->usercreated = $USER->id;
    $data->usermodified = $USER->id;
    //$data->issuerurl = $data->issuerurl;
    $data->expiredate = ($data->expiry == 1) ? $data->expiredate : null;
    $data->expireperiod = ($data->expiry == 2) ? $data->expireperiod : null;
    $data->type = $type;
    $data->courseid = null;
    $data->messagesubject = get_string('messagesubject', 'clickap_program');
    $data->message = get_string('messagebody', 'clickap_program',
            html_writer::link($CFG->wwwroot . '/admin/clickap/program/myprogram.php', get_string('manageprograms', 'clickap_program')));
    $data->attachment = 1;
    $data->notification = 0;
    $data->status = 0;

    $newid = $DB->insert_record('program', $data, true);
    //create defult category
    $cdata = new stdClass();
    $cdata->programid = $newid;
    $cdata->sortorder = 1;
    $cdata->timemodified = time();
    $cdata->usermodified = $USER->id;
    $DB->insert_record('program_category', $cdata);
                    
    //file_prepare_standard_filemanager($course, 'medal', $imageoptions, context_system::instance(), 'program', 'medal', 0);
    //file_prepare_standard_filemanager($course, 'banner', $imageoptions, context_system::instance(), 'program', 'banner', 0);
    $data = file_postupdate_standard_filemanager($data, 'medal', $imageoptions, context_system::instance(), 'clickap_program', 'medal', $newid);
    //$data = file_postupdate_standard_filemanager($data, 'banner', $imageoptions, context_system::instance(), 'clickap_program', 'banner', $newid);
    $data = file_postupdate_standard_filemanager($data, 'award', $imageoptions, context_system::instance(), 'clickap_program', 'award', $newid);
    $data->id = $newid;
    $DB->update_record('program', $data);
    
    $newprogram = new program($newid);
    
    // If a user can configure program criteria, they will be redirected to the criteria page.
    if (has_capability('clickap/programs:configurecriteria', $PAGE->context)) {
        redirect(new moodle_url('/admin/clickap/program/criteria.php', array('id' => $newid)));
    }
    redirect(new moodle_url('/admin/clickap/program/edit.php', array('id' => $newid)));
}

echo $OUTPUT->header();
echo $OUTPUT->box('', 'notifyproblem hide', 'check_connection');

$form->display();

echo $OUTPUT->footer();