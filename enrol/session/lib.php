<?php
/**
 * Session enrolment plugin.
 *
 * @package    enrol_session
 * @copyright  2015 Click-AP  {@link http://www.click-ap.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Session enrolment plugin implementation.
 * @author Petr Skoda
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class enrol_session_plugin extends enrol_plugin {

    protected $lasternoller = null;
    protected $lasternollerinstanceid = 0;

    /**
     * Returns optional enrolment information icons.
     *
     * This is used in course list for quick overview of enrolment options.
     *
     * We are not using single instance parameter because sometimes
     * we might want to prevent icon repetition when multiple instances
     * of one type exist. One instance may also produce several icons.
     *
     * @param array $instances all enrol instances of this type in one course
     * @return array of pix_icon
     */
    public function get_info_icons(array $instances) {
        $key = false;
        $nokey = false;
        foreach ($instances as $instance) {
            if ($this->can_self_enrol($instance, false) !== true) {
                // User can not enrol himself.
                // Note that we do not check here if user is already enrolled for performance reasons -
                // such check would execute extra queries for each course in the list of courses and
                // would hide self-enrolment icons from guests.
                continue;
            }
            if ($instance->password or $instance->customint1) {
                $key = true;
            } else {
                $nokey = true;
            }
        }
        $icons = array();
        if ($nokey) {
            $icons[] = new pix_icon('withoutkey', get_string('pluginname', 'enrol_session'), 'enrol_session');
        }
        if ($key) {
            $icons[] = new pix_icon('withkey', get_string('pluginname', 'enrol_session'), 'enrol_session');
        }
        return $icons;
    }

    /**
     * Returns localised name of enrol instance
     *
     * @param stdClass $instance (null is accepted too)
     * @return string
     */
    public function get_instance_name($instance) {
        global $DB;

        if (empty($instance->name)) {
            if (!empty($instance->roleid) and $role = $DB->get_record('role', array('id'=>$instance->roleid))) {
                $role = ' (' . role_get_name($role, context_course::instance($instance->courseid, IGNORE_MISSING)) . ')';
            } else {
                $role = '';
            }
            $enrol = $this->get_name();
            return get_string('pluginname', 'enrol_'.$enrol) . $role;
        } else {
            return format_string($instance->name);
        }
    }

    public function roles_protected() {
        // Users may tweak the roles later.
        return false;
    }

    public function allow_unenrol(stdClass $instance) {
        // Users with unenrol cap may unenrol other users manually manually.
        return true;
    }

    public function allow_manage(stdClass $instance) {
        // Users with manage cap may tweak period and status.
        return true;
    }

    public function show_enrolme_link(stdClass $instance) {

        if (true !== $this->can_self_enrol($instance, false)) {
            return false;
        }

        return true;
    }

    /**
     * Is it possible to hide/show enrol instance via standard UI?
     *
     * @param stdClass $instance
     * @return bool
     */
    public function can_hide_show_instance($instance) {
        $context = context_course::instance($instance->courseid);

        if (!has_capability('enrol/session:config', $context)) {
            return false;
        }

        // If the instance is currently disabled, before it can be enabled,
        // we must check whether the password meets the password policies.
        if ($instance->status == ENROL_INSTANCE_DISABLED) {
            if ($this->get_config('requirepassword')) {
                if (empty($instance->password)) {
                    return false;
                }
            }
            // Only check the password if it is set.
            if (!empty($instance->password) && $this->get_config('usepasswordpolicy')) {
                if (!check_password_policy($instance->password, $errmsg)) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Sets up navigation entries.
     *
     * @param stdClass $instancesnode
     * @param stdClass $instance
     * @return void
     */
    public function add_course_navigation($instancesnode, stdClass $instance) {
        if ($instance->enrol !== 'session') {
             throw new coding_exception('Invalid enrol instance type!');
        }

        $context = context_course::instance($instance->courseid);
        if (has_capability('enrol/session:config', $context)) { // enrol/self:config
            $managelink = new moodle_url('/enrol/session/edit.php', array('courseid'=>$instance->courseid, 'id'=>$instance->id));
            $instancesnode->add($this->get_instance_name($instance), $managelink, navigation_node::TYPE_SETTING);
        }
    }

    /**
     * Returns edit icons for the page with list of instances
     * @param stdClass $instance
     * @return array
     */
    public function get_action_icons(stdClass $instance) {
        global $OUTPUT;

        if ($instance->enrol !== 'session') {
            throw new coding_exception('invalid enrol instance!');
        }
        $context = context_course::instance($instance->courseid);

        $icons = array();

        if (has_capability('enrol/session:config', $context)) { // enrol/self:config
            $editlink = new moodle_url("/enrol/session/edit.php", array('courseid'=>$instance->courseid, 'id'=>$instance->id));
            $icons[] = $OUTPUT->action_icon($editlink, new pix_icon('t/edit', get_string('edit'), 'core',
                array('class' => 'iconsmall')));
        }

        return $icons;
    }

    /**
     * Returns link to page which may be used to add new instance of enrolment plugin in course.
     * @param int $courseid
     * @return moodle_url page url
     */
    public function get_newinstance_link($courseid) {
        $context = context_course::instance($courseid, MUST_EXIST);

        if (!has_capability('moodle/course:enrolconfig', $context) or !has_capability('enrol/self:config', $context)) {
            return NULL;
        }
        // Multiple instances supported - different roles with different password.
        return new moodle_url('/enrol/session/edit.php', array('courseid'=>$courseid));
    }

    /**
     * Session enrol user to course
     *
     * @param stdClass $instance enrolment instance
     * @param stdClass $data data needed for enrolment.
     * @return bool|array true if enroled else eddor code and messege
     */
    public function enrol_session(stdClass $instance, $data = null) {
        global $DB, $USER, $CFG;

        // Don't enrol user if password is not passed when required.
        if ($instance->password && !isset($data->enrolpassword)) {
            return;
        }

        $timestart = time();
        if ($instance->enrolperiod) {
            $timeend = $timestart + $instance->enrolperiod;
        } else {
            $timeend = 0;
        }

        $this->enrol_user($instance, $USER->id, $instance->roleid, $timestart, $timeend);

        if ($instance->password and $instance->customint1 and $data->enrolpassword !== $instance->password) {
            // It must be a group enrolment, let's assign group too.
            $groups = $DB->get_records('groups', array('courseid'=>$instance->courseid), 'id', 'id, enrolmentkey');
            foreach ($groups as $group) {
                if (empty($group->enrolmentkey)) {
                    continue;
                }
                if ($group->enrolmentkey === $data->enrolpassword) {
                    // Add user to group.
                    require_once($CFG->dirroot.'/group/lib.php');
                    groups_add_member($group->id, $USER->id);
                    break;
                }
            }
        }
        // Send welcome message.
        if ($instance->customint4) {
            $this->email_welcome_message($instance, $USER);
        }
    }

    /**
     * Creates course enrol form, checks if form submitted
     * and enrols user if necessary. It can also redirect.
     *
     * @param stdClass $instance
     * @return string html text, usually a form in a text box
     */
    public function enrol_page_hook(stdClass $instance) {
        global $CFG, $OUTPUT, $USER, $DB;

        require_once("$CFG->dirroot/enrol/session/locallib.php");
        
        $enrolstatus = $this->can_self_enrol($instance);
        
        $data = new stdClass();
        $data->header = $this->get_instance_name($instance);
        $data->info = $enrolstatus;
        $url = isguestuser() ? get_login_url() : null;
        $form = new enrol_session_empty_form($url, $data);
        ob_start();
        $form->display();
        $oboutput = ob_get_clean();
        $output = $OUTPUT->box($oboutput);
        if($enrolstatus !== true){
            $output .= $enrolstatus;
        }

        $table = new html_table();
        $table->attributes['class'] = 'generaltable simple';
        $table->width = '100%';
        $table->attributes = array('style'=>'display: table;', 'class'=>'');
        $table->align = array('center', 'left', 'left', 'center');
        $table->size = array('15%','40%','30%','15%');
        $action = '';

        $class_data = $class_date = '';
        if(!empty($instance->enrolstartdate)){
            $class_data .= get_string('enrolstartdate', 'enrol_session').':'.date('Y/m/d H:i:s',$instance->enrolstartdate).'<br />';
        }
        if(!empty($instance->enrolenddate)){
            $class_data .=get_string('enrolenddate', 'enrol_session').':'.date('Y/m/d H:i:s',$instance->enrolenddate).'<br />';
        }
        if($instance->customint3 == 0){
            $class_data .=get_string('maxenrolled', 'enrol_session').':'.get_string('notlimit','enrol_session').'<br />';
        }else{
            $class_data .=get_string('maxenrolled', 'enrol_session').':'.$instance->customint3.'<br />';
        }
        if($session = $DB->get_record('enrol_session', array('instanceid'=>$instance->id))){
            //$class_data .=get_string('sessiondate', 'enrol_session').':'.$session->sessdate.'<br />';
            //$class_data .=get_string('sessionenddate', 'enrol_session').':'.$session->sessenddate.'<br />';

            $dhours = floor($session->duration / HOURSECS);
            $dmins = floor(($session->duration - $dhours * HOURSECS) / MINSECS);
            $class_data .=get_string('duration', 'enrol_session').':'.$dhours.get_string('hour').$dmins.get_string('min').'<br />';
            //$class_data .=get_string('sessiondays', 'enrol_session').':'.$session->sdays.'<br />';

            $sessions = $this->enrol_session_construct_sessions_data_for_add($session);
            if(!empty($sessions)){
                $class_date .= get_string('sessiondate', 'enrol_session').':<br />';
                foreach($sessions as $sess){
                    $class_date .= date('Y/m/d H:i:s',$sess).'<br />';                                              
                }
            }else {
                $class_date .= get_string('sessiondate', 'enrol_session').':<br />';
                $class_date .= date('Y/m/d H:i:s',$session->sessdate);
            }
        }

        if(true === $enrolstatus){
            $str_button = get_string('enrolme', 'enrol_session');
            $params = array('instance'=>$instance->id);
            $url = new moodle_url('/enrol/session/enrolself.php', $params);
            $action = html_writer::empty_tag('input', array('type'=>'button', 'class'=>'form-submit', 'id'=>'btn', 'onclick'=>'javascript:location.href="'.$url.'"', 'value'=>$str_button));
        }
        $data = array($instance->name, html_writer::tag('span',$class_data),html_writer::tag('span',$class_date), $action);
        $table->data[] = $data;

        $output .= $OUTPUT->box(html_writer::table($table));
        return $output;
    }
    
    function enrol_session_construct_sessions_data_for_add($data) {
        global $CFG;

        $sessions = array();
        if (isset($data->addmultiply)) {
           
            foreach(explode(',',$data->sdays) as $key=>$val){
                $weeks[$val]=1;
            }
            
            $startdate = $data->sessdate;
            $starttime = $startdate - usergetmidnight($startdate);
            $enddate = $data->sessenddate + DAYSECS; // Because enddate in 0:0am.

            if ($enddate < $startdate) {
                return null;
            }

            $days = (int)ceil(($enddate - $startdate) / DAYSECS);

            // Getting first day of week.
            $sdate = $startdate;
            $dinfo = usergetdate($sdate);
            if ($CFG->calendar_startwday === '0') { // Week start from sunday.
                $startweek = $startdate - $dinfo['wday'] * DAYSECS; // Call new variable.
            } else {
                $wday = $dinfo['wday'] === 0 ? 7 : $dinfo['wday'];
                $startweek = $startdate - ($wday-1) * DAYSECS;
            }

            $wdaydesc = array(0=>'Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat');

            while ($sdate < $enddate) {
                if ($sdate < $startweek + WEEKSECS) {
                    $dinfo = usergetdate($sdate);

                    if (isset($weeks) && array_key_exists($wdaydesc[$dinfo['wday']], $weeks)) {
                        $sessions[] =  usergetmidnight($sdate) + $starttime;
                    }
                    $sdate += DAYSECS;
                } else {
                    $startweek += WEEKSECS * $data->period;
                    $sdate = $startweek;
                }
            }
        }

        return $sessions;
    }
    
    
    /**
     * Checks if user can self enrol.
     *
     * @param stdClass $instance enrolment instance
     * @param bool $checkuserenrolment if true will check if user enrolment is inactive.
     *             used by navigation to improve performance.
     * @return bool|string true if successful, else error message or false.
     */
    public function can_self_enrol(stdClass $instance, $checkuserenrolment = true) {
        global $CFG, $DB, $OUTPUT, $USER, $COURSE;

        $context = context_course::instance($COURSE->id, MUST_EXIST);
        if (is_enrolled($context)) {
            return $OUTPUT->box(get_string('enrolled', 'enrol_session'), 'notifyproblem');
        }

        if ($checkuserenrolment) {
            if (isguestuser()) {
                // Can not enrol guest.
                return $OUTPUT->box(get_string('noguestaccess', 'enrol') . $OUTPUT->continue_button(get_login_url()), 'notifyproblem');
            }
            // Check if user is already enroled.
            if ($DB->get_record('user_enrolments', array('userid' => $USER->id, 'enrolid' => $instance->id))) {
                //return get_string('canntenrol', 'enrol_session');
                return $OUTPUT->box(get_string('canntenrol', 'enrol_session'), 'notifyproblem');
            }
        }

        if ($instance->status != ENROL_INSTANCE_ENABLED) {
            return $OUTPUT->box(get_string('canntenrol', 'enrol_session'), 'notifyproblem');
        }

        if (isset($COURSE->enrolstartdate)
            AND ($COURSE->enrolstartdate > time() OR $COURSE->enrolenddate < time())) {
            $enroldate = new stdClass();
            $enroldate->start = userdate($COURSE->enrolstartdate);
            $enroldate->end = userdate($COURSE->enrolenddate);
            return $OUTPUT->box(get_string('canntenrolearly_bycourse', 'enrol_session', $enroldate), 'notifyproblem');
        }

        if ($instance->enrolstartdate != 0 and $instance->enrolstartdate > time()) {
            return $OUTPUT->box(get_string('canntenrol', 'enrol_session'), 'notifyproblem');
        }

        if ($instance->enrolenddate != 0 and $instance->enrolenddate < time()) {
            return $OUTPUT->box(get_string('canntenrol', 'enrol_session'), 'notifyproblem');
        }

        if (!$instance->customint6) {
            // New enrols not allowed.
            return $OUTPUT->box(get_string('canntenrol', 'enrol_session'), 'notifyproblem');
        }

        if ($DB->record_exists('user_enrolments', array('userid' => $USER->id, 'enrolid' => $instance->id))) {
            return $OUTPUT->box(get_string('canntenrol', 'enrol_session'), 'notifyproblem');
        }

        if ($instance->customint3 > 0) {
            // Max enrol limit specified.
            $count = $DB->count_records('user_enrolments', array('enrolid' => $instance->id));
            if ($count >= $instance->customint3) {
                // Bad luck, no more self enrolments here.
                return $OUTPUT->box(get_string('maxenrolledreached', 'enrol_session'), 'notifyproblem');
            }
        }

        if ($instance->customint5) {
            require_once("$CFG->dirroot/cohort/lib.php");
            if (!cohort_is_member($instance->customint5, $USER->id)) {
                $cohort = $DB->get_record('cohort', array('id' => $instance->customint5));
                if (!$cohort) {
                    return null;
                }
                $a = format_string($cohort->name, true, array('context' => context::instance_by_id($cohort->contextid)));
                return markdown_to_html(get_string('cohortnonmemberinfo', 'enrol_session', $a));
            }
        }

        return true;
    }

    /**
     * Return information for enrolment instance containing list of parameters required
     * for enrolment, name of enrolment plugin etc.
     *
     * @param stdClass $instance enrolment instance
     * @return stdClass instance info.
     */
    public function get_enrol_info(stdClass $instance) {

        $instanceinfo = new stdClass();
        $instanceinfo->id = $instance->id;
        $instanceinfo->courseid = $instance->courseid;
        $instanceinfo->type = $this->get_name();
        $instanceinfo->name = $this->get_instance_name($instance);
        $instanceinfo->status = $this->can_self_enrol($instance);

        if ($instance->password) {
            $instanceinfo->requiredparam = new stdClass();
            $instanceinfo->requiredparam->enrolpassword = get_string('password', 'enrol_session');
        }

        // If enrolment is possible and password is required then return ws function name to get more information.
        if ((true === $instanceinfo->status) && $instance->password) {
            $instanceinfo->wsfunction = 'enrol_session_get_instance_info';
        }
        return $instanceinfo;
    }

    /**
     * Add new instance of enrol plugin with default settings.
     * @param stdClass $course
     * @return int id of new instance
     */
    public function add_default_instance($course) {
        $fields = $this->get_instance_defaults();

        if ($this->get_config('requirepassword')) {
            $fields['password'] = generate_password(20);
        }

        return $this->add_instance($course, $fields);
    }

    /**
     * Returns defaults for new instances.
     * @return array
     */
    public function get_instance_defaults() {
        $expirynotify = $this->get_config('expirynotify');
        if ($expirynotify == 2) {
            $expirynotify = 1;
            $notifyall = 1;
        } else {
            $notifyall = 0;
        }

        $fields = array();
        $fields['status']          = $this->get_config('status');
        $fields['roleid']          = $this->get_config('roleid');
        $fields['enrolperiod']     = $this->get_config('enrolperiod');
        $fields['expirynotify']    = $expirynotify;
        $fields['notifyall']       = $notifyall;
        $fields['expirythreshold'] = $this->get_config('expirythreshold');
        $fields['customint1']      = $this->get_config('groupkey');
        $fields['customint2']      = $this->get_config('longtimenosee');
        $fields['customint3']      = $this->get_config('maxenrolled');
        $fields['customint4']      = $this->get_config('sendcoursewelcomemessage');
        $fields['customint5']      = 0;
        $fields['customint6']      = $this->get_config('newenrols');

        return $fields;
    }

    /**
     * Send welcome email to specified user.
     *
     * @param stdClass $instance
     * @param stdClass $user user record
     * @return void
     */
    function email_welcome_message($instance, $user) {
        global $CFG, $DB;

        $course = $DB->get_record('course', array('id'=>$instance->courseid), '*', MUST_EXIST);
        $context = context_course::instance($course->id);

        $a = new stdClass();
        $a->coursename = format_string($course->fullname, true, array('context'=>$context));
        $a->profileurl = "$CFG->wwwroot/user/view.php?id=$user->id&course=$course->id";

        if (trim($instance->customtext1) !== '') {
            $message = $instance->customtext1;
            $key = array('{$a->coursename}', '{$a->profileurl}');
            $value = array($a->coursename, $a->profileurl);
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
            $messagetext = get_string('welcometocoursetext', 'enrol_session', $a);
            $messagehtml = text_to_html($messagetext, null, false, true);
        }

        $subject = get_string('welcometocourse', 'enrol_session', format_string($course->fullname, true, array('context'=>$context)));

        $sendoption = $instance->customint4;
        $contact = $this->get_welcome_email_contact($sendoption, $context);

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
    public function get_welcome_email_contact($sendoption, $context) {
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
            // Send as the first user with enrol/waiting:holdkey capability assigned in the course.
            list($sort) = users_order_by_sql('u');
            $keyholders = get_users_by_capability($context, 'enrol/waiting:holdkey', 'u.*', $sort);
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
     * Enrol self cron support.
     * @return void
     */
    public function cron() {
        $trace = new text_progress_trace();
        $this->sync($trace, null);
        $this->send_expiry_notifications($trace);
    }

    /**
     * Sync all meta course links.
     *
     * @param progress_trace $trace
     * @param int $courseid one course, empty mean all
     * @return int 0 means ok, 1 means error, 2 means plugin disabled
     */
    public function sync(progress_trace $trace, $courseid = null) {
        global $DB;

        if (!enrol_is_enabled('session')) {
            $trace->finished();
            return 2;
        }

        // Unfortunately this may take a long time, execution can be interrupted safely here.
        core_php_time_limit::raise();
        raise_memory_limit(MEMORY_HUGE);

        $trace->output('Verifying session-enrolments...');

        $params = array('now'=>time(), 'useractive'=>ENROL_USER_ACTIVE, 'courselevel'=>CONTEXT_COURSE);
        $coursesql = "";
        if ($courseid) {
            $coursesql = "AND e.courseid = :courseid";
            $params['courseid'] = $courseid;
        }

        // Note: the logic of self enrolment guarantees that user logged in at least once (=== u.lastaccess set)
        //       and that user accessed course at least once too (=== user_lastaccess record exists).

        // First deal with users that did not log in for a really long time - they do not have user_lastaccess records.
        $sql = "SELECT e.*, ue.userid
                  FROM {user_enrolments} ue
                  JOIN {enrol} e ON (e.id = ue.enrolid AND e.enrol = 'session' AND e.customint2 > 0)
                  JOIN {user} u ON u.id = ue.userid
                 WHERE :now - u.lastaccess > e.customint2
                       $coursesql";
        $rs = $DB->get_recordset_sql($sql, $params);
        foreach ($rs as $instance) {
            $userid = $instance->userid;
            unset($instance->userid);
            $this->unenrol_user($instance, $userid);
            $days = $instance->customint2 / 60*60*24;
            $trace->output("unenrolling user $userid from course $instance->courseid as they have did not log in for at least $days days", 1);
        }
        $rs->close();

        // Now unenrol from course user did not visit for a long time.
        $sql = "SELECT e.*, ue.userid
                  FROM {user_enrolments} ue
                  JOIN {enrol} e ON (e.id = ue.enrolid AND e.enrol = 'session' AND e.customint2 > 0)
                  JOIN {user_lastaccess} ul ON (ul.userid = ue.userid AND ul.courseid = e.courseid)
                 WHERE :now - ul.timeaccess > e.customint2
                       $coursesql";
        $rs = $DB->get_recordset_sql($sql, $params);
        foreach ($rs as $instance) {
            $userid = $instance->userid;
            unset($instance->userid);
            $this->unenrol_user($instance, $userid);
                $days = $instance->customint2 / 60*60*24;
            $trace->output("unenrolling user $userid from course $instance->courseid as they have did not access course for at least $days days", 1);
        }
        $rs->close();

        $trace->output('...user session-enrolment updates finished.');
        $trace->finished();

        $this->process_expirations($trace, $courseid);

        return 0;
    }

    /**
     * Returns the user who is responsible for self enrolments in given instance.
     *
     * Usually it is the first editing teacher - the person with "highest authority"
     * as defined by sort_by_roleassignment_authority() having 'enrol/self:manage'
     * capability.
     *
     * @param int $instanceid enrolment instance id
     * @return stdClass user record
     */
    protected function get_enroller($instanceid) {
        global $DB;

        if ($this->lasternollerinstanceid == $instanceid and $this->lasternoller) {
            return $this->lasternoller;
        }

        $instance = $DB->get_record('enrol', array('id'=>$instanceid, 'enrol'=>$this->get_name()), '*', MUST_EXIST);
        $context = context_course::instance($instance->courseid);

        if ($users = get_enrolled_users($context, 'enrol/self:manage')) {
            $users = sort_by_roleassignment_authority($users, $context);
            $this->lasternoller = reset($users);
            unset($users);
        } else {
            $this->lasternoller = parent::get_enroller($instanceid);
        }

        $this->lasternollerinstanceid = $instanceid;

        return $this->lasternoller;
    }

    /**
     * Gets an array of the user enrolment actions.
     *
     * @param course_enrolment_manager $manager
     * @param stdClass $ue A user enrolment object
     * @return array An array of user_enrolment_actions
     */
    public function get_user_enrolment_actions(course_enrolment_manager $manager, $ue) {
        $actions = array();
        $context = $manager->get_context();
        $instance = $ue->enrolmentinstance;
        $params = $manager->get_moodlepage()->url->params();
        $params['ue'] = $ue->id;
        if ($this->allow_unenrol($instance) && has_capability("enrol/self:unenrol", $context)) {
            $url = new moodle_url('/enrol/unenroluser.php', $params);
            $actions[] = new user_enrolment_action(new pix_icon('t/delete', ''), get_string('unenrol', 'enrol'), $url, array('class'=>'unenrollink', 'rel'=>$ue->id));
        }
        if ($this->allow_manage($instance) && has_capability("enrol/self:manage", $context)) {
            $url = new moodle_url('/enrol/editenrolment.php', $params);
            $actions[] = new user_enrolment_action(new pix_icon('t/edit', ''), get_string('edit'), $url, array('class'=>'editenrollink', 'rel'=>$ue->id));
        }
        return $actions;
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
        if ($step->get_task()->get_target() == backup::TARGET_NEW_COURSE) {
            $merge = false;
        } else {
            $merge = array(
                'courseid'   => $data->courseid,
                'enrol'      => $this->get_name(),
                'roleid'     => $data->roleid,
            );
        }
        if ($merge and $instances = $DB->get_records('enrol', $merge, 'id')) {
            $instance = reset($instances);
            $instanceid = $instance->id;
        } else {
            if (!empty($data->customint5)) {
                if ($step->get_task()->is_samesite()) {
                    // Keep cohort restriction unchanged - we are on the same site.
                } else {
                    // Use some id that can not exist in order to prevent self enrolment,
                    // because we do not know what cohort it is in this site.
                    $data->customint5 = -1;
                }
            }
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
        $this->enrol_user($instance, $userid, null, $data->timestart, $data->timeend, $data->status);
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
        // This is necessary only because we may migrate other types to this instance,
        // we do not use component in manual or self enrol.
        role_assign($roleid, $userid, $contextid, '', 0);
    }
}
