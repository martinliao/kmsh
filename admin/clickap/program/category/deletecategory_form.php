<?php

defined('MOODLE_INTERNAL') || die;

require_once($CFG->libdir . '/formslib.php');

class clickap_program_deletecategory_form extends moodleform {

    protected $coursecat;

    /**
     * Defines the form.
     */
    public function definition() {
        global $DB;
        
        $mform = $this->_form;
        $this->programid = $this->_customdata['programid'];
        $this->category = $this->_customdata['category'];
        $categories = array();
        $courses = $DB->get_records_menu('program_category_courses', array('programid'=>$this->programid, 'categoryid'=>$this->category->id), 'sortorder', 'id, id as val');
        
        // Now build the form.
        $mform->addElement('header', 'general', get_string('categorycurrentcontents', '', $this->category->name));

        // Describe the contents of this category.
        $contents = '';
        if (sizeof($courses) > 0) {
            $contents .= '<li>' . get_string('courses') . '</li>';
        }

        if (!empty($contents)) {
            $str = get_string('notcategorised', 'clickap_program');
            $mform->addElement('static', 'emptymessage', get_string('thiscategorycontains'), html_writer::tag('ul', $contents));
            $sql = "SELECT id, CASE  name WHEN '' THEN '$str' ELSE name END as name 
                    FROM {program_category}
                    WHERE programid = :programid AND id != :categoryid";

            $categories = $DB->get_records_sql_menu($sql, array('programid'=>$this->programid, 'categoryid'=>$this->category->id));
        } else {
            $mform->addElement('static', 'emptymessage', '', get_string('deletecategoryempty'));
        }

        if ($categories) {
            $mform->addElement('select', 'newparent', get_string('movecategorycontentto'), $categories);
        }
        
        $mform->addElement('hidden', 'programid', $this->programid);
        $mform->setType('programid', PARAM_INT);
        $mform->addElement('hidden', 'categoryid', $this->category->id);
        $mform->setType('categoryid', PARAM_INT);
        $mform->addElement('hidden', 'action', 'deletecategory');
        $mform->setType('action', PARAM_ALPHANUM);
        $mform->addElement('hidden', 'sure');
        // This gets set by default to ensure that if the user changes it manually we can detect it.
        $mform->setDefault('sure', md5(serialize((int)$this->category->id)));
        $mform->setType('sure', PARAM_ALPHANUM);

        $this->add_action_buttons(true, get_string('delete'));
    }

    /**
     * Perform some extra moodle validation.
     *
     * @param array $data
     * @param array $files
     * @return array An array of errors.
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        if ($data['sure'] !== md5(serialize((int)$this->category->id))) {
            $errors['emptymessage'] = get_string('categorymodifiedcancel');
        }

        return $errors;
    }
}