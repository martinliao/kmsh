<?php

/**
 * Adds new instance of enrol_session to specified course
 * or edits current instance.
 *
 * @package    enrol_session
 * @copyright  2015 Click-AP  {@link http://www.click-ap.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');
require_once('edit_form.php');

$courseid   = required_param('courseid', PARAM_INT);
$instanceid = optional_param('id', 0, PARAM_INT);
$from       = optional_param('from', null, PARAM_ALPHANUMEXT);

$course = $DB->get_record('course', array('id'=>$courseid), '*', MUST_EXIST);
$context = context_course::instance($course->id, MUST_EXIST);

require_login($course);
require_capability('enrol/self:config', $context); // session

$PAGE->set_url('/enrol/session/edit.php', array('courseid'=>$course->id, 'id'=>$instanceid));
$PAGE->set_pagelayout('admin');

$return = new moodle_url('/enrol/instances.php', array('id'=>$course->id));

// If is coming from enrol_track.
$fromparams = array();
if ($from === 'track') {
    $trackid = optional_param('trackid', 0, PARAM_INT); // track instance
    $return = new moodle_url('/enrol/track/manage.php', array('id'=>$trackid));
    $fromparams = array('from'=>$from, 'trackid'=>$trackid);
}

if (!enrol_is_enabled('session')) {
    redirect($return);
}

/** @var enrol_session_plugin $plugin */
$plugin = enrol_get_plugin('session');

if ($instanceid) {
    $instance = $DB->get_record('enrol', array('courseid'=>$course->id, 'enrol'=>'session', 'id'=>$instanceid), '*', MUST_EXIST);

} else {
    require_capability('moodle/course:enrolconfig', $context);
    // No instance yet, we have to add new instance.
    navigation_node::override_active_url(new moodle_url('/enrol/instances.php', array('id'=>$course->id)));

    $instance = (object)$plugin->get_instance_defaults();
    $instance->id       = null;
    $instance->courseid = $course->id;
    $instance->status   = ENROL_INSTANCE_ENABLED; // Do not use default for automatically created instances here.
}

// Merge these settings to instance for edit_form.
if($sess = $DB->get_record('enrol_session', array('instanceid' => $instance->id), '*')){
    $instance->sessiondate      = $sess->sessdate;
    $hours = (int)($sess->duration/HOURSECS);
    $minutes = ($sess->duration - $hours*HOURSECS) /MINSECS;
    $instance->durtime          = array('hours'=>$hours, 'minutes'=>$minutes);
    // multi
    $instance->addmultiply      = $sess->addmultiply;
    $instance->sessionenddate   = $sess->sessenddate;
    $instance->period           = $sess->period;
    $sdays = explode(',', $sess->sdays);
    $weekdays = array();
    foreach($sdays as $key => $week){
        $weekdays[$week] = "1";
    }
    $instance->sdays            = $weekdays;
}

$mform = new enrol_session_edit_form(NULL, array($instance, $plugin, $context, $fromparams));

if ($mform->is_cancelled()) {
    redirect($return);

} else if ($data = $mform->get_data()) {

    if (!isset($data->customint6)) {
        // Add previous value of newenrols if disabled.
        $data->customint6 = $instance->customint6;
    }

    if ($instance->id) {
        //$reset = ($instance->status != $data->status);
        //$instance->status         = $data->status;
        $instance->name           = $data->name;
        $instance->customint3     = $data->customint3;
        $instance->customint4     = $data->customint4;
        $instance->customint6     = $data->customint6;
        $instance->customtext1    = $data->customtext1;
        $instance->roleid         = $data->roleid;
        $instance->enrolstartdate = $data->enrolstartdate;
        $instance->enrolenddate   = $data->enrolenddate;
        $instance->timemodified   = time();
        $DB->update_record('enrol', $instance);
        
        // process session data
        $existrecord = true;
        if(!isset($sess)){
            $sess = $DB->get_record('enrol_session', array('instanceid' => $instance->id), '*', MUST_EXIST);
        }
        $sess->name             = $data->name;
        $sess->courseid         = (int)$courseid;
        $sess->sessdate         = $data->sessiondate;
        $sess->duration         = $data->durtime['hours']*HOURSECS + $data->durtime['minutes']*MINSECS;
        if (isset($data->addmultiply)) {
            $sess->addmultiply      = $data->addmultiply;
            $sess->period           = $data->period;
            $sess->sdays            = implode(',', array_keys($data->sdays));
            $sess->sessenddate      = $data->sessionenddate;
        }
        $sess->timeupdated      = time();
        // trackid, groupid

        $DB->update_record("enrol_session", $sess);
        
        //if ($reset) {        //    $context->mark_dirty();        //}
    } else {
        $fields = array(
            'status'          => ENROL_INSTANCE_ENABLED,
            'name'            => $data->name,
            'password'        => null,
            'customint1'      => 0,
            'customint2'      => 0,
            'customint3'      => $data->customint3,
            'customint4'      => $data->customint4,
            'customint6'      => 1,
            'customtext1'     => $data->customtext1,
            'roleid'          => $data->roleid,
            'expirynotify'    => 0,
            'notifyall'       => 0,
            'enrolstartdate'  => $data->enrolstartdate,
            'enrolenddate'    => $data->enrolenddate);
        $recno = $plugin->add_instance($course, $fields);
        // process session data
        $sess = new stdClass();
        $sess->name             = $data->name;
        $sess->courseid         = (int)$courseid;
        $sess->instanceid       = (int)$recno;
        $sess->sessdate         = $data->sessiondate;
        $sess->duration         = $data->durtime['hours']*HOURSECS + $data->durtime['minutes']*MINSECS;
        if (isset($data->addmultiply)) {
            $sess->addmultiply      = $data->addmultiply;
            $sess->period           = $data->period;
            $sess->sdays            = implode(',', array_keys($data->sdays));
            $sess->sessenddate      = $data->sessionenddate;
        }
        $sess->timeupdated      = time();
        // trackid, groupid
        
        $DB->insert_record("enrol_session", $sess);
    }

    redirect($return);
}

$PAGE->set_heading($course->fullname);
$PAGE->set_title(get_string('pluginname', 'enrol_session'));

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('pluginname', 'enrol_session'));
$mform->display();
echo $OUTPUT->footer();
