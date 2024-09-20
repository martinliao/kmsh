<?php
/**
 *
 * @package    enrol
 * @subpackage profile
 * @author     Maria Tan(CLICK-AP)
 * @author     Martin Freeman(CLICK-AP)
 * @copyright  CLICK-AP {@link https://www.click-ap.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {

    // 1. Default role

    $options = get_default_enrol_roles(context_system::instance());

    $student = get_archetype_roles('student');
    $student_role = array_shift($student);

    //    $settings->add(new admin_setting_heading('enrol_myunil_defaults', get_string('enrolinstancedefaults', 'admin'),
    //            ''));
    $settings->add(new admin_setting_configselect('enrol_profile/default_roleid',
            get_string('defaultrole', 'enrol_profile'), get_string('defaultrole_desc', 'enrol_profile'),
            $student_role->id, $options));

    // 2. Fields to use in the selector
    $customfieldrecords = $DB->get_records('user_info_field');
    $customfields = [];
    if ($customfieldrecords) {
        foreach ($customfieldrecords as $customfieldrecord) {
            $customfields[$customfieldrecord->shortname] = format_string($customfieldrecord->name);
        }
        asort($customfields);
        $settings->add(new admin_setting_configmultiselect('enrol_profile/profilefields',
                get_string('profilefields', 'enrol_profile'), get_string('profilefields_desc', 'enrol_profile'),
                [], $customfields));
    }

    //$fields = enrol_profile_profile_field_options();
    $excludes = array(
        'id',              // makes no sense
        'mnethostid',      // makes no sense
        'timecreated',     // will be set to relative to the host anyway
        'timemodified',    // will be set to relative to the host anyway
        'auth',            // going to be set to 'mnet'
        'deleted',         // we should never get deleted users sent over, but don't send this anyway
        'confirmed',       // unconfirmed users can't log in to their home site, all remote users considered confirmed
        'password',        // no password for mnet users
        'theme',           // handled separately
        'lastip',          // will be set to relative to the host anyway
    );

    // these are the ones that user_not_fully_set_up will complain about
    // and also special case ones
    $forced = array(
        'username',
        'email',
        'firstname',
        'lastname',
        'auth',
        'wwwroot',
        'session.gc_lifetime',
        '_mnet_userpicture_timemodified',
        '_mnet_userpicture_mimetype',
    );

    // these are the ones we used to send/receive (pre 2.0)
    $legacy = array(
        'username',
        'email',
        'auth',
        'deleted',
        'firstname',
        'lastname',
        'city',
        'country',
        'lang',
        'timezone',
        'description',
        'mailformat',
        'maildigest',
        'maildisplay',
        'htmleditor',
        'wwwroot',
        'picture',
    );

    // get a random user record from the database to pull the fields off
    $randomuser = $DB->get_record('user', array(), '*', IGNORE_MULTIPLE);
    foreach ($randomuser as $key => $discard) {
        if (in_array($key, $excludes) || in_array($key, $forced)) {
            continue;
        }
        $fields[$key] = $key;
    }
    //$info = array(
    //    'forced'   => $forced,
    //    'optional' => $fields,
    //    'legacy'   => $legacy,
    //);
    if ($fields) {
        //$customfields = [];
        foreach ($fields as $field) {
            //$customfields[$field] = $field;
            if(in_array($field, array('department','institution'))){
                $customfields[$field] = get_string($field);
            }
        }
        asort($customfields);
    }
    $settings->add(new admin_setting_configmultiselect('enrol_profile/profilefields',
                get_string('profilefields', 'enrol_profile'), get_string('profilefields_desc', 'enrol_profile'),
                [], $customfields));/**/

    // 3. Fields to update via Shibboleth login
    if (in_array('shibboleth', get_enabled_auth_plugins())) {
        $settings->add(new admin_setting_configtextarea('enrol_profile/mappings',
                get_string('mappings', 'enrol_profile'), get_string('mappings_desc', 'enrol_profile'), '',
                PARAM_TEXT, 60, 10));
    }
    
    $settings->add(new admin_setting_configcheckbox('enrol_profile/sendcoursewelcomemessage',
        get_string('sendcoursewelcomemessage', 'enrol_profile'), get_string('sendcoursewelcomemessage_help', 'enrol_profile'), 1));
}