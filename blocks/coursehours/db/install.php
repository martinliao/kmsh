<?php
/**
 * coursehours block settings
 *
 * @package    block_coursehours
 * @copyright  2016 Mary Chen(http://www.click-ap.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

function xmldb_block_coursehours_install($oldversion=0) {
    global $CFG, $DB;  
       
    $dbman = $DB->get_manager();

    $enrols = "'creator','supervisor','waiting','session'";
    set_config('elective',$enrols,'block_coursehours');
    
    return true;   
}