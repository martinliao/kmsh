<?php
/**
 * @package   block_yakitory
 * @copyright 2021 MARY CHEN  {@link https://www.click-ap.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v1 or later
 */
defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot . '/user/selector/lib.php');

class block_yakitory_existing_user_holders extends user_selector_base {
    protected $videoid;
    protected $courseid;
    protected $context;

    public function __construct($name, $options) {

        $this->videoid  = $options['videoid'];
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
        $params['videoid'] = $this->videoid;

        $fields = "SELECT " . $this->required_fields_sql('u') .", u.username, v.id AS videoid " ;
        $countfields = "SELECT COUNT(*) ";
        $sql = " FROM {yakitory_videos} v
                 JOIN {user} u ON u.username = v.username
                 WHERE v.videoid = :videoid AND course = 'Share' $wherecondition";
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
        $options['videoid']  = $this->videoid;
        $options['courseid'] = $this->courseid;
        $options['file']     = 'blocks/yakitory/classes/user_selector.php';
        return $options;
    }
}

class block_yakitory_potential_users_selector extends user_selector_base {
    protected $videoid;
    protected $courseid;
    protected $context;

    public function __construct($name, $options) {
        $this->videoid  = $options['videoid'];
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

        $params['videoid'] = $this->videoid;
        $params['myself'] = $USER->id;
        $fields      = "SELECT ".$this->required_fields_sql("u").", u.username";
        $countfields = "SELECT COUNT(1)";
         
        $sql         = " FROM {user} u
                         WHERE u.username NOT IN (SELECT username FROM {yakitory_videos} WHERE videoid = :videoid AND course = 'Share')
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
        $options['videoid']  = $this->videoid;
        $options['courseid'] = $this->courseid;
        $options['file']     = 'blocks/yakitory/classes/user_selector.php';
        return $options;
    }
}