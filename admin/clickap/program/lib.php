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

/* Include required award criteria library. */
require_once($CFG->dirroot . '/admin/clickap/program/criteria/award_criteria.php');
require_once($CFG->libdir . '/filelib.php');
/*
 * Number of records per page.
*/
define('PROGRAM_PERPAGE', 50);

/*
 * Program award criteria aggregation method.
 */
define('PROGRAM_CRITERIA_AGGREGATION_ALL', 1);

/*
 * Program award criteria aggregation method.
 */
define('PROGRAM_CRITERIA_AGGREGATION_ANY', 2);

/*
 * Inactive program means that this program cannot be earned and has not been awarded
 * yet. Its award criteria can be changed.
 */
define('PROGRAM_STATUS_INACTIVE', 0);

/*
 * Active program means that this program can we earned, but it has not been awarded
 * yet. Can be deactivated for the purpose of changing its criteria.
 */
define('PROGRAM_STATUS_ACTIVE', 1);

/*
 * Inactive program can no longer be earned, but it has been awarded in the past and
 * therefore its criteria cannot be changed.
 */
define('PROGRAM_STATUS_INACTIVE_LOCKED', 2);

/*
 * Active program means that it can be earned and has already been awarded to users.
 * Its criteria cannot be changed any more.
 */
define('PROGRAM_STATUS_ACTIVE_LOCKED', 3);

/*
 * Archived program is considered deleted and can no longer be earned and is not
 * displayed in the list of all programs.
 */
define('PROGRAM_STATUS_ARCHIVED', 4);

/*
 * Program type for site programs.
 */
define('PROGRAM_TYPE_SITE', 1);

/*
 * Program type for course programs.
 */
define('PROGRAM_TYPE_COURSE', 2);

/*
 * Program messaging schedule options.
 */
define('PROGRAM_MESSAGE_NEVER', 0);
define('PROGRAM_MESSAGE_ALWAYS', 1);
define('PROGRAM_MESSAGE_DAILY', 2);
define('PROGRAM_MESSAGE_WEEKLY', 3);
define('PROGRAM_MESSAGE_MONTHLY', 4);

/**
 * Class that represents program.
 *
 */
class program {
    /** @var int Program id */
    public $id;

    /** Values from the table 'program' */
    public $name;
    public $description;
    public $timecreated;
    public $timemodified;
    public $usercreated;
    public $usermodified;
    //public $issuername;
    public $issuerurl;
    //public $issuercontact;
    public $expiredate;
    public $expireperiod;
    public $type;
    public $courseid;
    public $message;
    public $messagesubject;
    public $attachment;
    public $notification;
    public $status = 0;
    public $borderstyle = 0;
    public $nextcron;

    /** @var array Program criteria */
    public $criteria = array();

    /**
     * Constructs with program details.
     *
     * @param int $programid program ID.
     */
    public function __construct($programid) {
        global $DB;
        $this->id = $programid;

        $data = $DB->get_record('program', array('id' => $programid));

        if (empty($data)) {
            print_error('error:nosuchprogram', 'clickap_program', $programid);
        }

        foreach ((array)$data as $field => $value) {
            if (property_exists($this, $field)) {
                $this->{$field} = $value;
            }
        }

        $this->criteria = self::get_criteria();
    }

    /**
     * Use to get context instance of a program.
     * @return context instance.
     */
    public function get_context() {
        if ($this->type == PROGRAM_TYPE_SITE) {
            return context_system::instance();
        } else {
            debugging('Something is wrong...');
        }
    }

    /**
     * Return array of aggregation methods
     * @return array
     */
    public static function get_aggregation_methods() {
        return array(
                PROGRAM_CRITERIA_AGGREGATION_ALL => get_string('all', 'clickap_program'),
                PROGRAM_CRITERIA_AGGREGATION_ANY => get_string('any', 'clickap_program'),
        );
    }

    /**
     * Return array of accepted criteria types for this program
     * @return array
     */
    public function get_accepted_criteria() {
        $criteriatypes = array();

        if ($this->type == 1) {
            $criteriatypes = array(
                    PROGRAM_CRITERIA_TYPE_OVERALL,
                    //PROGRAM_CRITERIA_TYPE_MANUAL,
                    PROGRAM_CRITERIA_TYPE_COURSESET,
                    //PROGRAM_CRITERIA_TYPE_PROFILE,
            );
        }

        return $criteriatypes;
    }

    /**
     * Save/update program information in 'program' table only.
     * Cannot be used for updating awards and criteria settings.
     *
     * @return bool Returns true on success.
     */
    public function save() {
        global $DB;

        $fordb = new stdClass();
        foreach (get_object_vars($this) as $k => $v) {
            $fordb->{$k} = $v;
        }
        unset($fordb->criteria);

        $fordb->timemodified = time();
        
        $imageoptions = program_image_options();
        $fordb = file_postupdate_standard_filemanager($fordb, 'medal', $imageoptions, context_system::instance(), 'clickap_program', 'medal', $fordb->id);
        //$fordb = file_postupdate_standard_filemanager($fordb, 'banner', $imageoptions, context_system::instance(), 'clickap_program', 'banner', $fordb->id);
        $fordb = file_postupdate_standard_filemanager($fordb, 'award', $imageoptions, context_system::instance(), 'clickap_program', 'award', $fordb->id);
        
        if ($DB->update_record('program', $fordb)) {
            return true;
        } else {
            throw new moodle_exception('error:save', 'clickap_program');
            return false;
        }
    }

    /**
     * Creates and saves a clone of program with all its properties.
     * Clone is not active by default and has 'Copy of' attached to its name.
     *
     * @return int ID of new program.
     */
    public function make_clone() {
        global $DB, $USER;

        $fordb = new stdClass();
        foreach (get_object_vars($this) as $k => $v) {
            $fordb->{$k} = $v;
        }

        $fordb->name = get_string('copyof', 'clickap_program', $this->name);
        $fordb->status = PROGRAM_STATUS_INACTIVE;
        $fordb->usercreated = $USER->id;
        $fordb->usermodified = $USER->id;
        $fordb->timecreated = time();
        $fordb->timemodified = time();
        unset($fordb->id);

        if ($fordb->notification > 1) {
            $fordb->nextcron = program_calculate_message_schedule($fordb->notification);
        }

        $criteria = $fordb->criteria;
        unset($fordb->criteria);

        if ($new = $DB->insert_record('program', $fordb, true)) {
            $newprogram = new program($new);

            // Copy program image.
            $fs = get_file_storage();
            if ($file = $fs->get_file($this->get_context()->id, 'program', 'medal', $this->id, '/', 'f1.png')) {
                if ($imagefile = $file->copy_content_to_temp()) {
                    programs_process_program_image($newprogram, $imagefile);
                }
            }

            // Copy program criteria.
            foreach ($this->criteria as $crit) {
                $crit->make_clone($new);
            }

            // Copy category
            if($categories = $DB->get_records('program_category', array('programid'=>$this->id))){
                foreach($categories as $category){
                    $oldcategoryid = $category->id;
                    $category->programid = $newprogram->id;
                    $category->timemodified = time();
                    $category->usermodified = $USER->id;
                    unset($category->id);
                    $newcategoryid = $DB->insert_record('program_category', $category, true);
                    if($courses = $DB->get_records('program_category_courses', array('programid'=>$this->id,'categoryid'=>$oldcategoryid))){
                        foreach($courses as $course){
                            $course->programid = $newprogram->id;
                            $course->categoryid = $newcategoryid;
                            $course->timemodified = time();
                            $course->usermodified = $USER->id;
                            unset($course->id);
                            $DB->insert_record('program_category_courses', $course, true);
                        }
                    }
                }
            }
            
            // Copy Category courses
            
            
            return $new;
        } else {
            throw new moodle_exception('error:clone', 'program');
            return false;
        }
    }

    /**
     * Checks if programs is active.
     * Used in program award.
     *
     * @return bool A status indicating program is active
     */
    public function is_active() {
        if (($this->status == PROGRAM_STATUS_ACTIVE) ||
            ($this->status == PROGRAM_STATUS_ACTIVE_LOCKED)) {
            return true;
        }
        return false;
    }

    /**
     * Use to get the name of program status.
     *
     */
    public function get_status_name() {
        return get_string('programstatus_' . $this->status, 'clickap_program');
    }

    /**
     * Use to set program status.
     * Only active programs can be earned/awarded/issued.
     *
     * @param int $status Status from PROGRAM_STATUS constants
     */
    public function set_status($status = 0) {
        $this->status = $status;
        $this->save();
    }

    /**
     * Checks if programs is locked.
     * Used in program award and editing.
     *
     * @return bool A status indicating program is locked
     */
    public function is_locked() {
        if (($this->status == PROGRAM_STATUS_ACTIVE_LOCKED) ||
                ($this->status == PROGRAM_STATUS_INACTIVE_LOCKED)) {
            return true;
        }
        return false;
    }

    /**
     * Checks if program has been awarded to users.
     * Used in program editing.
     *
     * @return bool A status indicating program has been awarded at least once
     */
    public function has_awards() {
        global $DB;
        $awarded = $DB->record_exists_sql('SELECT b.uniquehash
                    FROM {program_issued} b INNER JOIN {user} u ON b.userid = u.id
                    WHERE b.programid = :programid AND u.deleted = 0', array('programid' => $this->id));

        return $awarded;
    }

    /**
     * Gets list of users who have earned an instance of this program.
     *
     * @return array An array of objects with information about program awards.
     */
    public function get_awards() {
        global $DB;

        $awards = $DB->get_records_sql(
                'SELECT b.userid, b.dateissued, b.uniquehash, u.firstname, u.lastname
                    FROM {program_issued} b INNER JOIN {user} u
                        ON b.userid = u.id
                    WHERE b.programid = :programid AND u.deleted = 0', array('programid' => $this->id));

        return $awards;
    }

    /**
     * Indicates whether program has already been issued to a user.
     *
     */
    public function is_issued($userid) {
        global $DB;
        return $DB->record_exists('program_issued', array('programid' => $this->id, 'userid' => $userid));
    }

    /**
     * Issue a program to user.
     *
     * @param int $userid User who earned the program
     * @param bool $nobake Not baking actual programs (for testing purposes)
     */
    public function issue($userid, $nobake = false) {
        global $DB, $CFG;

        $now = time();
        $issued = new stdClass();
        $issued->programid = $this->id;
        $issued->userid = $userid;
        $issued->uniquehash = sha1(rand() . $userid . $this->id . $now);
        $issued->dateissued = $now;

        if ($this->can_expire()) {
            $issued->dateexpire = $this->calculate_expiry($now);
        } else {
            $issued->dateexpire = null;
        }

        // Take into account user programs privacy settings.
        // If none set, programs default visibility is set to public.
        $issued->visible = get_user_preferences('programprivacysetting', 1, $userid);

        $result = $DB->insert_record('program_issued', $issued, true);

        if ($result) {
            // Trigger program awarded event.
            $eventdata = array (
                'context' => $this->get_context(),
                'objectid' => $this->id,
                'relateduserid' => $userid,
                'other' => array('dateexpire' => $issued->dateexpire, 'programissuedid' => $result)
            );
            \clickap_program\event\program_awarded::create($eventdata)->trigger();

            // Lock the program, so that its criteria could not be changed any more.
            if ($this->status == PROGRAM_STATUS_ACTIVE) {
                $this->set_status(PROGRAM_STATUS_ACTIVE_LOCKED);
            }

            // Update details in criteria_met table.
            $compl = $this->get_criteria_completions($userid);
            foreach ($compl as $c) {
                $obj = new stdClass();
                $obj->id = $c->id;
                $obj->issuedid = $result;
                $DB->update_record('program_criteria_met', $obj, true);
            }

            if (!$nobake) {
                // Bake a program image.
                /* mary
                $pathhash = programs_bake($issued->uniquehash, $this->id, $userid, true);
                
                // Notify recipients and program creators.
                programs_notify_program_award($this, $userid, $issued->uniquehash, $pathhash);*/
                programs_notify_program_award($this, $userid, $issued->uniquehash);
            }
        }
    }

    /**
     * Reviews all program criteria and checks if program can be instantly awarded.
     *
     * @return int Number of awards
     */
    public function review_all_criteria() {
        global $DB, $CFG;
        $awards = 0;

        // Raise timelimit as this could take a while for big web sites.
        core_php_time_limit::raise();
        raise_memory_limit(MEMORY_HUGE);

        foreach ($this->criteria as $crit) {
            // Overall criterion is decided when other criteria are reviewed.
            if ($crit->criteriatype == PROGRAM_CRITERIA_TYPE_OVERALL) {
                continue;
            }

            list($extrajoin, $extrawhere, $extraparams) = $crit->get_completed_criteria_sql();
            // For site level programs, get all active site users who can earn this program and haven't got it yet.
            if ($this->type == PROGRAM_TYPE_SITE) {
                $sql = "SELECT DISTINCT u.id, bi.programid
                        FROM {user} u
                        {$extrajoin}
                        LEFT JOIN {program_issued} bi
                            ON u.id = bi.userid AND bi.programid = :programid
                        WHERE bi.programid IS NULL AND u.id != :guestid AND u.deleted = 0 " . $extrawhere;
                $params = array_merge(array('programid' => $this->id, 'guestid' => $CFG->siteguest), $extraparams);
                $toearn = $DB->get_fieldset_sql($sql, $params);
            } else {
                // For course level programs, get all users who already earned the program in this course.
                // Then find the ones who are enrolled in the course and don't have a program yet.
                $earned = $DB->get_fieldset_select('program_issued', 'userid AS id', 'programid = :programid', array('programid' => $this->id));
                $wheresql = '';
                $earnedparams = array();
                if (!empty($earned)) {
                    list($earnedsql, $earnedparams) = $DB->get_in_or_equal($earned, SQL_PARAMS_NAMED, 'u', false);
                    $wheresql = ' WHERE u.id ' . $earnedsql;
                }
                list($enrolledsql, $enrolledparams) = get_enrolled_sql($this->get_context(), 'clikcap/programs:earnprogram', 0, true);
                $sql = "SELECT DISTINCT u.id
                        FROM {user} u
                        {$extrajoin}
                        JOIN ({$enrolledsql}) je ON je.id = u.id " . $wheresql . $extrawhere;
                $params = array_merge($enrolledparams, $earnedparams, $extraparams);
                $toearn = $DB->get_fieldset_sql($sql, $params);
            }

            foreach ($toearn as $uid) {
                $reviewoverall = false;
                if ($crit->review($uid)) {
                    $crit->mark_complete($uid);
                    if ($this->criteria[PROGRAM_CRITERIA_TYPE_OVERALL]->method == PROGRAM_CRITERIA_AGGREGATION_ANY) {
                        $this->criteria[PROGRAM_CRITERIA_TYPE_OVERALL]->mark_complete($uid);
                        $this->issue($uid);
                        $awards++;
                    } else {
                        $reviewoverall = true;
                    }
                } else {
                    // Will be reviewed some other time.
                    $reviewoverall = false;
                }
                // Review overall if it is required.
                if ($reviewoverall && $this->criteria[PROGRAM_CRITERIA_TYPE_OVERALL]->review($uid)) {
                    $this->criteria[PROGRAM_CRITERIA_TYPE_OVERALL]->mark_complete($uid);
                    $this->issue($uid);
                    $awards++;
                }
            }
        }

        return $awards;
    }

    /**
     * Gets an array of completed criteria from 'program_criteria_met' table.
     *
     * @param int $userid Completions for a user
     * @return array Records of criteria completions
     */
    public function get_criteria_completions($userid) {
        global $DB;
        $completions = array();
        $sql = "SELECT bcm.id, bcm.critid
                FROM {program_criteria_met} bcm
                    INNER JOIN {program_criteria} bc ON bcm.critid = bc.id
                WHERE bc.programid = :programid AND bcm.userid = :userid ";
        $completions = $DB->get_records_sql($sql, array('programid' => $this->id, 'userid' => $userid));

        return $completions;
    }

    /**
     * Checks if programs has award criteria set up.
     *
     * @return bool A status indicating program has at least one criterion
     */
    public function has_criteria() {
        if (count($this->criteria) > 0) {
            return true;
        }
        return false;
    }

    /**
     * Returns program award criteria
     *
     * @return array An array of program criteria
     */
    public function get_criteria() {
        global $DB;
        $criteria = array();

        if ($records = (array)$DB->get_records('program_criteria', array('programid' => $this->id))) {
            foreach ($records as $record) {
                $criteria[$record->criteriatype] = program_award_criteria::build((array)$record);
            }
        }

        return $criteria;
    }

    /**
     * Get aggregation method for program criteria
     *
     * @param int $criteriatype If none supplied, get overall aggregation method (optional)
     * @return int One of PROGRAM_CRITERIA_AGGREGATION_ALL or PROGRAM_CRITERIA_AGGREGATION_ANY
     */
    public function get_aggregation_method($criteriatype = 0) {
        global $DB;
        $params = array('programid' => $this->id, 'criteriatype' => $criteriatype);
        $aggregation = $DB->get_field('program_criteria', 'method', $params, IGNORE_MULTIPLE);

        if (!$aggregation) {
            return PROGRAM_CRITERIA_AGGREGATION_ALL;
        }

        return $aggregation;
    }

    /**
     * Checks if program has expiry period or date set up.
     *
     * @return bool A status indicating program can expire
     */
    public function can_expire() {
        if ($this->expireperiod || $this->expiredate) {
            return true;
        }
        return false;
    }

    /**
     * Calculates program expiry date based on either expirydate or expiryperiod.
     *
     * @param int $timestamp Time of program issue
     * @return int A timestamp
     */
    public function calculate_expiry($timestamp) {
        $expiry = null;

        if (isset($this->expiredate)) {
            $expiry = $this->expiredate;
        } else if (isset($this->expireperiod)) {
            $expiry = $timestamp + $this->expireperiod;
        }

        return $expiry;
    }

    /**
     * Checks if program has manual award criteria set.
     *
     * @return bool A status indicating program can be awarded manually
     */
    public function has_manual_award_criteria() {
        foreach ($this->criteria as $criterion) {
            if ($criterion->criteriatype == PROGRAM_CRITERIA_TYPE_MANUAL) {
                return true;
            }
        }
        return false;
    }

    /**
     * Fully deletes the program or marks it as archived.
     *
     * @param $archive bool Achive a program without actual deleting of any data.
     */
    public function delete($archive = true) {
        global $DB;

        if ($archive) {
            $this->status = PROGRAM_STATUS_ARCHIVED;
            $this->save();
            return;
        }

        $fs = get_file_storage();

        // Remove all issued program image files and program awards.
        // Cannot bulk remove area files here because they are issued in user context.
        $awards = $this->get_awards();
        foreach ($awards as $award) {
            $usercontext = context_user::instance($award->userid);
            $fs->delete_area_files($usercontext->id, 'clickap_program', 'userprogram', $this->id);
        }
        $DB->delete_records('program_issued', array('programid' => $this->id));

        // Remove all program criteria.
        $criteria = $this->get_criteria();
        foreach ($criteria as $criterion) {
            $criterion->delete();
        }

        // Delete program images.
        $programcontext = $this->get_context();
        $fs->delete_area_files($programcontext->id, 'clickap_program', 'medal', $this->id);
        $fs->delete_area_files($programcontext->id, 'clickap_program', 'banner', $this->id);
        $fs->delete_area_files($programcontext->id, 'clickap_program', 'award', $this->id);
        // Finally, remove program itself.
        $DB->delete_records('program', array('id' => $this->id));
    }
}

function programs_get_programs($type, $courseid = 0, $sort = '', $dir = '', $page = 0, $perpage = PROGRAM_PERPAGE, $user = 0) {
    global $DB;
    $records = array();
    $params = array();
    $where = "b.status != :deleted AND b.type = :type ";
    $params['deleted'] = PROGRAM_STATUS_ARCHIVED;

    $userfields = array('b.id, b.name, b.status');
    $usersql = "";
    if ($user != 0) {
        $userfields[] = 'bi.dateissued';
        $userfields[] = 'bi.uniquehash';
        $usersql = " LEFT JOIN {program_issued} bi ON b.id = bi.programid AND bi.userid = :userid ";
        $params['userid'] = $user;
        $where .= " AND (b.status = 1 OR b.status = 3) ";
    }
    $fields = implode(', ', $userfields);

    if ($courseid != 0 ) {
        $where .= "AND b.courseid = :courseid ";
        $params['courseid'] = $courseid;
    }

    $sorting = (($sort != '' && $dir != '') ? 'ORDER BY ' . $sort . ' ' . $dir : '');
    $params['type'] = $type;

    $sql = "SELECT $fields FROM {program} b $usersql WHERE $where $sorting";
    $records = $DB->get_records_sql($sql, $params, $page * $perpage, $perpage);

    $programs = array();
    foreach ($records as $r) {
        $program = new program($r->id);
        $programs[$r->id] = $program;
        if ($user != 0) {
            $programs[$r->id]->dateissued = $r->dateissued;
            $programs[$r->id]->uniquehash = $r->uniquehash;
        } else {
            $programs[$r->id]->awards = $DB->count_records_sql('SELECT COUNT(b.userid)
                                        FROM {program_issued} b INNER JOIN {user} u ON b.userid = u.id
                                        WHERE b.programid = :programid AND u.deleted = 0', array('programid' => $program->id));
            $programs[$r->id]->statstring = $program->get_status_name();
        }
    }
    return $programs;
}

function program_calculate_message_schedule($schedule) {
    $nextcron = 0;

    switch ($schedule) {
        case PROGRAM_MESSAGE_DAILY:
            $nextcron = time() + 60 * 60 * 24;
            break;
        case PROGRAM_MESSAGE_WEEKLY:
            $nextcron = time() + 60 * 60 * 24 * 7;
            break;
        case PROGRAM_MESSAGE_MONTHLY:
            $nextcron = time() + 60 * 60 * 24 * 7 * 30;
            break;
    }

    return $nextcron;
}

function programs_process_program_image(program $program, $iconfile, $filearea) {
    global $CFG, $USER;
    require_once($CFG->libdir. '/gdlib.php');

    if (!empty($CFG->gdversion)) {
        process_new_icon($program->get_context(), 'clickap_program', $filearea, $program->id, $iconfile, true);
        @unlink($iconfile);

        // Clean up file draft area after program image has been saved.
        $context = context_user::instance($USER->id, MUST_EXIST);
        $fs = get_file_storage();
        $fs->delete_area_files($context->id, 'user', 'draft');
    }
}

function print_program_image(program $program, stdClass $context, $size = 'small') {
    $imageurl = "";
    //$fsize = ($size == 'small') ? 'f2' : 'f1';
    $fs = get_file_storage();
    $files = $fs->get_area_files($context->id, 'clickap_program', 'medal', $program->id);
    foreach ($files as $file) {
        $isimage = $file->is_valid_image();
        if ($isimage) {
            $imageurl = moodle_url::make_pluginfile_url($context->id, 'clickap_program', $file->get_filearea(), $program->id, '/', $file->get_filename(), false);
            $imageurl->param('refresh', rand(1, 10000));
            break;
        }
    }

    $attributes = array('src' => $imageurl, 'alt' => s($program->name), 'class' => 'activateprogram');

    return html_writer::empty_tag('img', $attributes);
}

function clickap_program_pluginfile($course, $cm, $context, $filearea, array $args, $forcedownload, $options=array()) {
    global $CFG, $DB, $USER;

    //require_once(dirname(__FILE__) . '/locallib.php');

    if ($context->contextlevel != CONTEXT_SYSTEM) {
        return false;
    }

    //if (!has_capability('clickap/programs:viewprogram', $context)) {
    //    return false;
    //}

    if ($filearea !== 'medal' && $filearea !== 'banner' && $filearea !== 'award' && $filearea !== 'programbanner') {
        return false;
    }

    $fs = get_file_storage();
    $relativepath = implode('/', $args);
    $fullpath = rtrim('/' . $context->id . '/clickap_program/' . $filearea . '/'. $relativepath, '/');
    $file = $fs->get_file_by_hash(sha1($fullpath));

    if (!$file || $file->is_directory()) {
        return false;
    }

    // Default cache lifetime is 86400s.
    send_stored_file($file);
}

function program_image_options() {
    global $CFG;

    $accepted_types = preg_split('/\s*,\s*/', trim('.jpg,.png'), -1, PREG_SPLIT_NO_EMPTY);
    if (in_array('*', $accepted_types) || empty($accepted_types)) {
        $accepted_types = '*';
    } else {
        foreach ($accepted_types as $i => $type) {
            if (substr($type, 0, 1) !== '.') {
                require_once($CFG->libdir. '/filelib.php');
                if (!count(file_get_typegroup('extension', $type))) {
                    $accepted_types[$i] = '.'. $type;
                    $corrected = true;
                }
            }
        }
        if (!empty($corrected)) {
            set_config('teacherimagefilesext', join(',', $accepted_types));
        }
    }
    $options = array(
        'maxfiles' => "1",
        'maxbytes' => $CFG->maxbytes,//262144
        'subdirs' => 0,
        'accepted_types' => $accepted_types
    );
    
    return $options;
}

function programs_notify_program_award(program $program, $userid, $issued) {
    global $CFG, $DB;

    $admin = get_admin();
    $userfrom = new stdClass();
    $userfrom->id = $admin->id;
    $userfrom->email = $admin->email;
    foreach (get_all_user_name_fields() as $addname) {
        $userfrom->$addname = $admin->$addname;
    }
    $userfrom->firstname = $admin->firstname;
    $userfrom->maildisplay = true;

    $issuedlink = html_writer::link(new moodle_url('/local/program/courses.php', array('id' => $program->id)), $program->name);
    $userto = $DB->get_record('user', array('id' => $userid), '*', MUST_EXIST);

    $params = new stdClass();
    $params->programname = $program->name;
    $params->username = fullname($userto);
    $params->programlink = $issuedlink;
    $message = program_message_from_template($program->message, $params);
    $plaintext = html_to_text($message);

    // Notify recipient.
    $eventdata = new \core\message\message();
    $eventdata->component         = 'clickap_program';
    $eventdata->name              = 'programrecipientnotice';
    $eventdata->userfrom          = $userfrom;
    $eventdata->userto            = $userto;
    $eventdata->notification      = 1;
    $eventdata->subject           = $program->messagesubject;
    $eventdata->fullmessage       = $plaintext;
    $eventdata->fullmessageformat = FORMAT_HTML;
    $eventdata->fullmessagehtml   = $message;
    $eventdata->smallmessage      = '';

    message_send($eventdata);

    // Notify program creator about the award if they receive notifications every time.
    if ($program->notification == 1) {
        $userfrom = core_user::get_noreply_user();
        $userfrom->maildisplay = true;

        $creator = $DB->get_record('user', array('id' => $program->usercreated), '*', MUST_EXIST);
        $a = new stdClass();
        $a->user = fullname($userto);
        $a->link = $issuedlink;
        $creatormessage = get_string('creatorbody', 'clickap_program', $a);
        $creatorsubject = get_string('creatorsubject', 'clickap_program', $program->name);

        $eventdata = new \core\message\message();
        $eventdata->component         = 'clickap_program';
        $eventdata->name              = 'programcreatornotice';
        $eventdata->userfrom          = $userfrom;
        $eventdata->userto            = $creator;
        $eventdata->notification      = 1;
        $eventdata->subject           = $creatorsubject;
        $eventdata->fullmessage       = html_to_text($creatormessage);
        $eventdata->fullmessageformat = FORMAT_HTML;
        $eventdata->fullmessagehtml   = $creatormessage;
        $eventdata->smallmessage      = '';

        message_send($eventdata);
        $DB->set_field('program_issued', 'issuernotified', time(), array('programid' => $program->id, 'userid' => $userid));
    }
}

function program_message_from_template($message, $params) {
    $msg = $message;
    foreach ($params as $key => $value) {
        $msg = str_replace("%$key%", $value, $msg);
    }

    return $msg;
}

function programs_bake($hash, $programid, $userid = 0, $pathhash = false) {
    /* Mary
    global $CFG, $USER;
    require_once(dirname(dirname(dirname(dirname(__FILE__)))) . '/admin/clickap/program/lib/bakerlib.php');

    $program = new program($programid);
    $program_context = $program->get_context();
    $userid = ($userid) ? $userid : $USER->id;
    $user_context = context_user::instance($userid);

    $fs = get_file_storage();
    if (!$fs->file_exists($user_context->id, 'clickap_program', 'userprogram', $program->id, '/', $hash . '.png')) {
        if ($file = $fs->get_file($program_context->id, 'clickap_program', 'medal', $program->id, '/', 'f1.png')) {
            $contents = $file->get_content();

            $filehandler = new PNG_MetaDataHandler($contents);
            $assertion = new moodle_url('/admin/clickap/program/assertion.php', array('b' => $hash));
            if ($filehandler->check_chunks("tEXt", "openprograms")) {
                // Add assertion URL tExt chunk.
                $newcontents = $filehandler->add_chunks("tEXt", "openprograms", $assertion->out(false));
                $fileinfo = array(
                        'contextid' => $user_context->id,
                        'component' => 'clickap_program',
                        'filearea' => 'userprogram',
                        'itemid' => $program->id,
                        'filepath' => '/',
                        'filename' => $hash . '.png',
                );

                // Create a file with added contents.
                $newfile = $fs->create_file_from_string($fileinfo, $newcontents);
                if ($pathhash) {
                    return $newfile->get_pathnamehash();
                }
            }
        } else {
            debugging('Error baking program image!', DEBUG_DEVELOPER);
            return;
        }
    }

    // If file exists and we just need its path hash, return it.
    if ($pathhash) {
        $file = $fs->get_file($user_context->id, 'clickap_program', 'userprogram', $program->id, '/', $hash . '.png');
        return $file->get_pathnamehash();
    }

    $fileurl = moodle_url::make_pluginfile_url($user_context->id, 'clickap_program', 'userprogram', $program->id, '/', $hash, true);
    return $fileurl;
    */
}

//for block_program
function programs_get_related_programs($userid, $showall=false, $courseids=0, $page = 0, $perpage = 10, $search = '', $onlypublic = false) {
    global $CFG, $DB;

    $params = array(
        'userid' => $userid,
        'userid2' => $userid
    );
    $sql = 'SELECT DISTINCT p.id
                   , bi.uniquehash, bi.dateissued, bi.dateexpire, bi.id as issuedid, bi.visible,
                   p.status,
                   p.*
            FROM
                {program} p
            LEFT JOIN {program_issued} bi ON p.id = bi.programid 
            LEFT JOIN {program_criteria} pc ON pc.programid = p.id
            LEFT JOIN {program_criteria_param} cp ON cp.critid = pc.id 
            WHERE (p.status = 1 OR p.status =3 OR p.status =4)
                  AND bi.userid = :userid 
                  AND pc.criteriatype = 5
                  AND cp.name like "course_%"';
    if ($onlypublic) {
        $sql .= ' AND (bi.visible = 1) ';
    }
    
    $sql .= ' UNION 
            SELECT DISTINCT p.id, 
                   "" as uniquehash, "" as dateissued, "" as dateexpire, "" issuedid, "" as visible,
                   p.status,
                   p.*
            FROM
                {program} p
            LEFT JOIN {program_criteria} pc ON pc.programid = p.id
            LEFT JOIN {program_criteria_param} cp ON cp.critid = pc.id 
            WHERE (p.status = 1 OR p.status =3 OR p.status =4)
                  AND cp.value in('.$courseids.')
                  AND p.id not in (SELECT programid FROM {program_issued} where userid = :userid2)
                  AND pc.criteriatype = 5
                  AND cp.name like "course_%"';

    $sql .= ' ORDER BY dateissued DESC';
    
    if(!$showall){
        $programs = $DB->get_records_sql($sql, $params, $page * $perpage, $perpage);
    }
    else{
        $programs = $DB->get_records_sql($sql, $params);
    }

    return $programs;
}

function programs_get_program_courses($programid){
    global $DB;
    
    $sql = "SELECT cp.value as id, cp.value 
            FROM {program_criteria} pc 
            LEFT JOIN {program_criteria_param} cp ON cp.critid = pc.id
            WHERE criteriatype = 5 AND name like 'course_%' AND pc.programid = :programid";
    return $DB->get_records_sql_menu($sql, array('programid'=>$programid));
}

//for block_program
function programs_get_user_programs($userid, $showall=false, $page = 0, $perpage = 10, $search = '', $onlypublic = false) {
    global $CFG, $DB;

    $params = array(
        'userid' => $userid
    );
    $sql = 'SELECT
                bi.uniquehash,
                bi.dateissued,
                bi.dateexpire,
                bi.id as issuedid,
                bi.visible,
                u.email,
                b.*
            FROM
                {program} b,
                {program_issued} bi,
                {user} u
            WHERE b.id = bi.programid
                AND u.id = bi.userid
                AND bi.userid = :userid';

    if (!empty($search)) {
        $sql .= ' AND (' . $DB->sql_like('b.name', ':search', false) . ') ';
        $params['search'] = '%'.$DB->sql_like_escape($search).'%';
    }
    if ($onlypublic) {
        $sql .= ' AND (bi.visible = 1) ';
    }

    $sql .= ' ORDER BY bi.dateissued DESC';
    if(!$showall){
        $programs = $DB->get_records_sql($sql, $params, $page * $perpage, $perpage);
    }
    else{
        $programs = $DB->get_records_sql($sql, $params);
    }

    return $programs;
}

function programs_get_user_has_program($programid , $userid, $onlypublic = false) {
    global $CFG, $DB;

    $params = array(
        'programid' => $programid,
        'userid' => $userid
    );
    $sql = 'SELECT
                bi.uniquehash,
                bi.dateissued,
                bi.dateexpire,
                bi.id as issuedid,
                bi.visible,
                u.email,
                b.*
            FROM
                {program} b,
                {program_issued} bi,
                {user} u
            WHERE b.id = bi.programid
                AND u.id = bi.userid
                AND bi.userid = :userid
                AND b.id = :programid';

    if ($onlypublic) {
        $sql .= ' AND (bi.visible = 1) ';
    }

    $sql .= ' ORDER BY bi.dateissued DESC';

    return $DB->record_exists_sql($sql, $params);
}

/**
 * Changes the sort order of this categories parent shifting this category up or down one.
 *
 * @global \moodle_database $DB
 * @param bool $up If set to true the category is shifted up one spot, else its moved down.
 * @return bool True on success, false otherwise.
 */
function clickap_program_change_category_sortorder_by_one($programid, $id, $up=false) {
    global $DB;
    
    $category = $DB->get_record('program_category', array('id'=>$id));
    $params = array($category->sortorder, $programid);
    if($up){
        $select = 'sortorder < ? AND programid = ?';
        $sort = 'sortorder DESC';
    }else{
        $select = 'sortorder > ? AND programid = ?';
        $sort = 'sortorder ASC';
    }
    $swapcategory = $DB->get_records_select('program_category', $select, $params, $sort, '*', 0, 1);
    $swapcategory = reset($swapcategory);
    if ($swapcategory) {
        $DB->set_field('program_category', 'sortorder', $swapcategory->sortorder, array('id' => $category->id));
        $DB->set_field('program_category', 'sortorder', $category->sortorder, array('id' => $swapcategory->id));

        return true;
    }
    return false;
}

/**
 * Changes the course sortorder by one, moving it up or down one in respect to sort order.
 *
 * @param stdClass|course_in_list $course
 * @param bool $up If set to true the course will be moved up one. Otherwise down one.
 * @return bool
 */
function clickap_program_change_course_sortorder_by_one($programid, $categoryid, $courseid, $up = false) {
    global $DB;

    $course = $DB->get_record('program_category_courses', array('programid'=>$programid, 'categoryid'=>$categoryid, 'courseid'=>$courseid));
    
    $params = array($course->sortorder, $programid, $course->categoryid);
    if ($up) {
        $select = 'sortorder < ? AND programid = ? AND categoryid = ? ';
        $sort = 'sortorder DESC';
    } else {
        $select = 'sortorder > ? AND programid = ? AND categoryid = ?';
        $sort = 'sortorder ASC';
    }
    $swapcourse = $DB->get_records_select('program_category_courses', $select, $params, $sort, '*', 0, 1);
    if ($swapcourse) {
        $swapcourse = reset($swapcourse);
        $DB->set_field('program_category_courses', 'sortorder', $swapcourse->sortorder, array('id' => $course->id));
        $DB->set_field('program_category_courses', 'sortorder', $course->sortorder, array('id' => $swapcourse->id));

        return true;
    }
    return false;
}

function clickap_program_change_course_category($programid, $oldcategoryid, $newcategoryid, $courseids) {
    global $DB;

    $sql = "SELECT MAX(sortorder) FROM {program_category_courses} 
            WHERE programid = :programid AND categoryid = :categoryid";
    $sortorder = $DB->get_field_sql($sql, array('programid'=>$programid,'categoryid'=>$newcategoryid));
    if(is_array($courseids)){
        foreach($courseids as $id){
            $course = $DB->get_record('program_category_courses', array('programid'=>$programid, 'categoryid'=>$oldcategoryid, 'courseid'=>$id));
            if($course){
                //$DB->set_field('program_category_courses', 'categoryid', $newcategoryid, array('id' => $course->id));
                $data = new stdClass();
                $data->id = $course->id;
                $data->categoryid = $newcategoryid;
                $data->sortorder = ++$sortorder;
                $DB->update_record('program_category_courses', $data);
            }
        }
        return true;
    }else{
        $course = $DB->get_record('program_category_courses', array('programid'=>$programid, 'categoryid'=>$oldcategoryid, 'courseid'=>$courseids));
        if($course){
            //$DB->set_field('program_category_courses', 'categoryid', $newcategoryid, array('id' => $course->id));
            $data = new stdClass();
            $data->id = $course->id;
            $data->categoryid = $newcategoryid;
            $data->sortorder = $sortorder+1;
            $DB->update_record('program_category_courses', $data);
            return true;
        }
    }
    return false;
}

function clickap_program_action_course_change_sortorder_after_course($categoryid, $courseid, $afterid, $previousid=0){
    global $DB;

    $course = $DB->get_record('program_category_courses', array('categoryid'=>$categoryid, 'courseid'=>$courseid));

    if(!empty($previousid) && empty($afterid)){
        $previous = $DB->get_record('program_category_courses', array('categoryid'=>$categoryid, 'courseid'=>$previousid));
        $sql = 'UPDATE {program_category_courses}
                SET sortorder = sortorder + 1
                WHERE categoryid = :categoryid
                AND sortorder <= :sortorder';
        $params = array(
            'categoryid' => $categoryid,
            'sortorder' => $previous->sortorder
        );
        $DB->execute($sql, $params);
        $DB->set_field('program_category_courses', 'sortorder', 1, array('id' => $course->id));
        return true;
    }
    else if(empty($previousid) && !empty($afterid)){
        $after = $DB->get_record('program_category_courses', array('categoryid'=>$categoryid, 'courseid'=>$afterid));
        $sql = 'UPDATE {program_category_courses}
                SET sortorder = sortorder - 1
                WHERE categoryid = :categoryid
                AND sortorder <= :sortorder';
        $params = array(
            'categoryid' => $categoryid,
            'sortorder' => $after->sortorder
        );
        $DB->execute($sql, $params);
        $DB->set_field('program_category_courses', 'sortorder', $after->sortorder, array('id' => $course->id));
        return true;
    }
    else if(!empty($previousid) && !empty($afterid)){
        $after = $DB->get_record('program_category_courses', array('categoryid'=>$categoryid, 'courseid'=>$afterid));
        $sql = 'UPDATE {program_category_courses}
                SET sortorder = sortorder + 1
                WHERE categoryid = :categoryid
                AND sortorder > :sortorder';
        $params = array(
            'categoryid' => $categoryid,
            'sortorder' => $after->sortorder
        );
        $DB->execute($sql, $params);
        $DB->set_field('program_category_courses', 'sortorder', $after->sortorder+1, array('id' => $course->id));
        return true;
    }
    return false;
}

/**
 * Returns list of courses current $USER is enrolled in and can access
 *
 * - $fields is an array of field names to ADD
 *   so name the fields you really need, which will
 *   be added and uniq'd
 *
 * @param string|array $fields
 * @param string $sort
 * @param int $limit max number of courses
 * @return array
 */
function clickap_program_get_my_courses($userid, $sort = 'startdate DESC, sortorder DESC', $showall=false, $page=0, $perpage=5) {
    global $CFG, $DB, $USER;
    
    // Guest account does not have any courses
    if (isguestuser() or !isloggedin()) {
        return(array());
    }

    $basefields = array('id', 'category', 'sortorder',
                        'shortname', 'fullname', 'idnumber',
                        'startdate', 'visible',
                        'groupmode', 'groupmodeforce', 'cacherev', 'enablecompletion');

    if (empty($fields)) {
        $fields = $basefields;
    }
    else if (is_string($fields)) {
        // turn the fields from a string to an array
        $fields = explode(',', $fields);
        $fields = array_map('trim', $fields);
        $fields = array_unique(array_merge($basefields, $fields));
    }
    else if (is_array($fields)) {
        $fields = array_unique(array_merge($basefields, $fields));
    }
    else {
        throw new coding_exception('Invalid $fileds parameter in enrol_get_my_courses()');
    }
    if (in_array('*', $fields)) {
        $fields = array('*');
    }
    
    $wheres = array("c.id <> :siteid");
    $params = array('siteid'=>SITEID);

    $coursefields = 'c.' .join(',c.', $fields);
    $ccselect = ', ca.id as categoryid, ca.name as categoryname, cc.timecompleted, ' . context_helper::get_preload_record_columns_sql('ctx');
    $ccjoin = " LEFT JOIN {context} ctx ON (ctx.instanceid = c.id AND ctx.contextlevel = :contextlevel)
                LEFT JOIN {course_categories} ca ON ca.id = c.category";
    $params['contextlevel'] = CONTEXT_COURSE;

    //only display select roles enrol courses
    $roles = $enrols = "";
    
    $blocks = get_plugin_list('block');
    if(array_key_exists("mytoc", $blocks)){
        $config = get_config('block_mytoc');
        $roles = $config->enrolrole;
    }
    else{
       $roles = $CFG->gradebookroles;
    }
    
    $wheres[] = "c.visible = 1 ";

    if(!empty($roles)){
        $ccjoin .= " LEFT JOIN {role_assignments} ra ON ra.contextid = ctx.id AND ra.userid = :rauserid";
        $wheres[] = "ra.roleid IN ($roles) ";
        $params['rauserid']  = $userid;
    }
    
    $dbman = $DB->get_manager();
    //order by favorite course
    if($dbman->table_exists(new xmldb_table('favorite'))){
        if ($DB->record_exists('favorite', array('user' => $userid))) {
            $ccjoin .= " LEFT JOIN {favorite} fav ON fav.course = c.id AND fav.user = :favuserid";
            //$ccselect .= ', fav.timemodified ';
            $params['favuserid']  = $userid;
            $favoritesort = 'fav.timemodified DESC, ';
        }
    }
    if($dbman->table_exists(new xmldb_table('clickap_course_info'))){
        $ccjoin .= " LEFT JOIN {clickap_course_info} ci ON ci.courseid = c.id";
        if($dbman->table_exists(new xmldb_table('clickap_course_origin'))){
            $ccjoin .= " LEFT JOIN {clickap_course_origin} co ON co.id = ci.originid";
            $ccselect .= " , co.id AS originid , co.name AS originname";
        }
    }
    
    $orderby = "";
    $sort    = trim($sort);
    if (!empty($sort)) {
        $rawsorts = explode(',', $sort);
        $sorts = array();
        foreach ($rawsorts as $rawsort) {
            $rawsort = trim($rawsort);
            if (strpos($rawsort, 'c.') === 0) {
                $rawsort = substr($rawsort, 2);
            }
            $sorts[] = trim($rawsort);
        }
        $sort = 'c.enablecompletion DESC, cc.timecompleted ASC, c.'.implode(',c.', $sorts);
        if(isset($favoritesort)){
            $orderby = "ORDER BY $favoritesort $sort";
        }
        else{
            $orderby = "ORDER BY $sort";
        }
    }

    $wheres = implode(" AND ", $wheres);
    //note: we can not use DISTINCT + text fields due to Oracle and MS limitations, that is why we have the subselect there
    $sql = "SELECT DISTINCT $coursefields $ccselect
              FROM {course} c
              JOIN (SELECT DISTINCT e.courseid
                      FROM {enrol} e
                      JOIN {user_enrolments} ue ON (ue.enrolid = e.id AND ue.userid = :userid)
                     WHERE ue.status = :active AND e.status = :enabled
                     AND ue.timestart < :now1 AND (ue.timeend = 0 OR ue.timeend > :now2)";
    if(!empty($enrols)){
        $sql .= " AND e.enrol IN ($enrols)";
    }
    $sql .= " ) en ON (en.courseid = c.id)
           $ccjoin
           LEFT JOIN {course_completions} cc ON cc.course = c.id AND cc.userid = ra.userid
             WHERE $wheres
          $orderby";
    $params['userid']  = $userid;
    $params['active']  = ENROL_USER_ACTIVE;
    $params['enabled'] = ENROL_INSTANCE_ENABLED;
    $params['now1']    = round(time(), -2); // improves db caching
    $params['now2']    = $params['now1'];

    if(!$showall){
        $courses = $DB->get_records_sql($sql, $params, $page * $perpage, $perpage);
    }
    else{
        $courses = $DB->get_records_sql($sql, $params);
    }

    /*
    // preload contexts and check visibility
    foreach ($courses as $id=>$course) {
        context_helper::preload_from_record($course);
        if (!$course->visible) {
            if (!$context = context_course::instance($id, IGNORE_MISSING)) {
                unset($courses[$id]);
                continue;
            }
            if (!has_capability('moodle/course:viewhiddencourses', $context)) {
                unset($courses[$id]);
                continue;
            }
        }
        $courses[$id] = $course;
    }
    */
    //wow! Is that really all? :-D
    return $courses;
}