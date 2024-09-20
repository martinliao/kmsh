<?php
/**
 * 
 * @package    block_certverify
 * @author     Elaine Chen(CLICK-AP)
 * @copyright  CLICK-AP {@link https://www.click-ap.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();
require_once($CFG->libdir . '/tablelib.php');

class block_certverify_apply_table extends table_sql {
    public function __construct($userid = null) {
        parent::__construct('block_certverify_apply_table');

        global $DB, $USER;
        if(empty($userid)){
            $userid = $USER->id;
        }
        $sqlwhere = 'e.status IN (0) AND e.userid = :userid';
        $sqlparams = array('userid'=>$userid);

        $this->set_sql(
            'e.id as applyid, e.timecreated as applydate
            , e.certid, cc.name as certname, e.idnumber as certnumber, e.dateissued, e.dateexpire, e.remark
            , e.validators, e.validator, e.timeverify, e.reason, e.status
            , u.*, "detail" as detail',
            "{user_certs} e 
            JOIN {clickap_code} cc ON cc.id = e.certid
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

    public function col_applydate($row) {
        return block_certverify_date_transfer($row->applydate, true);
    }
    public function col_dateissued($row) {
        return block_certverify_date_transfer($row->dateissued);
    }
    public function col_dateexpire($row) {
        if(!empty($row->dateexpire)){
            return block_certverify_date_transfer($row->dateexpire);
        }
        return '';
    }
    public function col_remark($row) {
        return $row->remark;
    }
    public function col_validators($row) {
        global $CFG, $DB;

        if(!empty($row->validators)){
            $managers = explode(',', $row->validators);
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
    
    public function col_detail($row) {
        $wh = "width=620,height=450,toolbar=no,location=no,menubar=no,copyhistory=no,status=no,directories=no,scrollbars=yes,resizable=yes";
        $viewurl = block_certverify_get_certificate_image($row);
        if(!empty($viewurl)){
            //$extra = "onclick=\"window.open('$viewurl', '', '$wh'); return false;\"";
            return "<a href=\"$viewurl\">".get_string('download_cert', 'block_certverify')."</a>";
        }
        return '';
    }
}

class block_certverify_apply_history_table extends table_sql {
    public function __construct($userid = null, $filters = array()) {
        parent::__construct('block_certverify_applyhistory_table');
 
        $sqlwhere = 'e.status NOT IN (0) AND e.userid = :userid';
        $sqlparams = array('userid'=>$userid);

        if(!empty($filters['status'])){
            $sqlwhere .= ' AND e.status = :status';
            $sqlparams['status'] = $filters['status'];
        }

        $this->set_sql(          
            'e.id as applyid, e.timecreated as applydate
            , e.certid, cc.name as certname, e.idnumber as certnumber, e.dateissued, e.dateexpire, e.remark
            , e.validators, e.validator, e.timeverify, e.reason, e.status
            , u.*, "detail" as detail',
            "{user_certs} e 
            JOIN {clickap_code} cc ON cc.id = e.certid
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

    public function col_applydate($row) {
        return block_certverify_date_transfer($row->applydate, true);
    }
    public function col_dateissued($row) {
        return block_certverify_date_transfer($row->dateissued);
    }
    public function col_dateexpire($row) {
        if(!empty($row->dateexpire)){
            return block_certverify_date_transfer($row->dateexpire);
        }
        return '';
    }
    public function col_remark($row) {
        return $row->remark;
    }
    public function col_validators($row) {
        global $CFG, $DB;

        if(!empty($row->validators)){
            $managers = explode(',', $row->validators);
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
    public function col_validator($row) {
        if (!empty($row->validator)){
            global $DB;
            $user = $DB->get_record('user', array('id'=>$row->validator));
            return $user->firstname;
        }
        return '';
    }
    public function col_timeverify($row) {
        if (!empty($row->timeverify)){
            return block_certverify_date_transfer($row->timeverify, true);
        }
        return '';
    }
    public function col_status($row) {
        return block_certverify_get_verify_status($row->status);
    }
    public function col_reason($row) {
        return $row->reason;
    }

    public function col_detail($row) {
        if ($this->is_downloading()) {
            return '';
        }
        $wh = "width=620,height=450,toolbar=no,location=no,menubar=no,copyhistory=no,status=no,directories=no,scrollbars=yes,resizable=yes";
        $viewurl = block_certverify_get_certificate_image($row);
        if(!empty($viewurl)){
            //$extra = "onclick=\"window.open('$viewurl', '', '$wh'); return false;\"";
            return "<a href=\"$viewurl\">".get_string('download_cert', 'block_certverify')."</a>";
        }
        return '';
    }
}

class block_certverify_manage_table extends table_sql {
    public function __construct($validator, $filters = array()) {
        parent::__construct('block_certverify_manage_table');

        $sqlwhere = "e.status IN (0) AND (e.validators LIKE '$validator,%' OR e.validators LIKE '%,$validator' OR e.validators LIKE '%,$validator,%' OR e.validators = '$validator')";

        $sqlparams = array();
        if(!empty($filters['user'])){
            $sqlwhere .= ' AND (u.lastname LIKE "%'.$filters['user'].'%" OR u.firstname LIKE "%'.$filters['user'].'%")';
        }
        if(!empty($filters['keyword'])){
            $sqlwhere .= ' AND (cc.name LIKE "%'.$filters['keyword'].'%" OR e.idnumber LIKE "%'.$filters['keyword'].'%")';
        }

        $this->set_sql(
            'e.id as applyid, e.timecreated as applydate
            , e.certid, cc.name as certname, e.idnumber as certnumber, e.dateissued, e.dateexpire, e.remark
            , e.validators, e.validator, e.timeverify, e.reason, e.status
            , u.*, "detail" as detail',
            "{user_certs} e 
            JOIN {clickap_code} cc ON cc.id = e.certid
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
        $url = new moodle_url('/user/profile.php', array('id'=>$row->id));
        $col = '<a href="'.$url.'" target="_blank">'.$row->firstname. '</a>';;
        return $col;
    }
    public function col_applydate($row) {
        return block_certverify_date_transfer($row->applydate, true);
    }
    public function col_dateissued($row) {
        return block_certverify_date_transfer($row->dateissued);
    }
    public function col_dateexpire($row) {
        if(!empty($row->dateexpire)){
            return block_certverify_date_transfer($row->dateexpire);
        }
        return '';
    }
    public function col_remark($row) {
        return $row->remark;
    }
    public function col_validators($row) {
        global $CFG, $DB;

        if(!empty($row->validators)){
            $managers = explode(',', $row->validators);
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
    public function col_detail($row) {
        $wh = "width=620,height=450,toolbar=no,location=no,menubar=no,copyhistory=no,status=no,directories=no,scrollbars=yes,resizable=yes";
        $viewurl = block_certverify_get_certificate_image($row);
        if(!empty($viewurl)){
            //$extra = "onclick=\"window.open('$viewurl', '', '$wh'); return false;\"";
            return "<a href=\"$viewurl\">".get_string('download_cert', 'block_certverify')."</a>";
        }
        return '';
    }
}

class block_certverify_verify_history_table extends table_sql {
    public function __construct($validator, $filters = array()) {
        parent::__construct('block_certverify_verify_history_table');
        
        global $DB, $USER;

        if(is_siteadmin($USER)){
            $sqlwhere = '(e.status IN (1,2))';
            $sqlparams = array();
        }else{
            $sqlwhere = "e.status IN (1,2) AND (validator =:validator)";
            $sqlparams = array('validator'=>$validator);
        }
        
        if(!empty($filters['user'])){
            $sqlwhere .= ' AND (u.lastname LIKE "%'.$filters['user'].'%" OR u.firstname LIKE "%'.$filters['user'].'%")';
        }
        if(!empty($filters['keyword'])){
            $sqlwhere .= ' AND (cc.name LIKE "%'.$filters['keyword'].'%" OR e.idnumber LIKE "%'.$filters['keyword'].'%")';
        }
        if(!empty($filters['status'])){
            $sqlwhere .= ' AND e.status = :status';
            $sqlparams['status'] = $filters['status'];
        }
        
        $this->set_sql(
            'e.id as applyid, e.timecreated as applydate
            , e.certid, cc.name as certname, e.idnumber as certnumber, e.dateissued, e.dateexpire, e.remark
            , e.validators, e.validator, e.timeverify, e.reason, e.status
            , u.*, "detail" as detail',
            "{user_certs} e 
            JOIN {clickap_code} cc ON cc.id = e.certid
            JOIN {user} u ON u.id = e.userid
            ",
            $sqlwhere,
            $sqlparams);
    }

    public function get_row_class($row) {
        return '';
    }

    public function col_applyuser($row) {
        $url = new moodle_url('/user/profile.php', array('id'=>$row->id));
        $col = '<a href="'.$url.'" target="_blank">'.$row->firstname. '</a>';;
        return $col;
    }
    public function col_applydate($row) {
        return block_certverify_date_transfer($row->applydate, true);
    }
    public function col_dateissued($row) {
        return block_certverify_date_transfer($row->dateissued);
    }
    public function col_dateexpire($row) {
        if(!empty($row->dateexpire)){
            return block_certverify_date_transfer($row->dateexpire);
        }
        return '';
    }
    public function col_remark($row) {
        return $row->remark;
    }
    public function col_validator($row) {
        if (!empty($row->validator)){
            global $DB;
            $user = $DB->get_record('user', array('id'=>$row->validator));
            return $user->firstname;
        }
        return '';
    }
    public function col_timeverify($row) {
        if (!empty($row->timeverify)){
            return block_certverify_date_transfer($row->timeverify, true);
        }
        return '';
    }
    public function col_status($row) {
        return block_certverify_get_verify_status($row->status);
    }
    public function col_reason($row) {
        return $row->reason;
    }
    public function col_detail($row) {
        $wh = "width=620,height=450,toolbar=no,location=no,menubar=no,copyhistory=no,status=no,directories=no,scrollbars=yes,resizable=yes";
        $viewurl = block_certverify_get_certificate_image($row);
        if(!empty($viewurl)){
            //$extra = "onclick=\"window.open('$viewurl', '', '$wh'); return false;\"";
            return "<a href=\"$viewurl\">".get_string('download_cert', 'block_certverify')."</a>";
        }
        return '';
    }
}