<?php
/**
 * @package   block_uploaddoc
 * @copyright 2016 MARY CHEN  {@link http://www.click-ap.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v1 or later
 */
require_once('../../config.php');
require_once('client/http.php');
require_once('classes/upload_form.php');

$userid   = optional_param('userid', $USER->id , PARAM_INT);
$courseid = required_param('courseid', PARAM_INT);

$rurl = new moodle_url('/course/view.php', array('id'=>$courseid));
$returnurl = optional_param('returnurl', $rurl, PARAM_LOCALURL);

if (!$course = get_course($courseid)) {
    print_error("That's an invalid course id");
}

require_login($course, false);
$context = context_user::instance($userid);
$user = $DB->get_record('user', array('id'=>$userid));

$PAGE->set_context($context);
$PAGE->set_pagelayout('course');
$PAGE->set_url('/blocks/uploaddoc/upload.php', array('courseid'=>$course->id));
if($course->id == SITEID){
    require_capability('block/uploaddoc:upload', $context);
}else{
    $course_context = context_course::instance($course->id);
    require_capability('block/uploaddoc:upload', $course_context);
}
$data = new stdClass();
$options = array('subdirs' => 0,
                 'maxbytes' => 2147483648,//(500KB)
                 'maxfiles' => 1,
                 'accepted_types' => array('.doc','.docx','.xls','.xlsx','.ppt','.pptx'));
file_prepare_standard_filemanager($data, 'userfile', $options, $context, 'user', 'userfile', 0);
$editform = new block_derberus_upload_form(null, array('data'=>$data, 'options'=>$options, 'courseid'=>$courseid, 'returnurl'=>$returnurl));

if ($editform->is_cancelled()) {
    redirect($returnurl);
} else if ($data = $editform->get_data()) {
    set_time_limit(0);
    $upload_time = time();
    $filename = $editform->get_new_filename('userfile');
    $filepath = $CFG->tempdir . '/' . $filename;
    $return = true;
    if($editform->save_file('userfile', $filepath)){
        $http = new http_class;
        $http->timeout = 0;
        $http->data_timeout = 0;
        $http->debug = 0;
        $http->html_debug = 0;
    
        $config = get_config('derberus');
        $url = $config->upload_host . "/api/clients/" . $config->client_id . "/docs/upload";
        $error = $http->GetRequestArguments($url, $arguments);
        $arguments["RequestMethod"] = "POST";
        
        $arguments["PostValues"] = array(
            "access_key" => $config->access_key
        );
        
        /* post upload user data
        $arguments["PostValues"] = array(
            "access_key" => $config->access_key ,
            "watermark[text]" => $user->username,
            "watermark[attr]" => '{"size": "60", "opacity": "0.3", "angle": 45}'
        );
        */
        $arguments["PostFiles"] = array(
            "filepath" => array(
                "FileName" => $filepath,
                "Content-Type" => "automatic/name",
            )
        );
        
        flush();
        $error = $http->Open($arguments);

        if($error == ""){
            $error = $http->SendRequest($arguments);
            if($error == ""){
                //echo "<H2>Request:</H2>\n<PRE>\n".HtmlEntities($http->request)."</PRE>\n";
                //echo "<H2>Request body:</H2>\n<PRE>\n".HtmlEntities($http->request_body)."</PRE>\n";
                flush();

                $headers = array();
                $error = $http->ReadReplyHeaders($headers);
                if($error == "")
                {
                    //ho "<H2>Response headers:</H2>\n<PRE>\n";
                    for(Reset($headers), $header = 0; $header < count($headers); Next($headers) , $header++)
                    {
                        $header_name=Key($headers);
                        if(GetType($headers[$header_name]) == "array")
                        {
                            for($header_value = 0 ; $header_value < count($headers[$header_name]) ; $header_value++){
                                //echo $header_name.": ".$headers[$header_name][$header_value],"\r\n";
                            }
                        }
                        else{
                            //echo $header_name.": ".$headers[$header_name],"\r\n";
                        }
                    }
                    //echo "</PRE>\n";
                    flush();

                    //echo "<H2>Response body:</H2>\n<PRE>\n";
                    for(;;){
                        $error=$http->ReadReplyBody($body,1000);
                        if( $error!="" || strlen($body) == 0){
                             break;
                        }
                        $ret_body = HtmlSpecialChars($body);

                        $json_body = json_decode($body);
                        $file = json_decode($json_body->doc);

                        if(isset($file->client_id)){
                            $insertdata = new stdClass();
                            $insertdata->userid      = $userid;
                            if($course->id == SITEID){
                                $insertdata->course = 'My';
                            }else{
                                $course->fullname = str_replace('/', '_',$course->fullname);
                                $course->fullname = str_replace('\\', '_',$course->fullname);
                                $insertdata->course = $course->fullname;
                            }
                            $insertdata->upload_host = $config->view_host;
                            $insertdata->client_id   = $file->client_id;
                            $insertdata->access_key  = $config->access_key;
                            $insertdata->fileid     = $file->id;
                            $insertdata->filepath    = $file->filepath;
                            $insertdata->filename    = $file->filename;
                            $insertdata->state       = $file->state;
                            $insertdata->json_code   = $json_body->doc;
                            $insertdata->timecreated = time();
                            $DB->insert_record('derberus_files',$insertdata);
                        }else {
                            echo "<CENTER><H2>derberus server error</H2><CENTER>\n";
                            var_dump($body);
                            $return = false;
                        }
                    }
                    echo "</PRE>\n";
                    flush();
                }
            }else{
                echo "<CENTER><H2>connection error: ".$error."</H2><CENTER>\n";
                $return = false;
            }
            $http->Close();
        }else{
            echo "<CENTER><H2>connection error: ".$error."</H2><CENTER>\n";
            $return = false;
        }
    }
    unlink($filepath);
    if($return){    
        redirect($returnurl, get_string('upload_success', 'block_uploaddoc'), null, \core\output\notification::NOTIFY_INFO);
    }else{
        redirect($returnurl);
    }
}

$title = get_string('pluginname', 'block_uploaddoc');
$PAGE->navbar->add($title);
$PAGE->set_title("$course->shortname: $title");
$PAGE->set_heading($course->fullname);
echo $OUTPUT->header();
echo $OUTPUT->heading($title);
$editform->display();
echo $OUTPUT->footer();