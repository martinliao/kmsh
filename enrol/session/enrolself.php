<?php
require('../../config.php');

$instanceid = required_param('instance', PARAM_INT);

$instance = $DB->get_record('enrol', array('id'=>$instanceid, 'enrol'=>'session'), '*', MUST_EXIST);
$course = $DB->get_record('course', array('id'=>$instance->courseid), '*', MUST_EXIST);

$session = $DB->get_record('enrol_session', array('instanceid'=>$instanceid), '*', MUST_EXIST);

require_login();
$context = context_course::instance($course->id);
$PAGE->set_context($context);
$enrol = enrol_get_plugin('session');

if(!is_enrolled($context, $USER, '',true)){
    $sessions = $enrol->enrol_session_construct_sessions_data_for_add($session);
    if(!empty($sessions)){
        $timestart = $sessions[0];
        $timeend = end($sessions) + DAYSECS -1;
    }else {
        $timestart = $session->sessdate;
        $timeend = $session->sessdate + $session->duration;
    }

    $enrol->enrol_user($instance, $USER->id, $instance->roleid, $timestart, $timeend);    
    if ($instance->customint4) {
        $enrol->email_welcome_message($instance, $USER);
    }
}
      
redirect(new moodle_url('/course/view.php', array('id'=>$course->id)));

