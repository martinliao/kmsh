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

$programid  = required_param('id', PARAM_INT);

require_login();
$program = new program($programid);
$context = $program->get_context();
$user = $USER;
$urlparams = array('id' => $programid);
$hdr = get_string('program', 'clickap_program');
$returnurl = new moodle_url('/admin/clickap/program/award_download.php', $urlparams);
$PAGE->set_url($returnurl);

$title = get_string('pluginname', 'clickap_program');
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('admin');

$hasprogram = programs_get_user_has_program($program->id, $user->id);
if(!$hasprogram){
    print_error(get_string('noawards', 'clickap_program'));
}else{
    if (!has_any_capability(array('clickap/programs:viewprogram'), $PAGE->context)) {
        redirect($CFG->wwwroot.'/my');
    }
    require_once("$CFG->libdir/pdflib.php");
    require("$CFG->dirroot/admin/clickap/program/award/lib.php");
    make_cache_directory('tcpdf');

    require("$CFG->dirroot/admin/clickap/program/award/certificate.php");
    
    $output = $PAGE->get_renderer('clickap_program');

    $programname = format_string($program->name, true, array('context' => $context));
    //$filename = $programname . '_' . fullname($user);
    $filename = $programname . '_' . $user->firstname;
    $filename = core_text::entities_to_utf8($filename);
    $filename = strip_tags($filename);
    $filename = rtrim($filename, '.');
    $filename = str_replace('&', '_', $filename);
    $filename = clean_filename($filename) . '.pdf';
    // PDF contents are now in $file_contents as a string.
    $filecontents = $pdf->Output('', 'S');

    send_file($filecontents, $filename, 0, 0, true, true, 'application/pdf');
}