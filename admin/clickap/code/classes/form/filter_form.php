<?php
/**
 * Version details.
 *
 * @package    clickap_code
 * @copyright  2021 CLICK-AP {@link https://www.click-ap.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace clickap_code\form;

use moodleform;

defined('MOODLE_INTERNAL') || die;

require_once($CFG->libdir . '/formslib.php');

class filter_form extends moodleform {
    public static $datefieldoptions = array('optional' => true, 'step' => 1);
    
    public function definition() {
        global $DB;

        $type = $this->_customdata['type'];
        
        $mform =& $this->_form;
        $mform->addElement('header', 'details', get_string('filter-options', 'clickap_code'));

        $code = explode(',', get_config('clickap_code', 'type'));
        $typedata = array();
        foreach($code as $key){
            $typedata[$key] = get_string($key, 'clickap_code');
        }
        
        $mform->addElement('select', 'type', get_string('filter-type','clickap_code'), $typedata);        
        $mform->setType('type', PARAM_TEXT);
        $mform->setDefault('type',$type);
        $this->add_action_buttons(false, get_string('submit'));
    }

}
