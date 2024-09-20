<?php
/**
 * smartmenu
 *
 * @package    block
 * @subpackage block_smartmenu
 * @copyright  2015
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v2 or later
 */

defined('MOODLE_INTERNAL') || die();

function xmldb_block_smartmenu_uninstall($oldversion=0) {
    global $CFG, $DB;  

    $result = $DB->delete_records('block_instances', array('blockname'=>'smartmenu'));
    
    //For lock
    if (isset($CFG->undeletableblocktypes)) {
        $undeletableblocktypes = explode(',', $CFG->undeletableblocktypes);
        foreach($undeletableblocktypes as $key=>$val){
            if ($val == 'smartmenu') {
                unset($undeletableblocktypes[$key]);
                set_config('undeletableblocktypes', implode(',', $undeletableblocktypes));
            }
        }
    }
   
   return $result;   
}