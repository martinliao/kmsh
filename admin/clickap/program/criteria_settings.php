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
require_once($CFG->dirroot . '/admin/clickap/program/criteria_form.php');

$programid = optional_param('programid', 0, PARAM_INT);
$type    = optional_param('type', 0, PARAM_INT); // Criteria type.
$edit    = optional_param('edit', 0, PARAM_INT); // Edit criteria ID.
$crit    = optional_param('crit', 0, PARAM_INT); // Criteria ID for managing params.
$param   = optional_param('param', '', PARAM_TEXT); // Param name for managing params.
$goback    = optional_param('cancel', '', PARAM_TEXT);
$addcourse = optional_param('addcourse', '', PARAM_TEXT);
$submitcourse = optional_param('submitcourse', '', PARAM_TEXT);

require_login();

$return = new moodle_url('/admin/clickap/program/criteria.php', array('id' => $programid));
$program = new program($programid);
$context = context_system::instance();
$navurl = new moodle_url('/admin/clickap/program/index.php', array('type' => $program->type));

require_capability('clickap/programs:configurecriteria', $context);

if (!empty($goback)) {
    redirect($return);
}

// Make sure that no actions available for locked or active programs.
if ($program->is_active() || $program->is_locked()) {
    redirect($return);
}

$PAGE->set_pagelayout('admin');
navigation_node::override_active_url($navurl, true);

$urlparams = array('programid' => $programid, 'edit' => $edit, 'type' => $type, 'crit' => $crit);
$PAGE->set_context($context);
$PAGE->set_url('/admin/clickap/program/criteria_settings.php', $urlparams);
$PAGE->set_heading($program->name);
$PAGE->set_title($program->name);
$PAGE->navbar->add($program->name, new moodle_url('/admin/clickap/program/edit.php', array('id' => $program->id)))->add(get_string('criteria_' . $type, 'clickap_program'));

$cparams = array('criteriatype' => $type, 'programid' => $program->id);
if ($edit) {
    $criteria = $program->criteria[$type];
    $msg = 'criteriaupdated';
} else {
    $criteria = program_award_criteria::build($cparams);
    $msg = 'criteriacreated';
}

$mform = new program_edit_criteria_form($FULLME, array('criteria' => $criteria, 'addcourse' => $addcourse, 'course' => $program->courseid));

if (!empty($addcourse)) {
    if ($data = $mform->get_data()) {
        // If no criteria yet, add overall aggregation.
        if (count($program->criteria) == 0) {
            $criteria_overall = program_award_criteria::build(array('criteriatype' => PROGRAM_CRITERIA_TYPE_OVERALL, 'programid' => $program->id));
            $criteria_overall->save(array('agg' => PROGRAM_CRITERIA_AGGREGATION_ALL));
        }

        $id = $criteria->add_courses($data->courses);
        redirect(new moodle_url('/admin/clickap/program/criteria_settings.php',
            array('programid' => $programid, 'edit' => true, 'type' => PROGRAM_CRITERIA_TYPE_COURSESET, 'crit' => $id)));
    }
} else if ($data = $mform->get_data()) {
    // If no criteria yet, add overall aggregation.
    if (count($program->criteria) == 0) {
        $criteria_overall = award_criteria::build(array('criteriatype' => PROGRAM_CRITERIA_TYPE_OVERALL, 'programid' => $program->id));
        $criteria_overall->save(array('agg' => PROGRAM_CRITERIA_AGGREGATION_ALL));
    }
    
    $criteria->save((array)$data);
    $return->param('msg', $msg);
    redirect($return);
}

echo $OUTPUT->header();
$mform->display();
echo $OUTPUT->footer();