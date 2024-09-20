<?php
/**
 * 
 * @package    enrol
 * @subpackage profile
 * @author     Maria Tan(CLICK-AP)
 * @author     Martin Freeman(CLICK-AP)
 * @copyright  CLICK-AP {@link https://www.click-ap.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/formslib.php');

class enrol_profile_edit_form extends moodleform {

    function definition() {
        $mform = $this->_form;

        list($instance, $plugin, $context) = $this->_customdata;

        $mform->addElement('header', 'header', get_string('pluginname', 'enrol_profile'));

        $mform->addElement('static', 'notice', get_string('notice', 'enrol_profile'), get_string('notice_explain', 'enrol_profile'));
        
        $mform->addElement('text', 'name', get_string('custominstancename', 'enrol'));
        $mform->setType('name', PARAM_TEXT);

        if ($instance->id) {
            $roles = get_default_enrol_roles($context, $instance->roleid);
        } else {
            $roles = get_default_enrol_roles($context, $plugin->get_config('default_roleid'));
        }
        $mform->addElement('select', 'roleid', get_string('role'), $roles);
        $mform->setDefault('roleid', $plugin->get_config('default_roleid'));

        // javascript field
        $mform->addElement('textarea', 'customtext1', get_string('attrsyntax', 'enrol_profile'), array('cols'=>'60', 'rows'=>'8'));
        $mform->addHelpButton('customtext1', 'attrsyntax', 'enrol_profile');

        $mform->addElement('checkbox', 'customint1', get_string('removewhenexpired', 'enrol_profile'));
        $mform->addHelpButton('customint1', 'removewhenexpired', 'enrol_profile');

        $mform->addElement('advcheckbox', 'customint4', get_string('sendcoursewelcomemessage', 'enrol_profile'));
        $mform->addHelpButton('customint4', 'sendcoursewelcomemessage', 'enrol_profile');

        $mform->addElement('textarea', 'customtext2', get_string('customwelcomemessage', 'enrol_profile'), array('cols'=>'60', 'rows'=>'8'));
        $mform->addHelpButton('customtext2', 'customwelcomemessage', 'enrol_profile');
        
        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
        $mform->addElement('hidden', 'courseid');
        $mform->setType('courseid', PARAM_INT);

        $this->add_action_buttons(true, ($instance->id ? null : get_string('addinstance', 'enrol')));

        $this->set_data($instance);
    }


    function add_action_buttons($cancel = true, $submitlabel=null){
        if (is_null($submitlabel)){
            $submitlabel = get_string('savechanges');
        }
        $mform =& $this->_form;
        if ($cancel){
            //when two elements we need a group
            $buttonarray=array();
            $buttonarray[] = &$mform->createElement('submit', 'submitbutton', $submitlabel);
            $buttonarray[] = &$mform->createElement('cancel');
            $buttonarray[] = &$mform->createElement('button', 'purge', get_string('purge', 'enrol_profile'), array(
                    'onclick' => 'enrol_profile_purge(\'' . addslashes(get_string('confirmpurge',
                                    'enrol_profile')) . '\');'
            ));
            $buttonarray[] = &$mform->createElement('button', 'force', get_string('force', 'enrol_profile'), array(
                    'onclick' => 'enrol_profile_force(\'' . addslashes(get_string('confirmforce',
                                    'enrol_profile')) . '\');'
            ));
            $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
            $mform->closeHeaderBefore('buttonar');
        }
    }
}