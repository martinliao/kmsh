<?php
/**
 * Waiting enrolment plugin version specification.
 *
 * @package    enrol_waiting
 * @copyright  2019 CLICK-AP  {@link https://www.click-ap.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');

$enrolid = required_param('enrolid', PARAM_INT);
$confirm = optional_param('confirm', 0, PARAM_BOOL);

$instance = $DB->get_record('enrol', array('id'=>$enrolid, 'enrol'=>'waiting'), '*', MUST_EXIST);
$course = $DB->get_record('course', array('id'=>$instance->courseid), '*', MUST_EXIST);
$context = context_course::instance($course->id, MUST_EXIST);
$PAGE->set_context(context_system::instance());
require_login();
if (is_enrolled($context)) {
    redirect(new moodle_url('/'));
}

$plugin = enrol_get_plugin('waiting');
$PAGE->set_url('/enrol/waiting/standby.php', array('enrolid'=>$instance->id));
$PAGE->set_title($plugin->get_instance_name($instance));

if ($confirm && confirm_sesskey()) {
    $plugin->waitingenrol_user($instance, $USER->id);

    redirect(new moodle_url('/local/mooccourse/course_info.php', array('id'=>$course->id)));
}
$waitingcount = $DB->count_records('enrol_waiting', array('enrolid' => $instance->id, 'courseid'=>$course->id, 'status'=>0));
$data = new stdClass();
$data->coursename = format_string($course->fullname);
$data->waitingcount = $waitingcount;

echo $OUTPUT->header();
$yesurl = new moodle_url($PAGE->url, array('confirm'=>1, 'sesskey'=>sesskey()));
$nourl = new moodle_url('/local/mooccourse/course_info.php', array('id'=>$course->id));
$message = get_string('standbyenrolconfirm', 'enrol_waiting', $data);
echo $OUTPUT->confirm($message, $yesurl, $nourl);
echo $OUTPUT->footer();