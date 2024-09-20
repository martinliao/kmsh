<?php
/**
 * @package    clickap
 * @subpackage legacy
 * @copyright  2024 CLICK-AP {@https://www.click-ap.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once $CFG->libdir.'/formslib.php';

class clickap_legacy_upload_form extends moodleform {
    function definition () {
        global $DB;
        $mform = $this->_form;
        $thisYear = date('Y') - 1911 - 3;//before 3 year
        $mform->addElement('header', 'settingsheader', get_string('upload'));

        $mform->addElement('filepicker', 'csvfile', get_string('file'));
        $mform->addRule('csvfile', null, 'required');

        $sql = 'SELECT id, CONCAT(year, name) as name FROM {clickap_hourcategories} WHERE visible = 1 AND type = 0 AND year >= :year ORDER BY year DESC,sortorder';
        $hc = $DB->get_records_sql_menu($sql, array('year'=>$thisYear));
        $hcgroup = array();
        $hourcategories = array();
        foreach($hc as $key => $text){
            $hcgroup[] =& $mform->createElement('checkbox', $key, null, $text);
        }
        $mform->addGroup($hcgroup, 'hourcategory', get_string('course_hourcategories', 'local_mooccourse'),null,true);
        $mform->addRule('hourcategory', get_string('missinghourcategories','local_mooccourse'), 'required', null, 'client');
        
        $choices = csv_import_reader::get_delimiter_list();
        // csvdelimiter
        $mform->addElement('select', 'delimiter_name', get_string('csvdelimiter', 'clickap_legacy'), $choices);
        if (get_string('listsep', 'langconfig') == ';') {
            $mform->setDefault('delimiter_name', 'semicolon');
        } else {
            $mform->setDefault('delimiter_name', 'comma');
        }
        
        // encodings
        $choices = core_text::get_encodings();
        $mform->addElement('select', 'encoding', get_string('encoding', 'clickap_legacy'), $choices);
        $mform->setDefault('encoding', 'BIG5');
        
        // rows of preview
        $choices = array('10'=>10, '20'=>20, '100'=>100, '1000'=>1000, '100000'=>100000);
        $mform->addElement('select', 'previewrows', get_string('rowpreviewnum', 'clickap_legacy'), $choices);
        $mform->setType('previewrows', PARAM_INT);

        $this->add_action_buttons(false, get_string('uploadlegacy', 'clickap_legacy'));
    }
}