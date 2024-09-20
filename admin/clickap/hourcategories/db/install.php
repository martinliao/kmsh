<?php
/**
 * 
 * @package clickap_hourcategories
 * @author 2018 Mary Tan
 * @copyright CLICK-AP (https://www.click-ap.com)
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
function xmldb_clickap_hourcategories_install() {
    global $CFG, $DB;
    
    $dbman = $DB->get_manager();

    $table = new xmldb_table('mooccourse_hour_categories');
    if($dbman->table_exists($table)){
        $dbman->rename_table($table, 'clickap_hourcategories');

        $table = new xmldb_table('clickap_hourcategories');
        $field = new xmldb_field('requirement');
        if ($dbman->field_exists($table, $field)) {
            $field = new xmldb_field('requirement', XMLDB_TYPE_NUMBER, '5,1', null, null, null, 0);
            $dbman->change_field_precision($table, $field);
        }
        
        $index = new xmldb_index('fk_year', XMLDB_INDEX_NOTUNIQUE, array('year'));
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }
        $index = new xmldb_index('fk_type', XMLDB_INDEX_NOTUNIQUE, array('type'));
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }
    }
    else{
        $table = new xmldb_table('clickap_hourcategories');
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('year', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('idnumber', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, null);
        $table->add_field('requirement', XMLDB_TYPE_NUMBER, '5,1', null, null, null, 0);
        $table->add_field('type', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, 0);
        $table->add_field('visible', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, 1);
        $table->add_field('sortorder', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        //$table->add_index('fk_year_idnumber', XMLDB_INDEX_UNIQUE, ['year', 'idnumber']);
        $table->add_index('fk_year', XMLDB_INDEX_NOTUNIQUE, ['year']);
        $table->add_index('fk_type', XMLDB_INDEX_NOTUNIQUE, ['type']);
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }
        
        $sortorder = 0;
        $kinds = array('permanent', 'contract', 'newcomer');
        //$kinds = array('permanent','mode-1','mode-2');
        $defaults = array();
        foreach($kinds as $kind){
            if($kind == 'permanent'){
                $name = '正職人員';//正職人員
                $requirement = 40;
            } else if($kind == 'contract'){
                $name = '約聘人員';
                $requirement = 30;
            } else if($kind == 'newcomer'){
                $name = '新進人員';
                $requirement = 16;
            } else if($kind == 'mode-1'){
                $name = '線上課程';
                $requirement = 20;
            } else if($kind == 'mode-2'){
                $name = '面授課程';
                $requirement = 20;
            }
            
            $defaults[] = array('year'=>'0', 'name'=>$name, 'idnumber'=>$kind, 'requirement'=>$requirement, 'sortorder'=>++$sortorder, 'type'=>1, 'visible'=>1, 'timemodified'=>time());
        }
        $DB->insert_records('clickap_hourcategories', $defaults);
    }

    //transfer old data
    $table = new xmldb_table('course');
    $field = new xmldb_field('hourcategories', XMLDB_TYPE_CHAR, '255');
    if ($dbman->field_exists($table, $field)) {
        $sql = "SELECT id, hourcategories FROM {course} WHERE id != ".SITEID;
        $courses = $DB->get_records_sql_menu($sql);
        foreach($courses as $cid =>$hourcategories){
            $hc = explode(',', $hourcategories);
            foreach($hc as $hcid){
                if(!empty($hcid) && !$DB->record_exists('clickap_course_categories', array('courseid'=>$cid, 'hcid'=>$hcid))){
                    $info = new stdClass();
                    $info->courseid = $cid;
                    $info->hcid = $hcid;
                    $info->usermodified = 2;
                    $info->timemodified = time();
                    $DB->insert_record('clickap_course_categories', $info);
                }
            }
        }
    }

    return true;
}