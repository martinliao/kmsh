<?php
/**
 * coursehours block settings
 *
 * @package    block_coursehours
 * @copyright  2016 Mary Chen(http://www.click-ap.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();
require_once($CFG->libdir . '/tablelib.php');
require_once($CFG->dirroot.'/enrol/locallib.php');
require_once($CFG->dirroot.'/blocks/coursehours/lib.php');

class blocks_coursehours_manage_table extends table_sql {
    public function __construct($currentyear = null, $userid = null) {
        parent::__construct('block_coursehours_manage_table');
        global $DB, $USER;

        if(empty($userid)){
           $userid = $USER->id; 
        }
        $tablename = 'temp_coursehours_'.$userid;
        $DB->execute('DROP TEMPORARY TABLE IF EXISTS '.$tablename);
        $maketempsql = "
            CREATE TEMPORARY TABLE $tablename (
              `id` bigint(10) NOT NULL AUTO_INCREMENT,
              `courseid` varchar(20) NOT NULL,
              `coursefullname` varchar(255) NOT NULL,
              `startdate` bigint(10) NOT NULL,
              `category` varchar(255) NOT NULL,
              `hourcategories` varchar(255) NOT NULL,
              `hours` varchar(6) NOT NULL,
              `unit` bigint(10) NULL,
              `model` bigint(10) NULL,
              `enrolmethod` int NOT NULL,
              `status` int NULL,
              `userid` bigint(10) NOT NULL,
              `lastcourseaccess` int NULL,
              PRIMARY KEY(id)
            )"; 
        $currentyear = $currentyear + 1911;
        if($DB->execute($maketempsql)){
            $params = array('siteid' => SITEID, 'user'=>$userid, 'user0'=>$userid, 'user1'=>$userid, 'year'=>$currentyear);
            $insertsql = "
            INSERT INTO $tablename
              (`courseid`, `coursefullname`, `startdate`, `category`, `hourcategories`, `hours`, `enrolmethod`, `status`, `userid`, `unit`, `model`, `lastcourseaccess`)
            SELECT c.id, c.fullname, c.startdate, cc.name as category, '' as hourcategories
            , (CASE WHEN (info.hours IS NOT NULL) AND (info.hours > 0) THEN info.hours ELSE '-' END) AS hours
            , 1 as enrolmethod
            , (CASE WHEN (ccp.timecompleted IS NOT NULL) THEN 1 ELSE 0 END) AS status
            , $userid as userid
            , info.unit, info.model
            , COALESCE(ul.timeaccess, 0) AS lastcourseaccess
            FROM {course} c
            JOIN (SELECT DISTINCT e.courseid FROM {enrol} e
                  JOIN {user_enrolments} ue ON (ue.enrolid = e.id AND ue.userid = :user)
                  WHERE ue.status = '0' AND e.status = '0') en ON (en.courseid = c.id)
            LEFT JOIN {context} ctx ON (ctx.instanceid = c.id AND ctx.contextlevel = '50')
            LEFT JOIN {course_categories} cc ON cc.id = c.category
            LEFT JOIN {clickap_course_info} info ON info.courseid = c.id
            LEFT JOIN {course_completions} ccp ON ccp.userid = :user0 AND ccp.course = c.id -- AND ccp.reaggregate = 0 
            LEFT JOIN {user_lastaccess} ul ON ul.userid = :user1 AND ul.courseid = c.id
            WHERE c.id <> :siteid 
            AND FROM_UNIXTIME(c.startdate, '%Y') = :year -- AND info.hours > 0
            ";        
            
            $blocks_plugins = get_plugin_list('block');
            if(array_key_exists("externalverify", $blocks_plugins)){
                $insertsql .= " UNION
                          SELECT CONCAT('ec_', c.id) as id, c.fullname, c.startdate, c.org as category, c.hourcategories, c.hours , 2 as enrolmethod, 1 as status
                          , $userid as userid
                          , c.unit, c.model
                          , 0 AS lastcourseaccess
                          FROM {course_external} c
                          WHERE c.status = 1 AND c.userid = :user2
                          AND FROM_UNIXTIME(c.startdate, '%Y') = :year2";
                $params['user2'] = $userid;
                $params['year2'] = $currentyear;
            }

            $clickap_plugins = get_plugin_list('clickap');
            if(array_key_exists("legacy", $clickap_plugins)){
                $insertsql .= " UNION
                          SELECT CONCAT('lc_', c.id) as id, c.fullname, c.startdate, '' as category, c.hourcategories, c.hours , 3 as enrolmethod, 1 as status
                          , $userid as userid
                          , c.unit, c.model
                          , 0 AS lastcourseaccess
                          FROM {clickap_legacy} c
                          WHERE c.userid = :user3
                          AND FROM_UNIXTIME(c.startdate, '%Y') = :year3";
                $params['user3'] = $userid;
                $params['year3'] = $currentyear;
            }
            
            $DB->execute($insertsql, $params);              
        }
                
        $sqlparams = array();
        $sqlwhere =  " true ";
        $this->set_sql('courseid as id, coursefullname, startdate, category, hourcategories, hours, enrolmethod, status, userid, unit, model, lastcourseaccess',
        $tablename,$sqlwhere,$sqlparams);
    }

    public function get_row_class($row) {
        return '';
    }
    public function col_hours($row){
        global $DB;
        $hours = 0;
        /*
        if(isset($row->unit) && !empty($row->unit)){
            $unit = $DB->get_field('clickap_code', 'code', array('id'=>$row->unit));
            if($unit == 6){//credit *18
                $hours = $row->hours * 18;
            }else if($unit == 2){//day *6
                $hours = $row->hours * 6;
            }else{
                $hours = $row->hours;
            }
        }else{
            $hours = $row->hours;
        }
        */
        $hours = $row->hours;
        if(isset($row->unit) && !empty($row->unit)){
            $hours.= $DB->get_field('clickap_code', 'name', array('id'=>$row->unit));
        }
        return $hours;
    }

    public function col_coursefullname($row) {
        global $CFG;
        if($row->enrolmethod == 1){
            return '<a href="'.$CFG->wwwroot.'/course/view.php?id='.$row->id.'" target="_blank">'.$row->coursefullname.'</a>';
        }else{
            return $row->coursefullname;
        }
    }
    public function col_startdate($row) {
        return date("Y-m-d H:i", $row->startdate);
    }
    public function col_enddate($row) {
        return date("Y-m-d H:i", $row->enddate);
    }
    public function col_enrolmethod($row) {
        global $DB;
        $elective_enrols = get_config('block_coursehours','elective');
        if($row->enrolmethod == 1){
            $enrolmethod = get_string('obligatory', 'block_coursehours');
            $params = array('userid' => $row->userid, 'courseid'=>$row->id);
            $sql = "SELECT e.id FROM {user_enrolments} ue
                    LEFT JOIN {enrol} e ON ue.enrolid = e.id
                    WHERE e.courseid = :courseid AND ue.userid = :userid AND e.enrol in ($elective_enrols)";
            if($DB->record_exists_sql($sql, $params)){
                $enrolmethod = get_string('elective', 'block_coursehours');
            }
        }else if($row->enrolmethod == 2){
            $enrolmethod = get_string('external', 'block_coursehours');
        }else if($row->enrolmethod == 3){
            $enrolmethod = get_string('obligatory', 'block_coursehours');
        }
        
        return $enrolmethod;        
    }
    public function col_model($row) {
        global $DB;
        
        $data = '-';
        if(isset($row->model)) {
            $data = $DB->get_field('clickap_code', 'name', array('id'=>$row->model));
        }
        return $data;
    }
    public function col_unit($row) {
        global $DB;
        
        $data = '-';
        if(isset($row->unit)) {
            $data = $DB->get_field('clickap_code', 'name', array('id'=>$row->unit));
        }
        return $data;
    }
    public function col_hourcategories($row) {
        global $DB;
        
        if(!empty($row->hourcategories)){
            $categories = explode(',', $row->hourcategories);
        }else{
            $categories = $DB->get_records_menu('clickap_course_categories', array('courseid'=>$row->id),'','hcid as id , hcid');
        }
        
        $content = '';
        if(!empty($categories)){
            foreach($categories as $hc){
                if(empty($hc)){
                    continue;
                }
                if(!empty($content)){
                    $content .= '<br>';
                }
                $content .= $DB->get_field('clickap_hourcategories', 'name', array('id'=>$hc));
            }
        }else {
            return '-';
        }
        
        return $content;
    }
    public function col_status($row) {
        global $DB;

        $status = '-';
        if($row->enrolmethod == 1){
            $context = context_course::instance($row->id, MUST_EXIST);
            if (has_capability('moodle/course:isincompletionreports', $context, $row->userid)) {
                if($row->status == 1){
                    $status = get_string('completed', 'block_coursehours');
                }else{
                    $status = get_string('not-completed', 'block_coursehours');
                    /*
                    $sql = "SELECT * FROM {course_completions} WHERE userid = :userid AND course = :courseid AND reaggregate = 0 AND timecompleted is not null";
                    $completion = $DB->record_exists_sql($sql, array('userid'=>$row->userid, 'courseid'=>$row->id));
                    if($completion){
                        $status = get_string('completed', 'block_coursehours');
                    }else{
                        $status = get_string('not-completed', 'block_coursehours');
                    }
                    */
                }
            }
        }else {
            $status = get_string('completed', 'block_coursehours');
        }

        return $status;
    }
    
    public function col_finalgrade($row) {
        global $CFG;
        require_once($CFG->dirroot . '/grade/querylib.php');
        require_once($CFG->libdir . '/gradelib.php');

        if($row->enrolmethod == 1){
            $courseitem = grade_item::fetch_course_item($row->id);
            if (!$grades = grade_get_course_grade($row->userid, $row->id)) {
                return '';
            } else {
                // only one grade - not array
                if($grades->hidden){
                    return '-';
                }
                $finalgrade = reset($grades);
                return grade_format_gradevalue($finalgrade, $courseitem, true);
            }
        }
        return '-';
    }
    
    public function col_lastcourseaccess($row) {
        if ($row->lastcourseaccess) {
            //return format_time(time() - $row->lastcourseaccess);
            return date("Y-m-d H:i", $row->lastcourseaccess);
        }

        return get_string('never');
    }
    
    /**
     * This function is not part of the public api.
     */
    function print_nothing_to_display() {
        global $OUTPUT;

        // Render button to allow user to reset table preferences.
        echo $this->render_reset_button();

        $this->print_initials_bar();
                                                       
        echo get_string('nocourses', 'block_coursehours');
    }   
}