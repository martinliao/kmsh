<?php
/**
 * freshmanhours block settings
 *
 * @package    block_freshmanhours
 * @copyright  2016 Mary Chen(http://www.click-ap.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
function block_freshmanhours_enrol_get_my_courses_year($userid){
    global $DB;
    
    $params = array('siteid' => SITEID, 'userid1' => $userid);
    $sql = "SELECT from_unixtime(c.startdate, '%Y')-1911 as year
            FROM {course} c 
            JOIN (SELECT DISTINCT e.courseid FROM {enrol} e
                  JOIN {user_enrolments} ue ON (ue.enrolid = e.id AND ue.userid = :userid1)
                  WHERE ue.status = '0' AND e.status = '0') en ON (en.courseid = c.id)
            LEFT JOIN {context} ctx ON (ctx.instanceid = c.id AND ctx.contextlevel = '50')
            WHERE c.id <> :siteid";

    $blocks_plugins = get_plugin_list('block');
    if(array_key_exists("externalverify", $blocks_plugins)){
        $params['userid2'] = $userid;
        $sql .= " UNION          
                 SELECT from_unixtime(c.startdate, '%Y')-1911 as year
                 FROM {course_external} c
                 WHERE c.status = 1 AND c.userid = :userid2";
    }

    $clickap_plugins = get_plugin_list('clickap');
    if(array_key_exists("legacy", $clickap_plugins)){
        $params['userid3'] = $userid;
        $sql .= " UNION
                 SELECT from_unixtime(c.startdate, '%Y')-1911 as year
                 FROM {clickap_legacy} c 
                 WHERE c.userid = :userid3";
    }

    $years = $DB->get_records_sql($sql, $params);
    
    return $years;
}
 
function block_freshmanhours_enrol_get_my_courses($userid, $enddate){
    global $DB;
    
    $params = array('siteid' => SITEID, 'user1' => $userid, 'date1' => $enddate);
    $sql = "SELECT c.id, c.fullname, c.startdate, c.enddate, cc.name as category, '' as hourcategories
            , info.model, info.credit, info.unit
            , (CASE WHEN (info.hours IS NOT NULL) AND (info.hours > 0) THEN info.hours ELSE '-' END) AS hours
            FROM {course} c
            JOIN (SELECT DISTINCT e.courseid FROM {enrol} e
                  JOIN {user_enrolments} ue ON (ue.enrolid = e.id AND ue.userid = :user1)
                  WHERE ue.status = '0' AND e.status = '0') en ON (en.courseid = c.id)
            LEFT JOIN {context} ctx ON (ctx.instanceid = c.id AND ctx.contextlevel = '50')
            LEFT JOIN {course_categories} cc ON cc.id = c.category
            LEFT JOIN {clickap_course_info} info ON info.courseid = c.id
            WHERE c.id <> :siteid 
            AND c.startdate <= :date1";


    $blocks_plugins = get_plugin_list('block');
    if(array_key_exists("externalverify", $blocks_plugins)){
        $params['user2'] = $userid;
        $params['date2'] = $enddate;
        $sql .= " UNION
                 SELECT c.id*-1 as id, c.fullname, c.startdate, c.enddate, c.org as category, c.hourcategories, c.model, c.credit, c.unit, c.hours
                 FROM {course_external} c
                 WHERE c.status = 1 AND c.userid = :user2
                 AND c.startdate <= :date2";
    }

    $clickap_plugins = get_plugin_list('clickap');
    if(array_key_exists("legacy", $clickap_plugins)){
        $params['user3'] = $userid;
        $params['date3'] = $enddate;
        $sql .= " UNION
                 SELECT CONCAT('kl_', c.id) as id, c.fullname, c.startdate, c.enddate, '' as category, c.hourcategories, c.model, c.credit, c.unit, c.hours
                 FROM {clickap_legacy} c 
                 WHERE c.userid = :user3
                 AND c.startdate <= :date3";
    }
            
    $courses = $DB->get_records_sql($sql, $params);
    
    return $courses;
}

function block_freshmanhours_list_hour($userid, $enddate){
    global $DB, $USER, $OUTPUT;
    $user = $DB->get_record('user', array('id'=>$userid));
    profile_load_data($user);

    $content = '';
    $courses = block_freshmanhours_enrol_get_my_courses($user->id, $enddate);
    $myhours = array();
    foreach($courses as $course){
        //course hours
        $unit = $DB->get_field('clickap_code', 'idnumber', array('id'=>$course->unit));
        if($unit == 6){//credit *18
            $chours = $course->hours * 18;
        }else if($unit == 2){//day *6
            $chours = $course->hours * 6;
        }else{
            $chours = $course->hours;
        }

        if(substr($course->id,0,2) == 'kl'){//legacy course
            $myhours[] = $chours;
        }else if($course->id < 0){//external
            $myhours[] = $chours;
        }else{
            $sql = "SELECT * FROM {course_completions} WHERE userid = :userid AND course = :courseid AND reaggregate = 0 AND timecompleted is not null";
            $completion = $DB->record_exists_sql($sql, array('userid'=>$user->id, 'courseid'=>$course->id));
            if($completion){
                $myhours[] = $chours;
            }
        }
    }
    
    $str_requirement = get_string('hour_requirement', 'block_freshmanhours');
    $str_completed = get_string('hour_completed', 'block_freshmanhours');
    $str_notcompletion = get_string('hour_not-completed', 'block_freshmanhours');

    $total = array_sum($myhours);
    $ruleYear = date('Y', strtotime($user->profile_field_ArrivalDate)) - 1911;
    if($rules = $DB->get_record('clickap_hourcategories', array('year'=>$ruleYear, 'type'=>1 , 'idnumber'=>'newcomer'))){        
        $not_completed = $rules->requirement - $total;
        if($not_completed < 0){
            $not_completed = 0;
        }else{
            $not_completed = '<font color="red">'.$not_completed.'</font>';
        }

        $table = new html_table();
        $table->attributes = array('class'=>'admintable generaltable','style'=>'width:50%; white-space: nowrap; display: table;');//table-layout:fixed;
        $table->head  = array('&nbsp;', $str_requirement, $str_completed, $str_notcompletion);
        $table->align  = array('left', 'center', 'center', 'center');    
        $table->data[] = new html_table_row(array($rules->name,$rules->requirement, $total, $not_completed));

        return html_writer::table($table);
    }else {
        return $OUTPUT->notification(get_string('notification', 'block_freshmanhours'), 'notifymessage');
    }
}