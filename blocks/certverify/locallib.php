<?php
/**
 * 
 * @package    block_certverify
 * @author     Elaine Chen(CLICK-AP)
 * @copyright  CLICK-AP {@link https://www.click-ap.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


function block_certverify_date_transfer($date, $showtime = false){
    if($showtime){
        return date("Y-m-d H:i:s", $date);
    }
    return date("Y-m-d", $date);
}

function block_certverify_get_certificate_image($data){
    $url = '';
    $context = context_user::instance($data->id);

    $fs = get_file_storage();
    $files = $fs->get_area_files($context->id, 'user', 'cert_attachments', $data->applyid, 'sortorder DESC, id ASC', false);
    if (count($files) >= 1) {
        foreach($files as $file){
            $url = new moodle_url('/pluginfile.php/'.$context->id.'/user/cert_attachments/'.$data->applyid.'/'.$file->get_filename(), array('forcedownload'=>1));            
        }
    }
    return $url;
}

function block_certverify_get_verify_status($verify_status){
    $status = "";
    switch($verify_status){
        case 1:
            $status = get_string('agree', 'block_certverify');
            break;
        case 2:
            $status = get_string('reject', 'block_certverify');
            break;
        case 3:
            $status = get_string('cancel', 'block_certverify');
            break;
        default:
            break;
    }
    return $status;
}

function block_certverify_get_certs(){
    global $DB;
    
    return $DB->get_records_menu('clickap_code', array('type'=>'cert', 'status'=>1), 'sortorder, idnumber', 'id, name');
}

function block_certverify_get_depts(){
    global $DB;

    $sql = "SELECT DISTINCT data as id, data FROM {user_info_data} 
            WHERE data !=''AND fieldid = (SELECT id FROM {user_info_field} WHERE shortname = 'DeptName')
            ORDER BY data";
    return $DB->get_records_sql_menu($sql);
}

function block_certverify_isManager($user){
    global $DB;

    $sql = "SELECT * FROM {user_info_data}
            WHERE fieldid = (SELECT id FROM {user_info_field} WHERE shortname = 'Supervisor')
            AND data = :username";
    
    return $DB->record_exists_sql($sql, array('username'=> $user->username));    
}

function block_certverify_create_certificate($data, $user, $filesoptions = NULL) {
    global $DB;

    $context = context_user::instance($user->id);
    $validators = block_certverify_get_verify_manager();

    if(empty($data->userid)){
        $data->userid = $user->id;
    }
    $data->idnumber = $data->certnumber;
    $data->validators = $validators;
    $data->timecreated = time();

    $data->id = $DB->insert_record('user_certs', $data);
    block_certverify_appy_notifications($data);
    if ($filesoptions) {
        $data = file_postupdate_standard_filemanager($user, 'attachments', $filesoptions, $context, 'user', 'cert_attachments', $data->id);
    }

    return $data->id;
}

function block_certverify_appy_notifications($data){
    global $CFG, $DB;
    require_once($CFG->libdir.'/messagelib.php');

    $validators = explode(',', $data->validators);
    foreach($validators as $validator){
        if(empty($validator)){
            continue;
        }
        $user = $DB->get_record('user', array('username'=>$validator));
        block_certverify_notifications($data, $user);
    }
}

function block_certverify_notifications($data, $validator){
    global $CFG, $DB;
    
    $user = $DB->get_record('user', array('id'=>$data->userid));
    $certname = $DB->get_field('clickap_code', 'name', array('id'=>$data->certid));

    $msg = new stdClass();
    $msg->certname = $certname;
    $msg->applyuser = $user->firstname;
    $msg->timecreated = block_certverify_date_transfer($data->timecreated, true);
    $message = get_string('mail_apply', 'block_certverify', $msg);
    
    $eventdata                     = new \core\message\message();//new \core\message\message();
    $eventdata->courseid           = SITEID;
    $eventdata->component          = 'block_certverify';
    $eventdata->name               = 'notification';
    $eventdata->userfrom           = core_user::get_noreply_user();
    $eventdata->userto             = $validator;
    $eventdata->subject            = get_string('mail_apply_subject', 'block_certverify');
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

function block_certverify_batch_verify($applys, $status){
    global $DB, $USER;

    $reason = '';
    if(isset($applys->rejectusers)){
        $reason = $applys->reason;
        $applys = explode(',', $applys->rejectusers);
    }

    foreach($applys as $applyid){
        $data = $DB->get_record('user_certs', array('id'=>$applyid));

        $data->validator = $USER->id;
        $data->timeverify = time();
        $data->status = $status;
        $data->reason = $reason;
        $data->usermodified = $USER->id;
        $data->timemodified = time();

        $DB->update_record('user_certs', $data);

        if($data->status == 2){
            block_certverify_verify_notifications($data);
        }
        else if($data->status == 1){
            block_certverify_verify_notifications($data, true);
        }
    }
}

function block_certverify_verify_notifications($data, $status = false){
    global $CFG, $USER, $DB;
    require_once($CFG->libdir.'/messagelib.php');

    $config = get_config('block_certverify');
    $user = $DB->get_record('user', array('id'=>$data->userid));
    
    $data->timemodified = block_certverify_date_transfer($data->timemodified, true);
    $data->validator = $USER->firstname;
    $data->certname = $DB->get_field('clickap_code', 'name', array('id'=>$data->certid));

    if($status){
        $content = get_string('mail_verify', 'block_certverify', $data);
    }else{
        $content = get_string('mail_verify_reject', 'block_certverify', $data);
    }
    $mail_subject = isset($config->mail_subject) ? $config->mail_subject : get_string('mail_subject_title', 'block_certverify');
    $mail_content = isset($config->mail_content) ? $config->mail_content : '';

    $message = '';
    if(!empty($mail_content)){
        $message .= '<p>'.$mail_content. '</p>';
    }
    $message .= $content;

    $eventdata                     = new \core\message\message();//new \core\message\message();
    $eventdata->courseid           = SITEID;
    $eventdata->component          = 'block_certverify';
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

function block_certverify_due_notifications($data){
    global $CFG, $DB;

    $applyuser = $DB->get_record('user', array('id'=>$data->userid));

    $msg = new stdClass();
    $msg->applyuser = $data->firstname;
    $msg->certname = $data->certname;
    $msg->certnumber = $data->idnumber;
    $msg->dateexpire = $data->dateexpire;
    $message = get_string('mail_duenotify', 'block_certverify', $msg);

    $eventdata                     = new \core\message\message();//new \core\message\message();
    $eventdata->courseid           = SITEID;
    $eventdata->component          = 'block_certverify';
    $eventdata->name               = 'notification';
    $eventdata->userfrom           = core_user::get_noreply_user();
    $eventdata->userto             = $applyuser;
    $eventdata->subject            = get_string('mail_duenotify_subject', 'block_certverify');
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

    return message_send($eventdata);
}