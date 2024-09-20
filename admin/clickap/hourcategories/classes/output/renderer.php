<?php
/**
 * 
 *
 * @package clickap_hourcategories
 * @author 2019 Mary Tan
 * @copyright CLICK-AP (https://www.click-ap.com)
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
require_once($CFG->libdir.'/formslib.php');

class clickap_hourcategories_renderer extends plugin_renderer_base {
    
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
    
    public function get_my_hours($currentyear, $user = null){
        global $DB, $USER;

        if(empty($user)){$user = $USER;}
        
        $myHours = array('total'=>0);
        //get my enroll courses hours
        $courses = self::get_my_courses_per_year($currentyear, $user->id);
        foreach($courses as $course){
            $chours = self::get_course_hour($course);
            self::get_categories_hour($user, $course, $chours, $myHours);
        }
        
        return $myHours;
    }
    
    public function get_categories_hour($user, $course, $hour, &$myHours){
        global $DB;

        if(empty($course->hourcategories)){
            if($hourcategories = $DB->get_records_menu('clickap_course_categories', array('courseid'=>$course->id),'','hcid as id , hcid')){
                foreach($hourcategories as $hc){
                    if(!isset($myHours[$hc])){
                        $myHours[$hc] = 0;
                        $myHours['ext'][$hc] = 0;
                    }
                    if($course->status == 1){
                        $myHours[$hc] += $hour;
                        if($course->enrolmethod == 2){
                            $myHours['ext'][$hc] += $hour;
                        }
                    }
                }
                
                if($course->status == 1){
                    $myHours['total'] += $hour;
                }
            }
        }else{//external course
            $hourcategories = explode(',', $course->hourcategories);
            foreach($hourcategories as $hc){
                if(empty($hc)){continue;}
                
                if(!isset($myHours[$hc])){
                    $myHours[$hc] = 0;
                    $myHours['ext'][$hc] = 0;
                }

                if(strpos($course->id, 'ec_') !== false){
                    $myHours[$hc] += $hour;
                    $myHours['ext'][$hc] += $hour;
                }
            }
            $myHours['total'] += $hour;
        }

        if(isset($course->model) && !empty($course->model)){
            $model = "mode-".$course->model;
            if(!isset($myHours[$model])){
                $myHours[$model] = 0;
            }
            if($course->status){
                $myHours[$model] += $hour;
            }
        }
        return true;
    }

    public function get_my_courses_per_year($currentyear, $userid){
        //$currentyear = 105
        global $DB;
        $thisYear = $currentyear + 1911;
        
        $params = array('siteid' => SITEID, 'user' => $userid, 'user0' => $userid, 'user1' => $userid, 'year1' => $thisYear);
        $sql = "SELECT c.id, c.fullname, c.startdate, cc.name as category
                , '' as hourcategories, info.hours, 1 as enrolmethod
                , (CASE WHEN (ccp.timecompleted is not null) THEN 1 ELSE 0 END) AS status
                , info.model, info.unit
                , $userid as userid
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
                AND FROM_UNIXTIME(c.startdate, '%Y') = :year1 AND info.hours != '' ";
        
        $blocks_plugins = get_plugin_list('block');
        if(array_key_exists("externalverify", $blocks_plugins)){
            $sql .= " UNION
                      SELECT CONCAT('ec_', c.id) as id, c.fullname, c.startdate, c.org as category
                      , c.hourcategories, c.hours, 2 as enrolmethod, 1 as status
                      , c.model, c.unit
                      , $userid as userid
                      , 0  AS lastcourseaccess
                      FROM {course_external} c
                      WHERE c.status = 1 AND c.userid = :user2
                      AND FROM_UNIXTIME(c.startdate, '%Y') = :year2";
            $params['user2'] = $userid;
            $params['year2'] = $thisYear;
        }

        //add clickap_legacy
        $clickap_plugins = get_plugin_list('clickap');
        if(array_key_exists("legacy", $clickap_plugins)){
            $sql .= " UNION
                      SELECT CONCAT('lc_', c.id) as id, c.fullname, c.startdate, '' as category
                      , c.hourcategories, c.hours, 3 as enrolmethod, 1 as status
                      , c.model, c.unit
                      , $userid as userid
                      , 0  AS lastcourseaccess
                      FROM {clickap_legacy} c 
                      WHERE c.userid = :user3
                      AND FROM_UNIXTIME(c.startdate, '%Y') = :year3";
            $params['user3'] = $userid;
            $params['year3'] = $thisYear;
        }

        $courses = $DB->get_records_sql($sql, $params);
        return $courses;
    }

    public function get_user_hour_by_scheduled($currentyear, $user){
        global $DB;
        /*
        $sql = "SELECT hcid, SUM(hours) FROM {clickap_hourcompletions}
                WHERE status = 1 AND year = :year AND userid = :userid
                GROUP BY hcid";
        $myHours = $DB->get_records_sql_menu($sql, array('year'=>$currentyear, 'userid'=>$user->id));
        
        /*
        $tsql = "SELECT courseid, hours FROM {clickap_hourcompletions}
                 WHERE status = 1 AND year = :year AND userid = :userid
                 GROUP BY courseid";
        $total = $DB->get_records_sql_menu($tsql, array('year'=>$currentyear, 'userid'=>$user->id));
        
        $myHours['total'] = array_sum($total);
        */

        $myHours = array('total'=>0);
        $sql = "SELECT id, courseid, hcid, `hours`, `status` FROM {clickap_hourcompletions}
                WHERE status = 1 AND year = :year AND userid = :userid";
        if($courses = $DB->get_records_sql($sql, array('year'=>$currentyear, 'userid'=>$user->id))){
            foreach($courses as $data){
                $hc = $data->hcid;
                if(!isset($myHours[$hc])){
                    $myHours[$hc] = 0;
                    $myHours['ext'][$hc] = 0;
                }

                if($data->status == 1){
                    $myHours[$hc] += $data->hours;
                    if(strpos($data->courseid, 'ec_') !== false){//external course
                        $myHours['ext'][$hc] += $data->hours;
                    }
                }
            }
        }
        
        return $myHours;
    }
    
    public function get_users_hour_by_scheduled($currentyear, $userids){
        global $DB;
        $userHours = array();
        
        $sql = "SELECT CONCAT_WS('_', userid, hcid) as id, userid, hcid, SUM(hours) as hours 
                FROM {clickap_hourcompletions}
                WHERE status = 1 AND year = :year AND userid in ($userids)
                GROUP BY userid, hcid
                ORDER BY userid";
        if ($records = $DB->get_records_sql($sql, array('year'=>$currentyear))) {
            foreach ($records as $key => $val) {
                $userHours[$val->hcid][$val->userid] = $val->hours;
            }
        }

        return $userHours;
    }
    
    public function get_user_course_completions_scheduled($currentyear, $userid){
        global $DB;

        $params = array('year'=>$currentyear, 'userid'=>$userid, 'year2'=>$currentyear, 'userid2'=>$userid); 
        $sql = "SELECT c.id, c.fullname, c.startdate, c.enddate, hc.hcid, hc.hours, '' as hourcategories
                FROM {clickap_hourcompletions} hc
                LEFT JOIN {course} c ON hc.courseid = c.id
                WHERE hc.status = 1 AND hc.userid = :userid AND hc.year = :year AND c.id is not null
                GROUP BY c.id";
        $blocks_plugins = get_plugin_list('block');
        if(array_key_exists("externalverify", $blocks_plugins)){
              $params['year2'] = $currentyear;
              $params['userid2'] = $userid;
              $sql .= " UNION 
                        SELECT hc.courseid as id , c.fullname, c.startdate, c.enddate, hc.hcid, hc.hours, c.hourcategories
                        FROM {clickap_hourcompletions} hc
                        LEFT JOIN {course_external} c ON c.id = REPLACE(hc.courseid, 'ec_', '')
                        WHERE hc.status = 1 AND hc.userid = :userid2 AND hc.year = :year2 AND hc.courseid like 'ec_%'
                        GROUP BY hc.courseid";       
        }            
        $courses = $DB->get_records_sql($sql, $params);
        
        return $courses;
    }
}