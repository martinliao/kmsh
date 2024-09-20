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

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');
}

require_once($CFG->libdir.'/formslib.php');
require_once($CFG->dirroot.'/blocks/externalverify/lib.php');

class block_externalverify_detail_form extends moodleform {
    function definition() {
        
        $mform =& $this->_form;
        $applyid = $this->_customdata['id'];
        $stage = $this->_customdata['stage'];
        
        $mform->addElement('hidden', 'id', $applyid);
        $mform->setType('id', PARAM_INT);
        $mform->addElement('hidden', 'stage', $stage);
        $mform->setType('stage', PARAM_INT);
        
        $mform->addElement('textarea', 'reason', get_string('reason', 'block_externalverify'), array('rows'=>'1', 'cols'=>'50'));
        $mform->setType('reason', PARAM_TEXT);
        
        //$this->add_action_buttons(true, get_string('agree'));
        
        $buttonarray=array();
        $buttonarray[] = &$mform->createElement('submit', 'submitbutton', get_string('btnagree', 'block_externalverify'));
        $buttonarray[] = &$mform->createElement('submit', 'rejectbutton', get_string('btnreject', 'block_externalverify'));
        $buttonarray[] = &$mform->createElement('cancel');
        $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
        $mform->closeHeaderBefore('buttonar');
            
    }

    function validation($data, $files) {
        global $DB;

        $errors = parent::validation($data, $files);
        return $errors;
    }
}