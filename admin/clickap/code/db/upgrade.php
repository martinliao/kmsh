<?php
/**
 * Version details.
 *
 * @package    clickap_code
 * @copyright  2021 CLICK-AP {@link https://www.click-ap.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
function xmldb_clickap_code_upgrade($oldversion=0) {

    global $CFG,$DB;
    $dbman = $DB->get_manager(); // Loads ddl manager and xmldb classes.

    if ($oldversion < 2021021700) {
        $table = new xmldb_table('mooccourse_course_code');
        if($dbman->table_exists($table)){
            $dbman->rename_table($table, 'clickap_code');
        }
        
        $table = new xmldb_table('clickap_code');
        $index = new xmldb_index('fk_type', XMLDB_INDEX_NOTUNIQUE, array('type'));
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }
        upgrade_plugin_savepoint(true, 2021021700, 'clickap', 'code');
    }

    if ($oldversion < 2022061001) {
        $table = new xmldb_table('clickap_code');

        $field = new xmldb_field('code');
        if ($dbman->field_exists($table, $field)) {
            $field->set_attributes(XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, null);
            $dbman->rename_field($table, $field, 'idnumber');
        }

        upgrade_plugin_savepoint(true, 2022061001, 'clickap', 'code');
    }
    
    if ($oldversion < 2024080101) {
        $sql = "UPDATE {clickap_code} SET type ='model' WHERE type ='mode'";
        $DB->execute($sql);

        $table = new xmldb_table('clickap_code');
        $field = new xmldb_field('usermodified', XMLDB_TYPE_INTEGER, '10');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        set_config('type','model,credit,unit,city,cert','clickap_code');

        upgrade_plugin_savepoint(true, 2024080101, 'clickap', 'code');
    }
    return true;
}
