<?php
/**
 * 
 * @package    block_certverify
 * @author     Elaine Chen(CLICK-AP)
 * @copyright  CLICK-AP {@link https://www.click-ap.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');
}

require_once($CFG->libdir.'/formslib.php');
require_once($CFG->dirroot.'/blocks/certverify/lib.php');

class block_certverify_request_reject_form extends moodleform {
    function definition() {
        $mform =& $this->_form;
        $applyids = $this->_customdata['applyids'];
        
        $mform->addElement('hidden', 'rejectusers', $applyids);
        $mform->setType('rejectusers', PARAM_TEXT);

        $mform->addElement('textarea', 'reason', get_string('reason', 'block_certverify'), array('rows'=>'1', 'cols'=>'50'));
        //$mform->addRule('reason', get_string('missingreqreason'), 'required', null, 'client');
        $mform->setType('reason', PARAM_TEXT);
        $this->add_action_buttons(true, get_string('reject'));
    }

    function validation($data, $files) {
        $errors = parent::validation($data, $files);
        return $errors;
    }
}