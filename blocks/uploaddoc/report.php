<?php
/**
 * @package   block_uploaddoc
 * @copyright 2018 MARY CHEN  {@link http://www.click-ap.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v1 or later
 */
require_once('../../config.php');
require_once($CFG->dirroot . '/blocks/uploaddoc/locallib.php');

$courseid = required_param('courseid', PARAM_INT);
$filters['page'] = optional_param('page', 0, PARAM_INT);
$filters['perpage'] = optional_param('perpage', 20, PARAM_INT);

if (!$course = get_course($courseid)) {
    print_error("That's an invalid course id");
}

require_login($course, false);
$userid  = $USER->id;
$context = context_user::instance($userid);
$course_context = context_course::instance($course->id);
$PAGE->set_context($course_context);
$PAGE->requires->js_init_call('M.block_uploaddoc.init', array(), true);
require_capability('block/uploaddoc:upload', $course_context);

$PAGE->set_pagelayout('report');
$PAGE->set_url('/blocks/uploaddoc/report.php', array('courseid'=>$course->id));

$returnurl = new moodle_url('/blocks/uploaddoc/report.php', array('courseid'=>$course->id));
$pluginname = get_string('pluginname', 'block_uploaddoc');
$title      = get_string('filereport', 'block_uploaddoc');
//$PAGE->navbar->add($pluginname, new moodle_url('/blocks/uploaddoc/upload.php', array('courseid'=>$course->id, 'returnurl'=>$returnurl)));
$PAGE->navbar->add($title);
$PAGE->set_title("$course->fullname: $title");
$PAGE->set_heading($course->fullname);

/*
if (optional_param('delete', false, PARAM_BOOL) && confirm_sesskey()) {
    //delete file , include share to other user.
    //to do: derberus Server delete API

    $fileid = optional_param('fileid', null, PARAM_INT);    
}
*/

$str_filename      = get_string('filename','block_uploaddoc');
$str_status        = get_string('state','block_uploaddoc');
$str_streaming_url = get_string('streaming_url','block_uploaddoc');
$str_timecreated   = get_string('timecreated','block_uploaddoc');
$str_timemodified  = get_string('timemodified','block_uploaddoc');

$table             = new html_table();
$table->attributes = array('class'=>'admintable generaltable','style'=>'display: table;');
$table->head       = array('&nbsp;', $str_filename, $str_status, $str_timecreated, $str_streaming_url, $str_timemodified, '');
$table->align      = array('center', 'left', 'center', 'center', 'center', 'center', 'right');

$files = block_uploaddoc_get_files($userid, $filters);
$totalcount = block_uploaddoc_get_files($userid);
$cnt = 0;
foreach($files as $file ){
    $data   = array();
    $data[] = ++$cnt;
    $data[] = $file->filename;
        
    if($file->state == 'completed'){
        $data[] = $file->state;
        $data[] = date('Y-m-d H:i', $file->timecreated);
        //$data[] = $file->upload_host.$file->streaming_url;
        //copy to Clipboard
        $streamingurl = $file->upload_host.$file->streaming_url;
        $data[] = html_writer::tag('button', get_string('copytoclipboard', 'block_uploaddoc') , array('class'=>'', 'type' => 'button', 'onclick'=>'M.block_uploaddoc.CopyToClipboard("'.$streamingurl.'")'));
        $data[] = date('Y-m-d H:i', $file->timemodified);
        
        $vurl = new moodle_url('/blocks/uploaddoc/preview.php', array('userid'=>$file->userid, 'fid' => $file->id));
        $vicon = new image_icon('t/preview', get_string('preview'));
        $preview = $OUTPUT->action_icon($vurl, $vicon, null, array('target'=>"_blank"));
        
        $surl = new moodle_url('/blocks/uploaddoc/assign.php', array('courseid'=>$course->id, 'id'=>$file->id, 'sesskey' =>sesskey() ));
        $sicon = new image_icon('i/enrolusers',get_string('fileshare', 'block_uploaddoc'));//for essential
        $share = $OUTPUT->action_icon($surl, $sicon);
        
        $durl = new moodle_url('/blocks/uploaddoc/report.php', array('delete'=>true, 'userid'=>$userid, 'courseid'=>$course->id, 'fileid'=>$file->fileid, 'sesskey' =>sesskey() ));
        $dicon = new image_icon('t/delete', get_string('delete'));
        $delete = $OUTPUT->action_icon($durl, $dicon, new confirm_action(get_string('delete_confirm', 'block_uploaddoc')));
        
        $data[] = $preview.$share;
    }else{
        $data[] = '<font color="red">'.$file->state.'</font>';
        $data[] = date('Y-m-d H:i', $file->timecreated);
        //$data[] = '';
        $data[] = '';
        $data[] = '';
    }
    $table->data[] = new html_table_row($data);
}
echo $OUTPUT->header();
echo $OUTPUT->heading($title);
echo $OUTPUT->paging_bar($totalcount, $filters['page'], $filters['perpage'], $returnurl);
echo html_writer::table($table);
echo $OUTPUT->footer();
