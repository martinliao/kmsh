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
$sortby     = optional_param('sort', 'dateissued', PARAM_ALPHA);
$sorthow    = optional_param('dir', 'DESC', PARAM_ALPHA);
$page       = optional_param('page', 0, PARAM_INT);

require_login();

if (!in_array($sortby, array('firstname', 'lastname', 'dateissued'))) {
    $sortby = 'dateissued';
}

if ($sorthow != 'ASC' and $sorthow != 'DESC') {
    $sorthow = 'DESC';
}

if ($page < 0) {
    $page = 0;
}

$program = new program($programid);
$context = $program->get_context();
$navurl = new moodle_url('/admin/clickap/program/index.php', array('type' => $program->type));

$PAGE->set_pagelayout('admin');
navigation_node::override_active_url($navurl, true);

$PAGE->set_context($context);
$PAGE->set_url('/admin/clickap/program/recipients.php', array('id' => $programid, 'sort' => $sortby, 'dir' => $sorthow));
$PAGE->set_heading($program->name);
$PAGE->set_title($program->name);
$PAGE->navbar->add($program->name);

$output = $PAGE->get_renderer('clickap_program');

echo $output->header();
echo $OUTPUT->heading($program->name);

echo $output->print_program_status_box($program);
$output->print_program_tabs($programid, $context, 'awards');

// Add button for program manual award.
if ($program->has_manual_award_criteria() && has_capability('clickap/programs:awardprogram', $context) && $program->is_active()) {
    $url = new moodle_url('/admin/clickap/program/award.php', array('id' => $program->id));
    echo $OUTPUT->box($OUTPUT->single_button($url, get_string('award', 'clickap_program')), 'clearfix mdl-align');
}

$namefields = get_all_user_name_fields(true, 'u');
$sql = "SELECT b.userid, b.dateissued, b.uniquehash, $namefields
    FROM {program_issued} b INNER JOIN {user} u
        ON b.userid = u.id
    WHERE b.programid = :programid AND u.deleted = 0
    ORDER BY $sortby $sorthow";

$totalcount = $DB->count_records('program_issued', array('programid' => $program->id));

if ($program->has_awards()) {
    $users = $DB->get_records_sql($sql, array('programid' => $program->id), $page * PROGRAM_PERPAGE, PROGRAM_PERPAGE);
    $recipients             = new program_recipients($users);
    $recipients->sort       = $sortby;
    $recipients->dir        = $sorthow;
    $recipients->page       = $page;
    $recipients->perpage    = PROGRAM_PERPAGE;
    $recipients->totalcount = $totalcount;

    echo $output->render($recipients);
} else {
    echo $output->notification(get_string('noawards', 'clickap_program'));
}

echo $output->footer();