<?php
/**
 * @package    clickap
 * @subpackage longlearn_categories
 * @copyright  2024 CLICK-AP {@https://www.click-ap.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

require_once $CFG->libdir.'/formslib.php';
require_once($CFG->dirroot . '/user/editlib.php');

class upload_form1 extends moodleform {
    function definition () {
        $mform = $this->_form;

        $mform->addElement('header', 'settingsheader', get_string('upload'));

        $mform->addElement('filepicker', 'categoriesfile', get_string('file'));
        $mform->addRule('categoriesfile', null, 'required');

        $choices = csv_import_reader::get_delimiter_list();
        
        // csvdelimiter
        $mform->addElement('select', 'delimiter_name', get_string('csvdelimiter', 'clickap_longlearn_categories'), $choices);
        if (array_key_exists('cfg', $choices)) {
            $mform->setDefault('delimiter_name', 'cfg');
        } else if (get_string('listsep', 'langconfig') == ';') {
            $mform->setDefault('delimiter_name', 'semicolon');
        } else {
            $mform->setDefault('delimiter_name', 'comma');
        }
        
        // encodings
        $choices = core_text::get_encodings();
        $mform->addElement('select', 'encoding', get_string('encoding', 'clickap_longlearn_categories'), $choices);
        $mform->setDefault('encoding', 'BIG5');
        
        // rows of preview
        $choices = array('10'=>10, '20'=>20, '100'=>100, '1000'=>1000, '100000'=>100000);
        $mform->addElement('select', 'previewrows', get_string('rowpreviewnum', 'clickap_longlearn_categories'), $choices);
        $mform->setType('previewrows', PARAM_INT);

        $this->add_action_buttons(false, get_string('uploadcategories', 'clickap_longlearn_categories'));
    }
}