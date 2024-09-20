<?php
/**
 * 
 * @package clickap_hourcategories
 * @author 2018 Mary Tan
 * @copyright CLICK-AP (https://www.click-ap.com)
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die;
require_once($CFG->libdir.'/formslib.php');

class clickap_hourcategories_edit_form extends moodleform {
    protected $code;

    function definition() {
        global $USER, $CFG, $DB;

        $mform = $this->_form;
        $category = $this->_customdata['category'];
        $context   = context_system::instance();

		$mform->addElement('header', 'general', get_string('header_edit', 'clickap_hourcategories'));
        
        $options = $DB->get_records_sql_menu('SELECT DISTINCT year as id , year FROM {clickap_hourcategories} WHERE year > 0 ',array());
        $mform->addElement('select', 'year', get_string('year', 'clickap_hourcategories'), $options);
        $mform->addRule('year', get_string('missingyear', 'clickap_hourcategories'), 'required', null, 'client');
        $mform->setType('year', PARAM_INT);
        $mform->setDefault('year', date('Y',time())-1911 );
        
        $mform->addElement('text', 'name', get_string('categoryname', 'clickap_hourcategories'), 'maxlength="254" size="50"');
        $mform->addRule('name', get_string('missingname', 'clickap_hourcategories'), 'required', null, 'client');
        $mform->setType('name', PARAM_TEXT);
        if (!empty($category->idnumber) and $category->idnumber== 'e-learn') {
            $mform->hardFreeze('name');
            $mform->setConstants('name', $category->name);
        }
        
        /*
        $mform->addElement('text', 'idnumber', get_string('categoryidnumber', 'clickap_hourcategories'), 'maxlength="254" size="50"');
        $mform->setType('idnumber', PARAM_TEXT);
        if (!empty($category->type) and $category->type == 1) {
            $mform->hardFreeze('idnumber');
            $mform->setConstants('idnumber', $category->idnumber);
        }
        */
        $mform->addElement('hidden', 'idnumber');
        $mform->setType('idnumber', PARAM_TEXT);
        
        $mform->addElement('text', 'requirement', get_string('condition', 'clickap_hourcategories'), 'maxlength="10" size="50"');
        //$mform->addRule('requirement', get_string('condition_rule','clickap_hourcategories'), 'numeric', null, 'client');
        //$mform->addRule('requirement', get_string('missingcondition','clickap_hourcategories'), 'required', null, 'client');
        $mform->setType('requirement', PARAM_NUMBER);
        /*
        if (!empty($category->idnumber) and $category->idnumber == "permanent") {
            $mform->hardFreeze('requirement');
            $mform->setConstants('requirement', $category->requirement);
        }
        */
        
        $option = array('0'=>get_string('hide', 'clickap_hourcategories'), '1'=>get_string('show', 'clickap_hourcategories'));
        $mform->addElement('select', 'visible', get_string('categoryvisible', 'clickap_hourcategories'), $option);
        $mform->setType('visible', PARAM_INT);
        $mform->setDefault('visible', 1);
        
        if (isset($category->type) && $category->type == 1) {
            $mform->hardFreeze('visible');
            $mform->setConstants('visible', $category->visible);
        }
        
        $this->add_action_buttons();
        
		$mform->addElement('hidden', 'id', null);
        $mform->setType('id', PARAM_INT);

        $this->set_data($category);
	}

    function validation($data, $files) {
        global $DB;

        $errors = parent::validation($data, $files);

        if (!empty($data['idnumber'])) {
            $sql = "SELECT * FROM {clickap_hourcategories} WHERE year = :year AND idnumber = :idnumber AND id != :id";
            if($DB->record_exists_sql($sql, array('idnumber'=>$data['idnumber'], 'year'=>$data['year'], 'id'=>$data['id']))){
                $errors['idnumber'] = get_string('idnumberisexist', 'clickap_hourcategories');
            }
        }
        
        return $errors;
    }
}