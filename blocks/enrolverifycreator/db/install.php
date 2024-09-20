<?php
/**
 * plugin infomation
 *
 * @package    block_enrolverifycreator
 * @copyright  2017 Mary Chen {@link http://www.click-ap.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

function xmldb_block_enrolverifycreator_install($oldversion=0) {
    global $CFG, $DB;  
       
    $dbman = $DB->get_manager();

    $instances = new stdClass();
    $instances->blockname         = 'enrolverifycreator';
    $instances->parentcontextid   = '1';
    $instances->showinsubcontexts = '1';
    $instances->pagetypepattern   = 'my-index-*';
    $instances->subpagepattern    = '';
    $instances->defaultregion     = 'side-post';
    $instances->defaultweight     = '-1';
    $instances->configdata        = '';
    $instances->timecreated       = time();
    $instances->timemodified      = time();                

    $DB->insert_record('block_instances', $instances);

    return true; 
}