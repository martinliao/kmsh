<?php
/**
 * Version details.
 *
 * @package    clickap
 * @subpackage program
 * @copyright  2018 Click-AP <elaine@click-ap.com>
 * @license    http://www.click-ap.com/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

function xmldb_clickap_program_upgrade($oldversion) {
    global $CFG, $DB, $USER;  
    $dbman = $DB->get_manager();
    
    if ($oldversion < 2018032100) {
        $table = new xmldb_table('program');
        $field = new xmldb_field('borderstyle', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, '0', 'notification');

        // Conditionally launch add field latest.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        
        upgrade_plugin_savepoint(true, 2018032100, 'clickap', 'program');
    }
    
    if ($oldversion < 2019091503) {
        $table = new xmldb_table('program_category');
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('programid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('sortorder', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, 0);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('usermodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('fk_prgroamid', XMLDB_KEY_FOREIGN, array('programid'), 'program', array('id'));
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }
        
        $table = new xmldb_table('program_category_courses');
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('programid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('categoryid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('courseid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('sortorder', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, 0);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('usermodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('fk_prgroamid', XMLDB_KEY_FOREIGN, array('programid'), 'program', array('id'));
        $table->add_key('fk_categoryid', XMLDB_KEY_FOREIGN, array('categoryid'), 'program_category', array('id'));
        $table->add_key('fk_courseid', XMLDB_KEY_FOREIGN, array('courseid'), 'course', array('id'));
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }
        
        $admin = get_admin();
        if($programs = $DB->get_records_menu('program', array(), 'id', 'id,id as value')){
            foreach($programs as $key=>$pid){
                $data = new stdClass();
                $data->programid = $pid;
                $data->sortorder = 1;
                $data->timemodified = time();
                $data->usermodified = $admin->id;
                
                if($cid = $DB->insert_record('program_category', $data)){
                    $sql = "SELECT pcp.id, pcp.value 
                            FROM {program_criteria_param} pcp
                            LEFT JOIN {program_criteria} pc ON pcp.critid = pc.id
                            WHERE pc.programid = :programid AND pc.criteriatype='5' AND pcp.name like 'course_%'";
                    if($courses = $DB->get_records_sql_menu($sql, array('programid'=>$pid))){
                        $sortorder=0;
                        foreach($courses as $key=>$courseid){
                            $cdata = new stdClass();
                            $cdata->programid = $pid;
                            $cdata->categoryid = $cid;
                            $cdata->courseid = $courseid;
                            $cdata->sortorder = ++$sortorder;
                            $cdata->timemodified = time();
                            $cdata->usermodified = $admin->id;
                            $DB->insert_record('program_category_courses', $cdata);
                        }
                    }
                }
            }
        }
        
        upgrade_plugin_savepoint(true, 2019091503, 'clickap', 'program');
    }

    return true;
}
?>