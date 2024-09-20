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
 * DB authentication plugin upgrade code
 *
 * @package    auth_kmsh
 * @copyright  2024 CLICK-AP {@https://www.click-ap.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Function to upgrade auth_kmsh.
 * @param int $oldversion the version we are upgrading from
 * @return bool result
 */
function xmldb_auth_kmsh_upgrade($oldversion) {
    global $CFG, $DB;
    $dbman = $DB->get_manager();
    
    if ($oldversion < 2024072500) {
        require_once($CFG->dirroot.'/user/profile/definelib.php');
        require_once($CFG->dirroot . '/cohort/lib.php');

        set_config('setupsql', '','auth_kmsh');

        if(!$kmshcohort = $DB->get_record('cohort', array('idnumber'=>'kmsh'))){
            $kmshcohort = new stdClass();
            $kmshcohort->contextid = context_system::instance()->id;
            $kmshcohort->name      = '院內人員';
            $kmshcohort->idnumber  = 'kmsh';
            $kmshcohort->id        = cohort_add_cohort($kmshcohort);
        }
        if(!$lectorcohort = $DB->get_record('cohort', array('idnumber'=>'lector'))){
            $lectorcohort = new stdClass();
            $lectorcohort->contextid = context_system::instance()->id;
            $lectorcohort->name      = '講師群組';
            $lectorcohort->idnumber  = 'lector';
            $lectorcohort->id        = cohort_add_cohort($lectorcohort);
        }

        $infocate = new stdClass();
        $infocate->name = '其他資料';
        $infocate->sortorder = $DB->count_records('user_info_category') + 1;
        if( ! $infocate->id = $DB->get_field('user_info_category', 'id', array('name'=>$infocate->name))){
            $infocate->id = $DB->insert_record('user_info_category', $infocate);
        }

        $fields = array();
        $fields['deptname']['datatype'] = 'text';
        $fields['deptname']['shortname'] = 'DeptName';
        $fields['deptname']['name'] = '單位名稱';
        $fields['deptname']['required'] = 0;
        $fields['deptname']['param1'] = 30;
        $fields['deptname']['param2'] = 100;
        $fields['deptname']['param3'] = 0;

        $fields['instname']['datatype'] = 'text';
        $fields['instname']['shortname'] = 'InstitutionName';
        $fields['instname']['name'] = '職位名稱';
        $fields['instname']['required'] = 0;
        $fields['instname']['param1'] = 30;
        $fields['instname']['param2'] = 100;
        $fields['instname']['param3'] = 0;

        $fields['arrivaldate']['datatype'] = 'text';
        $fields['arrivaldate']['shortname'] = 'ArrivalDate';
        $fields['arrivaldate']['name'] = '到職日';
        $fields['arrivaldate']['required'] = 0;
        $fields['arrivaldate']['param1'] = 30;
        $fields['arrivaldate']['param2'] = 100;
        $fields['arrivaldate']['param3'] = 0;
    
        $fields['supervisor']['datatype'] = 'text';
        $fields['supervisor']['shortname'] = 'Supervisor';
        $fields['supervisor']['name'] = '直屬主管';
        $fields['supervisor']['required'] = 0;
        $fields['supervisor']['param1'] = 30;
        $fields['supervisor']['param2'] = 100;
        $fields['supervisor']['param3'] = 0;

        $fields['specialty']['datatype'] = 'textarea';
        $fields['specialty']['shortname'] = 'Specialty';
        $fields['specialty']['name'] = '專長';
        $fields['specialty']['required'] = 0;

        $infofield                    = new stdClass();
        $infofield->description       = '';
        $infofield->descriptionformat = FORMAT_HTML;
        $infofield->categoryid        = $infocate->id;
        $infofield->locked            = 1;
        $infofield->visible           = 1;
        $infofield->forceunique       = 0;
        $infofield->signup            = 1;
        $infofield->defaultdata       = '';
        $infofield->defaultdataformat = 0;
        //$infofield->required          = 0;
        $sortorder = $DB->count_records('user_info_field', array('categoryid' => $infocate->id));
        foreach($fields as $items){
            unset($infofield->param2);
            unset($infofield->param3);
            
            foreach($items as $field => $value){
                $infofield->$field = $value;
            }
            $infofield->sortorder =  ++$sortorder;
            if( ! $DB->record_exists('user_info_field', array('shortname'=>$infofield->shortname))){
                $DB->insert_record('user_info_field', $infofield);
            }
        }

        profile_reorder_fields();
        profile_reorder_categories();

        //transfer user data
        $users = $DB->get_records('user');
        if($users){
            $DeptNameId = $DB->get_field('user_info_field', 'id', array('shortname'=>'DeptName'));
            $InstitutionNameId = $DB->get_field('user_info_field', 'id', array('shortname'=>'InstitutionName'));
            $ArrivalDateId = $DB->get_field('user_info_field', 'id', array('shortname'=>'ArrivalDate'));
            $SupervisorId = $DB->get_field('user_info_field', 'id', array('shortname'=>'Supervisor'));

            foreach($users as $user){
                if(isguestuser($user)){continue;}

                $userinfo = new stdClass();
                $userinfo->userid = $user->id;
                if(!empty($user->department)){
                    $userinfo->fieldid = $DeptNameId;
                    $userinfo->data = $user->department;
                    $userinfo->dataformat = 0;
                    $DB->insert_record('user_info_data', $userinfo);
                }
                if(!empty($user->institution)){
                    $userinfo->fieldid = $InstitutionNameId;
                    $userinfo->data = $user->institution;
                    $userinfo->dataformat = 0;
                    $DB->insert_record('user_info_data', $userinfo);
                }
                if(!empty($user->arrivaldate)){
                    $userinfo->fieldid = $ArrivalDateId;
                    $userinfo->data = $user->arrivaldate;
                    $userinfo->dataformat = 0;
                    $DB->insert_record('user_info_data', $userinfo);
                }
                if(!empty($user->supervisor)){
                    $userinfo->fieldid = $SupervisorId;
                    $userinfo->data = $user->supervisor;
                    $userinfo->dataformat = 0;
                    $DB->insert_record('user_info_data', $userinfo);
                }
                
                if(!empty($user->dept_code) OR !empty($user->inst_code)){
                    $user->department = !empty($user->dept_code) ? $user->dept_code : '';
                    $user->institution = !empty($user->inst_code) ? $user->inst_code : '';
                    $DB->update_record('user', $user);
                }
            }
        }

        upgrade_plugin_savepoint(true, 2024072500, 'auth', 'kmsh');
    }

/*
    if ($oldversion < 2024072503) {
        require_once($CFG->dirroot.'/user/profile/definelib.php');
        require_once($CFG->dirroot . '/cohort/lib.php');

        $infocate = new stdClass();
        $infocate->name = '其他資料';
        $infocate->sortorder = $DB->count_records('user_info_category') + 1;
        if( ! $infocate->id = $DB->get_field('user_info_category', 'id', array('name'=>$infocate->name))){
            $infocate->id = $DB->insert_record('user_info_category', $infocate);
        }

        $fields = array();
        $fields['supervisor1']['datatype'] = 'text';
        $fields['supervisor1']['shortname'] = 'Agent1';
        $fields['supervisor1']['name'] = '代理人1';
        $fields['supervisor1']['required'] = 0;
        $fields['supervisor1']['param1'] = 30;
        $fields['supervisor1']['param2'] = 100;
        $fields['supervisor1']['param3'] = 0;
    
        $fields['supervisor2']['datatype'] = 'text';
        $fields['supervisor2']['shortname'] = 'Agent2';
        $fields['supervisor2']['name'] = '代理人2';
        $fields['supervisor2']['required'] = 0;
        $fields['supervisor2']['param1'] = 30;
        $fields['supervisor2']['param2'] = 100;
        $fields['supervisor2']['param3'] = 0;
    
        $fields['supervisor3']['datatype'] = 'text';
        $fields['supervisor3']['shortname'] = 'Agent3';
        $fields['supervisor3']['name'] = '代理人3';
        $fields['supervisor3']['required'] = 0;
        $fields['supervisor3']['param1'] = 30;
        $fields['supervisor3']['param2'] = 100;
        $fields['supervisor3']['param3'] = 0;

        $infofield                    = new stdClass();
        $infofield->description       = '';
        $infofield->descriptionformat = FORMAT_HTML;
        $infofield->categoryid        = $infocate->id;
        $infofield->locked            = 1;
        $infofield->visible           = 1;
        $infofield->forceunique       = 0;
        $infofield->signup            = 1;
        $infofield->defaultdata       = '';
        $infofield->defaultdataformat = 0;
        //$infofield->required          = 0;
        $sortorder = $DB->count_records('user_info_field', array('categoryid' => $infocate->id));
        foreach($fields as $items){
            unset($infofield->param2);
            unset($infofield->param3);
            
            foreach($items as $field => $value){
                $infofield->$field = $value;
            }
            $infofield->sortorder =  ++$sortorder;
            if( ! $DB->record_exists('user_info_field', array('shortname'=>$infofield->shortname))){
                $DB->insert_record('user_info_field', $infofield);
            }
        }

        profile_reorder_fields();
        profile_reorder_categories();

        upgrade_plugin_savepoint(true, 2024072503, 'auth', 'kmsh');
    }
*/

    if ($oldversion < 2024072504) {
        $table = new xmldb_table('user');

        $field = new xmldb_field('arrivaldate');
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }
        $field = new xmldb_field('supervisor');
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }
        $field = new xmldb_field('supervisor_sdate');
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }
        $field = new xmldb_field('supervisor_edate');
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        upgrade_plugin_savepoint(true, 2024072504, 'auth', 'kmsh');
    }

    return true;
}
