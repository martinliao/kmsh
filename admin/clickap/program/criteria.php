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
$update = optional_param('update', 0, PARAM_INT);

require_login();

$program = new program($programid);
$context = context_system::instance();
$navurl = new moodle_url('/admin/clickap/program/index.php', array('type' => $program->type));

require_capability('clickap/programs:configurecriteria', $context);

$PAGE->set_pagelayout('admin');
navigation_node::override_active_url($navurl, true);

$currenturl = new moodle_url('/admin/clickap/program/criteria.php', array('id' => $program->id));

$PAGE->set_context($context);
$PAGE->set_url($currenturl);
$PAGE->set_heading($program->name);
$PAGE->set_title($program->name);
$PAGE->navbar->add($program->name);

$output = $PAGE->get_renderer('clickap_program');
$msg = optional_param('msg', '', PARAM_TEXT);
$emsg = optional_param('emsg', '', PARAM_TEXT);

if ((($update == PROGRAM_CRITERIA_AGGREGATION_ALL) || ($update == PROGRAM_CRITERIA_AGGREGATION_ANY))) {
    require_sesskey();
    $obj = new stdClass();
    $obj->id = $program->criteria[PROGRAM_CRITERIA_TYPE_OVERALL]->id;
    $obj->method = $update;
    if ($DB->update_record('program_criteria', $obj)) {
        $msg = 'criteriaupdated';
    } else {
        $emsg = get_string('error:save', 'clickap_program');
    }
}

echo $OUTPUT->header();
echo $OUTPUT->heading($program->name);

if ($emsg !== '') {
    echo $OUTPUT->notification($emsg);
} else if ($msg !== '') {
    echo $OUTPUT->notification(get_string($msg, 'clickap_program'), 'notifysuccess');
}

echo $output->print_program_status_box($program);
$output->print_program_tabs($programid, $context, 'criteria');

if (!$program->is_locked() && !$program->is_active()) {
    echo $output->print_criteria_actions($program);
}

if ($program->has_criteria()) {
    ksort($program->criteria);

    foreach ($program->criteria as $crit) {
        $crit->config_form_criteria($program);
    }
} else {
    echo $OUTPUT->box(get_string('addcriteriatext', 'clickap_program'));
}

echo $OUTPUT->footer();