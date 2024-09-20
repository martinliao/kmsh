<?php
/**
 * 
 * @package    block_certverify
 * @author     Elaine Chen(CLICK-AP)
 * @copyright  CLICK-AP {@link https://www.click-ap.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

function xmldb_block_certverify_uninstall() {
    global $CFG, $DB;

    $delete = "DELETE FROM {block_instances} WHERE blockname ='certverify'";
    $DB->execute($delete);
   
    //remove user_menu
    $usermenu = $CFG->customusermenuitems;
    $isexist = strpos($usermenu , "/blocks/certverify/request.php");
    if($isexist){
        $strword = '\r\ncourserequest,block_certverify|/blocks/certverify/request.php|up';
        $CFG->customusermenuitems = str_replace($strword, "", $usermenu);
    }

    return true;
}