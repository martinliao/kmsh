<?php
/**
 * Version details.
 *
 * @package    clickap_code
 * @copyright  2021 CLICK-AP {@link https://www.click-ap.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace clickap_code\form;

use moodleform;
use context_system;

defined('MOODLE_INTERNAL') || die;

require_once($CFG->libdir.'/formslib.php');
require_once($CFG->dirroot . '/admin/clickap/code/lib.php');

class edit_form extends moodleform {
    protected $data;

    function definition() {
        global $USER, $CFG, $DB;

        $mform = $this->_form;
        $data = $this->_customdata['data']; // this contains the data of this form
        $type = $this->_customdata['type'];
        $context   = context_system::instance();

        $this->data  = $data;
        $this->context = $context;

		/// form definition with new course defaults
		//--------------------------------------------------------------------------------
        $mform->addElement('header','general', get_string('general', 'form'));

        $code2 = explode(',', get_config('clickap_code', 'type'));
        $typedata = array();
        foreach($code2 as $key){
            $typedata[$key] = get_string($key, 'clickap_code');
        }
        $mform->addElement('select', 'type', get_string('field-type','clickap_code'), $typedata);        
        $mform->setType('type', PARAM_TEXT);
        if(!empty($type)){
            $mform->setDefault('type', $type);
        }
        
        $mform->addElement('text', 'name', get_string('field-name', 'clickap_code'), 'maxlength="254" size="20"');
        $mform->addRule('name', get_string('missing-name', 'clickap_code'), 'required', null, 'client');
        $mform->setType('name', PARAM_TEXT);
        
        $mform->addElement('text', 'idnumber', get_string('field-idnumber', 'clickap_code'), 'maxlength="20" size="20"');
        $mform->addRule('idnumber', get_string('missing-idnumber', 'clickap_code'), 'required', null, 'client');
        $mform->setType('idnumber', PARAM_TEXT);
        
        $option = array('0'=>get_string('disable', 'clickap_code'), '1'=>get_string('enable', 'clickap_code'));
        $mform->addElement('select', 'status', get_string('field-status', 'clickap_code'), $option);
        $mform->setType('status', PARAM_INT);
        $mform->setDefault('status', 1);
        
        //$mform->addElement('hidden', 'sortorder', 0);
        //$mform->setType('sortorder', PARAM_INT);

        $this->add_action_buttons();
        
		$mform->addElement('hidden', 'id', null);
        $mform->setType('id', PARAM_INT);

        $this->set_data($data);
	}
    
    function validation($data, $files) {
        global $DB;

        $errors = parent::validation($data, $files);

        // Add field validation check for duplicate code.
        if (!empty($data['idnumber']) && (empty($data['id']) || $this->data->idnumber != $data['idnumber'])) {
            if ($code = $DB->get_record('clickap_code', array('type'=>$data['type'], 'idnumber' => $data['idnumber']), '*', IGNORE_MULTIPLE)) {
                if (empty($data['id']) || $code->id != $data['id']) {
                    $errors['idnumber'] = get_string('codeidnumbertaken', 'clickap_code');
                }
            }
        }

        return $errors;
    }
}

