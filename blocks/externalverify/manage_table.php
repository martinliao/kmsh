<?php
/**
 * plugin infomation
 * 
 * @package    block
 * @subpackage externalverify
 * @author     Elaine Chen(CLICK-AP)
 * @copyright  CLICK-AP {@link https://www.click-ap.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();
require_once($CFG->libdir . '/tablelib.php');

class blocks_externalverify_manage_table extends table_sql {
    protected $stage;

    public function __construct($supervisor = null, $filters = array(), $stage = null) {
        parent::__construct('block_enternalverify_manage_table');

        global $DB, $USER;
        if(empty($supervisor)){
            $supervisor = $USER->username;
        }
        
        /*
        $sqlwhere = 'e.status = 0  AND e.supervisor = :supervisor';
        $sqlparams = array('supervisor'=>$supervisor);
        */
        $this->stage = $stage;
        if($this->stage ==2){
            $sqlwhere = "e.status = 4";
        }else {
            $sqlwhere = "e.status = 0 AND (e.supervisor LIKE '$supervisor,%' OR e.supervisor LIKE '%,$supervisor' OR e.supervisor LIKE '%,$supervisor,%' OR e.supervisor = '$supervisor')";
        }

        $sqlparams = array();
        if(!empty($filters['user'])){
            $sqlwhere .= ' AND (u.lastname LIKE "%'.$filters['user'].'%" OR u.firstname LIKE "%'.$filters['user'].'%")';
        }
        if(!empty($filters['course'])){
            $sqlwhere .= ' AND e.fullname LIKE "%'.$filters['course'].'%"';
        }

        $this->set_sql(
             'e.id as applyid, e.timecreated as applydate
            , e.fullname as course, e.startdate, e.enddate, e.summary
            , e.validator, e.manager, e.timeverify1, e.timeverify2
            , e.org, e.expense, e.hours
            , u.id, u.lastname as applyuser, u.firstname
            , "detail" as detail',
            "{course_external} e 
            JOIN {user} u ON u.id = e.userid
            ",
            $sqlwhere,
            $sqlparams);
    }

    public function get_row_class($row) {
        return '';
    }

    public function col_checkboxcolumn($row) {
        return html_writer::checkbox('applyids[]', $row->applyid, false);
    }

    public function col_applyuser($row) {
        global $CFG;
        $col = '<a href="'.$CFG->wwwroot.'/user/profile.php?id='.$row->id.'" target="_blank">'.$row->applyuser. '</a>';;
        return $col;
    }
    public function col_applydate($row) {
        return block_externalverify_get_date_format($row->applydate);
    }
    public function col_startdate($row) {
        return block_externalverify_get_date_format($row->startdate);
    }
    public function col_enddate($row) {
        return block_externalverify_get_date_format($row->enddate);
    }
    public function col_validator($row) {
        if (!empty($row->validator)){
            global $DB;
            $user = $DB->get_record('user', array('id'=>$row->validator));
            return $user->firstname;
        }
        return '';
    }
    public function col_timeverify1($row) {
        if (!empty($row->timeverify1)){
            return block_externalverify_get_date_format($row->timeverify1);
        }
        return '';
    }
    public function col_detail($row) {
        global $CFG;
        
        $url = $CFG->wwwroot. '/blocks/externalverify/detail.php?id='.$row->applyid;
        if(!empty($this->stage)){
            $url .= "&stage=".$this->stage;
        }
        $link = '<a href ="'.$url.'" target="_blank">'.get_string('applydetail', 'block_externalverify').'</a>';
        return $link;
    }
}

class blocks_externalverify_apply_table extends table_sql {
    public function __construct($userid = null) {
        parent::__construct('block_enternalverify_apply_table');

        global $DB, $USER;
        if(empty($userid)){
            $userid = $USER->id;
        }
        $sqlwhere = 'e.status IN (0,4) AND e.userid = :userid';
        $sqlparams = array('userid'=>$userid);

        $this->set_sql(
            'e.id as applyid, e.timecreated as applydate
            , e.fullname as course, e.startdate, e.enddate, e.summary, e.supervisor
            , e.validator, e.manager, e.timeverify1, e.timeverify2
            , e.org, e.expense, e.hours, e.status
            , u.*, "detail" as detail',
            "{course_external} e 
            JOIN {user} u ON u.id = e.userid
            ",
            $sqlwhere,
            $sqlparams);
    }

    public function get_row_class($row) {
        return '';
    }

    public function col_checkboxcolumn($row) {
        return html_writer::checkbox('applyids[]', $row->applyid, false);
    }

    public function col_supervisor($row) {
        global $CFG, $DB;

        if(!empty($row->supervisor)){
            $managers = explode(',', $row->supervisor);
            $data = array();
            foreach($managers as $manage){
                if(empty($manage)){
                    continue;
                }
                $data[] = $DB->get_field('user', 'firstname', array('username'=>$manage));
            }
            $col = implode(',', $data);
        }else{
            return '';
        }

        return $col;
    }
    public function col_applydate($row) {
        return block_externalverify_get_date_format($row->applydate);
    }
    public function col_startdate($row) {
        return block_externalverify_get_date_format($row->startdate);
    }
    public function col_enddate($row) {
        return block_externalverify_get_date_format($row->enddate);
    }
    public function col_validator($row) {
        if (!empty($row->validator)){
            global $DB;
            $user = $DB->get_record('user', array('id'=>$row->validator));
            return $user->firstname;
        }
        return '';
    }
    public function col_timeverify1($row) {
        if (!empty($row->timeverify1)){
            return block_externalverify_get_date_format($row->timeverify1);
        }
        return '';
    }
    public function col_status($row) {
        return block_externalverify_get_verify_status($row->status);
    }

    public function col_detail($row) {
        global $CFG;
        
        $url = $CFG->wwwroot. '/blocks/externalverify/detail.php?id='.$row->applyid;
        $link = '<a href ="'.$url.'" target="_blank">'.get_string('applydetail', 'block_externalverify').'</a>';
        return $link;
    }
}

class blocks_externalverify_applyhistory_table extends table_sql {
    public function __construct($userid = null, $filters = array()) {
        parent::__construct('block_enternalverify_applyhistory_table');

        global $DB, $USER;
        if(empty($userid)){
            $userid = $USER->id;
        }
 
        $sqlwhere = 'e.status NOT IN (0,4) AND e.userid = :userid';
        $sqlparams = array('userid'=>$userid);
        
        if(!empty($filters['course'])){
            $sqlwhere .= ' AND e.fullname like "%'.$filters['course'].'%"';
        }
        if(!empty($filters['status'])){
            $sqlwhere .= ' AND e.status = :status';
            $sqlparams['status'] = $filters['status'];
        }
        
        $this->set_sql(          
            'e.id as applyid, e.timecreated as applydate, e.timemodified as verifydate
            , e.fullname as course, e.startdate, e.enddate, e.summary, e.supervisor, e.reason, e.status
            , e.org, e.expense, e.hours
            , u.*, "detail" as detail',
            "{course_external} e 
            JOIN {user} u ON u.id = e.userid
            ",
            $sqlwhere,
            $sqlparams);
    }

    public function get_row_class($row) {
        return '';
    }

    public function col_checkboxcolumn($row) {
        return html_writer::checkbox('applyids[]', $row->applyid, false);
    }

    public function col_supervisor($row) {
        global $CFG, $DB;

        if(!empty($row->supervisor)){
            $managers = explode(',', $row->supervisor);
            $data = array();
            foreach($managers as $manage){
                if(empty($manage)){
                    continue;
                }
                $data[] = $DB->get_field('user', 'firstname', array('username'=>$manage));
            }
            $col = implode(',', $data);
        }else{
            return '';
        }
        return $col;
    }
    public function col_applydate($row) {
        return block_externalverify_get_date_format($row->applydate);
    }
    public function col_startdate($row) {
        return block_externalverify_get_date_format($row->startdate);
    }
    public function col_enddate($row) {
        return block_externalverify_get_date_format($row->enddate);
    }
    public function col_verifydate($row) {
        return block_externalverify_get_date_format($row->verifydate);
    }
    public function col_status($row) {
        return block_externalverify_get_verify_status($row->status);
    }
        
    public function col_detail($row) {
        global $CFG;
        if ($this->is_downloading()) {
            return '';
        }
        
        $url = $CFG->wwwroot. '/blocks/externalverify/detail.php?id='.$row->applyid;
        $link = '<a href ="'.$url.'" target="_blank">'.get_string('applydetail', 'block_externalverify').'</a>';
        return $link;
    }
}

class blocks_externalverify_verifyhistory_table extends table_sql {
    public function __construct($supervisor = null, $filters = array()) {
        parent::__construct('block_enternalverify_verifyhistory_table');
        
        global $DB, $USER;

        if(empty($supervisor)){
            $supervisor = $USER->username;
        }else{
            $supervisor = $DB->get_field('user','username', array('id'=>$supervisor));
        }

        if(is_siteadmin($USER)){
            $sqlwhere = '(e.status IN (1,2,4))';
            $sqlparams = array();
        }else{
            /*
            $sqlwhere = '(e.status =1 OR e.status =2) AND e.supervisor = :supervisor';
            $sqlparams = array('supervisor'=>$supervisor);
            */
            $sqlwhere = "e.status IN (1,2,4) AND (validator =:validator OR manager =:manager)";
            $sqlparams = array('validator'=>$USER->id, 'manager'=>$USER->id);
        }
        
        if(!empty($filters['user'])){
            $sqlwhere .= ' AND (u.lastname LIKE "%'.$filters['user'].'%" OR u.firstname LIKE "%'.$filters['user'].'%")';
        }
        if(!empty($filters['course'])){
            $sqlwhere .= ' AND e.fullname LIKE "%'.$filters['course'].'%"';
        }
        if(!empty($filters['status'])){
            $sqlwhere .= ' AND e.status = :status';
            $sqlparams['status'] = $filters['status'];
        }
        
        $this->set_sql(
            'e.id as applyid, e.timecreated as applydate, e.timemodified as verifydate
            , e.fullname as course, e.startdate, e.enddate, e.summary, e.supervisor, e.reason, e.status
            , e.org, e.expense, e.hours
            , u.id, u.lastname as applyuser, u.firstname, "detail" as detail',
            "{course_external} e 
            JOIN {user} u ON u.id = e.userid
            ",
            $sqlwhere,
            $sqlparams);
    }

    public function get_row_class($row) {
        return '';
    }

    public function col_checkboxcolumn($row) {
        return html_writer::checkbox('applyids[]', $row->applyid, false);
    }

    public function col_applyuser($row) {
        global $OUTPUT, $CFG;
        $col = '<a href="'.$CFG->wwwroot.'/user/profile.php?id='.$row->id.'" target="_blank">'.$row->applyuser. '</a>';;
        return $col;
    }

    public function col_applydate($row) {
        return block_externalverify_get_date_format($row->applydate);
    }
    public function col_startdate($row) {
        return block_externalverify_get_date_format($row->startdate);
    }
    public function col_enddate($row) {
        return block_externalverify_get_date_format($row->enddate);
    }
    public function col_verifydate($row) {
        return block_externalverify_get_date_format($row->verifydate);
    }
    public function col_status($row) {
        return block_externalverify_get_verify_status($row->status);
    }
        
    public function col_detail($row) {
        global $CFG;
        
        $url = $CFG->wwwroot. '/blocks/externalverify/detail.php?id='.$row->applyid;
        $link = '<a href ="'.$url.'" target="_blank">'.get_string('applydetail', 'block_externalverify').'</a>';
        return $link;
    }
}