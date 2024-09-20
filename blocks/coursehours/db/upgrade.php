<?php
/**
 * coursehours block settings
 *
 * @package    block_coursehours
 * @copyright  2016 Mary Chen(http://www.click-ap.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
function xmldb_block_coursehours_upgrade($oldversion) {
    global $CFG, $DB;

    if($oldversion < 2017102602){
        $enrols = "'creator','supervisor','waiting'";
        set_config('elective',$enrols,'block_coursehours');
        upgrade_block_savepoint(true, 2017102602, 'coursehours');
    }
    if($oldversion < 2017102603){
        $enrols = "'creator','supervisor','waiting','session'";
        set_config('elective',$enrols,'block_coursehours');
        upgrade_block_savepoint(true, 2017102603, 'coursehours');
    }
    return true;
}
