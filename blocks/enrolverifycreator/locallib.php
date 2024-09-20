<?php
/**
 * plugin infomation
 *
 * @package    block_enrolverifycreator
 * @copyright  2017 Mary Chen {@link http://www.click-ap.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

 function block_enrolverifycreator_get_history_count($userid){
     global $DB;
     $SQL = "SELECT count(n.id)
            FROM {enrol_creator} n
            LEFT JOIN {user} u ON u.id = n.userid
            LEFT JOIN {course} c ON c.id = n.courseid
            WHERE n.userid =:userid AND status != 0";
     return $DB->count_records_sql($SQL, array('userid'=>$userid));
 }

 function block_enrolverifycreator_get_history_list($userid, $page, $perpage){
     global $DB;
     $before = $page*$perpage;
     $SQL = "SELECT n.id, c.fullname as coursename, n.verifyuser, n.status, n.timecreated, n.timemodified , n.reason, n.courseid
            FROM {enrol_creator} n
            LEFT JOIN {user} u ON u.id = n.userid
            LEFT JOIN {course} c ON c.id = n.courseid
            WHERE n.userid =:userid AND status != 0
            ORDER By n.timecreated DESC, n.timemodified
            LIMIT $before, $perpage";
     return $DB->get_records_sql($SQL, array('userid'=>$userid));
 }
 
  function block_enrolverifycreator_get_mamage_history_count($userid){
     global $DB;
     $SQL = "SELECT count(n.id)
            FROM {enrol_creator} n
            LEFT JOIN {user} u ON u.id = n.userid
            LEFT JOIN {course} c ON c.id = n.courseid
            WHERE n.verifyuser =:userid AND (status =1 OR status =2)";
     return $DB->count_records_sql($SQL, array('userid'=>$userid));
 }

 function block_enrolverifycreator_get_mamage_history_list($userid, $page, $perpage){
     global $DB;
     $before = $page*$perpage;
     $SQL = "SELECT n.id as applyid, c.fullname as coursename, n.status, n.timecreated, n.timemodified , n.reason, u.id as userid, n.courseid
            FROM {enrol_creator} n
            LEFT JOIN {user} u ON u.id = n.userid
            LEFT JOIN {course} c ON c.id = n.courseid
            WHERE n.verifyuser =:userid AND (status =1 OR status =2) 
            ORDER By n.timemodified DESC
            LIMIT $before, $perpage";
     return $DB->get_records_sql($SQL, array('userid'=>$userid));
 }

function block_enrolverifycreator_get_verify_status($verify_status){
    $status = "";
    switch($verify_status){
        case 1:
            $status = get_string('agree', 'block_enrolverifycreator');
            break;
        case 2:
            $status = get_string('reject', 'block_enrolverifycreator');
            break;
        case 3:
            $status = get_string('cancel', 'block_enrolverifycreator');
            break;
        default:
            break;
    }
    return $status;
}

function block_enrolverifycreator_get_date_format($date){
    return date("Y-m-d H:i", $date);
}