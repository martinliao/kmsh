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
function xmldb_block_externalverify_upgrade($oldversion, $block) {
    global $DB;
    $dbman = $DB->get_manager();
    $table = new xmldb_table('course_external');
    
    if ($oldversion < 2016122003) {
        $field = new xmldb_field('credits', XMLDB_TYPE_INTEGER, '10');
        if ($dbman->field_exists($table, $field)) {
            $dbman->rename_field($table, $field, 'credit');
        }
        upgrade_block_savepoint(true, 2016122003, 'externalverify');
    }

    if ($oldversion < 2017062902) {
        $field = new xmldb_field('superior', XMLDB_TYPE_INTEGER, '10');
        if ($dbman->field_exists($table, $field)) {
            $dbman->rename_field($table, $field, 'supervisor');
        }
        upgrade_block_savepoint(true, 2017062902, 'externalverify');
    }

    if ($oldversion < 2017011601) {
        $field = new xmldb_field('usermodified', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'timecreated');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        upgrade_block_savepoint(true, 2017011601, 'externalverify');
    }
    
    if ($oldversion < 2019072500) {
        $field = new xmldb_field('supervisor', XMLDB_TYPE_INTEGER, '10');
        if ($dbman->field_exists($table, $field)) {
            $field = new xmldb_field('supervisor', XMLDB_TYPE_CHAR, '254');
            $dbman->change_field_type($table, $field);
        }
        
        $index = new xmldb_index('userid', XMLDB_INDEX_NOTUNIQUE, array('userid'));
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }
        $index = new xmldb_index('supervisor', XMLDB_INDEX_NOTUNIQUE, array('supervisor'));
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }
        $index = new xmldb_index('status', XMLDB_INDEX_NOTUNIQUE, array('status'));
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }
        upgrade_block_savepoint(true, 2019072500, 'externalverify');
    }
    
    if ($oldversion < 2020051503) {
        $field = new xmldb_field('leavetype', XMLDB_TYPE_INTEGER, '1', null, null, null, 0, 'city');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        $field = new xmldb_field('expensetype', XMLDB_TYPE_INTEGER, '1', null, null, null, 0, 'leavetype');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        $field = new xmldb_field('expense', XMLDB_TYPE_INTEGER, '10', null, null, null, 0, 'expensetype');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        upgrade_block_savepoint(true, 2020051503, 'externalverify');
    }
    
    if ($oldversion < 2021021700) {
        $field = new xmldb_field('hours');
        if ($dbman->field_exists($table, $field)) {
            $field = new xmldb_field('hours', XMLDB_TYPE_NUMBER, '5,1', null, null, null, 0);
            $dbman->change_field_precision($table, $field);
        }
        upgrade_block_savepoint(true, 2021021700, 'externalverify');
    }

    if ($oldversion < 2024073105) {
        set_config('managerverify', true, 'block_externalverify');

        $field = new xmldb_field('validator', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'supervisor');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('timeverify1', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'validator');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('manager', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'timeverify1');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('timeverify2', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'manager');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $DB->execute("UPDATE {course_external} SET validator = usermodified WHERE status IN (1,2)");
        $DB->execute("UPDATE {course_external} SET timeverify1 = timemodified WHERE status IN (1,2)");

        upgrade_block_savepoint(true, 2024073105, 'externalverify');
    }

    if ($oldversion < 2024091800) {
        $DB->execute("UPDATE {block_instances} SET pagetypepattern = 'my-index-*', showinsubcontexts = 1, subpagepattern = '' WHERE blockname = 'externalverify' AND parentcontextid = 1");
        upgrade_block_savepoint(true, 2024091800, 'externalverify');
    }
    
    return true;
}