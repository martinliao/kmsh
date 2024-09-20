<?php
/**
 * @package   block_uploaddoc
 * @copyright 2016 MARY CHEN  {@link http://www.click-ap.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v1 or later
 */
function xmldb_block_uploaddoc_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2016092302) {
        $table = new xmldb_table('derberus_files');
        $field = new xmldb_field('json_cdoe', XMLDB_TYPE_TEXT);
        if ($dbman->field_exists($table, $field)) {
            $dbman->rename_field($table, $field, 'json_code');
        }
        upgrade_block_savepoint(true, 2016092302, 'uploaddoc');
    }

    if ($oldversion < 2018080603) {
        $table = new xmldb_table('derberus_files');
        $field = new xmldb_field('course', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, 'upload_host');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        $field = new xmldb_field('filesize', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'fileid');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        $field = new xmldb_field('access_key', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, 'client_id');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        $field = new xmldb_field('thumbnail', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'client_id');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        
        $field = new xmldb_field('supplier', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'course');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        upgrade_block_savepoint(true, 2018080603, 'uploaddoc');
    }
    
    if ($oldversion < 2020031501) {
        $table = new xmldb_table('derberus_files');
        
        $key = new xmldb_key('fk_userid', XMLDB_KEY_FOREIGN, array('userid'), 'user', array('id'));
        $dbman->add_key($table, $key);
        
        $key = new xmldb_key('fk_supplier', XMLDB_KEY_FOREIGN, array('supplier'), 'user', array('id'));
        $dbman->add_key($table, $key);
        
        $index = new xmldb_index('fileid', XMLDB_INDEX_NOTUNIQUE, array('fileid'));
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }
        
        $index = new xmldb_index('clienthostid2', XMLDB_INDEX_NOTUNIQUE, array('upload_host','client_id'));
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }
        
        upgrade_block_savepoint(true, 2020031501, 'uploaddoc');
    }
    return true;
}
