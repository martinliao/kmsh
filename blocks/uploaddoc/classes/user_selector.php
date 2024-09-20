<?php
/**
 * @package   block_uploaddoc
 * @copyright 2018 MARY CHEN  {@link http://www.click-ap.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v1 or later
 */
defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot . '/user/selector/lib.php');

class block_uploaddoc_existing_user_holders extends user_selector_base {
    protected $fileid;
    protected $courseid;
    protected $context;

    public function __construct($name, $options) {
        $this->fileid  = $options['fileid'];
        $this->courseid = $options['courseid'];
        $this->context  = context_course::instance($this->courseid);
        parent::__construct($name, $options);
    }

    public function find_users($search) {
        global $CFG, $DB;

        list($enrolsql, $eparams) = get_enrolled_sql($this->context);

        // Now we have to go to the database.
        list($wherecondition, $params) = $this->search_sql($search, 'u');
        $params = array_merge($params, $eparams);

        if ($wherecondition) {
            $wherecondition = ' AND ' . $wherecondition;
        }
        $params['fileid'] = $this->fileid;

        $fields = "SELECT " . $this->required_fields_sql('u') ." , v.id AS fileid " ;
        $countfields = "SELECT COUNT(*) ";
        $sql = " FROM {derberus_files} v
                 JOIN {user} u ON u.id = v.userid
                 WHERE v.fileid = :fileid AND course = 'Share' $wherecondition";
        list($sort, $sortparams) = users_order_by_sql('u');
        $order = " ORDER BY u.username, $sort";

        if (!$this->is_validating()) {
            $potentialmemberscount = $DB->count_records_sql($countfields . $sql, $params);
            if ($potentialmemberscount > 100) {
                return $this->too_many_results($search, $potentialmemberscount);
            }
        }

        $availableusers = $DB->get_records_sql($fields . $sql . $order, array_merge($params, $sortparams));

        if (empty($availableusers)) {
            return array();
        }

        if ($search) {
            $groupname = get_string('enrolcandidatesmatching', 'enrol', $search);
        } else {
            $groupname = get_string('enrolcandidates', 'enrol');
        }

        return array($groupname => $availableusers);
    }

    protected function get_options() {
        $options = parent::get_options();
        $options['context']  = $this->context;
        $options['fileid']  = $this->fileid;
        $options['courseid'] = $this->courseid;
        $options['file']     = 'blocks/uploaddoc/classes/user_selector.php';
        return $options;
    }
}

class block_uploaddoc_potential_users_selector extends user_selector_base {
    protected $fileid;
    protected $courseid;
    protected $context;

    public function __construct($name, $options) {
        $this->fileid  = $options['fileid'];
        $this->courseid = $options['courseid'];
        $this->context  = context_course::instance($this->courseid);
        
        parent::__construct($name, $options);
    }

    public function find_users($search) {
        global $CFG, $DB, $USER;

        list($enrolsql, $eparams) = get_enrolled_sql($this->context);

        // Now we have to go to the database.
        list($wherecondition, $params) = $this->search_sql($search, 'u');
        $params = array_merge($params, $eparams);

        if ($wherecondition) {
            $wherecondition = ' AND ' . $wherecondition;
        }

        $params['fileid'] = $this->fileid;
        $params['myself'] = $USER->id;
        $fields      = "SELECT ".$this->required_fields_sql("u");
        $countfields = "SELECT COUNT(1)";
         
        $sql         = " FROM {user} u
                         WHERE u.id NOT IN (SELECT userid FROM {derberus_files} WHERE fileid = :fileid AND course = 'Share')
                               AND u.id <> :myself
                         $wherecondition ";

        list($sort, $sortparams) = users_order_by_sql('u');
        $order = " ORDER BY u.username, $sort";

        if (!$this->is_validating()) {
            $potentialmemberscount = $DB->count_records_sql($countfields . $sql, $params);
            if ($potentialmemberscount > 100) {
                return $this->too_many_results($search, $potentialmemberscount);
            }
        }

        $availableusers = $DB->get_records_sql($fields . $sql . $order, array_merge($params, $sortparams));

        if (empty($availableusers)) {
            return array();
        }

        if ($search) {
            $groupname = get_string('enrolcandidatesmatching', 'enrol', $search);
        } else {
            $groupname = get_string('enrolcandidates', 'enrol');
        }

        return array($groupname => $availableusers);
    }

    protected function get_options() {
        $options = parent::get_options();
        $options['context']  = $this->context;
        $options['fileid']  = $this->fileid;
        $options['courseid'] = $this->courseid;
        $options['file']     = 'blocks/uploaddoc/classes/user_selector.php';
        return $options;
    }
}