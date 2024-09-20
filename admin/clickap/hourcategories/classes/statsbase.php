<?php
/**
 * 
 *
 * @package    clickap_hourcategories
 * @author     Jack Liou <jack@click-ap.com>
 * @author     Elaine Chen <elaine@click-ap.com>
 * @copyright  2020 Click-AP {@link https://www.click-ap.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace clickap_hourcategories;
use context_course;
use stdClass;
use completion_info;
use xmldb_table;

defined('MOODLE_INTERNAL') || die();

class statsbase {

    protected $currentyear;
    protected $logs;

    public function __construct($currentyear) {
        global $CFG;
        require_once($CFG->libdir.'/completionlib.php');

        $this->currentyear = $currentyear;
        $this->logs = array();
    }

    public function get_course_hour($course){
        global $DB;

        $hours = 0;
        if(isset($course->unit) && !empty($course->unit)){
            $unit = $DB->get_field('clickap_code', 'idnumber', array('id'=>$course->unit));
            if($unit == 6){//credit *18
                $hours = $course->hours * 18;
            }else if($unit == 2){//day *6
                $hours = $course->hours * 6;
            }else{
                $hours = $course->hours;
            }
        }else{
            $hours = $course->hours;
        }
        return $hours;
    }
    
    public function check_log($id){
        global $DB;
        
        if(isset($this->logs[$id])){
            return true;
        }

        $sql1 = "SELECT MAX(timemodified) FROM {clickap_hourcompletions} 
                 WHERE courseid =:id";
        if(!$lasttime = $DB->get_field_sql($sql1, array('id'=>$id))){
            $lasttime = 0;
        }
        
        $where = "timecreated > :lasttime AND courseid = :id";
        $logs = $DB->count_records_select('logstore_standard_log', $where, array('lasttime'=>$lasttime, 'id'=>$id));
        
        if($logs > 0){
            $this->logs[$id] = true;
            return true;
        }
        return false;
    }
    
    public function execute_hourcategories() {
        global $CFG, $DB;
        $dbman = $DB->get_manager();

        mtrace('Course completion hour process start');
        $gradebookroles = explode(',', $CFG->gradebookroles);

        $hourcategories = $DB->get_records('clickap_hourcategories', array('year'=>$this->currentyear, 'visible'=>1, 'type'=>0), 'sortorder');
        foreach($hourcategories as $hc){
            mtrace('.hcid => '.$hc->id);
            $sql = "SELECT cc.courseid, ci.hours FROM {clickap_course_categories} cc
                    LEFT JOIN {clickap_course_info} ci ON cc.courseid = ci.courseid
                    WHERE cc.hcid = :hcid";
            $courses = $DB->get_records_sql_menu($sql, array('hcid'=>$hc->id));
            foreach($courses as $cid => $hours){

                if(!self::check_log($cid)){
                    continue;
                }
                mtrace('...courseid => '.$cid);

                if($DB->record_exists('course', array('id'=>$cid))){
                    $course = get_course($cid);
                }else{
                    $DB->delete_records('clickap_course_categories', array('courseid'=>$cid));
                    $DB->delete_records('clickap_hourcompletions', array('courseid'=>$cid));
                    continue;
                }                
                
                $context = context_course::instance($course->id);
                $info = new completion_info($course);
                //get real hours
                
                //$course->hours = $hours;
                $table = new xmldb_table('clickap_course_info');
                if($dbman->table_exists($table)){
                    $cinfo = $DB->get_record('clickap_course_info', array('courseid'=>$course->id));
                    $course->model = !empty($cinfo->model) ? $cinfo->model : null;
                    $course->unit = !empty($cinfo->unit) ? $cinfo->unit : null;
                    $course->hours = !empty($cinfo->hours) ? $cinfo->hours : null;
                }
                
                $hours = self::get_course_hour($course);
                
                $data = array();
                //if($info->is_enabled()){
                    foreach($gradebookroles as $roleid){
                        $users = get_role_users($roleid, $context);
                        foreach($users as $user){
                            $status = 0;
                            $coursecomplete = $info->is_course_complete($user->id);
                            if($coursecomplete){
                                $status = 1;
                            }

                            if($hourcompletions = $DB->get_record('clickap_hourcompletions', array('userid'=>$user->id, 'courseid'=>$course->id, 'hcid'=>$hc->id))){
                                if($hourcompletions->status != $status or $hourcompletions->hours != $hours){
                                    //$DB->delete_records('clickap_hourcompletions', array('userid'=>$user->id, 'courseid'=>$course->id, 'hcid'=>$hc->id));
                                    //$data[] = array('userid'=>$user->id, 'courseid'=>$course->id, 'year'=>$year, 'hcid'=>$hc->id, 'hours'=>$hours, 'status'=>$status, 'timemodified'=>time());
                                    $updatedata = new stdClass();
                                    $updatedata->id = $hourcompletions->id;
                                    $updatedata->status = $status;
                                    $updatedata->hours = $hours;
                                    $updatedata->timemodified = time();
                                    $DB->update_record('clickap_hourcompletions', $updatedata);
                                }
                            }else{
                                $data[] = array('userid'=>$user->id, 'courseid'=>$course->id, 'year'=>$this->currentyear, 'hcid'=>$hc->id, 'hours'=>$hours, 'status'=>$status, 'timemodified'=>time());
                            }
                        }
                    }
                //}
                if(!empty($data)){
                    $DB->insert_records('clickap_hourcompletions', $data);
                }                
            }
        }
        mtrace('Course completion hour process end');
    }
    
    public function execute_externalverify(){
        global $CFG, $DB;

        mtrace('External course hour completion stats process start');

        $hourcategories = $DB->get_records('clickap_hourcategories', array('year'=>$this->currentyear, 'visible'=>1, 'type'=>0), 'sortorder');
        foreach($hourcategories as $hc){
            mtrace('.hour category id => '.$hc->id);

            $like1 = "%,".$hc->id;
            $like2 = $hc->id.",%";
            $like3 = "%,".$hc->id.",%";
            $sql = "SELECT CONCAT('ec_', id) as id, userid, startdate, hourcategories, hours, unit
                    FROM {course_external}
                    WHERE status = 1 
                    AND (hourcategories like :hcid1 OR hourcategories like :hcid2 
                        OR hourcategories like :hcid3 OR hourcategories = :hcid4) ";
            $courses = $DB->get_records_sql($sql, array('hcid1'=>$like1, 'hcid2'=>$like2, 'hcid3'=>$like3, 'hcid4'=>$hc->id));
            foreach($courses as $course){
                $hours = self::get_course_hour($course);

                $updatedata = new stdClass();
                $updatedata->status = 1;
                $updatedata->hours = $hours;
                $updatedata->timemodified = time();
                if($hourcompletions = $DB->get_record('clickap_hourcompletions', array('courseid'=>$course->id, 'userid'=>$course->userid, 'hcid'=>$hc->id))){
                    if($hourcompletions->status != 1 or $hourcompletions->hours != $hours){
                        $updatedata->id = $hourcompletions->id;
                        $DB->update_record('clickap_hourcompletions', $updatedata);
                    }
                }else{
                    $updatedata->userid = $course->userid;
                    $updatedata->courseid = $course->id;
                    $updatedata->year = $this->currentyear;
                    $updatedata->hcid = $hc->id;
                    $DB->insert_record('clickap_hourcompletions', $updatedata);
                }
            }
        }
        mtrace('External course hour completion stats process end');
    }

    public function execute_legacy(){
        global $CFG, $DB;

        mtrace('Legacy course hour completion stats process start');

        $hourcategories = $DB->get_records('clickap_hourcategories', array('year'=>$this->currentyear, 'visible'=>1, 'type'=>0), 'sortorder');
        foreach($hourcategories as $hc){
            mtrace('.hour category id => '.$hc->id);

            $like1 = "%,".$hc->id;
            $like2 = $hc->id.",%";
            $like3 = "%,".$hc->id.",%";
            $sql = "SELECT CONCAT('lc_', id) as id, userid, startdate, hourcategories, hours, unit
                    FROM {clickap_legacy}
                    WHERE  (hourcategories like :hcid1 OR hourcategories like :hcid2 
                        OR hourcategories like :hcid3 OR hourcategories = :hcid4) ";
            $courses = $DB->get_records_sql($sql, array('hcid1'=>$like1, 'hcid2'=>$like2, 'hcid3'=>$like3, 'hcid4'=>$hc->id));
            foreach($courses as $course){
                $hours = self::get_course_hour($course);

                $updatedata = new stdClass();
                $updatedata->status = 1;
                $updatedata->hours = $hours;
                $updatedata->timemodified = time();
                if($hourcompletions = $DB->get_record('clickap_hourcompletions', array('courseid'=>$course->id, 'userid'=>$course->userid, 'hcid'=>$hc->id))){
                    if($hourcompletions->status != 1 or $hourcompletions->hours != $hours){
                        $updatedata->id = $hourcompletions->id;
                        $DB->update_record('clickap_hourcompletions', $updatedata);
                    }
                }else{
                    $updatedata->userid = $course->userid;
                    $updatedata->courseid = $course->id;
                    $updatedata->year = $this->currentyear;
                    $updatedata->hcid = $hc->id;
                    $DB->insert_record('clickap_hourcompletions', $updatedata);
                }
            }
        }
        mtrace('Legacy course hour completion stats process end');
    }
}
