<?php
/**
 * plugin infomation
 * 
 * @package    block
 * @subpackage externalverify
 * @author     Elaine Chen(CLICK-AP)
 * @copyright  CLICK-AP {@link https://www.click-ap.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
function block_external_create_course($data, $user = NULL, $filesoptions = NULL) {
    global $DB, $USER;

    if(empty($user)){
        $user = $USER;
    }
    $data->userid = $user->id;
    $context = context_user::instance($user->id);

    $data->hourcategories = '';
    /*
    if(!empty($data->hourcategory)){
        foreach($data->hourcategory as $key => $value){
            $data->hourcategories .= $key.',';
        }
    }
    */
    /*click-ap*/
    if(isset($data->hourcategory[1]) && !empty($data->hourcategory[1])){
        $data->hourcategories = implode(',', $data->hourcategory[1]);
    }

    $supervisor = block_externalverify_get_verify_supervisor($user);
    
    $data->supervisor = $supervisor;
    $data->timecreated = time();

    $dataid = $DB->insert_record('course_external', $data);
    block_external_appy_send_notifications($data);
    if ($filesoptions) {
        $data = file_postupdate_standard_filemanager($user, 'attachments', $filesoptions, $context, 'user', 'externalcourse_attachments', $dataid);
    }
    
        /*
    $event = \core\event\course_created::create(array(
        'objectid' => $course->id,
        'context' => context_course::instance($course->id),
        'other' => array('shortname' => $course->shortname,
                         'fullname' => $course->fullname)
    ));
    $event->trigger();
    */
    return $dataid;
}

function block_external_appy_send_notifications($data){
    global $CFG, $DB;
    require_once($CFG->libdir.'/messagelib.php');

    $managers = explode(',', $data->supervisor);
    foreach($managers as $manager){
        if(empty($manager)){
            continue;
        }
        $user = $DB->get_record('user', array('username'=>$manager));
        block_external_send_notifications($data, $user);
    }
}

function block_external_send_notifications($data, $supervisor){
    global $CFG, $DB;
    
    $user = $DB->get_record('user', array('id'=>$data->userid));
    
    $mailmsg = new stdClass();
    $mailmsg->timecreated = block_externalverify_get_date_format($data->timecreated);
    $mailmsg->fullname = $data->fullname;
    //$mailmsg->supervisor = fullname($supervisor);
    $mailmsg->applyuser = fullname($user);
    $message = get_string('mail_apply', 'block_externalverify', $mailmsg);
    
    $eventdata                     = new \core\message\message();//new \core\message\message();
    $eventdata->courseid           = SITEID;
    $eventdata->component          = 'block_externalverify';
    $eventdata->name               = 'notification';
    $eventdata->userfrom           = core_user::get_noreply_user();
    $eventdata->userto             = $supervisor;
    $eventdata->subject            = get_string('mail_apply_subject', 'block_externalverify');
    $eventdata->fullmessage        = html_to_text($message);
    $eventdata->fullmessageformat  = FORMAT_HTML;
    $eventdata->fullmessagehtml    = $message;
    $eventdata->smallmessage       = '';
    $eventdata->notification       = '1';
    if (!empty($CFG->supportemail)) {
        $eventdata->replyto = $CFG->supportemail;
    }else if (!empty($CFG->noreplyaddress)) {
        $eventdata->replyto = $CFG->noreplyaddress;
    }

    message_send($eventdata);
}

function block_external_verify_course($fromdata){
    global $DB, $USER;
    $managerVerify = get_config('block_externalverify', 'managerverify');
    
    $data = $DB->get_record('course_external', array('id'=>$fromdata->id));
    if($data->status == 0){
        if(isset($fromdata->rejectbutton)){
            $data->status = 2;
        }else if(isset($fromdata->submitbutton)){
            if($managerVerify){
                $data->status = 4;
            }
            else {
                $data->status = 1;
            }
        }
        $data->reason = $fromdata->reason;
        $data->validator = $USER->id;
        $data->timeverify1 = time();
        $data->usermodified = $USER->id;
        $data->timemodified = time();
        $DB->update_record('course_external', $data);
        if($data->status == 2){
            block_external_verify_send_notifications($data);
        }
        else if($data->status == 1){
            block_external_verify_send_notifications($data, true);
        }
        else if($data->status == 4){
            block_external_verify_send_notifications_manager($data);
        }
    }
    else if($data->status == 4){
        if(isset($fromdata->rejectbutton)){
            $data->status = 2;
        }else if(isset($fromdata->submitbutton)){
            $data->status = 1;
        }
        $data->reason = $fromdata->reason;
        $data->manager = $USER->id;
        $data->timeverify2 = time();
        $data->usermodified = $USER->id;
        $data->timemodified = time();
        $DB->update_record('course_external', $data);
        //reject message notifications
        if($data->status == 2){
            block_external_verify_send_notifications($data);
        }else if($data->status == 1){
            block_external_verify_send_notifications($data, true);
        }
    }
}

function block_external_verify_course_batch($applys, $status, $stage){
    global $DB, $USER;
    $managerVerify = get_config('block_externalverify', 'managerverify');

    $reason = '';
    if(isset($applys->rejectusers)){
        $reason = $applys->reason;
        $applys = explode(',', $applys->rejectusers);
    }

    foreach($applys as $applyid){
        $data = $DB->get_record('course_external', array('id'=>$applyid));
        $data->reason = $reason;
        $data->usermodified = $USER->id;
        $data->timemodified = time();

        if($data->status == 0){
            if($status == 1 && $managerVerify){
                $data->status = 4;
            }else{
                $data->status = $status;
            }
            $data->validator = $USER->id;
            $data->timeverify1 = time();
            $DB->update_record('course_external', $data);
            
            if($data->status == 2){
                block_external_verify_send_notifications($data);
            }
            else if($data->status == 1){
                block_external_verify_send_notifications($data, true);
            }
            else if($data->status == 4){
                block_external_verify_send_notifications_manager($data);
            }
        }
        else if($data->status == 4){
            $data->status = $status;
            $data->manager = $USER->id;
            $data->timeverify2 = time();
            $DB->update_record('course_external', $data);

            if($data->status == 2){
                block_external_verify_send_notifications($data);
            }else if($data->status == 1){
                block_external_verify_send_notifications($data, true);
            }
        }
    }
}

function block_external_verify_send_notifications($data, $status = false){
    global $CFG, $USER, $DB;
    require_once($CFG->libdir.'/messagelib.php');
    
    $config = get_config('block_externalverify');
    
    $user = $DB->get_record('user', array('id'=>$data->userid));
    //$supervisor = $DB->get_record('user', array('username'=>$data->supervisor));
    /*
    $supervisor = '';
    if(!empty($row->supervisor)){
        $managers = explode(',', $row->supervisor);
        $data = array();
        foreach($managers as $manage){
            if(empty($manage)){
                continue;
            }
            $data[] = fullname($DB->get_field('user', 'firstname', array('username'=>$manage)));
        }
        $supervisor = implode(',', $data);
    }
    */
    $data->timecreated = block_externalverify_get_date_format($data->timecreated);
    $data->timemodified = block_externalverify_get_date_format($data->timemodified);
    $data->supervisor = fullname($USER);
    if($status){
        $content = get_string('mail_course', 'block_externalverify', $data);
    }else{
        $content = get_string('mail_course_reject', 'block_externalverify', $data);
    }
    $mail_subject = isset($config->mail_subject) ? $config->mail_subject : get_string('mail_subject_title', 'block_externalverify');
    $mail_content = isset($config->mail_content) ? $config->mail_content : '';

    $message = '';
    if(!empty($mail_content)){
        $message .= '<p>'.$mail_content. '</p>';
    }
    $message .= $content;

    $eventdata                     = new \core\message\message();//new \core\message\message();
    $eventdata->courseid           = SITEID;
    $eventdata->component          = 'block_externalverify';
    $eventdata->name               = 'notification';
    $eventdata->userfrom           = core_user::get_noreply_user();
    $eventdata->userto             = $user;
    $eventdata->subject            = $mail_subject;
    $eventdata->fullmessage        = html_to_text($message);
    $eventdata->fullmessageformat  = FORMAT_HTML;
    $eventdata->fullmessagehtml    = $message;
    $eventdata->smallmessage       = '';
    $eventdata->notification       = '1';
    if (!empty($CFG->supportemail)) {
        $eventdata->replyto = $CFG->supportemail;
    }else if (!empty($CFG->noreplyaddress)) {
        $eventdata->replyto = $CFG->noreplyaddress;
    }

    message_send($eventdata);
}

function block_external_verify_send_notifications_manager($data){
    global $CFG, $DB;
    require_once($CFG->libdir.'/messagelib.php');

    $admins = get_admins();
    foreach($admins as $uid => $user){
        $managers[] = $user->username;
    }

    foreach($managers as $manager){
        if(empty($manager)){
            continue;
        }
        $user = $DB->get_record('user', array('username'=>$manager));
        block_external_send_notifications($data, $user);
    }
}

function block_externalverify_get_verify_status($verify_status){
    $status = "";
    switch($verify_status){
        case 1:
            $status = get_string('agree', 'block_externalverify');
            break;
        case 2:
            $status = get_string('reject', 'block_externalverify');
            break;
        case 3:
            $status = get_string('cancel', 'block_externalverify');
            break;
        case 4:
            $status = get_string('waitingverify', 'block_externalverify');
            break;
        default:
            break;
    }
    return $status;
}

function block_externalverify_get_date_format($date){
    return date("Y-m-d H:i:s", $date);
}