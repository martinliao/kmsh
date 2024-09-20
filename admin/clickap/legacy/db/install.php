<?php
/**
 * 
 * @package    clickap_legacy
 * @copyright  2024 CLICK-AP {@https://www.click-ap.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

function xmldb_clickap_legacy_install() {
    global $CFG, $DB;
    
    $dbman = $DB->get_manager();

    $table = new xmldb_table('kmsh_legacy');
    if($dbman->table_exists($table)){
        $dbman->rename_table($table, 'clickap_legacy');

        $table = new xmldb_table('clickap_legacy');
        $index = new xmldb_index('fk_userid', XMLDB_INDEX_NOTUNIQUE, array('userid'));
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }
        $index = new xmldb_index('fk_idnumber', XMLDB_INDEX_NOTUNIQUE, array('idnumber'));
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }
    }

    return true;
}