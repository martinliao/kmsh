<?php
/**
 * coursehours block settings
 *
 * @package    block_coursehours
 * @copyright  2016 Mary Chen(http://www.click-ap.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->dirroot.'/blocks/coursehours/locallib.php');

global $CFG, $DB;
$userid = required_param('userid', PARAM_INT);
$currentyear = required_param('selectyear', PARAM_INT);
$category = required_param('category', PARAM_INT);

$user = $DB->get_record('user', array('id'=>$userid));
if (!$hourcategory = $DB->get_record("clickap_hourcategories", array("id"=>$category))) {
    print_error("invalidcategoryid");
}

//$context = context_user::instance($USER->id);
$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_pagelayout('mydashboard');
$PAGE->set_url('/blocks/coursehours/courses.php', array('userid' => $userid, 'category' => $category, 'selectyear' => $currentyear));
$data = new stdClass();
$data->user = fullname($user);
$data->category = $hourcategory->name;
$data->year = $currentyear;
$header = get_string("summaryofcourses", 'block_coursehours', $data);
$PAGE->set_title($header);
$PAGE->set_heading($header);
//$PAGE->navbar->add(get_string('myhome'), new moodle_url($CFG->wwwroot.'/my'));
$PAGE->navbar->add($header);
echo $OUTPUT->header();
echo $OUTPUT->heading($header);

$str_fullname = get_string('coursefullname', 'block_coursehours');
$str_org = get_string('coursecategory', 'block_coursehours');
$str_startdate = get_string('startdate', 'block_coursehours');
$str_hours = get_string('hours', 'block_coursehours');
$str_unit = get_string('unit', 'block_coursehours');
$str_model = get_string('model', 'block_coursehours');
$str_hourcategory = get_string('hourcategories', 'block_coursehours');
$str_enrolmentod = get_string('enrolmethod', 'block_coursehours');
$str_status = get_string('status', 'block_coursehours');
            
$table = new html_table();
$table->attributes = array('class'=>'admintable generaltable','style'=>'');//width:50%; white-space: nowrap; display: table;table-layout:fixed;
$table->head  = array('&nbsp;', $str_fullname, $str_org, $str_startdate, $str_model, $str_hours, $str_unit, $str_hourcategory, $str_enrolmentod, $str_status);
$table->align  = array('center', 'left', 'center', 'center', 'center', 'center', 'center', 'center', 'center', 'center', 'center');

$render = $PAGE->get_renderer('clickap_hourcategories');
$courses = $render->get_my_courses_per_year($currentyear, $user->id);
$linenum = 0;
foreach($courses as $course){
    if($course->enrolmethod == 1){
        $context = context_course::instance($course->id, MUST_EXIST);
    }

    if(!empty($course->hourcategories)){
        $hourcategories = explode(',', $course->hourcategories);
    }
    else{
        $hourcategories = $DB->get_records_menu('clickap_course_categories', array('courseid'=>$course->id),'','hcid as id , hcid');
    }
    
    $exist = false;
    $c = 0;
    $categoryname = '<p>';
    foreach($hourcategories as $hc){
        if(empty($hc)){
            continue;
        }
        if($hc == $category){
            $exist = true;
        }
        if($c > 0){
            $categoryname .= '<p>';
        }
        $categoryname .= $DB->get_field('clickap_hourcategories', 'name', array('id'=>$hc)).'</p>';
        $c++;
    }
    //c.id, c.fullname, c.startdate, c.enddate, cc.name as category, c.hourcategories, c.model, c.credit, c.unit, c.hours , 1 as enrolmethod, 0 as status
    if($exist){
        $list = array();
        $list[] = ++$linenum;
        if($course->enrolmethod == 1){
            $list[] = '<a href="'.$CFG->wwwroot.'/course/view.php?id='.$course->id.'" target="_blank">'.$course->fullname.'</a>';
        }else{
            $list[] = $course->fullname;
        }
        $list[] = $course->category;
        $list[] = date("Y-m-d H:i", $course->startdate);
        if(isset($course->model) && !empty($course->model)){
            $list[] = $DB->get_field('clickap_code', 'name', array('id'=>$course->model));
        }
        
        //$list[] = $render->get_course_hour($course);
        $list[] = $course->hours;
        if(isset($course->unit) && !empty($course->unit)){
            $list[] = $DB->get_field('clickap_code', 'name', array('id'=>$course->unit));
        }
        $list[] = $categoryname;
        
        $elective_enrols = get_config('block_coursehours','elective');
        if($course->enrolmethod == 1){
            $enrolmethod = get_string('obligatory', 'block_coursehours');
            $params = array('userid' => $user->id, 'courseid'=>$course->id);
            $sql = "SELECT e.id FROM {user_enrolments} ue
                    LEFT JOIN {enrol} e ON ue.enrolid = e.id
                    WHERE e.courseid = :courseid AND ue.userid = :userid AND e.enrol in ($elective_enrols)";
            if($DB->record_exists_sql($sql, $params)){
                $enrolmethod = get_string('elective', 'block_coursehours');
            }
        }else if($course->enrolmethod == 2){//external
            $enrolmethod = get_string('external', 'block_coursehours');
        }else{//legacy
            $enrolmethod = get_string('obligatory', 'block_coursehours');
        }
        $list[] = $enrolmethod;
        
        $status = '-';
        if($course->status == 1){
            if($course->enrolmethod == 1){
                if (has_capability('moodle/course:isincompletionreports', $context)) {
                    $status = get_string('completed', 'block_coursehours');
                }
            }
        }else{
            $status = get_string('not-completed', 'block_coursehours');
            /*
            $sql = "SELECT * FROM {course_completions} WHERE userid = :userid AND course = :courseid AND reaggregate = 0 AND timecompleted is not null";
            $completion = $DB->record_exists_sql($sql, array('userid'=>$user->id, 'courseid'=>$course->id));
            if($completion){
                $status = get_string('completed', 'block_coursehours');
            }else{
                $status = get_string('not-completed', 'block_coursehours');
            }
            */
        }

        $list[] = $status;
        $table->data[] = new html_table_row($list); 
    }
}

echo html_writer::table($table);
echo $OUTPUT->footer();