<?php
/**
 * @package    enrol
 * @subpackage profile
 * @author     Maria Tan(CLICK-AP)
 * @author     Martin Freeman(CLICK-AP)
 * @copyright  CLICK-AP {@link https://www.click-ap.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once('lib.php');

require_sesskey();

$courseid   = required_param('courseid', PARAM_INT);
$instanceid = required_param('instanceid', PARAM_INT);

$course = $DB->get_record('course', array('id'=>$courseid), '*', MUST_EXIST);
$context = context_course::instance($course->id);

require_login($course);
require_capability('enrol/profile:config', $context);

if (!$courseid || !$instanceid) {
    print_string('ajax-error', 'enrol_profile');
    exit;
}

$customint1 = $DB->get_field('enrol', 'customint1', array('enrol' => 'profile', 'status' => 0, 'id' => $instanceid));
if($customint1){
    //purge users
    enrol_profile_plugin::purge_instance($instanceid, $context);
}
//force users
$nbenrolled = enrol_profile_plugin::process_enrolments(null, $instanceid);

if (ob_get_length() > 0 ) {
    ob_end_clean();
}

if($nbenrolled !== false) {
    print_string('ajax-okforced', 'enrol_profile', $nbenrolled);
}
else {
    print_string('ajax-error', 'enrol_profile');
}