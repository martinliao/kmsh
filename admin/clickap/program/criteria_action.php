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

$programid = optional_param('programid', 0, PARAM_INT);
$crit    = optional_param('crit', 0, PARAM_INT);
$type    = optional_param('type', 0, PARAM_INT); // Criteria type.
$delete  = optional_param('delete', 0, PARAM_BOOL);
$confirm = optional_param('confirm', 0, PARAM_BOOL);

require_login();

$return = new moodle_url('/admin/clickap/program/criteria.php', array('id' => $programid));
$program = new program($programid);
$context = context_system::instance();
$navurl = new moodle_url('/admin/clickap/program/index.php', array('type' => $program->type));

// Make sure that no actions available for locked or active programs.
if ($program->is_active() || $program->is_locked()) {
    redirect($return);
}

$PAGE->set_pagelayout('admin');
navigation_node::override_active_url($navurl, true);
$PAGE->set_context($context);
$PAGE->set_url('/admin/clickap/program/criteria_action.php');
$PAGE->set_heading($program->name);
$PAGE->set_title($program->name);

if ($delete && has_capability('clickap/programs:configurecriteria', $context)) {
    if ($type == PROGRAM_CRITERIA_TYPE_OVERALL) {
        redirect($return, get_string('error:cannotdeletecriterion', 'clickap_program'));
    }
    if (!$confirm) {
        $optionsyes = array('confirm' => 1, 'sesskey' => sesskey(), 'programid' => $programid, 'delete' => true, 'type' => $type);

        $strdeletecheckfull = get_string('delcritconfirm', 'clickap_program');

        echo $OUTPUT->header();
        $formcontinue = new single_button(new moodle_url('/admin/clickap/program/criteria_action.php', $optionsyes), get_string('yes'));
        $formcancel = new single_button($return, get_string('no'), 'get');
        echo $OUTPUT->confirm($strdeletecheckfull, $formcontinue, $formcancel);
        echo $OUTPUT->footer();

        die();
    }

    require_sesskey();
    if (count($program->criteria) == 2) {
        // Remove overall criterion as well.
        $program->criteria[$type]->delete();
        $program->criteria[PROGRAM_CRITERIA_TYPE_OVERALL]->delete();
    } else {
        $program->criteria[$type]->delete();
    }
    $return->param('msg', 'criteriadeleted');
    redirect($return);
}

redirect($return);