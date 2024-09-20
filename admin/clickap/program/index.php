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

$type       = optional_param('type', '1', PARAM_INT);
$page       = optional_param('page', 0, PARAM_INT);
$deactivate = optional_param('lock', 0, PARAM_INT);
$sortby     = optional_param('sort', 'timecreated', PARAM_ALPHA);
$sorthow    = optional_param('dir', 'ASC', PARAM_ALPHA);
$confirm    = optional_param('confirm', false, PARAM_BOOL);
$delete     = optional_param('delete', 0, PARAM_INT);
$archive    = optional_param('archive', 0, PARAM_INT);
$msg        = optional_param('msg', '', PARAM_TEXT);

if (!in_array($sortby, array('timecreated', 'status', 'name'))) {
    $sortby = 'timecreated';
}

if ($sorthow != 'ASC' and $sorthow != 'DESC') {
    $sorthow = 'ASC';
}

if ($page < 0) {
    $page = 0;
}

require_login();

$err = '';
$urlparams = array('sort' => $sortby, 'dir' => $sorthow, 'page' => $page);
$urlparams['type'] = $type;

$hdr = get_string('manageprograms', 'clickap_program');
$returnurl = new moodle_url('/admin/clickap/program/index.php', $urlparams);
$PAGE->set_url($returnurl);

$title = get_string('pluginname', 'clickap_program');
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('admin');
$PAGE->set_heading($title . ': ' . $hdr);
navigation_node::override_active_url(new moodle_url('/admin/clickap/program/index.php', array('type' => 1)), true);

if (!has_any_capability(array(
        'clickap/programs:viewawarded',
        'clickap/programs:createprogram',
        'clickap/programs:awardprogram',
        'clickap/programs:configuremessages',
        'clickap/programs:configuredetails',
        'clickap/programs:deleteprogram'), $PAGE->context)) {
    redirect($CFG->wwwroot);
}

$PAGE->set_title($hdr);
//$PAGE->requires->js('/program /backpack.js');
//$PAGE->requires->js_init_call('check_site_access', null, false);

$output = $PAGE->get_renderer('clickap_program');

if (($delete || $archive) && has_capability('clickap/programs:deleteprogram', $PAGE->context)) {
    $programid = ($archive != 0) ? $archive : $delete;
    $program  = new program($programid);
    if (!$confirm) {
        echo $output->header();
        // Archive this program?
        echo $output->heading(get_string('archiveprogram', 'clickap_program', $program->name));
        $archivebutton = $output->single_button(
                            new moodle_url($PAGE->url, array('archive' => $program->id, 'confirm' => 1)),
                            get_string('archiveconfirm', 'clickap_program'));
        echo $output->box(get_string('archivehelp', 'clickap_program') . $archivebutton, 'generalbox');

        // Delete this program?
        echo $output->heading(get_string('delprogram', 'clickap_program', $program->name));
        $deletebutton = $output->single_button(
                            new moodle_url($PAGE->url, array('delete' => $program->id, 'confirm' => 1)),
                            get_string('delconfirm', 'clickap_program'));
        echo $output->box(get_string('deletehelp', 'clickap_program') . $deletebutton, 'generalbox');

        // Go back.
        echo $output->action_link($returnurl, get_string('cancel'));

        echo $output->footer();
        die();
    } else {
        require_sesskey();
        $archiveonly = ($archive != 0) ? true : false;
        $program->delete($archiveonly);
        redirect($returnurl);
    }
}

if ($deactivate && has_capability('clickap/programs:configuredetails', $PAGE->context)) {
    require_sesskey();
    $program  = new program($deactivate);
    if ($program->is_locked()) {
        $program->set_status(PROGRAM_STATUS_INACTIVE_LOCKED);
    } else {
        $program->set_status(PROGRAM_STATUS_INACTIVE);
    }
    $msg = 'deactivatesuccess';
    $returnurl->param('msg', $msg);
    redirect($returnurl);
}

echo $OUTPUT->header();
echo $OUTPUT->heading_with_help($PAGE->heading, 'program', 'clickap_program');
echo $OUTPUT->box('', 'notifyproblem hide', 'check_connection');

$totalcount = count(programs_get_programs($type,  '', '', '' , 0, 0));
$records = programs_get_programs($type, '', $sortby, $sorthow, $page, 25);

if ($totalcount) {
    echo $output->heading(get_string('programstoearn', 'clickap_program', $totalcount), 4);

    if ($err !== '') {
        echo $OUTPUT->notification($err, 'notifyproblem');
    }

    if ($msg !== '') {
        echo $OUTPUT->notification(get_string($msg, 'clickap_program'), 'notifysuccess');
    }

    $programs             = new program_management($records);
    $programs->sort       = $sortby;
    $programs->dir        = $sorthow;
    $programs->page       = $page;
    $programs->perpage    = 25;
    $programs->totalcount = $totalcount;

    echo $output->render($programs);
} else {
    echo $output->notification(get_string('noprograms', 'clickap_program'));

    if (has_capability('clickap/programs:createprogram', $PAGE->context)) {
        echo "<BR />".$OUTPUT->single_button(new moodle_url('newprogram.php', array('type' => $type)),
            get_string('newprogram', 'clickap_program'));
    }
}

echo $OUTPUT->footer();
