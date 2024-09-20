<?php
/**
 * Version details.
 *
 * @package    clickap_code
 * @copyright  2021 CLICK-AP {@link https://www.click-ap.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once('../../../config.php');
require_once($CFG->dirroot . '/admin/clickap/code/lib.php');

$id = optional_param('id', null, PARAM_INT);
$delete = optional_param('delete', '', PARAM_ALPHANUM);

$urlparams = array();
if (!empty($id)) {
    $data = $DB->get_record('clickap_code',array('id'=>$id));
    $urlparams['id'] = $id;
}
$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url('/admin/clickap/code/delete.php', $urlparams);
$PAGE->set_pagelayout('admin');
if(!clickap_code_table_exists()) {
    redirect(new moodle_url($CFG->wwwroot.'/'));
}
require_login();
require_capability('clickap/code:manage', $context);
$returnurl = new moodle_url('/admin/clickap/code/index.php?type='.$data->type);
if ($delete === md5($data->timemodified)) {
    require_sesskey();

    clickap_code_delete($data);
    
    redirect($returnurl);
}

if($id) {
    $PAGE->set_title("$SITE->fullname: $data->name");
    $PAGE->set_heading($SITE->fullname);
    echo $OUTPUT->header();

    $isUsed = false;
    if($DB->get_manager()->table_exists('clickap_course_info')) {
        if($data->type == "genre"){
            $like1 = "%,".$data->id;
            $like2 = $data->id.",%";
            $like3 = "%,".$data->id.",%";
            $sql = "SELECT * FROM {clickap_course_info} WHERE (genreids LIKE :gid1 OR genreids LIKE :gid2 
                        OR genreids LIKE :gid3 OR genreids = :gid4)";
                        
            if($DB->record_exists_sql($sql, array('gid1'=>$like1, 'gid2'=>$like2, 'gid3'=>$like3, 'gid4'=>$data->id))){
                $isUsed = true;
            }
        }else if ($DB->record_exists('clickap_course_info', array($data->type => $data->id))){
            $isUsed = true;
        }
    }

    if($isUsed) {
        echo $OUTPUT->box_start('informationbox');
        echo get_string('codeinuse', 'clickap_code');
        echo $OUTPUT->box_end();
        //redirect($returnurl);
        echo $OUTPUT->continue_button($returnurl);
    }else {
        $message = get_string('confirm-delete','clickap_code', $data->name);
        $continueurl = new moodle_url('/admin/clickap/code/delete.php', array('id' => $id, 'delete' => md5($data->timemodified)));
        echo $OUTPUT->confirm($message, $continueurl, $returnurl);
    }
}
else {
    $data = array();
    
    $title = $SITE->fullname;
    $PAGE->set_title($title);
    $PAGE->set_heading($title);
    
    echo $OUTPUT->header();
    echo $OUTPUT->heading($title);
}
echo $OUTPUT->footer();