<?php

/**
 *  Change log:
 *    status= yes, customint6= yes, password=null, customint1= no, expirynotify=no, expirythreshold= ??, 
 *    customint2=0, customint5=0
 */

/**
 * Adds new instance of enrol_session to specified course
 * or edits current instance.
 *
 * @package    enrol_session
 * @copyright  2015 Click-AP  {@link http://www.click-ap.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/formslib.php');

class enrol_session_edit_form extends moodleform {

    function definition() {
        global $CFG, $DB;

        $mform = $this->_form;

        list($instance, $plugin, $context, $fromparams) = $this->_customdata;

        $mform->addElement('header', 'header', get_string('pluginname', 'enrol_session'));

        $nameattribs = array('size' => '20', 'maxlength' => '255');
        $mform->addElement('text', 'name', get_string('custominstancename', 'enrol_session'), $nameattribs);
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'server');

        $roles = $this->extend_assignable_roles($context, $instance->roleid);
        $mform->addElement('select', 'roleid', get_string('role', 'enrol_session'), $roles);

        $mform->addElement('date_time_selector', 'enrolstartdate', get_string('enrolstartdate', 'enrol_session'), array('optional' => true));
        $mform->setDefault('enrolstartdate', 0);
        $mform->addHelpButton('enrolstartdate', 'enrolstartdate', 'enrol_session');

        $mform->addElement('date_time_selector', 'enrolenddate', get_string('enrolenddate', 'enrol_session'), array('optional' => true));
        $mform->setDefault('enrolenddate', 0);
        $mform->addHelpButton('enrolenddate', 'enrolenddate', 'enrol_session');

        $mform->addElement('text', 'customint3', get_string('maxenrolled', 'enrol_session'));
        $mform->addHelpButton('customint3', 'maxenrolled', 'enrol_session');
        $mform->setType('customint3', PARAM_INT);

        // Session Date
        $mform->addElement('date_time_selector', 'sessiondate', get_string('sessiondate', 'enrol_session'));
        $mform->setDefault('sessiondate', 0);
        $mform->addHelpButton('sessiondate', 'sessiondate', 'enrol_session');

        for ($i=0; $i<=23; $i++) {
            $hours[$i] = sprintf("%02d", $i);
        }
        for ($i=0; $i<60; $i+=5) {
            $minutes[$i] = sprintf("%02d", $i);
        }
        $durtime = array();
        $durtime[] =& $mform->createElement('select', 'hours', get_string('hour', 'form'), $hours, false, true);
        $durtime[] =& $mform->createElement('select', 'minutes', get_string('minute', 'form'), $minutes, false, true);
        $mform->addGroup($durtime, 'durtime', get_string('duration', 'enrol_session'), array(' '), true);
        
        // ----------------------------- Multiply Session : addmultiply, period, sdays
        // Multi 
        $mform->addElement('checkbox', 'addmultiply', '', get_string('createmultiplesessions', 'enrol_session'));
        $mform->addHelpButton('addmultiply', 'createmultiplesessions', 'enrol_session');
        
        // Weeks
        $sdays = array();
        if ($CFG->calendar_startwday === '0') { // Week start from sunday.
            $sdays[] =& $mform->createElement('checkbox', 'Sun', '', get_string('sunday', 'calendar'));
        }
        $sdays[] =& $mform->createElement('checkbox', 'Mon', '', get_string('monday', 'calendar'));
        $sdays[] =& $mform->createElement('checkbox', 'Tue', '', get_string('tuesday', 'calendar'));
        $sdays[] =& $mform->createElement('checkbox', 'Wed', '', get_string('wednesday', 'calendar'));
        $sdays[] =& $mform->createElement('checkbox', 'Thu', '', get_string('thursday', 'calendar'));
        $sdays[] =& $mform->createElement('checkbox', 'Fri', '', get_string('friday', 'calendar'));
        $sdays[] =& $mform->createElement('checkbox', 'Sat', '', get_string('saturday', 'calendar'));
        if ($CFG->calendar_startwday !== '0') { // Week start from sunday.
            $sdays[] =& $mform->createElement('checkbox', 'Sun', '', get_string('sunday', 'calendar'));
        }
        $mform->addGroup($sdays, 'sdays', get_string('sessiondays', 'enrol_session'), array('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'), true);
        $mform->disabledIf('sdays', 'addmultiply', 'notchecked');
        
        // Frequency
        $period = array(1=>1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36);
        $periodgroup = array();
        $periodgroup[] =& $mform->createElement('select', 'period', '', $period, false, true);
        $periodgroup[] =& $mform->createElement('static', 'perioddesc', '', get_string('week', 'enrol_session'));
        $mform->addGroup($periodgroup, 'periodgroup', get_string('period', 'enrol_session'), array(' '), false);
        $mform->disabledIf('periodgroup', 'addmultiply', 'notchecked');
        
        // End of Sessoin Date
        $mform->addElement('date_selector', 'sessionenddate', get_string('sessionenddate', 'enrol_session'));
        $mform->disabledIf('sessionenddate', 'addmultiply', 'notchecked');       

        $mform->addElement('advcheckbox', 'customint4', get_string('sendcoursewelcomemessage', 'enrol_session'));
        $mform->addHelpButton('customint4', 'sendcoursewelcomemessage', 'enrol_session');

        $mform->addElement('textarea', 'customtext1', get_string('customwelcomemessage', 'enrol_session'), array('cols'=>'60', 'rows'=>'8'));
        $mform->addHelpButton('customtext1', 'customwelcomemessage', 'enrol_session');

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
        $mform->addElement('hidden', 'courseid');
        $mform->setType('courseid', PARAM_INT);

        foreach($fromparams as $key => $value){
            $mform->addElement('hidden', $key, $value);
            $mform->setType($key, is_int($value) ? PARAM_INT : PARAM_TEXT);
        }

        if (enrol_accessing_via_instance($instance)) {
            $mform->addElement('static', 'selfwarn', get_string('instanceeditselfwarning', 'core_enrol'), get_string('instanceeditselfwarningtext', 'core_enrol'));
        }

        $this->add_action_buttons(true, ($instance->id ? null : get_string('addinstance', 'enrol')));

        $this->set_data($instance);
    }

    function validation($data, $files) {
        global $DB, $CFG;
        $errors = parent::validation($data, $files);

        list($instance, $plugin, $context) = $this->_customdata;
        $checkpassword = false;

        if($data['durtime']['hours'] ==0 && $data['durtime']['minutes'] == 0){
            $errors['durtime'] = get_string('durationerror', 'enrol_session');
        }
        if (!empty($data['enrolenddate']) and $data['enrolenddate'] <= $data['enrolstartdate']) {
            $errors['enrolenddate'] = get_string('enrolenddaterror', 'enrol_session');
        }

        if (!empty($data['addmultiply']) && $data['sessiondate'] != 0 
                && $data['sessionenddate'] != 0 && $data['sessionenddate'] <= $data['sessiondate']) {
            $errors['sessionenddate'] = get_string('invalidsessionenddate', 'enrol_session');
        }
        
        $addmulti = isset($data['addmultiply'])? (int)$data['addmultiply'] : 0;
        if (($addmulti != 0) && (!array_key_exists('sdays',$data) || empty($data['sdays']))) {
            $data['sdays']= array();
            $errors['sdays'] = get_string('required', 'enrol_session');
        }
        if (isset($data['sdays'])) {
            if (!$this->checkWeekDays($data['sessiondate'], $data['sessionenddate'], $data['sdays']) ) {
                $errors['sdays'] = get_string('checkweekdays', 'enrol_session');
            }
        }

        return $errors;
    }

    /**
    * Gets a list of roles that this user can assign for the course as the default for self-enrolment.
    *
    * @param context $context the context.
    * @param integer $defaultrole the id of the role that is set as the default for self-enrolment
    * @return array index is the role id, value is the role name
    */
    function extend_assignable_roles($context, $defaultrole) {
        global $DB;

        $roles = get_assignable_roles($context, ROLENAME_BOTH);
        if (!isset($roles[$defaultrole])) {
            if ($role = $DB->get_record('role', array('id'=>$defaultrole))) {
                $roles[$defaultrole] = role_get_name($role, $context, ROLENAME_BOTH);
            }
        }
        return $roles;
    }
    
    private function checkWeekDays($sessiondate, $sessionenddate, $sdays) {

        $found = false;

        $daysOfWeek = array(0 => "Sun", 1 => "Mon", 2 => "Tue", 3 => "Wed", 4 => "Thu", 5 => "Fri", 6 => "Sat");
        $start = new DateTime( date("Y-m-d",$sessiondate) );
        $interval = new DateInterval('P1D');
        $end = new DateTime( date("Y-m-d",$sessionenddate) );
        $end->add( new DateInterval('P1D') );

        $period = new DatePeriod($start, $interval, $end);
        foreach ($period as $date) {
            if (!$found) {
                foreach ($sdays as $name => $value) {
                    $key = array_search($name, $daysOfWeek);
                    if ($date->format("w") == $key) {
                        $found = true;
                        break;
                    }
                }
            }
        }

        return $found;
    }
}
