<?php
/**
 * @package   block_yakitory
 * @copyright 2021 MARY CHEN  {@link https://www.click-ap.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v1 or later
 */
require_once('../../config.php');
require_once('client/http.php');
require_once('upload_form.php');

$userid   = optional_param('userid', $USER->id , PARAM_INT);
$username   = optional_param('username', $USER->username , PARAM_INT);
$courseid = required_param('courseid', PARAM_INT);

$rurl = new moodle_url('/course/view.php', array('id'=>$courseid));
$returnurl = optional_param('returnurl', $rurl, PARAM_LOCALURL);

if (!$course = get_course($courseid)) {
    print_error("That's an invalid course id");
}

require_login($course, false);
$context = context_user::instance($userid);

$course_context = context_course::instance($course->id);
$PAGE->set_context($course_context);
$PAGE->set_pagelayout('report');
$PAGE->set_url('/blocks/yakitory/upload.php', array('courseid'=>$course->id));
                    
require_capability('block/yakitory:upload', $course_context);

$data = new stdClass();
$options = array('subdirs' => 0,
                 'maxbytes' => 2147483648,//(500KB)
                 'maxfiles' => 1,
                 'accepted_types' => array('.mp4','.avi','.mpg','.mpeg','.mov','.mkv','.wmv','.flv','.m4v','.m2ts','.m2t','.vob','.3gp','.webm','.ogv'));
file_prepare_standard_filemanager($data, 'uservideo', $options, $context, 'user', 'uservideo', 0);
$editform = new block_yakitory_edit_form(null, array('data'=>$data, 'options'=>$options, 'courseid'=>$courseid, 'returnurl'=>$returnurl));

if ($editform->is_cancelled()) {
    redirect($returnurl);
} else if ($data = $editform->get_data()) {
    //$data = file_postupdate_standard_filemanager($data, 'video', $options, $context, 'user', 'video', 0);
    set_time_limit(0);
    //$content = $editform->get_file_content('uservideo');
    $upload_time = time();
    $filename = $editform->get_new_filename('uservideo');
    $filepath = $CFG->tempdir . '/' . $filename;
    $return = true;
    if($editform->save_file('uservideo', $filepath)){
        $http = new http_class;
        $http->timeout = 0;
        $http->data_timeout = 0;
        $http->debug = 0;
        $http->html_debug = 0;
    
        $config = get_config('yakitory');
        $url = $config->server_host . "/api/clients/" . $config->client_id . "/videos/upload";
        $error = $http->GetRequestArguments($url, $arguments);
        $arguments["RequestMethod"] = "POST";
        $arguments["PostValues"] = array(
            "access_key" => $config->access_key
        );
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
                        $video = json_decode($json_body->video);
                        /*
                        object(stdClass)#181 (17) { ["id"]=> string(2) "15" 
                        ["filename"]=> string(22) "1474340280_iframe_.mp4" 
                        ["filepath"]=> string(77) "/home/vagrant/panda/public/data/tmp_uploads/0012846751_1474340280_iframe_.mp4" 
                        ["video_codec"]=> NULL ["video_bitrate"]=> NULL ["audio_codec"]=> NULL ["audio_sample_rate"]=> NULL ["thumbnail_filename"]=> NULL ["thumbnail_filepath"]=> NULL ["duration"]=> NULL ["container"]=> NULL ["width"]=> NULL ["height"]=> NULL ["fps"]=> NULL 
                        ["state"]=> string(6) "queued" ["error_msg"]=> NULL ["client_id"]=> string(1) "6" }
                        */
                        if(isset($video->client_id)){
                            $insertdata = new stdClass();
                            $insertdata->username   = $username;
                            $insertdata->course = 'My';
                            /*
                            if($course->id == SITEID){
                                $insertdata->course = 'My';
                            }else{
                                $course->fullname = str_replace('/', '_',$course->fullname);
                                $course->fullname = str_replace('\\', '_',$course->fullname);
                                $insertdata->course = $course->fullname;
                            }
                            */
                            $insertdata->client_host = $config->video_host;
                            $insertdata->client_id   = $video->client_id;
                            $insertdata->access_key  = $config->access_key;
                            $insertdata->videoid     = $video->id;
                            $insertdata->filepath    = $video->filepath;
                            $insertdata->filename    = $video->filename;
                            $insertdata->state       = $video->state;
                            $insertdata->json_code   = $json_body->video;
                            $insertdata->timecreated = time();
                            $DB->insert_record('yakitory_videos',$insertdata);
                        }else {
                            echo "<CENTER><H2>video client error</H2><CENTER>\n";
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
        redirect($returnurl, get_string('upload_success', 'block_yakitory'), null, \core\output\notification::NOTIFY_INFO);
    }else{
        redirect($returnurl);
    }
}

$title = get_string('pluginname', 'block_yakitory');
$PAGE->navbar->add($title);
$PAGE->set_title("$course->shortname: $title");
$PAGE->set_heading($course->fullname);
echo $OUTPUT->header();
echo $OUTPUT->heading($title);
$editform->display();
echo $OUTPUT->footer();