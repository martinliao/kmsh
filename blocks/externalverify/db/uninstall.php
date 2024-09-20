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

defined('MOODLE_INTERNAL') || die();

function xmldb_block_externalverify_uninstall() {
    global $CFG, $DB;  

    $delete = "DELETE FROM {block_instances} WHERE blockname ='externalverify'";
    $DB->execute($delete);
   
    //remove user_menu
    $usermenu = $CFG->customusermenuitems;
    $isexist = strpos($usermenu , "/blocks/externalverify/request.php");
    if($isexist){
        $strword = '\r\ncourserequest,block_externalverify|/blocks/externalverify/request.php|up';
        $CFG->customusermenuitems = str_replace($strword, "", $usermenu);
    }
    return true;
}