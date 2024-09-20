<?php
/**
 * Version details.
 *
 * @package    clickap
 * @subpackage program
 * @copyright  2018 Click-AP <elaine@click-ap.com>
 * @license    http://www.click-ap.com/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(dirname(dirname(__FILE__)))) . '/config.php');


defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');
require_once($CFG->dirroot . '/admin/clickap/program/lib.php');

/**
 * Form to edit program criteria.
 *
 */
class program_edit_criteria_form extends moodleform {
    public function definition() {
        global $DB;
        $mform = $this->_form;
        $criteria = $this->_customdata['criteria'];
        $addcourse = $this->_customdata['addcourse'];
        $course = $this->_customdata['course'];

        // Get course selector first if it's a new courseset criteria.
        if (($criteria->id == 0 || $addcourse) && $criteria->criteriatype == PROGRAM_CRITERIA_TYPE_COURSESET) {
            $criteria->get_courses($mform);
        } else {
            if ($criteria->id == 0 && $criteria->criteriatype == PROGRAM_CRITERIA_TYPE_COURSE) {
                $mform->addElement('hidden', 'course', $course);
                $mform->setType('course', PARAM_INT);
            }
            list($none, $message) = $criteria->get_options($mform);

            if ($none) {
                $mform->addElement('html', html_writer::tag('div', $message));
                $mform->addElement('submit', 'cancel', get_string('continue'));
            } else {
                /*
                $mform->addElement('header', 'description_header', get_string('description'));
                $mform->addElement('editor', 'description', '', null, null);
                $mform->setType('description', PARAM_RAW);
                $mform->setDefault('description', array(
                        'text' => $criteria->description,
                        'format' => $criteria->descriptionformat
                    )
                );
                */
                $mform->closeHeaderBefore('buttonar');
                $this->add_action_buttons(true, get_string('save', 'clickap_program'));
            }
        }
    }

    /**
     * Validates form data
     */
    public function validation($data, $files) {
        global $OUTPUT;
        $errors = parent::validation($data, $files);
        $addcourse = $this->_customdata['addcourse'];

        if (!$addcourse && isset($this->_customdata['criteria']->required_param)) {
            $required = $this->_customdata['criteria']->required_param;
            $pattern1 = '/^' . $required . '_(\d+)$/';
            $pattern2 = '/^' . $required . '_(\w+)$/';

            $ok = false;
            foreach ($data as $key => $value) {
                if ((preg_match($pattern1, $key) || preg_match($pattern2, $key)) && !($value === 0 || $value == '0')) {
                    $ok = true;
                }
            }

            $warning = $this->_form->createElement('html',
                    $OUTPUT->notification(get_string('error:parameter', 'clickap_program'), 'notifyproblem'), 'submissionerror');

            if (!$ok) {
                $errors['formerrors'] = 'Error';
                $this->_form->insertElementBefore($warning, 'first_header');
            }
        }
        return $errors;
    }
}
