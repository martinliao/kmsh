<?php
/**
 * @package    enrol
 * @subpackage profile
 * @author     Maria Tan(CLICK-AP)
 * @author     Martin Freeman(CLICK-AP)
 * @copyright  CLICK-AP {@link https://www.click-ap.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
//require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require(__DIR__ . '/../../config.php');
//require_once('lib.php');

header('Content-type: application/javascript');

$courseid = required_param('courseid', PARAM_INT);
$context = context_course::instance($courseid);
$PAGE->set_context($context);

$customfieldrecords = $DB->get_records('user_info_field');
$customfields = array();
foreach ($customfieldrecords as $customfieldrecord) {
    $customfields[$customfieldrecord->shortname] = $customfieldrecord->name;
}
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
    $userfields[$key] = $key;
}

if ($userfields) {
    //$customfields = [];
    foreach ($userfields as $field) {
        $customfields[$field] = $field;
    }
    asort($customfields);
}

$items = array();

$profilefields = explode(',', get_config('enrol_profile', 'profilefields'));

foreach ($profilefields as $profilefield) {
    if (array_key_exists($profilefield, $customfields)) {
        if(get_string_manager()->string_exists($customfields[$profilefield], 'moodle')){
            $items[] = array('value' => $profilefield, 'label' => get_string($customfields[$profilefield]));
        }else{
            $name = $customfields[$profilefield];
            $items[] = array('value' => $profilefield, 'label' => format_string($name));
        }
    }
}

$jsvar = json_encode($items);

echo <<<EOF
M.enrol_profile = M.enrol_profile || {};
M.enrol_profile.paramList = {$jsvar};
EOF;

