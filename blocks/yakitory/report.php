<?php
/**
 * @package   block_yakitory
 * @copyright 2021 MARY CHEN  {@link https://www.click-ap.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v1 or later
 */
require_once('../../config.php');

global $CFG, $DB, $USER;
$courseid = required_param('courseid', PARAM_INT);
//$returnurl = required_param('returnurl', PARAM_URL);
$filters['page'] = optional_param('page', 0, PARAM_INT);
$filters['perpage'] = optional_param('perpage', 20, PARAM_INT);

if (!$course = get_course($courseid)) {
    print_error("That's an invalid course id");
}

require_login($course, false);
$userid  = $USER->id;
$username  = $USER->username;
$context = context_user::instance($userid);
$course_context = context_course::instance($course->id);
$PAGE->set_context($course_context);
require_capability('block/yakitory:upload', $course_context);

$returnurl = new moodle_url('/blocks/yakitory/report.php', array('courseid'=>$course->id));
$PAGE->set_pagelayout('report');
$PAGE->set_url($returnurl);
$PAGE->requires->js_init_call('M.block_yakitory.init', array(), true);

$pluginname = get_string('pluginname', 'block_yakitory');
$title      = get_string('videoreport', 'block_yakitory');
//$PAGE->navbar->add($pluginname, new moodle_url('/blocks/yakitory/upload.php', array('courseid'=>$course->id, 'returnurl'=>$returnurl)));
$PAGE->navbar->add($title);
$PAGE->set_title("$course->fullname: $title");
$PAGE->set_heading($course->fullname);
echo $OUTPUT->header();
echo $OUTPUT->heading($title);

$config = get_config('yakitory');
$offset = $filters['page'] * $filters['perpage'];

$sql = "SELECT * FROM {yakitory_videos} 
        WHERE username = :username AND course !='Share' AND client_host = :clienthost 
        ORDER BY timecreated DESC, timemodified DESC";
$totalcount = count($DB->get_records_sql($sql, array('username'=>$username, 'clienthost'=>$config->video_host)));
$videos = $DB->get_records_sql($sql, array('username'=>$username, 'clienthost'=>$config->video_host), $offset, $filters['perpage']);

if (optional_param('delete', false, PARAM_BOOL) && confirm_sesskey()) {
    //delete video , include share to other user.
    //to do: yakitory Server delete API
    $videoid = optional_param('videoid', null, PARAM_INT);
}

$str_filename      = get_string('filename','block_yakitory');
$str_status        = get_string('state','block_yakitory');
$str_streaming_url = get_string('streaming_url','block_yakitory');
$str_timecreated   = get_string('timecreated','block_yakitory');
$str_timemodified  = get_string('timemodified','block_yakitory');

$table             = new html_table();
$table->attributes = array('class'=>'admintable generaltable','style'=>'display: table;');
$table->head       = array('&nbsp;', $str_filename, $str_status, $str_timecreated, $str_streaming_url, $str_timemodified, '');
$table->align      = array('center', 'left', 'center', 'center', 'left', 'center', 'center');

$cnt = 0;
foreach($videos as $video ){
    $data   = array();
    $data[] = ++$cnt;
    $data[] = $video->filename;
        
    if($video->state == 'completed'){
        $data[] = $video->state;
        $data[] = date('Y-m-d H:i', $video->timecreated);
        //$data[] = $video->client_host.$video->streaming_url;
        //copy to Clipboard
        $streamingurl = $video->client_host.$video->streaming_url;
        $data[] = html_writer::tag('button', get_string('copytoclipboard', 'block_yakitory') , array('class'=>'', 'type' => 'button', 'onclick'=>'M.block_yakitory.CopyToClipboard("'.$streamingurl.'")'));
        $data[] = date('Y-m-d H:i', $video->timemodified);
        
        $surl = new moodle_url('/blocks/yakitory/assign.php', array('courseid'=>$course->id, 'id'=>$video->id, 'sesskey' =>sesskey() ));
        //$sicon = new image_icon('t/assignroles',get_string('videoshare', 'block_yakitory'));//for adaptable
        $sicon = new image_icon('i/enrolusers',get_string('videoshare', 'block_yakitory'));//for essential
        $share = $OUTPUT->action_icon($surl, $sicon);
        
        $durl = new moodle_url('/blocks/yakitory/report.php', array('delete'=>true, 'username'=>$username, 'courseid'=>$course->id, 'videoid'=>$video->videoid, 'sesskey' =>sesskey() ));
        $dicon = new image_icon('t/delete', get_string('delete'));
        $delete = $OUTPUT->action_icon($durl, $dicon, new confirm_action(get_string('delete_confirm', 'block_yakitory')));

        //share to all users
		$action = $video->is_open ? 'show' : 'hide';
		$anchortagcontents = $OUTPUT->pix_icon('t/' . $action, get_string('share_status:' . $action,  'block_yakitory'));
		$anchortag = html_writer::link("", $anchortagcontents,
			array('title' => get_string('share_status:' . $action, 'block_yakitory'),
				'data-action' => $action, 'data-username' => $username, 'id' => 'change-video-visibility'));
		//$share .= '&nbsp;<div class="videovisibility">' . $anchortag . '</div>';

        if(!isset($config->is_open) OR $config->is_open != 1){
            $data[] = $share;
        }else {
            $data[] = '';
        }
    }else{
        $data[] = '<font color="red">'.$video->state.'</font>';
        $data[] = date('Y-m-d H:i', $video->timecreated);
        $data[] = '';
        $data[] = '';
        $data[] = '';
    }
    $table->data[] = new html_table_row($data);
}
echo $OUTPUT->paging_bar($totalcount, $filters['page'], $filters['perpage'], $returnurl);
echo html_writer::table($table);
echo $OUTPUT->footer();
