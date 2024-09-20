<?php
/**
 * @package   block_yakitory
 * @copyright 2021 MARY CHEN  {@link https://www.click-ap.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v1 or later
 */
defined('MOODLE_INTERNAL') || die;

require_once($CFG->libdir.'/formslib.php');

class block_yakitory_edit_form extends moodleform {
    
    function definition() {
        global $CFG, $USER;

        $mform     = $this->_form;
        //$data    = $this->_customdata['data'];
        $options   = $this->_customdata['options'];
        $courseid  = $this->_customdata['courseid'];
        $returnurl = $this->_customdata['returnurl'];
        
        $mform->addElement('static', 'description', get_string('description', 'moodle'), get_string('upload_file_limit', 'block_yakitory'));
    
        $mform->addElement('filepicker', 'uservideo', get_string('video', 'block_yakitory'), null, $options);
        $mform->addRule('uservideo', null, 'required');
        $mform->addHelpButton('uservideo', 'video', 'block_yakitory');
        
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