<?php
/**
 * Version details.
 *
 * @package    clickap
 * @subpackage program
 * @copyright  2018 Click-AP <elaine@click-ap.com>
 * @license    http://www.click-ap.com/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot . '/admin/clickap/program/lib.php');

function clickap_program_cron() {
    global $CFG;

    clickap_program_review_cron();
    clickap_program_message_cron();
}

/**
 * Reviews criteria and awards programs
 *
 * First find all programs that can be earned, then reviews each program.
 * (Not sure how efficient this is timewise).
 */
function clickap_program_review_cron() {
    global $DB, $CFG;
    $total = 0;

    $sql = 'SELECT id
                FROM {program}
                WHERE (status = :active OR status = :activelocked)
                    AND (type = :site )';
    $programparams = array(
                    'active' => PROGRAM_STATUS_ACTIVE,
                    'activelocked' => PROGRAM_STATUS_ACTIVE_LOCKED,
                    'site' => PROGRAM_TYPE_SITE
                    );
    $params = array_merge($programparams);
    $programs = $DB->get_fieldset_sql($sql, $params);

    mtrace('Started reviewing available programs.');
    foreach ($programs as $pid) {
        $program = new program($pid);

        if ($program->has_criteria()) {
            if (debugging()) {
                mtrace('Processing program "' . $program->name . '"...');
            }

            $issued = $program->review_all_criteria();

            if (debugging()) {
                mtrace('...program was issued to ' . $issued . ' users.');
            }
            $total += $issued;
        }
    }

    mtrace('Programs were issued ' . $total . ' time(s).');
}

/**
 * Sends out scheduled messages to program creators
 *
 */
function clickap_program_message_cron() {
    global $DB;

    mtrace('Sending scheduled program notifications.');

    $scheduled = $DB->get_records_select('program', 'notification > ? AND (status != ?) AND nextcron < ?',
                            array(PROGRAM_MESSAGE_ALWAYS, PROGRAM_STATUS_ARCHIVED, time()),
                            'notification ASC', 'id, name, notification, usercreated as creator, timecreated');

    foreach ($scheduled as $sch) {
        // Send messages.
        clickap_program_assemble_notification($sch);

        // Update next cron value.
        $nextcron = program_calculate_message_schedule($sch->notification);
        $DB->set_field('program', 'nextcron', $nextcron, array('id' => $sch->id));
    }
}

/**
 * Creates single message for all notification and sends it out
 *
 * @param object $program A program which is notified about.
 */
function clickap_program_assemble_notification(stdClass $program) {
    global $DB;

    $userfrom = core_user::get_noreply_user();
    $userfrom->maildisplay = true;
    
    if ($msgs = $DB->get_records_select('program_issued', 'issuernotified IS NULL AND programid = ?', array($program->id))) {
        // Get program creator.
        $creator = $DB->get_record('user', array('id' => $program->creator), '*', MUST_EXIST);
        $creatorsubject = get_string('creatorsubject', 'clickap_program', $program->name);
        $creatormessage = '';

        // Put all messages in one digest.
        foreach ($msgs as $msg) {
            $issuedlink = html_writer::link(new moodle_url('/my', array()), $program->name);
            $recipient = $DB->get_record('user', array('id' => $msg->userid), '*', MUST_EXIST);

            $a = new stdClass();
            $a->user = fullname($recipient);
            $a->link = $issuedlink;
            $creatormessage .= get_string('creatorbody', 'clickap_program', $a);
            $DB->set_field('program_issued', 'issuernotified', time(), array('programid' => $msg->programid, 'userid' => $msg->userid));
        }

        // Create a message object.
        $eventdata = new \core\message\message();
        $eventdata->component         = 'clickap_program';
        $eventdata->name              = 'programcreatornotice';
        $eventdata->userfrom          = $userfrom;
        $eventdata->userto            = $creator;
        $eventdata->notification      = 1;
        $eventdata->subject           = $creatorsubject;
        $eventdata->fullmessage       = format_text_email($creatormessage, FORMAT_HTML);
        $eventdata->fullmessageformat = FORMAT_PLAIN;
        $eventdata->fullmessagehtml   = $creatormessage;
        $eventdata->smallmessage      = $creatorsubject;

        message_send($eventdata);
    }
}
