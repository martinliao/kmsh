<?php
/**
 * plugin infomation
 *
 * @package    block_enrolverifysupervisor
 * @copyright  2020 CLICK-AP  {@link https://www.click-ap.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();
require_once($CFG->libdir . '/tablelib.php');

class blocks_enrolverifysupervisor_manage_table extends table_sql {
    public function __construct($verifyuser = null, $filters = array()) {
        parent::__construct('block_enrolverifysupervisor_manage_table');

        global $DB, $USER;
        if(empty($verifyuser)){
            $verifyuser = $USER->username;
        }
        if(is_siteadmin($USER)){
            $sqlwhere = 'n.status = 0 AND n.enrolid = e.id ';
            $sqlparams = array();
        }else{
            $like1 = $verifyuser.',%';
            $like2 = '%,'.$verifyuser;
            
            $sqlwhere = 'n.status = 0 AND n.enrolid = e.id 
                         AND (n.verifyuser like :censor1 OR n.verifyuser like :censor2 OR verifyuser = :verifyuser)';
            $sqlparams = array('censor1'=>$like1, 'censor2'=>$like2, 'verifyuser'=>$USER->username);
        }
        
        if(!empty($filters['user'])){
            $sqlwhere .= ' AND (u.lastname like "%'.$filters['user'].'%" OR u.firstname like "%'.$filters['user'].'%")';
        }
        if(!empty($filters['course'])){
            $sqlwhere .= ' AND c.fullname like "%'.$filters['course'].'%"';
        }
        
        $this->set_sql(
            'n.id as applyid, u.id, u.lastname as applyuser, u.firstname , c.fullname as course, n.timecreated as applydate, n.courseid, c.startdate
            , cc.name as category, n.verifyuser',
            "{enrol_supervisor} n 
            JOIN {user} u ON u.id = n.userid
            JOIN {enrol} e ON e.courseid = n.courseid
            JOIN {course} c ON c.id = n.courseid
            JOIN {course_categories} cc ON cc.id = c.category
            ",
            $sqlwhere,
            $sqlparams);
    }

    public function get_row_class($row) {
        return '';
    }

    public function col_checkboxcolumn($row) {
        return html_writer::checkbox('userenrolments[]', $row->applyid, false);
    }
    public function col_course($row) {
        global $CFG;
        $col = '<a href="'.$CFG->wwwroot.'/local/mooccourse/course_info.php?id='.$row->courseid.'" target="_blank">'.$row->course. '</a>';
        return $col;
    }
    public function col_applyuser($row) {
        global $CFG;
        $col = '<a href="'.$CFG->wwwroot.'/user/profile.php?id='.$row->id.'" target="_blank">'.$row->applyuser. '</a>';
        return $col;
    }
    public function col_verifyuser($row) {
        global $DB;

        $users = explode(',', $row->verifyuser);
        foreach($users as $key => $username){
            if($user = $DB->get_record('user', array('username'=>$username))){
                $users[$key] = fullname($user);            
            }else{
                unset($users[$key]);
            }
        }        
        return implode(',', $users);
    }
    public function col_startdate($row) {
        return date("Y-m-d", $row->startdate);
    }
    public function col_applydate($row) {
        return block_enrolverifysupervisor_get_date_format($row->applydate);
    }
}

class blocks_enrolverifysupervisor_apply_table extends table_sql {
    public function __construct($verifyuser = null) {
        parent::__construct('block_enrolverifysupervisor_apply_table');

        global $DB, $USER;
        if(empty($verifyuser)){
            $verifyuser = $USER->username;
        }
        $sqlwhere = 'n.status = 0 AND n.enrolid = e.id AND n.userid = :verifyuser';
        $sqlparams = array('verifyuser'=>$verifyuser);

        $this->set_sql(
            'n.id as applyid, u.*, c.fullname as course, n.timecreated as applydate, n.verifyuser, n.courseid, c.startdate
            , cc.name as category',
            "{enrol_supervisor} n 
            JOIN {user} u ON u.id = n.userid
            JOIN {enrol} e ON e.courseid = n.courseid
            JOIN {course} c ON c.id = n.courseid
            JOIN {course_categories} cc ON cc.id = c.category
            ",
            $sqlwhere,
            $sqlparams);
    }

    public function get_row_class($row) {
        return '';
    }

    public function col_checkboxcolumn($row) {
        return html_writer::checkbox('userenrolments[]', $row->applyid, false);
    }
    public function col_course($row) {
        global $CFG;
        $col = '<a href="'.$CFG->wwwroot.'/local/mooccourse/course_info.php?id='.$row->courseid.'" target="_blank">'.$row->course. '</a>';
        return $col;
    }
    public function col_verifyuser($row) {
        global $DB;
        $users = explode(',', $row->verifyuser);
        foreach($users as $key => $username){
            if($user = $DB->get_record('user', array('username'=>$username))){
                $users[$key] = fullname($user);            
            }else{
                unset($users[$key]);
            }
        }        
        return implode(',', $users);        
    }
    public function col_startdate($row) {
        return date("Y-m-d", $row->startdate);
    }

    public function col_applydate($row) {
        return block_enrolverifysupervisor_get_date_format($row->applydate);
    }
}

class blocks_enrolverifysupervisor_applyhistory_table extends table_sql {
    public function __construct($userid = null, $filters = array()) {
        parent::__construct('block_enrolverifysupervisor_applyhistory_table');

        global $DB, $USER;
        if(empty($userid)){
            $userid = $USER->id;
        }
        $sqlwhere = 'n.status != 0 AND n.enrolid = e.id AND n.userid = :userid';
        $sqlparams = array('userid'=>$userid);

        if(!empty($filters['course'])){
            $sqlwhere .= ' AND c.fullname like "%'.$filters['course'].'%"';
        }
        if(!empty($filters['status'])){
            $sqlwhere .= ' AND n.status = :status';
            $sqlparams['status'] = $filters['status'];
        }
        
        $this->set_sql(
            'n.id as applyid, c.fullname as course, n.verifyuser, n.status, n.timecreated as applydate, n.timemodified as verifydate, n.reason, n.courseid, c.startdate
            , cc.name as category, n.reason, n.usermodified',
            "{enrol_supervisor} n 
            JOIN {user} u ON u.id = n.userid
            JOIN {enrol} e ON e.courseid = n.courseid
            JOIN {course} c ON c.id = n.courseid
            JOIN {course_categories} cc ON cc.id = c.category
            ",
            $sqlwhere,
            $sqlparams);
    }

    public function get_row_class($row) {
        return '';
    }

    public function col_course($row) {
        global $CFG;
        $col = '<a href="'.$CFG->wwwroot.'/local/mooccourse/course_info.php?id='.$row->courseid.'" target="_blank">'.$row->course. '</a>';
        return $col;
    }
    public function col_verifyuser($row) {
        global $OUTPUT, $DB, $CFG;
        $user = $DB->get_record('user', array('id'=>$row->usermodified));
        //$col = $user->firstname;
        $col = fullname($user);
        return $col;
    }
    public function col_status($row) {
        return block_enrolverifysupervisor_get_verify_status($row->status);
    }
    public function col_startdate($row) {
        return date("Y-m-d", $row->startdate);
    }
    public function col_applydate($row) {
        return block_enrolverifysupervisor_get_date_format($row->applydate);
    }
    public function col_verifydate($row) {
        return block_enrolverifysupervisor_get_date_format($row->verifydate);
    }
}

class blocks_enrolverifysupervisor_verifyhistory_table extends table_sql {
    public function __construct($verifyuser = null, $filters = array()) {
        parent::__construct('block_enrolverifysupervisor_verifyhistory_table');

        global $DB, $USER;
        if(empty($verifyuser)){
            $verifyuser = $USER->username;
        }
        if(is_siteadmin($USER)){
            $sqlwhere = '(n.status =1 OR n.status =2) AND n.enrolid = e.id ';
            $sqlparams = array();
        }else{
            $like1 = $verifyuser.',%';
            $like2 = '%,'.$verifyuser;
            
            $sqlwhere = '(n.status =1 OR n.status =2) AND n.enrolid = e.id AND (n.verifyuser like :censor1 OR n.verifyuser like :censor2 OR verifyuser = :verifyuser)';
            $sqlparams = array('censor1'=>$like1, 'censor2'=>$like2, 'verifyuser'=>$USER->username);
        }

        if(!empty($filters['user'])){
            $sqlwhere .= ' AND (u.lastname like "%'.$filters['user'].'%" OR u.firstname like "%'.$filters['user'].'%")';
        }
        if(!empty($filters['course'])){
            $sqlwhere .= ' AND c.fullname like "%'.$filters['course'].'%"';
        }
        if(!empty($filters['status'])){
            $sqlwhere .= ' AND n.status = :status';
            $sqlparams['status'] = $filters['status'];
        }
        
        $this->set_sql(
            'n.id as applyid, c.fullname as course, n.verifyuser, n.status, n.timecreated as applydate, n.timemodified as verifydate, n.reason, n.courseid, c.startdate
             , u.id as applyuser, u.firstname, u.lastname, u.idnumber, cc.name as category, n.reason, n.usermodified',
            "{enrol_supervisor} n 
            JOIN {user} u ON u.id = n.userid
            JOIN {enrol} e ON e.courseid = n.courseid
            JOIN {course} c ON c.id = n.courseid
            JOIN {course_categories} cc ON cc.id = c.category
            ",
            $sqlwhere,
            $sqlparams);
    }

    public function get_row_class($row) {
        return '';
    }

    public function col_course($row) {
        global $CFG;
        $col = '<a href="'.$CFG->wwwroot.'/local/mooccourse/course_info.php?id='.$row->courseid.'" target="_blank">'.$row->course. '</a>';
        return $col;
    }
    public function col_applyuser($row) {
        global $DB, $CFG;
        $user = $DB->get_record('user', array('id'=>$row->applyuser));
        $col = '<a href="'.$CFG->wwwroot.'/user/profile.php?id='.$user->id.'" target="_blank">'.$user->lastname. '</a>';
        return $col;
    }
    public function col_status($row) {
        return block_enrolverifysupervisor_get_verify_status($row->status);
    }
    public function col_verifyuser($row) {
        global $DB;
        $user = $DB->get_record('user', array('id'=>$row->usermodified));
        return fullname($user);//$user->firstname;
    }
    public function col_startdate($row) {
        return date("Y-m-d", $row->startdate);
    }
    public function col_applydate($row) {
        return block_enrolverifysupervisor_get_date_format($row->applydate);
    }
    public function col_verifydate($row) {
        return block_enrolverifysupervisor_get_date_format($row->verifydate);
    }
}