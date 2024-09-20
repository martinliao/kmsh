<?php
/**
 * @package   block_uploaddoc
 * @copyright 2016 MARY CHEN  {@link http://www.click-ap.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v1 or later
 */
defined('MOODLE_INTERNAL') || die;

require_once($CFG->libdir.'/formslib.php');

class block_derberus_upload_form extends moodleform {
    
    function definition() {
        global $USER;

        $mform     = $this->_form;
        //$data    = $this->_customdata['data'];
        $options   = $this->_customdata['options'];
        $courseid  = $this->_customdata['courseid'];
        $returnurl = $this->_customdata['returnurl'];
        
        $mform->addElement('static', 'description', get_string('description', 'moodle'), get_string('upload_file_limit', 'block_uploaddoc'));
    
        $mform->addElement('filepicker', 'userfile', get_string('file', 'block_uploaddoc'), null, $options);
        $mform->addRule('userfile', null, 'required');
        $mform->addHelpButton('userfile', 'file', 'block_uploaddoc');
        
        $this->add_action_buttons();
        $mform->addElement('hidden', 'returnurl', $returnurl);
        $mform->setType('returnurl', PARAM_URL);
        
        $mform->addElement('hidden', 'courseid', $courseid);
        $mform->setType('courseid', PARAM_INT);
        
        $mform->addElement('hidden', 'userid', $USER->id);
        $mform->setType('userid', PARAM_INT);
        //$this->set_data($data);
    }

    /**
     * Validation.
     *
     * @param array $data
     * @param array $files
     * @return array the errors that were found
     */
    function validation($data, $files) {
        global $DB, $USER;

    }
}