<?php
/** Database enrolment plugin implementation
 *
 * @package    enrol
 * @subpackage profile
 * @author     Maria Tan(CLICK-AP)
 * @author     Martin Freeman(CLICK-AP)
 * @copyright  CLICK-AP {@link https://www.click-ap.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class enrol_profile_plugin extends enrol_plugin {
    /**
     * Is it possible to delete enrol instance via standard UI?
     *
     * @param object $instance
     * @return bool
     */
    public function instance_deleteable($instance) {
        return true;
    }

    /**
     * Returns link to page which may be used to add new instance of enrolment plugin in course.
     * @param int $courseid
     * @return moodle_url page url
     */
    public function get_newinstance_link($courseid) {
        $context = context_course::instance($courseid);

        if (!has_capability('moodle/course:enrolconfig', $context) or !has_capability('enrol/profile:config', $context)) {
            return NULL;
        }
        $configured_profilefields = explode(',', get_config('enrol_profile', 'profilefields'));
        if (!strlen(array_shift($configured_profilefields))) {
            // no profile fields are configured for this plugin
            return NULL;
        }
        // multiple instances supported - different roles with different password
        return new moodle_url('/enrol/profile/edit.php', array('courseid'=>$courseid));
    }

    /**
     * Is it possible to delete enrol instance via standard UI?
     *
     * @param object $instance
     * @return bool
     */
    public function can_delete_instance($instance) {
        $context = context_course::instance($instance->courseid);
        return has_capability('enrol/profile:config', $context);
    }

    /**
     * Is it possible to hide/show enrol instance via standard UI?
     *
     * @param stdClass $instance
     * @return bool
     */
    public function can_hide_show_instance($instance) {
        $context = context_course::instance($instance->courseid);
        return has_capability('enrol/profile:config', $context);
    }

    /**
     * Returns edit icons for the page with list of instances
     * @param stdClass $instance
     * @return array
     */
    public function get_action_icons(stdClass $instance) {
        global $OUTPUT;

        if ($instance->enrol !== 'profile') {
            throw new coding_exception('invalid enrol instance!');
        }
        $context = context_course::instance($instance->courseid);

        $icons = array();

        if (has_capability('enrol/profile:config', $context)) {
            $editlink = new moodle_url("/enrol/profile/edit.php", array('courseid'=>$instance->courseid, 'id'=>$instance->id));
            $icons[] = $OUTPUT->action_icon($editlink, new pix_icon('i/edit', get_string('edit'), 'core', array('class'=>'icon')));
        }

        return $icons;
    }

    public static function attrsyntax_toarray($attrsyntax) { // TODO : protected
        global $DB;
        
        $rules ='';
        $attrsyntax_object = json_decode($attrsyntax);
        if(!empty($attrsyntax_object->rules)){
            $rules = $attrsyntax_object->rules;
        }
        $customfields = array();
        foreach ($DB->get_records('user_info_field') as $customfieldrecord) {
            $customfields[$customfieldrecord->id] = $customfieldrecord->shortname;
        }

        return array(
            'customuserfields'  => $customfields,
            'rules'             => $rules
        );
    }


    public static function arraysyntax_tosql($arraysyntax) { // TODO : protected
        global $CFG;
        $select = '';
        $where = 'true';
        static $join_id = 0;

        $customfields = $arraysyntax['customuserfields'];
        if(!empty($arraysyntax['rules'])){
            foreach ($arraysyntax['rules'] as $rule) {
                // first just check if we have a value 'ANY' to enroll all people :
                if (isset($rule->value) && $rule->value == 'ANY') {
                    return array(
                        'select' => '',
                        'where'  => 'true'
                    );
                }
            }

            $next = 0;
            foreach ($arraysyntax['rules'] as $rule) {
                $next++;
                if (isset($rule->cond_op)) {
                    $where .= ' '.strtoupper($rule->cond_op).' ';
                }
                else {
                    $where .= ' AND ';
                }
                
                if(isset($arraysyntax['rules'][$next])){//have next rule
                    if(isset($arraysyntax['rules'][$next]->rules)){//next rule is group?
                        $where .= ' ( ';
                    }
                }
                
                if (isset($rule->rules)) {
                    $sub_arraysyntax = array(
                        'customuserfields'  => $customfields,
                        'rules'             => $rule->rules
                    );
                    $sub_sql = self::arraysyntax_tosql($sub_arraysyntax);
                    $select .= ' '.$sub_sql['select'].' ';
                    $where .= ' ( '.$sub_sql['where'].' ) ) ';
                }
                else {
                    if ($customkey = array_search($rule->param, $customfields, true)) {
                        // custom user field actually exists
                        $join_id++;
                        $select .= ' RIGHT JOIN '.$CFG->prefix.'user_info_data d'.$join_id.' ON d'.$join_id.'.userid = u.id';
                        //$where .= ' (d'.$join_id.'.fieldid = '.$customkey.' AND d'.$join_id.'.data = \''.$rule->value.'\' )';
                        /*
                        $likestr = ' OR d'.$join_id.'.data LIKE \''.$rule->value.';%\'';
                        $likestr .= ' OR d'.$join_id.'.data LIKE \'%;'.$rule->value.'\'';
                        $likestr .= ' OR d'.$join_id.'.data LIKE \'%;'.$rule->value.';%\'';
                        */
                        $likestr = ' OR d'.$join_id.'.data LIKE \'%'.$rule->value.'%\'';
                        $where .= ' (d'.$join_id.'.fieldid = '.$customkey.' AND (d'.$join_id.'.data = \''.$rule->value.'\' '.$likestr.'))';
                    }
                    else{
                        //$where .=  $rule->param. ' = \''.$rule->value.'\'';
                        /*
                        $likestr = ' OR '.$rule->param.' LIKE \''.$rule->value.';%\'';
                        $likestr .= ' OR '.$rule->param.' LIKE \'%;'.$rule->value.'\'';
                        $likestr .= ' OR '.$rule->param.' LIKE \'%;'.$rule->value.';%\'';
                        */
                        $likestr = ' OR '.$rule->param.' LIKE \'%'.$rule->value.'%\'';
                        $where .=  '( '.$rule->param. ' = \''.$rule->value.'\' '.$likestr.')';
                    }
                }
            }
        }
        $where = preg_replace('/^true AND/', '', $where);
        $where = preg_replace('/^true OR/', '', $where);
        $where = preg_replace('/^true/', '', $where);

        return array(
            'select' => $select,
            'where' => $where
        );
    }
    
    public static function arraysyntax_tostring($arraysyntax) {
        global $CFG;

        $where = '';
        $customfields = $arraysyntax['customuserfields'];

        foreach ($arraysyntax['rules'] as $rule) {
            // first just check if we have a value 'ANY' to enroll all people :
            if (isset($rule->value) && $rule->value == 'ANY') {
                return get_string('any', 'enrol_profile');
            }
        }

        $next = 0;
        foreach ($arraysyntax['rules'] as $rule) {
            $next++;
            if (isset($rule->cond_op)) {
                $where .= ' '.strtoupper($rule->cond_op).' ';
            }
            if(isset($arraysyntax['rules'][$next])){//have next rule
                if(isset($arraysyntax['rules'][$next]->rules)){//next rule is group?
                    $where .= ' ( ';
                }
            }

            if (isset($rule->rules)) {
                $sub_arraysyntax = array(
                    'customuserfields'  => $customfields,
                    'rules'             => $rule->rules
                );
                $sub_where = self::arraysyntax_tostring($sub_arraysyntax);
                $where .= ' ('.$sub_where.') ) ';
            }
            else {
                /*
                $likestr = ' OR '.$rule->param.' LIKE \''.$rule->value.';%\'';
                $likestr .= ' OR '.$rule->param.' LIKE \'%;'.$rule->value.'\'';
                $likestr .= ' OR '.$rule->param.' LIKE \'%;'.$rule->value.';%\'';
                */
                $likestr = ' OR '.$rule->param.' LIKE \'%'.$rule->value.'%\'';

                if ($customkey = array_search($rule->param, $customfields, true)) {
                    //$where .= ' '.$rule->param.' = \''.$rule->value.'\' ';
                    $where .= ' ( '.$rule->param.' = \''.$rule->value.'\' '.$likestr.') ';
                }
                else{
                    //$where .=  $rule->param. ' = \''.$rule->value.'\'';
                    $where .=  '( '.$rule->param. ' = \''.$rule->value.'\' '.$likestr.')';
                }
            }
        }

        $where = preg_replace('/^true AND/', '', $where);
        $where = preg_replace('/^true OR/', '', $where);
        $where = preg_replace('/^true/', '', $where);

        return $where;
    }

    public static function process_login(\core\event\user_loggedin $event) {
        global $CFG, $DB;
        // we just received the event from the authentication system; check if well-formed:
        if (!$event->userid) {
            // didn't get an user ID, return as there is nothing we can do
            return true;
        }

        if (in_array('shibboleth', get_enabled_auth_plugins()) && $_SERVER['SCRIPT_FILENAME'] == $CFG->dirroot.'/auth/shibboleth/index.php') {
            // we did get this event from the Shibboleth authentication plugin,
            // so let's try to make the relevant mappings, ensuring that necessary profile fields exist and Shibboleth profile are provided:
            $customfieldrecords = $DB->get_records('user_info_field');
            $customfields = array();
            foreach ($customfieldrecords as $customfieldrecord) {
                $customfields[] = $customfieldrecord->shortname;
            }
            $excludes = array(
                'id',              // makes no sense
                'mnethostid',      // makes no sense
                'timecreated',     // will be set to relative to the host anyway
                'timemodified',    // will be set to relative to the host anyway
                'auth',            // going to be set to 'mnet'
                'deleted',         // we should never get deleted users sent over, but don't send this anyway
                'confirmed',       // unconfirmed users can't log in to their home site, all remote users considered confirmed
                'password',        // no password for mnet users
                'theme',           // handled separately
                'lastip',          // will be set to relative to the host anyway
            );

            // these are the ones that user_not_fully_set_up will complain about
            // and also special case ones
            $forced = array(
                'username',
                'email',
                'firstname',
                'lastname',
                'auth',
                'wwwroot',
                'session.gc_lifetime',
                '_mnet_userpicture_timemodified',
                '_mnet_userpicture_mimetype',
            );

            // these are the ones we used to send/receive (pre 2.0)
            $legacy = array(
                'username',
                'email',
                'auth',
                'deleted',
                'firstname',
                'lastname',
                'city',
                'country',
                'lang',
                'timezone',
                'description',
                'mailformat',
                'maildigest',
                'maildisplay',
                'htmleditor',
                'wwwroot',
                'picture',
            );

            // get a random user record from the database to pull the fields off
            $randomuser = $DB->get_record('user', array(), '*', IGNORE_MULTIPLE);
            foreach ($randomuser as $key => $discard) {
                if (in_array($key, $excludes) || in_array($key, $forced)) {
                    continue;
                }
                $userfields[$key] = $key;
            }
            if ($userfields) {
                foreach ($userfields as $field) {
                    $customfields[$field] = $field;
                }
                asort($customfields);
            }
            
            $mapping = array();
            $mappings_str = explode("\n", str_replace("\r", '', get_config('enrol_profile', 'mappings')));
            foreach ($mappings_str as $mapping_str) {
                if (preg_match('/^\s*([^: ]+)\s*:\s*([^: ]+)\s*$/', $mapping_str, $matches) && in_array($matches[2], $customfields) && array_key_exists($matches[1], $_SERVER)) {
                    $mapping[$matches[1]] = $matches[2];
                }
            }
            if (count($mapping)) {
                // now update user profile data from Shibboleth params received as part of the event:
                $user = $DB->get_record('user', ['id' => $event->userid], '*', MUST_EXIST);
                foreach ($mapping as $shibattr => $fieldname) {
                    if (isset($_SERVER[$shibattr])) {
                        $propertyname = 'profile_field_' . $fieldname;
                        $user->$propertyname = $_SERVER[$shibattr];
                    }
                }
                require_once($CFG->dirroot . '/user/profile/lib.php');
                profile_save_data($user);
            }
        }
        // last, process the actual enrolments, whether we're using Shibboleth authentication or not:
        self::process_enrolments($event);
    }

    public static function process_enrolments($event = null, $instanceid = null) {
        global $CFG, $DB;
        $nbenrolled = 0;
        $possible_unenrolments = array();

        if ($instanceid) {
            // We're processing one particular instance, making sure it's active
            $enrol_profile_records = $DB->get_records('enrol', array('enrol' => 'profile', 'status' => 0, 'id' => $instanceid));
        }
        else {
            // We're processing all active instances,
            // because a user just logged in
            // OR we're running the cron
            $enrol_profile_records = $DB->get_records('enrol', array('enrol' => 'profile', 'status' => 0));
            if (!is_null($event)) {
                // Let's check if there are any potential unenroling instances
                $userid = (int)$event->userid;
                $possible_unenrolments = $DB->get_records_sql("SELECT id, enrolid FROM {user_enrolments} WHERE userid = ? AND status = 0 AND enrolid IN ( SELECT id FROM {enrol} WHERE enrol = 'profile' AND customint1 = 1 ) ", array($userid));
            }
        }

        // are we to unenrol from anywhere?
        foreach ($possible_unenrolments as $id => $user_enrolment) {

            $unenrol_profile_record = $DB->get_record('enrol', array('enrol' => 'profile', 'status' => 0, 'customint1' => 1, 'id' => $user_enrolment->enrolid));
            if (!$unenrol_profile_record) {
                continue;
            }
            
            $select = 'SELECT DISTINCT u.id FROM {user} u';
            $where = ' WHERE u.id='.$userid.' AND u.deleted=0 AND (';
            $arraysyntax = self::attrsyntax_toarray($unenrol_profile_record->customtext1);
            $arraysql    = self::arraysyntax_tosql($arraysyntax);
            $users = $DB->get_records_sql($select . $arraysql['select'] . $where . $arraysql['where'] .')');

            if (!array_key_exists($userid, $users)) {
                $enrol_profile_instance = new enrol_profile_plugin();
                $enrol_profile_instance->unenrol_user($unenrol_profile_record, (int)$userid);
            }

        }

        // are we to enrol anywhere?
        foreach ($enrol_profile_records as $enrol_profile_record) {
            $rules = json_decode($enrol_profile_record->customtext1)->rules;
            $configured_profilefields = explode(',', get_config('enrol_profile', 'profilefields'));
            foreach ($rules as $rule) {
                if (!isset($rule->param)) {
                    break;
                }
                if (!in_array($rule->param, $configured_profilefields)) {
                    break 2;
                }                               
            }
            $enrol_profile_instance = new enrol_profile_plugin();
            $enrol_profile_instance->name = $enrol_profile_record->name;

            $select = 'SELECT DISTINCT u.id FROM {user} u';
            if ($event) { // called by an event, i.e. user login
                $userid = (int)$event->userid;
                $where = ' WHERE u.id='.$userid;
            }
            else { // called by cron or by construct
                $where = ' WHERE true';
            }

            $where .= ' AND u.deleted = 0 AND suspended = 0 AND (';
            
            $arraysyntax = self::attrsyntax_toarray($enrol_profile_record->customtext1);
            $arraysql    = self::arraysyntax_tosql($arraysyntax);

            $users = $DB->get_records_sql($select . $arraysql['select'] . $where . $arraysql['where'].')');
            foreach ($users as $user) {
                if (is_enrolled(context_course::instance($enrol_profile_record->courseid), $user)) {
                    continue;
                }
                $enrol_profile_instance->enrol_user($enrol_profile_record, $user->id, $enrol_profile_record->roleid);
                $nbenrolled++;
                // Send welcome message.
                if ($enrol_profile_record->customint4) {
                    $userdata = $DB->get_record('user', array('id'=>$user->id));
                    self::email_welcome_message($enrol_profile_record, $userdata);
                }
            }
        }

        if (!$event && !$instanceid) {
            // we only want output if runnning within the cron
            mtrace('enrol_profile : enrolled '.$nbenrolled.' users.');
        }
        return $nbenrolled;
    }


    /*
     *
     */
    public static function purge_instance($instanceid, $context) {
        if (!$instanceid) {
            return false;
        }
        global $DB;
        if (!$DB->delete_records('role_assignments', array('component' => 'enrol_profile', 'itemid' => $instanceid))) {
            return false;
        }
        if (!$DB->delete_records('user_enrolments', array('enrolid'=>$instanceid))) {
            return false;
        }
        $context->mark_dirty();
        return true;
    }


    /**
     * Returns enrolment instance manage link.
     *
     * By defaults looks for manage.php file and tests for manage capability.
     *
     * @param navigation_node $instancesnode
     * @param stdClass $instance
     * @return moodle_url;
     */
    public function add_course_navigation($instancesnode, stdClass $instance) {
        if ($instance->enrol !== 'profile') {
             throw new coding_exception('Invalid enrol instance type!');
        }

        $context = context_course::instance($instance->courseid);
        if (has_capability('enrol/profile:config', $context)) {
            $managelink = new moodle_url('/enrol/profile/edit.php', array('courseid' => $instance->courseid, 'id' => $instance->id));
            $instancesnode->add($this->get_instance_name($instance), $managelink, navigation_node::TYPE_SETTING);
        }
    }
    
    /**
     * Return an array of information about all moodle's profile fields
     * which ones are optional, which ones are forced.
     * This is used as the basis of providing lists of profile fields to the administrator
     * to pick which fields to import/export over MNET
     *
     * @return array(forced => array, optional => array)
     */
    public static function profile_field_options() {
        global $DB;
        static $info;
        if (!empty($info)) {
            return $info;
        }

        $excludes = array(
            'id',              // makes no sense
            'mnethostid',      // makes no sense
            'timecreated',     // will be set to relative to the host anyway
            'timemodified',    // will be set to relative to the host anyway
            'auth',            // going to be set to 'mnet'
            'deleted',         // we should never get deleted users sent over, but don't send this anyway
            'confirmed',       // unconfirmed users can't log in to their home site, all remote users considered confirmed
            'password',        // no password for mnet users
            'theme',           // handled separately
            'lastip',          // will be set to relative to the host anyway
        );

        // these are the ones that user_not_fully_set_up will complain about
        // and also special case ones
        $forced = array(
            'username',
            'email',
            'firstname',
            'lastname',
            'auth',
            'wwwroot',
            'session.gc_lifetime',
            '_mnet_userpicture_timemodified',
            '_mnet_userpicture_mimetype',
        );

        // these are the ones we used to send/receive (pre 2.0)
        $legacy = array(
            'username',
            'email',
            'auth',
            'deleted',
            'firstname',
            'lastname',
            'city',
            'country',
            'lang',
            'timezone',
            'description',
            'mailformat',
            'maildigest',
            'maildisplay',
            'htmleditor',
            'wwwroot',
            'picture',
        );

        // get a random user record from the database to pull the fields off
        $randomuser = $DB->get_record('user', array(), '*', IGNORE_MULTIPLE);
        foreach ($randomuser as $key => $discard) {
            if (in_array($key, $excludes) || in_array($key, $forced)) {
                continue;
            }
            $fields[$key] = $key;
        }
        $info = array(
            'forced'   => $forced,
            'optional' => $fields,
            'legacy'   => $legacy,
        );
        return $info;
    }
    /**
     * Send welcome email to specified user.
     *
     * @param stdClass $instance
     * @param stdClass $user user record
     * @return void
     */
    public static function email_welcome_message($instance, $user) {
        global $CFG, $DB;

        $course = $DB->get_record('course', array('id'=>$instance->courseid), '*', MUST_EXIST);
        $context = context_course::instance($course->id);

        $a = new stdClass();
        $a->coursename = format_string($course->fullname, true, array('context'=>$context));
        $a->profileurl = "$CFG->wwwroot/user/view.php?id=$user->id&course=$course->id";
        $a->courseurl = "$CFG->wwwroot/course/view.php?id=$course->id";

        if (trim($instance->customtext2) !== '') {
            $message = $instance->customtext2;
            $key = array('{$a->coursename}', '{$a->profileurl}', '{$a->fullname}', '{$a->email}');
            $value = array($a->coursename, $a->profileurl, fullname($user), $user->email);
            $message = str_replace($key, $value, $message);
            if (strpos($message, '<') === false) {
                // Plain text only.
                $messagetext = $message;
                $messagehtml = text_to_html($messagetext, null, false, true);
            } else {
                // This is most probably the tag/newline soup known as FORMAT_MOODLE.
                $messagehtml = format_text($message, FORMAT_MOODLE, array('context'=>$context, 'para'=>false, 'newlines'=>true, 'filter'=>true));
                $messagetext = html_to_text($messagehtml);
            }
        } else {
            $messagetext = get_string('welcometocoursetext', 'enrol_profile', $a);
            $messagehtml = text_to_html($messagetext, null, false, true);
        }

        $subject = get_string('welcometocourse', 'enrol_profile', format_string($course->fullname, true, array('context'=>$context)));

        $sendoption = $instance->customint4;

        $contact = self::get_welcome_email_contact($sendoption, $context);

        // Directly emailing welcome message rather than using messaging.
        email_to_user($user, $contact, $subject, $messagetext, $messagehtml);
    }

    /**
     * Get the "from" contact which the email will be sent from.
     *
     * @param int $sendoption send email from constant ENROL_SEND_EMAIL_FROM_*
     * @param $context context where the user will be fetched
     * @return mixed|stdClass the contact user object.
     */
    public static function get_welcome_email_contact($sendoption, $context) {
        global $CFG;

        $contact = null;
        // Send as the first user assigned as the course contact.
        if ($sendoption == ENROL_SEND_EMAIL_FROM_COURSE_CONTACT) {
            $rusers = array();
            if (!empty($CFG->coursecontact)) {
                $croles = explode(',', $CFG->coursecontact);
                list($sort, $sortparams) = users_order_by_sql('u');
                // We only use the first user.
                $i = 0;
                do {
                    $allnames = get_all_user_name_fields(true, 'u');
                    $rusers = get_role_users($croles[$i], $context, true, 'u.id,  u.confirmed, u.username, '. $allnames . ',
                    u.email, r.sortorder, ra.id', 'r.sortorder, ra.id ASC, ' . $sort, null, '', '', '', '', $sortparams);
                    $i++;
                } while (empty($rusers) && !empty($croles[$i]));
            }
            if ($rusers) {
                $contact = array_values($rusers)[0];
            }
        } else if ($sendoption == ENROL_SEND_EMAIL_FROM_KEY_HOLDER) {
            // Send as the first user with enrol/self:holdkey capability assigned in the course.
            list($sort) = users_order_by_sql('u');
            $keyholders = get_users_by_capability($context, 'enrol/self:holdkey', 'u.*', $sort);
            if (!empty($keyholders)) {
                $contact = array_values($keyholders)[0];
            }
        }

        // If send welcome email option is set to no reply or if none of the previous options have
        // returned a contact send welcome message as noreplyuser.
        if ($sendoption == ENROL_SEND_EMAIL_FROM_NOREPLY || empty($contact)) {
            $contact = core_user::get_noreply_user();
        }

        return $contact;
    }
    
    /**
     * Add new instance of enrol plugin with default settings.
     * @param stdClass $course
     * @return int id of new instance
     */
    public function add_default_instance($course) {
        $fields = $this->get_instance_defaults();

        return $this->add_instance($course, $fields);
    }

    /**
     * Returns defaults for new instances.
     * @return array
     */
    public function get_instance_defaults() {
        
        $fields = array();
        
        $fields['customint4']      = $this->get_config('sendcoursewelcomemessage');

        return $fields;
    }
    
    /**
     * Restore instance and map settings.
     *
     * @param restore_enrolments_structure_step $step
     * @param stdClass $data
     * @param stdClass $course
     * @param int $oldid
     */
    public function restore_instance(restore_enrolments_structure_step $step, stdClass $data, $course, $oldid) {
        global $DB;
        
        $instance = $DB->get_record('enrol', array('roleid'=>$data->roleid, 'courseid'=>$course->id, 'enrol'=>'manual'));
        if ($instance) {
            $instanceid = $instance->id;
        } else {
            $instanceid = $this->add_instance($course, (array)$data);
        }
        $step->set_mapping('enrol', $oldid, $instanceid);
   
    }

    /**
     * Restore user enrolment.
     *
     * @param restore_enrolments_structure_step $step
     * @param stdClass $data
     * @param stdClass $instance
     * @param int $oldinstancestatus
     * @param int $userid
     */
    public function restore_user_enrolment(restore_enrolments_structure_step $step, $data, $instance, $userid, $oldinstancestatus) {
        global $DB;

        //Enrol from manul
        $ue = $DB->get_record('user_enrolments', array('enrolid'=>$instance->id, 'userid'=>$userid));
        $enrol = false;
        if ($ue and $ue->status == ENROL_USER_ACTIVE) {
            // We do not want to restrict current active enrolments, let's kind of merge the times only.
            // This prevents some teacher lockouts too.
            if ($data->status == ENROL_USER_ACTIVE) {
                if ($data->timestart > $ue->timestart) {
                    $data->timestart = $ue->timestart;
                    $enrol = true;
                }

                if ($data->timeend == 0) {
                    if ($ue->timeend != 0) {
                        $enrol = true;
                    }
                } else if ($ue->timeend == 0) {
                    $data->timeend = 0;
                } else if ($data->timeend < $ue->timeend) {
                    $data->timeend = $ue->timeend;
                    $enrol = true;
                }
            }
        } else {
            if ($instance->status == ENROL_INSTANCE_ENABLED and $oldinstancestatus != ENROL_INSTANCE_ENABLED) {
                // Make sure that user enrolments are not activated accidentally,
                // we do it only here because it is not expected that enrolments are migrated to other plugins.
                $data->status = ENROL_USER_SUSPENDED;
            }
            $enrol = true;
        }

        if ($enrol) {
            $this->enrol_user($instance, $userid, null, $data->timestart, $data->timeend, $data->status);
        }        
    }

    /**
     * Restore role assignment.
     *
     * @param stdClass $instance
     * @param int $roleid
     * @param int $userid
     * @param int $contextid
     */
    public function restore_role_assignment($instance, $roleid, $userid, $contextid) {
        if ($this->get_config('unenrolaction') == ENROL_EXT_REMOVED_UNENROL or $this->get_config('unenrolaction') == ENROL_EXT_REMOVED_SUSPENDNOROLES) {
            // Role assignments were already synchronised in restore_instance(), we do not want any leftovers.
            return;
        }
        role_assign($roleid, $userid, $contextid, 'enrol_manual', $instance->id);
    }
}