<?php
/**
 * signin enrolment plugin settings and presets.
 * 
 * @package    enrol_signin
 * @copyright  2018 CLICK-AP {@link https://www.click-ap.com/}
 * @author     Wei Chi & Maria Tan
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {

    //--- enrol instance defaults ----------------------------------------------------------------------------
    $settings->add(new admin_setting_heading('enrol_signin_defaults',
        get_string('enrolinstancedefaults', 'admin'), get_string('enrolinstancedefaults_desc', 'admin')));

    if (!during_initial_install()) {
        $options = get_default_enrol_roles(context_system::instance());
        $student = get_archetype_roles('student');
        $student = reset($student);
        $settings->add(new admin_setting_configselect('enrol_signin/roleid',
            get_string('defaultrole', 'enrol_signin'), get_string('defaultrole_desc', 'enrol_signin'), $student->id, $options));
    }
}