<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * 
 * @package    block_certverify
 * @author     Elaine Chen(CLICK-AP)
 * @copyright  CLICK-AP {@link https://www.click-ap.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_certverify\output;

use context_system;
use renderable;
use stdClass;
use table_sql;
use moodle_url;
use core_user\fields;

defined('MOODLE_INTERNAL') || die;

global $CFG;
require_once($CFG->libdir . '/tablelib.php');

class report_table extends table_sql implements renderable {

    protected $keyword;
    protected $certid;
    protected $status;
    protected $depts;
    
    /**
     * Class constructor
     */
        
    public function __construct($uniqueid, $filters) {
        parent::__construct($uniqueid);

        $this->keyword = $filters['user'];
        $this->certid = $filters['certid'];
        $this->status = $filters['status'];
        if(!empty($filters['depts'])){
            $this->depts = explode(',', $filters['depts']);
        }else {
            $this->depts = '';
        }

        // Define columns.
        $columns = [
            'lastname' => get_string('lastname'),
            'firstname' => get_string('firstname'),
            'idnumber' => get_string('idnumber'),
            'deptname' => get_string('deptname', 'block_certverify'),
            'certname' => get_string('certname', 'block_certverify'),
            'certnumber' => get_string('certnumber', 'block_certverify'),
            'dateissued' => get_string('dateissued', 'block_certverify'),
            'dateexpire' => get_string('dateexpire', 'block_certverify'),
            'validator' => get_string('validator', 'block_certverify'),
            'timeverify' => get_string('timeverify', 'block_certverify'),
            'detail' => '',
        ];

        $this->define_columns(array_keys($columns));
        $this->define_headers(array_values($columns));

        // Table configuration.
        $this->set_attribute('cellspacing', '0');

        $this->sortable(true, 'dateissued', SORT_DESC);
        $this->no_sorting('detail');

        $this->initialbars(false);
        $this->collapsible(false);

        $this->useridfield = 'userid';

        // Initialize table SQL properties.
        $this->init_sql();
    }

    /**
     * Helper method to ensure appropriate method is called to retrieve user name fields
     *
     * @param string $usertablealias
     * @return string
     */
    private function user_name_fields(string $usertablealias): string {
        if (class_exists(fields::class)) {
            return fields::for_name()->get_sql($usertablealias, false, '', '', false)->selects;
        }

        return get_all_user_name_fields(true, $usertablealias);
    }

    /**
     * Initializes table SQL properties
     */
    protected function init_sql(): void {
        global $USER;
        $params = [];

        $fields = 'e.id as applyid, e.timecreated as applydate
        , e.certid, cc.name as certname, e.idnumber as certnumber, e.dateissued, e.dateexpire, e.remark
        , e.validators, e.validator, e.timeverify, e.reason, e.status
        , "detail" as detail 
        , u.id, u.idnumber, uid.data as deptname, ' . $this->user_name_fields('u');

        $from = '{user_certs} e 
                LEFT JOIN {clickap_code} cc ON cc.id = e.certid
                LEFT JOIN {user} u ON u.id = e.userid
                LEFT JOIN {user_info_data} uid ON u.id = uid.userid 
                     AND uid.fieldid = (SELECT id FROM {user_info_field} WHERE shortname = "DeptName")';
        
        $where = " e.status IN (1)";
        if(!has_capability('block/certverify:viewreport', context_system::instance())){
            $from .= ' LEFT JOIN {user_info_data} uid2 ON u.id = uid2.userid 
                            AND uid2.fieldid = (SELECT id FROM {user_info_field} WHERE shortname = "Supervisor")';

            $params['username'] = $USER->username;
            $where .= ' AND uid2.data = :username';
        }

        if(!empty($this->keyword)){
            $where .= ' AND (u.lastname LIKE "%'.$this->keyword.'%" OR u.firstname LIKE "%'.$this->keyword.'%")';
        }
        if(!empty($this->certid)){
            $params['certid'] = $this->certid;
            $where .= ' AND e.certid = :certid';
        }
        if(!empty($this->status)){
            $params['today'] = time();
            /*'1'=>vaild;'2'=>expire*/
            if($this->status == 1){
                $where .= ' AND (e.dateexpire >= :today OR e.dateexpire = 0)';
            }else if($this->status == 2){
                $where .= ' AND (e.dateexpire < :today AND e.dateexpire != 0)';
            }
        }
        if(!empty($this->depts)){
            $cnt = 0; $where_dept = '';
            foreach($this->depts as $dept){
                if($cnt > 0) {
                    $where_dept .= ',';
                }
                $where_dept .= "'".$dept."'";
                $cnt++;
            }
            $where .= " AND uid.data IN ($where_dept)";
        }

        $this->set_sql($fields, $from, $where, $params);
        $this->set_count_sql("SELECT COUNT(1) FROM {$from} WHERE {$where}", $params);
    }

    public function col_firstname(stdClass $row) {
        if ($this->is_downloading()) {
            return $row->firstname;
        }
        $url = new moodle_url('/user/profile.php', array('id'=>$row->id));
        $col = '<a href="'.$url.'" target="_blank">'.$row->firstname. '</a>';;
        return $col;
    }
    public function col_dateissued(stdClass $row) {
        return block_certverify_date_transfer($row->dateissued);
    }
    public function col_dateexpire(stdClass $row) {
        if(!empty($row->dateexpire)){
            return block_certverify_date_transfer($row->dateexpire);
        }
        return '';
    }
    public function col_validator(stdClass $row) {
        if (!empty($row->validator)){
            global $DB;
            $user = $DB->get_record('user', array('id'=>$row->validator));
            return $user->firstname;
        }
        return '';
    }
    public function col_timeverify(stdClass $row) {
        if (!empty($row->timeverify)){
            return block_certverify_date_transfer($row->timeverify, true);
        }
        return '';
    }
    public function col_detail(stdClass $row) {
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
