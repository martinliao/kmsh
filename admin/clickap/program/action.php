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

$programid = required_param('id', PARAM_INT);
$copy = optional_param('copy', 0, PARAM_BOOL);
$activate = optional_param('activate', 0, PARAM_BOOL);
$deactivate = optional_param('lock', 0, PARAM_BOOL);
$confirm   = optional_param('confirm', 0, PARAM_BOOL);
$return = optional_param('return', 0, PARAM_LOCALURL);

require_login();

$program = new program($programid);
$context = $program->get_context();
$navurl = new moodle_url('/admin/clickap/program/index.php', array('type' => $program->type));

$PAGE->set_pagelayout('admin');
navigation_node::override_active_url($navurl, true);

$PAGE->set_context($context);
$PAGE->set_url('/admin/clickap/program/action.php', array('id' => $program->id));

if ($return !== 0) {
    $returnurl = new moodle_url($return);
} else {
    $returnurl = new moodle_url('/admin/clickap/program/edit.php', array('id' => $program->id));
}
$returnurl->remove_params('awards');

if ($copy) {
    require_sesskey();
    require_capability('clickap/programs:createprogram', $context);

    $cloneid = $program->make_clone();
    // If a user can edit program details, they will be redirected to the edit page.
    if (has_capability('clickap/programs:configuredetails', $context)) {
        redirect(new moodle_url('/admin/clickap/program/index.php', array('id' => $cloneid, 'action' => 'details')));
    }
    redirect(new moodle_url('/admin/clickap/program/index.php', array('id' => $cloneid)));
}

if ($activate) {
    require_capability('clickap/programs:configurecriteria', $context);

    $PAGE->url->param('activate', 1);
    $status = ($program->status == PROGRAM_STATUS_INACTIVE) ? PROGRAM_STATUS_ACTIVE : PROGRAM_STATUS_ACTIVE_LOCKED;
    if ($confirm == 1) {
        require_sesskey();
        $program->set_status($status);
        $returnurl->param('msg', 'activatesuccess');

        if ($program->type == PROGRAM_TYPE_SITE) {
            // Review on cron if there are more than 1000 users who can earn a site-level program.
            $sql = 'SELECT COUNT(u.id) as num
                        FROM {user} u
                        LEFT JOIN {program_issued} bi
                            ON u.id = bi.userid AND bi.programid = :programid
                        WHERE bi.programid IS NULL AND u.id != :guestid AND u.deleted = 0';
            $toearn = $DB->get_record_sql($sql, array('programid' => $program->id, 'guestid' => $CFG->siteguest));

            if ($toearn->num < 1000) {
                $awards = $program->review_all_criteria();
                $returnurl->param('awards', $awards);
            } else {
                $returnurl->param('awards', 'cron');
            }
        } else {
            $awards = $program->review_all_criteria();
            $returnurl->param('awards', $awards);
        }
        redirect($returnurl);
    }

    $strheading = get_string('reviewprogram', 'clickap_program');
    $PAGE->navbar->add($strheading);
    $PAGE->set_title($strheading);
    $PAGE->set_heading($program->name);
    echo $OUTPUT->header();
    echo $OUTPUT->heading($strheading);

    $params = array('id' => $program->id, 'activate' => 1, 'sesskey' => sesskey(), 'confirm' => 1, 'return' => $return);
    $url = new moodle_url('/admin/clickap/program/action.php', $params);

    if (!$program->has_criteria()) {
        redirect($returnurl, get_string('error:cannotact', 'clickap_program') . get_string('nocriteria', 'clickap_program'), null, \core\output\notification::NOTIFY_ERROR);
    } else {
        $message = get_string('reviewconfirm', 'clickap_program', $program->name);
        echo $OUTPUT->confirm($message, $url, $returnurl);
    }
    echo $OUTPUT->footer();
    die;
}

if ($deactivate) {
    require_sesskey();
    require_capability('clickap/programs:configurecriteria', $context);

    $status = ($program->status == PROGRAM_STATUS_ACTIVE) ? PROGRAM_STATUS_INACTIVE : PROGRAM_STATUS_INACTIVE_LOCKED;
    $program->set_status($status);
    redirect($returnurl);
}
