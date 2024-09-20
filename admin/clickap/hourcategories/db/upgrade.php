<?php

function xmldb_clickap_hourcategories_upgrade($oldversion=0) {
    global $DB;
    
    $dbman = $DB->get_manager();
    
    if ($oldversion < 2020051501) {
        $table = new xmldb_table('clickap_hourcategories');
        $field = new xmldb_field('requirement');
        if ($dbman->field_exists($table, $field)) {
            $field = new xmldb_field('requirement', XMLDB_TYPE_NUMBER, '5,1', null, null, null, 0);
            $dbman->change_field_precision($table, $field);
        }
        
        $table = new xmldb_table('clickap_hourcompletions');
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('courseid', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, null);
        $table->add_field('year', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('hcid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('status', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, 0);
        $table->add_field('hours', XMLDB_TYPE_NUMBER, '5,1', null, XMLDB_NOTNULL, null, 0);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_index('fk_user', XMLDB_INDEX_NOTUNIQUE, ['userid']);
        //$table->add_index('fk_course', XMLDB_INDEX_NOTUNIQUE, ['courseid']);
        $table->add_index('fk_hc', XMLDB_INDEX_NOTUNIQUE, ['hcid']);
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }
        
        upgrade_plugin_savepoint(true, 2020051501, 'clickap', 'hourcategories');
    }
    
    return true;
}