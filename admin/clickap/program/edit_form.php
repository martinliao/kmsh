<?php
/**
 * Version details.
 *
 * @package    clickap
 * @subpackage program
 * @copyright  2018 Click-AP <elaine@click-ap.com>
 * @license    http://www.click-ap.com/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');
require_once($CFG->libdir . '/filelib.php');

/**
 * Form to edit program details.
 *
 */
class program_edit_details_form extends moodleform {

    /**
     * Defines the form
     */
    public function definition() {
        global $CFG;

        $mform = $this->_form;
        $program = (isset($this->_customdata['program'])) ? $this->_customdata['program'] : false;
        $action = $this->_customdata['action'];
        $this->program = $program;
        
        $mform->addElement('header', 'programdetails', get_string('programdetails', 'clickap_program'));
        $mform->addElement('text', 'name', get_string('name'), array('size' => '70'));
        // Using PARAM_FILE to avoid problems later when downloading program files.
        $mform->setType('name', PARAM_FILE);
        $mform->addRule('name', null, 'required');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');

        $mform->addElement('textarea', 'description', get_string('description', 'clickap_program'), 'wrap="virtual" rows="8" cols="70"');
        $mform->setType('description', PARAM_NOTAGS);
        $mform->addRule('description', null, 'required');

        $imageoptions = program_image_options();
        $mform->addElement('filemanager', 'medal_filemanager', get_string('programimage', 'clickap_program'), null, $imageoptions);
        $mform->addHelpButton('medal_filemanager', 'programimage', 'clickap_program');
        $mform->addRule('medal_filemanager', null, 'required');
        
        //$mform->addElement('filemanager', 'banner_filemanager', get_string('programbanner', 'clickap_program'), null, $imageoptions);
        //$mform->addHelpButton('banner_filemanager', 'programbanner', 'clickap_program');
        //$mform->addRule('banner_filemanager', null, 'required');
        
        $mform->addElement('filemanager', 'award_filemanager', get_string('programaward', 'clickap_program'), null, $imageoptions);
        $mform->addRule('award_filemanager', null, 'required');
        
        /*
        $mform->addElement('select', 'borderstyle', get_string('borderstyle', 'clickap_program'), clickap_program_award_get_images('borders'));
        $mform->setDefault('borderstyle', '0');
        */

        $mform->addElement('header', 'issuancedetails', get_string('issuancedetails', 'clickap_program'));
        $issuancedetails = array();
        $issuancedetails[] =& $mform->createElement('radio', 'expiry', '', get_string('never', 'clickap_program'), 0);
        $issuancedetails[] =& $mform->createElement('static', 'none_break', null, '<br/>');
        $issuancedetails[] =& $mform->createElement('radio', 'expiry', '', get_string('fixed', 'clickap_program'), 1);
        $issuancedetails[] =& $mform->createElement('date_selector', 'expiredate', '');
        $issuancedetails[] =& $mform->createElement('static', 'expirydate_break', null, '<br/>');
        $issuancedetails[] =& $mform->createElement('radio', 'expiry', '', get_string('relative', 'clickap_program'), 2);
        $issuancedetails[] =& $mform->createElement('duration', 'expireperiod', '', array('defaultunit' => 86400, 'optional' => false));
        $issuancedetails[] =& $mform->createElement('static', 'expiryperiods_break', null, get_string('after', 'clickap_program'));

        $mform->addGroup($issuancedetails, 'expirydategr', get_string('expirydate', 'clickap_program'), array(' '), false);
        $mform->addHelpButton('expirydategr', 'expirydate', 'clickap_program');
        $mform->setDefault('expiry', 0);
        $mform->setDefault('expiredate', strtotime('+1 year'));
        $mform->disabledIf('expiredate[day]', 'expiry', 'neq', 1);
        $mform->disabledIf('expiredate[month]', 'expiry', 'neq', 1);
        $mform->disabledIf('expiredate[year]', 'expiry', 'neq', 1);
        $mform->disabledIf('expireperiod[number]', 'expiry', 'neq', 2);
        $mform->disabledIf('expireperiod[timeunit]', 'expiry', 'neq', 2);

        // Set issuer URL.
        // Have to parse URL because program issuer origin cannot be a subfolder in wwwroot.
        $url = parse_url($CFG->wwwroot);
        $mform->addElement('hidden', 'issuerurl', $url['scheme'] . '://' . $url['host']);
        $mform->setType('issuerurl', PARAM_URL);

        $mform->addElement('hidden', 'action', $action);
        $mform->setType('action', PARAM_TEXT);

        if ($action == 'new') {
            $this->add_action_buttons(true, get_string('createbutton', 'clickap_program'));
        } else {
            // Add hidden fields.
            $mform->addElement('hidden', 'id', $program->id);
            $mform->setType('id', PARAM_INT);

            $this->add_action_buttons();
            $this->set_data($program);

            // Freeze all elements if program is active or locked.
            if ($program->is_active() || $program->is_locked()) {
                $mform->hardFreezeAllVisibleExcept(array());
            }
        }
    }

    /**
     * Load in existing data as form defaults
     *
     * @param stdClass|array $default_values object or array of default values
     */
    public function set_data($program) {
        $default_values = array();
        parent::set_data($program);

        if (!empty($program->expiredate)) {
            $default_values['expiry'] = 1;
            $default_values['expiredate'] = $program->expiredate;
        } else if (!empty($program->expireperiod)) {
            $default_values['expiry'] = 2;
            $default_values['expireperiod'] = $program->expireperiod;
        }

        parent::set_data($default_values);
        
    }

    /**
     * Validates form data
     */
    public function validation($data, $files) {
        global $DB;
        $errors = parent::validation($data, $files);

        if ($data['expiry'] == 2 && $data['expireperiod'] <= 0) {
            $errors['expirydategr'] = get_string('error:invalidexpireperiod', 'clickap_program');
        }

        if ($data['expiry'] == 1 && $data['expiredate'] <= time()) {
            $errors['expirydategr'] = get_string('error:invalidexpiredate', 'clickap_program');
        }

        // Check for duplicate program names.
        if ($data['action'] == 'new') {
            $duplicate = $DB->record_exists_select('program', 'name = :name AND status != :deleted',
                array('name' => $data['name'], 'deleted' => PROGRAM_STATUS_ARCHIVED));
        } else {
            $duplicate = $DB->record_exists_select('program', 'name = :name AND id != :programid AND status != :deleted',
                array('name' => $data['name'], 'programid' => $data['id'], 'deleted' => PROGRAM_STATUS_ARCHIVED));
        }

        if ($duplicate) {
            $errors['name'] = get_string('error:duplicatename', 'clickap_program');
        }

        return $errors;
    }
}

/**
 * Form to edit program message.
 *
 */
class program_edit_message_form extends moodleform {
    public function definition() {
        global $CFG, $OUTPUT;

        $mform = $this->_form;
        $program = $this->_customdata['program'];
        $action = $this->_customdata['action'];
        $editoroptions = $this->_customdata['editoroptions'];

        // Add hidden fields.
        $mform->addElement('hidden', 'id', $program->id);
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'action', $action);
        $mform->setType('action', PARAM_TEXT);

        $mform->addElement('header', 'programmessage', get_string('configuremessage', 'clickap_program'));
        $mform->addHelpButton('programmessage', 'variablesubstitution', 'clickap_program');

        $mform->addElement('text', 'messagesubject', get_string('subject', 'clickap_program'), array('size' => '70'));
        $mform->setType('messagesubject', PARAM_TEXT);
        $mform->addRule('messagesubject', null, 'required');
        $mform->addRule('messagesubject', get_string('maximumchars', '', 255), 'maxlength', 255);

        $mform->addElement('editor', 'message_editor', get_string('message', 'clickap_program'), null, $editoroptions);
        $mform->setType('message_editor', PARAM_RAW);
        $mform->addRule('message_editor', null, 'required');
        /*
        $mform->addElement('advcheckbox', 'attachment', get_string('attachment', 'clickap_program'), '', null, array(0, 1));
        $mform->addHelpButton('attachment', 'attachment', 'clickap_program');
        if (empty($CFG->allowattachments)) {
            $mform->freeze('attachment');
        }
        */
        $options = array(
                PROGRAM_MESSAGE_NEVER   => get_string('never'),
                PROGRAM_MESSAGE_ALWAYS  => get_string('notifyevery', 'clickap_program'),
                PROGRAM_MESSAGE_DAILY   => get_string('notifydaily', 'clickap_program'),
                PROGRAM_MESSAGE_WEEKLY  => get_string('notifyweekly', 'clickap_program'),
                PROGRAM_MESSAGE_MONTHLY => get_string('notifymonthly', 'clickap_program'),
                );
        $mform->addElement('select', 'notification', get_string('notification', 'clickap_program'), $options);
        $mform->addHelpButton('notification', 'notification', 'clickap_program');

        $this->add_action_buttons();
        $this->set_data($program);
    }

    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        return $errors;
    }
}
