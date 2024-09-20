<?php
/**
 * @package    clickap
 * @subpackage legacy
 * @copyright  2024 CLICK-AP {@https://www.click-ap.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

function clickap_legacy_get_list($separator = ' / ') {
    global $CFG, $DB;

    $sql = "SELECT cc.id, cc.sortorder, cc.name, cc.visible, cc.parent, cc.path
            FROM {clickap_legacy} cc
            WHERE cc.visible =1
            ORDER BY cc.path";
    $rs = $DB->get_recordset_sql($sql);
    $baselist = array();
    $thislist = array();
    foreach ($rs as $record) {
        if (!$record->parent || isset($baselist[$record->parent])) {
            context_helper::preload_from_record($record);
            $baselist[$record->id] = array(
                'name' => format_string($record->name, true),
                'path' => $record->path
            );
            
            $thislist[] = $record->id;
        }
    }
    $rs->close();

    $names = array();
    foreach ($thislist as $id) {
        $path = preg_split('|/|', $baselist[$id]['path'], -1, PREG_SPLIT_NO_EMPTY);
        $namechunks = array();
        foreach ($path as $parentid) {
            $namechunks[] = $baselist[$parentid]['name'];
        }
        $names[$id] = join($separator, $namechunks);
    }
    return $names;
}
