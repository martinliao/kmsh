<?php
/**
 * @package   block_yakitory
 * @copyright 2021 MARY CHEN  {@link https://www.click-ap.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v1 or later
 */
function xmldb_block_yakitory_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2021052000) {
        $table = new xmldb_table('yakitori_videos');
        if ($dbman->table_exists($table)) {
            $dbman->rename_table($table, 'yakitory_videos');
        }

        $table = new xmldb_table('yakitory_videos');
        $field = new xmldb_field('course', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'userid');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        $DB->execute("UPDATE {yakitory_videos} SET course ='My'");

        $field = new xmldb_field('access_key', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'client_id');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        $field = new xmldb_field('duration', XMLDB_TYPE_INTEGER, '10', null, null, null, '0', 'videoid');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        $field = new xmldb_field('thumbnail', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'filename');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_block_savepoint(true, 2021052000, 'yakitory');
    }

    if ($oldversion < 2024050603) {
        $table = new xmldb_table('yakitory_videos');
        
        $field = new xmldb_field('userid');
        if ($dbman->field_exists($table, $field)) {
            $index = new xmldb_index('fk_userid', XMLDB_INDEX_NOTUNIQUE, array('userid'));
            if ($dbman->index_exists($table, $index)) {
                $dbman->drop_index($table, $index);
            }

            $field->set_attributes(XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);
            $dbman->change_field_type($table, $field);
            $dbman->rename_field($table, $field, 'username');

            //change supplier
            if($results = $DB->get_records('yakitory_videos')){
                foreach($results as $data){
                    if($username = $DB->get_field('user', 'username', array('id'=>$data->username))){
                        $data->username = $username;
                        $DB->update_record('yakitory_videos', $data);
                    }
                }
            }

            $index = new xmldb_index('fk_username', XMLDB_INDEX_NOTUNIQUE, array('username'));
            if (!$dbman->index_exists($table, $index)) {
                $dbman->add_index($table, $index);
            }
        }

        $field = new xmldb_field('supplier');
        if ($dbman->field_exists($table, $field)) {
            $index = new xmldb_index('fk_supplier', XMLDB_INDEX_NOTUNIQUE, array('supplier'));
            if ($dbman->index_exists($table, $index)) {
                $dbman->drop_index($table, $index);
            }

            $field->set_attributes(XMLDB_TYPE_CHAR, '100', null, null, null, '');
            $dbman->change_field_type($table, $field);

            //change supplier
            if($results = $DB->get_records_sql("SELECT * FROM {yakitory_videos} WHERE supplier IS NOT NULL OR supplier !=''")){
                foreach($results as $data){
                    if($username = $DB->get_field('user', 'username', array('id'=>$data->supplier))){
                        $data->supplier = $username;
                        $DB->update_record('yakitory_videos', $data);
                    }
                }
            }

            $index = new xmldb_index('fk_supplier', XMLDB_INDEX_NOTUNIQUE, array('supplier'));
            if (!$dbman->index_exists($table, $index)) {
                $dbman->add_index($table, $index);
            }
        }else{
            $field = new xmldb_field('supplier', XMLDB_TYPE_CHAR, '100', null, null, null, null, 'course');
            if (!$dbman->field_exists($table, $field)) {
                $dbman->add_field($table, $field);
            }

            $index = new xmldb_index('fk_supplier', XMLDB_INDEX_NOTUNIQUE, array('supplier'));
            if (!$dbman->index_exists($table, $index)) {
                $dbman->add_index($table, $index);
            }
        }

        upgrade_block_savepoint(true, 2024050603, 'yakitory');
    }

    if ($oldversion < 2024050604) {
        set_config('is_open', 0, 'yakitory');

        $table = new xmldb_table('yakitory_videos');
        $field = new xmldb_field('is_open', XMLDB_TYPE_INTEGER, '1', null, null, null, '0', 'json_code');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_block_savepoint(true, 2024050604, 'yakitory');
    }

    return true;
}
