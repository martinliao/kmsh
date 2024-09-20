<?php
/**
 *
 * @package clickap_hourcategories
 * @author 2019 Mary Tan
 * @copyright CLICK-AP (https://www.click-ap.com)
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die;
require_once($CFG->libdir.'/formslib.php');

class clickap_hourcategories_duplicate_form extends moodleform {
    protected $code;

    function definition() {
        global $CFG, $DB;

        $mform = $this->_form;
        $context   = context_system::instance();

		$mform->addElement('header', 'general', get_string('header_duplicate', 'clickap_hourcategories'));
        
        $options = $DB->get_records_sql_menu('SELECT DISTINCT year as id , year FROM {clickap_hourcategories} WHERE year <> 0 ',array());
        $mform->addElement('select', 'origin', get_string('origin_year', 'clickap_hourcategories'), $options);
        $mform->addRule('origin', get_string('missingyear', 'clickap_hourcategories'), 'required', null, 'client');
        $mform->setType('origin', PARAM_INT);
        
        $mform->addElement('select', 'dest', get_string('dest_year', 'clickap_hourcategories'), $options);
        $mform->addRule('dest', get_string('missingyear', 'clickap_hourcategories'), 'required', null, 'client');
        $mform->setType('dest', PARAM_INT);
        
        $options = array('0'=>get_string('no'), '1'=>get_string('yes'));
        $mform->addElement('select', 'retain', get_string('retain_origin_year', 'clickap_hourcategories'), $options);
        $mform->setType('retain', PARAM_INT);
        $mform->setDefault('retain', 1);
        
        $dbman = $DB->get_manager();
        $table = new xmldb_table('clickap_hourcredit_profile');
        if ($dbman->table_exists($table)) {
            $mform->addElement('select', 'copyprofile', get_string('duplicate_profile', 'clickap_hourcategories'), $options);
            $mform->setType('copyprofile', PARAM_INT);
            $mform->setDefault('copyprofile', 1);
        }
                
        $this->add_action_buttons();
	}

    function validation($data, $files) {
        global $DB;

        $errors = parent::validation($data, $files);
        
        return $errors;
    }
}