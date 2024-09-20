<?php
/**
 * 
 * @package    block_certverify
 * @author     Elaine Chen(CLICK-AP)
 * @copyright  CLICK-AP {@link https://www.click-ap.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

function xmldb_block_certverify_install($oldversion=0) {
    global $CFG, $DB;

    $instances = new stdClass();
    $instances->blockname         = 'certverify';
    $instances->parentcontextid   = '1';
    $instances->showinsubcontexts = '1';
    $instances->pagetypepattern   = 'my-index-*';
    $instances->subpagepattern    = '';
    $instances->defaultregion     = 'side-post';
    $instances->defaultweight     = '-1';
    $instances->configdata        = '';
    $instances->timecreated       = time();
    $instances->timemodified      = time();                

    $result = $DB->insert_record('block_instances', $instances);
   
    //add to user_menu
    $usermenu = $CFG->customusermenuitems;
    $isexist = strpos($usermenu , "/blocks/certverify/request.php");
    if(!$isexist){
        $customusermenuitems = $usermenu . chr(10).'courserequest,block_certverify|/blocks/certverify/request.php|up';
        set_config('customusermenuitems', $customusermenuitems);
    }

    return true;   
}