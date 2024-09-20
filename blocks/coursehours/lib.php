<?php
/**
 * coursehours block settings
 *
 * @package    block_coursehours
 * @copyright  2016 Mary Chen(http://www.click-ap.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
function block_coursehours_is_freshman($currentyear, $arrivaldate){
    //$currentyear = 105
    //$arrivaldate = 20160406
    $isfreshman = 1;
    $thisYear = date('Y') - 1911;
    if($thisYear == $currentyear){
        $currentyear = date('Ymd');
    }else{
        $currentyear = ((int)$currentyear * 10000) + 19111231;
    }
    $currentyear = strtotime($currentyear);
    $threemonthlater = strtotime("+3 months", strtotime((int)$arrivaldate));
    if($threemonthlater <= $currentyear){
        $isfreshman = 0;
    }
    
    return $isfreshman;
}

function block_coursehours_identity($username){
    
    $ispermanent = 1;
    //scontract = 0;
    if(substr($username, 0,1) == '1'){
        $ispermanent = 0;
    }else if(substr($username, 0,1) == '5'){
        $ispermanent = 0;
    }else if(substr($username, 0,2) == '05'){
        $ispermanent = 0;
    }
    return $ispermanent;
}

function block_coursehours_list_sheet($currentyear, $userid, $lang='zh_tw'){
    global $CFG, $DB, $PAGE;
    
    require_once($CFG->libdir.'/excellib.class.php');
    require_once('locallib.php');
    
    $str_fullname = get_string('coursefullname', 'block_coursehours');
    $str_org = get_string('coursecategory', 'block_coursehours');
    $str_startdate = get_string('startdate', 'block_coursehours');
    $str_enddate = get_string('enddate', 'block_coursehours');
    $str_model = get_string('model', 'block_coursehours');
    $str_hours = get_string('hours', 'block_coursehours');
    $str_unit = get_string('unit', 'block_coursehours');
    $str_hourcategory = get_string('hourcategories', 'block_coursehours');
    $str_enrolmentod = get_string('enrolmethod', 'block_coursehours');
    $str_status = get_string('status', 'block_coursehours');
    $str_finalgrade = get_string('finalgrade', 'grades');
    $str_lastcourseaccess = get_string('lastcourseaccess');
    
    $user = $DB->get_record('user', array('id'=>$userid));

    $workbook = new MoodleExcelWorkbook('-');
    $filename = new stdClass();
    $filename->year = $currentyear;
    $filename->user = $user->firstname;
    $str_filename = new lang_string('filename','block_coursehours', $filename ,$lang).'_'.userdate(time(),'%Y%m%d',99,false);
    $workbook->send($str_filename . '.xls');
    $worksheet = $workbook->add_worksheet(get_string('categorieshour', 'block_coursehours'));
    
    block_coursehours_list_myhours_excel($currentyear, $user, $worksheet);
        
    $worksheet = $workbook->add_worksheet(get_string('enrolledcourses', 'block_coursehours'));
    $worksheet->write(0, 0, $str_fullname);
    $worksheet->write(0, 1, $str_startdate);
    $worksheet->write(0, 2, $str_model);
    $worksheet->write(0, 3, $str_hours);
    $worksheet->write(0, 4, $str_unit);
    $worksheet->write(0, 5, $str_hourcategory);
    $worksheet->write(0, 6, $str_enrolmentod);
    $worksheet->write(0, 7, $str_status);
    $worksheet->write(0, 8, $str_finalgrade);
    $worksheet->write(0, 9, $str_lastcourseaccess);
    
    $render = $PAGE->get_renderer('clickap_hourcategories');
    $courses = $render->get_my_courses_per_year($currentyear, $userid);
    
    $row = 1;
    foreach($courses as $course){
        $categoryname = '';
        
        if(!empty($course->hourcategories)){
            $categories = explode(',', $course->hourcategories);
        }else{
            $categories = $DB->get_records_menu('clickap_course_categories', array('courseid'=>$course->id),'','hcid as id , hcid');
        }

        if(!empty($categories)){
            foreach($categories as $hc){
                if(empty($hc)){continue;}
                if(!empty($categoryname)){
                    $categoryname .= " \r\n";
                }
                $categoryname .= $DB->get_field('clickap_hourcategories', 'name', array('id'=>$hc));
            }
        }
            
        $worksheet->write($row, 0 , $course->fullname);
        $worksheet->write($row, 1 , date("Y-m-d H:i", $course->startdate));

        $model = $DB->get_field('clickap_code', 'name', array('id'=>$course->model));
        $worksheet->write($row, 2 , $model);
        
        //$worksheet->write($row, 3 , $render->get_course_hour($course));
        $worksheet->write($row, 3 , $course->hours);

        $unit = $DB->get_field('clickap_code', 'name', array('id'=>$course->unit));
        $worksheet->write($row, 4 , $unit);
        $worksheet->write($row, 5 , $categoryname);

        $elective_enrols = get_config('block_coursehours','elective');
        if($course->enrolmethod == 1){
            $enrolmethod = get_string('obligatory', 'block_coursehours');
            $params = array('userid' => $userid, 'courseid'=>$course->id);
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
        $worksheet->write($row, 6 , $enrolmethod);
        
        $status = '-';
        if($course->enrolmethod == 1){
            $context = context_course::instance($course->id, MUST_EXIST);
            if (has_capability('moodle/course:isincompletionreports', $context, $userid)) {
                if($course->status == 1){
                    $status = get_string('completed', 'block_coursehours');
                }else{
                    $status = get_string('notcompleted', 'block_coursehours');
                    /*
                    $sql = "SELECT * FROM {course_completions} WHERE userid = :userid AND course = :courseid AND reaggregate = 0 AND timecompleted is not null";
                    $completion = $DB->record_exists_sql($sql, array('userid'=>$userid, 'courseid'=>$course->id));
                    if($completion){
                        $status = get_string('completed', 'block_coursehours');
                    }else{
                        $status = get_string('notcompleted', 'block_coursehours');
                    }
                    */
                }
            }
        }
        $worksheet->write($row, 7 , $status);
        
        //get user finalgrade
        require_once($CFG->dirroot . '/grade/querylib.php');
        require_once($CFG->libdir . '/gradelib.php');

        if($course->enrolmethod == 1){
            $courseitem = grade_item::fetch_course_item($course->id);
            if (!$grades = grade_get_course_grade($course->userid, $course->id)) {
                $worksheet->write($row, 8 , '');
            } else {
                // only one grade - not array
                if($grades->hidden){
                    $worksheet->write($row, 8 , '-');
                }
                $finalgrade = reset($grades);
                $worksheet->write($row, 8 , grade_format_gradevalue($finalgrade, $courseitem, true));
            }
        }else{
            $worksheet->write($row, 8 , '');
        }
        
        if ($course->lastcourseaccess) {
            $worksheet->write($row, 9 , date("Y-m-d H:i", $course->lastcourseaccess));
        } else {
            $worksheet->write($row, 9 , get_string('never'));
        }

        
        $row++;
    }
    $workbook->close();
    return true;
}