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
require_once($CFG->dirroot . '/admin/clickap/program/edit_form.php');
require_once($CFG->dirroot . '/admin/clickap/program/lib.php');
require_once($CFG->dirroot . '/admin/clickap/program/award/lib.php');

$programid = required_param('id', PARAM_INT);
$action = optional_param('action', 'details', PARAM_TEXT);

require_login();

$program = new program($programid);
$context = $program->get_context();
$navurl = new moodle_url('/admin/clickap/program/index.php', array('type' => $program->type));

if ($action == 'message') {
    require_capability('clickap/programs:configuremessages', $context);
} else {
    require_capability('clickap/programs:configuredetails', $context);
}

$PAGE->set_pagelayout('admin');
navigation_node::override_active_url($navurl, true);

$currenturl = new moodle_url('/admin/clickap/program/edit.php', array('id' => $program->id, 'action' => $action));

$PAGE->set_context($context);
$PAGE->set_url($currenturl);
$PAGE->set_heading($program->name);
$PAGE->set_title($program->name);
$PAGE->navbar->add($program->name);

$output = $PAGE->get_renderer('clickap_program');
$statusmsg = '';
$errormsg  = '';

$program->message = clean_text($program->message, FORMAT_HTML);
$editoroptions = array(
        'subdirs' => 0,
        'maxbytes' => 0,
        'maxfiles' => 0,
        'changeformat' => 0,
        'context' => $context,
        'noclean' => false,
        'trusttext' => false
        );

$imageoptions = program_image_options();
$program = file_prepare_standard_editor($program, 'message', $editoroptions, $context);

file_prepare_standard_filemanager($program, 'medal', $imageoptions, context_system::instance(), 'clickap_program', 'medal', $programid);
//file_prepare_standard_filemanager($program, 'banner', $imageoptions, context_system::instance(), 'clickap_program', 'banner', $programid);
file_prepare_standard_filemanager($program, 'award', $imageoptions, context_system::instance(), 'clickap_program', 'award', $programid);
$form_class = 'program_edit_' . $action . '_form';

$form = new $form_class($currenturl, array('program' => $program, 'action' => $action, 'editoroptions' => $editoroptions));

if ($form->is_cancelled()) {
    redirect(new moodle_url('/admin/clickap/program/edit.php', array('id' => $programid)));
} else if ($form->is_submitted() && $form->is_validated() && ($data = $form->get_data())) {
    if ($action == 'details') {
        $program->name = $data->name;
        $program->description = $data->description;
        $program->usermodified = $USER->id;
        //$program->issuername = $data->issuername;
        $program->issuerurl = $data->issuerurl;
        //$program->issuercontact = $data->issuercontact;
        $program->expiredate = ($data->expiry == 1) ? $data->expiredate : null;
        $program->expireperiod = ($data->expiry == 2) ? $data->expireperiod : null;
        
        $program->borderstyle = isset($data->borderstyle) ? $data->borderstyle : 0 ;
        // Need to unset message_editor options to avoid errors on form edit.
        unset($program->messageformat);
        unset($program->message_editor);
        
        
        if ($program->save()) {
            //programs_process_program_image($program, $form->save_temp_file('image'));
            $form->set_data($program);
            $statusmsg = get_string('changessaved');
        } else {
            $errormsg = get_string('error:save', 'clickap_program');
        }
    } else if ($action == 'message') {
        // Calculate next message cron if form data is different from original program data.
        if ($data->notification != $program->notification) {
            if ($data->notification > PROGRAM_MESSAGE_ALWAYS) {
                $program->nextcron = program_calculate_message_schedule($data->notification);
            } else {
                $program->nextcron = null;
            }
        }

        $program->message = clean_text($data->message_editor['text'], FORMAT_HTML);
        $program->messagesubject = $data->messagesubject;
        $program->notification = $data->notification;
        $program->attachment = $data->attachment;

        unset($program->messageformat);
        unset($program->message_editor);
        if ($program->save()) {
            $statusmsg = get_string('changessaved');
        } else {
            $errormsg = get_string('error:save', 'clickap_program');
        }
    }
}

echo $OUTPUT->header();
//echo $OUTPUT->heading(print_program_image($program, $context, 'small') . ' ' . $program->name);
echo $OUTPUT->heading(get_string('programtitle', 'clickap_program') . $program->name);
if ($errormsg !== '') {
    echo $OUTPUT->notification($errormsg);

} else if ($statusmsg !== '') {
    echo $OUTPUT->notification($statusmsg, 'notifysuccess');
}

echo $output->print_program_status_box($program);
$output->print_program_tabs($programid, $context, $action);

$form->display();

echo $OUTPUT->footer();