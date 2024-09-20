<?php
/**
 *
 * @package    block_certverify
 * @author     Elaine Chen(CLICK-AP)
 * @copyright  CLICK-AP {@link https://www.click-ap.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->libdir . '/formslib.php');
require_once($CFG->dirroot.'/blocks/certverify/locallib.php');

class block_certverify_filter_form extends moodleform {
    public function definition() {        
        $mform =& $this->_form;

        $options = array(0=>get_string('choose')) + block_certverify_get_certs();
        $mform->addElement('select', 'certid', get_string('certname', 'block_certverify'), $options);        
        $mform->setType('certid', PARAM_INT);
        
        $mform->addElement('text', 'user', get_string('user'));
        $mform->setType('user', PARAM_TEXT);

        if(has_capability('block/certverify:viewreport', context_system::instance())){
            $depts = array();
            if($this->_customdata['depts']){
                foreach ($this->_customdata['depts'] as $name) {
                    $depts[$name] = $name;
                }
            }
            $options = array('multiple' => true);
            $mform->addElement('autocomplete', 'depts', get_string('deptname', 'block_certverify'), $depts, $options);
            $mform->setType('depts', PARAM_TEXT);
        }

        $options =  array(0=>get_string('all', 'block_certverify'),
                        '1'=>get_string('vaild', 'block_certverify'),
                        '2'=>get_string('expire', 'block_certverify'));
        $options = array(0=>get_string('choose')) + $options;
        $mform->addElement('select', 'status', get_string('status', 'block_certverify'), $options);        
        $mform->setType('status', PARAM_INT);
                
        $this->add_action_buttons(true, get_string('submit'));
    }
    
    function validation($data, $files) {
        $errors = parent::validation($data, $files);

        return $errors;
    }
}
