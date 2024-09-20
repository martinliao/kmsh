<?php
defined('MOODLE_INTERNAL') || die;

require_once($CFG->libdir.'/formslib.php');

class clickap_program_editcategory_form extends moodleform {

    public function definition() {
        global $CFG, $DB;
        $mform = $this->_form;
        $category = $this->_customdata['category'];
        $prgramid = $this->_customdata['programid'];
        
        $mform->addElement('text', 'name', get_string('categoryname'), array('size' => '30'));
        $mform->addRule('name', get_string('required'), 'required', null);
        $mform->setType('name', PARAM_TEXT);
        if ($category->id) {
            $strsubmit = get_string('savechanges');
        } else {
            $strsubmit = get_string('createcategory');
        }

        $mform->addElement('hidden', 'id', 0);
        $mform->setType('id', PARAM_INT);
        $mform->setDefault('id', $category->id);
        $mform->addElement('hidden', 'programid', 0);
        $mform->setType('programid', PARAM_INT);
        $mform->setDefault('programid', $prgramid);

        $this->add_action_buttons(true, $strsubmit);
        $this->set_data($category);
    }

    /**
     * Validates the data submit for this form.
     *
     * @param array $data An array of key,value data pairs.
     * @param array $files Any files that may have been submit as well.
     * @return array An array of errors.
     */
    public function validation($data, $files) {
        global $DB;
        $errors = parent::validation($data, $files);
        
        return $errors;
    }
}
