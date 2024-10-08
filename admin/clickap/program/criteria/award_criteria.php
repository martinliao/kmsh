<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Program award criteria
 *
 * @package    core
 * @subpackage programs
 * @copyright  2012 onwards Totara Learning Solutions Ltd {@link http://www.totaralms.com/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Yuliya Bozhko <yuliya.bozhko@totaralms.com>
 */

defined('MOODLE_INTERNAL') || die();

/*
 * Role completion criteria type
 * Criteria type constant, primarily for storing criteria type in the database.
 */
define('PROGRAM_CRITERIA_TYPE_OVERALL', 0);

/*
 * Activity completion criteria type
 * Criteria type constant, primarily for storing criteria type in the database.
 */
define('PROGRAM_CRITERIA_TYPE_ACTIVITY', 1);

/*
 * Duration completion criteria type
 * Criteria type constant, primarily for storing criteria type in the database.
 */
define('PROGRAM_CRITERIA_TYPE_MANUAL', 2);

/*
 * Grade completion criteria type
 * Criteria type constant, primarily for storing criteria type in the database.
 */
define('PROGRAM_CRITERIA_TYPE_SOCIAL', 3);

/*
 * Course completion criteria type
 * Criteria type constant, primarily for storing criteria type in the database.
*/
define('PROGRAM_CRITERIA_TYPE_COURSE', 4);

/*
 * Courseset completion criteria type
 * Criteria type constant, primarily for storing criteria type in the database.
 */
define('PROGRAM_CRITERIA_TYPE_COURSESET', 5);

/*
 * Course completion criteria type
 * Criteria type constant, primarily for storing criteria type in the database.
 */
define('PROGRAM_CRITERIA_TYPE_PROFILE', 6);

/*
 * Criteria type constant to class name mapping
 */
global $PROGRAM_CRITERIA_TYPES;
$PROGRAM_CRITERIA_TYPES = array(
    PROGRAM_CRITERIA_TYPE_OVERALL   => 'overall',
    PROGRAM_CRITERIA_TYPE_ACTIVITY  => 'activity',
    PROGRAM_CRITERIA_TYPE_MANUAL    => 'manual',
    PROGRAM_CRITERIA_TYPE_SOCIAL    => 'social',
    PROGRAM_CRITERIA_TYPE_COURSE    => 'course',
    PROGRAM_CRITERIA_TYPE_COURSESET => 'courseset',
    PROGRAM_CRITERIA_TYPE_PROFILE   => 'profile'
);

/**
 * Award criteria abstract definition
 *
 */
abstract class program_award_criteria {

    /**
     * ID of the criterion.
     * @var integer
     */
    public $id;

    /**
     * Aggregation method [PROGRAM_CRITERIA_AGGREGATION_ANY, PROGRAM_CRITERIA_AGGREGATION_ALL].
     * @var integer
     */
    public $method;

    /**
     * ID of a program this criterion belongs to.
     * @var integer
     */
    public $programid;

    /**
     * Criterion HTML/plain text description.
     * @var string
     */
    public $description;

    /**
     * Format of the criterion description.
     * @var integer
     */
    public $descriptionformat;

    /**
     * Any additional parameters.
     * @var array
     */
    public $params = array();

    /**
     * The base constructor
     *
     * @param array $params
     */
    public function __construct($params) {
        $this->id = isset($params['id']) ? $params['id'] : 0;
        $this->method = isset($params['method']) ? $params['method'] : PROGRAM_CRITERIA_AGGREGATION_ANY;
        $this->programid = $params['programid'];
        $this->description = isset($params['description']) ? $params['description'] : '';
        $this->descriptionformat = isset($params['descriptionformat']) ? $params['descriptionformat'] : FORMAT_HTML;
        if (isset($params['id'])) {
            $this->params = $this->get_params($params['id']);
        }
    }

    /**
     * Factory method for creating criteria class object
     *
     * @param array $params associative arrays varname => value
     * @return award_criteria
     */
    public static function build($params) {
        global $CFG, $PROGRAM_CRITERIA_TYPES;

        if (!isset($params['criteriatype']) || !isset($PROGRAM_CRITERIA_TYPES[$params['criteriatype']])) {
            print_error('error:invalidcriteriatype', 'clickap_program');
        }

        $class = 'program_award_criteria_' . $PROGRAM_CRITERIA_TYPES[$params['criteriatype']];
        require_once($CFG->dirroot . '/admin/clickap/program/criteria/' . $class . '.php');

        return new $class($params);
    }

    /**
     * Return criteria title
     *
     * @return string
     */
    public function get_title() {
        return get_string('criteria_' . $this->criteriatype, 'clickap_program');
    }

    /**
     * Get criteria details for displaying to users
     *
     * @param string $short Print short version of criteria
     * @return string
     */
    abstract public function get_details($short = '');

    /**
     * Add appropriate criteria options to the form
     *
     */
    abstract public function get_options(&$mform);

    /**
     * Add appropriate parameter elements to the criteria form
     *
     */
    public function config_options(&$mform, $param) {
        global $OUTPUT;
        $prefix = $this->required_param . '_';

        if ($param['error']) {
            $parameter[] =& $mform->createElement('advcheckbox', $prefix . $param['id'], '',
                    $OUTPUT->error_text($param['name']), null, array(0, $param['id']));
            $mform->addGroup($parameter, 'param_' . $prefix . $param['id'], '', array(' '), false);
        } else {
            $parameter[] =& $mform->createElement('advcheckbox', $prefix . $param['id'], '', $param['name'], null, array(0, $param['id']));
            $parameter[] =& $mform->createElement('static', 'break_start_' . $param['id'], null, '<div style="margin-left: 3em;">');

            if (in_array('grade', $this->optional_params)) {
                $parameter[] =& $mform->createElement('static', 'mgrade_' . $param['id'], null, get_string('mingrade', 'clickap_program'));
                $parameter[] =& $mform->createElement('text', 'grade_' . $param['id'], '', array('size' => '5'));
                $mform->setType('grade_' . $param['id'], PARAM_INT);
            }

            if (in_array('bydate', $this->optional_params)) {
                $parameter[] =& $mform->createElement('static', 'complby_' . $param['id'], null, get_string('bydate', 'clickap_program'));
                $parameter[] =& $mform->createElement('date_selector', 'bydate_' . $param['id'], "", array('optional' => true));
            }

            $parameter[] =& $mform->createElement('static', 'break_end_' . $param['id'], null, '</div>');
            $mform->addGroup($parameter, 'param_' . $prefix . $param['id'], '', array(' '), false);
            if (in_array('grade', $this->optional_params)) {
                $mform->addGroupRule('param_' . $prefix . $param['id'], array(
                    'grade_' . $param['id'] => array(array(get_string('err_numeric', 'form'), 'numeric', '', 'client'))));
            }
            $mform->disabledIf('bydate_' . $param['id'] . '[day]', 'bydate_' . $param['id'] . '[enabled]', 'notchecked');
            $mform->disabledIf('bydate_' . $param['id'] . '[month]', 'bydate_' . $param['id'] . '[enabled]', 'notchecked');
            $mform->disabledIf('bydate_' . $param['id'] . '[year]', 'bydate_' . $param['id'] . '[enabled]', 'notchecked');
            $mform->disabledIf('param_' . $prefix . $param['id'], $prefix . $param['id'], 'notchecked');
        }

        // Set default values.
        $mform->setDefault($prefix . $param['id'], $param['checked']);
        if (isset($param['bydate'])) {
            $mform->setDefault('bydate_' . $param['id'], $param['bydate']);
        }
        if (isset($param['grade'])) {
            $mform->setDefault('grade_' . $param['id'], $param['grade']);
        }
    }

    /**
     * Add appropriate criteria elements
     *
     * @param stdClass $data details of various criteria
     */
    public function config_form_criteria($data) {
        global $OUTPUT;
        $agg = $data->get_aggregation_methods();

        $editurl = new moodle_url('/admin/clickap/program/criteria_settings.php',
                array('programid' => $this->programid, 'edit' => true, 'type' => $this->criteriatype, 'crit' => $this->id));
        $deleteurl = new moodle_url('/admin/clickap/program/criteria_action.php',
                array('programid' => $this->programid, 'delete' => true, 'type' => $this->criteriatype));
        $editaction = $OUTPUT->action_icon($editurl, new pix_icon('t/edit', get_string('edit')), null, array('class' => 'criteria-action'));
        $deleteaction = $OUTPUT->action_icon($deleteurl, new pix_icon('t/delete', get_string('delete')), null, array('class' => 'criteria-action'));

        echo $OUTPUT->box_start();
        if (!$data->is_locked() && !$data->is_active()) {
            echo $OUTPUT->box($deleteaction . $editaction, array('criteria-header'));
        }
        echo $OUTPUT->heading($this->get_title() . $OUTPUT->help_icon('criteria_' . $this->criteriatype, 'clickap_program'), 3, 'main help');

        if (!empty($this->description)) {
            $program = new program($this->programid);
            echo $OUTPUT->box(
                format_text($this->description, $this->descriptionformat, array('context' => $program->get_context())),
                'criteria-description'
                );
        }

        if (!empty($this->params)) {
            if (count($this->params) > 1) {
                echo $OUTPUT->box(get_string('criteria_descr_' . $this->criteriatype, 'clickap_program',
                        core_text::strtoupper($agg[$data->get_aggregation_method($this->criteriatype)])), array('clearfix'));
            } else {
                echo $OUTPUT->box(get_string('criteria_descr_single_' . $this->criteriatype , 'clickap_program'), array('clearfix'));
            }
            echo $OUTPUT->box($this->get_details(), array('clearfix'));
        }
        echo $OUTPUT->box_end();
    }

    /**
     * Review this criteria and decide if the user has completed
     *
     * @param int $userid User whose criteria completion needs to be reviewed.
     * @param bool $filtered An additional parameter indicating that user list
     *        has been reduced and some expensive checks can be skipped.
     *
     * @return bool Whether criteria is complete
     */
    abstract public function review($userid, $filtered = false);

    /**
     * Returns array with sql code and parameters returning all ids
     * of users who meet this particular criterion.
     *
     * @return array list($join, $where, $params)
     */
    abstract public function get_completed_criteria_sql();

    /**
     * Mark this criteria as complete for a user
     *
     * @param int $userid User whose criteria is completed.
     */
    public function mark_complete($userid) {
        global $DB;
        $obj = array();
        $obj['critid'] = $this->id;
        $obj['userid'] = $userid;
        $obj['datemet'] = time();
        if (!$DB->record_exists('program_criteria_met', array('critid' => $this->id, 'userid' => $userid))) {
            $DB->insert_record('program_criteria_met', $obj);
        }
    }

    /**
     * Return criteria parameters
     *
     * @param int $critid Criterion ID
     * @return array
     */
    public function get_params($cid) {
        global $DB;
        $params = array();

        $records = $DB->get_records('program_criteria_param', array('critid' => $cid));
        foreach ($records as $rec) {
            $arr = explode('_', $rec->name);
            $params[$arr[1]][$arr[0]] = $rec->value;
        }

        return $params;
    }

    /**
     * Delete this criterion
     *
     */
    public function delete() {
        global $DB;

        // Remove any records if it has already been met.
        $DB->delete_records('program_criteria_met', array('critid' => $this->id));

        // Remove all parameters records.
        $DB->delete_records('program_criteria_param', array('critid' => $this->id));

        // Finally remove criterion itself.
        $DB->delete_records('program_criteria', array('id' => $this->id));
        
        // Remove all category courses.
        $DB->delete_records_select('program_category', 'name !="" AND programid =:programid', array('programid' => $this->programid));
        $DB->delete_records('program_category_courses', array('programid' => $this->programid));
    }

    /**
     * Saves intial criteria records with required parameters set up.
     *
     * @param array $params Values from the form or any other array.
     */
    public function save($params = array()) {
        global $DB;

        // Figure out criteria description.
        // If it is coming from the form editor, it is an array(text, format).
        $description = '';
        $descriptionformat = FORMAT_HTML;
        if (isset($params['description']['text'])) {
            $description = $params['description']['text'];
            $descriptionformat = $params['description']['format'];
        } else if (isset($params['description'])) {
            $description = $params['description'];
        }

        $fordb = new stdClass();
        $fordb->criteriatype = $this->criteriatype;
        $fordb->method = isset($params['agg']) ? $params['agg'] : PROGRAM_CRITERIA_AGGREGATION_ALL;
        $fordb->programid = $this->programid;
        $fordb->description = $description;
        $fordb->descriptionformat = $descriptionformat;
        $t = $DB->start_delegated_transaction();

        // Pick only params that are required by this criterion.
        // Filter out empty values first.
        $params = array_filter($params);
        // Find out which param matches optional and required ones.
        $match = array_merge($this->optional_params, array($this->required_param));
        //$regex = implode('|', array_map(create_function('$a', 'return $a . "_";'), $match));
        $regex = implode('|', array_map(function($a) {
            return $a . "_";
        }, $match));
        $requiredkeys = preg_grep('/^(' . $regex . ').*$/', array_keys($params));

        if ($this->id !== 0) {
            $cid = $this->id;

            // Update criteria before doing anything with parameters.
            $fordb->id = $cid;
            $DB->update_record('program_criteria', $fordb, true);

            $existing = $DB->get_fieldset_select('program_criteria_param', 'name', 'critid = ?', array($cid));
            $todelete = array_diff($existing, $requiredkeys);
            if (!empty($todelete)) {
                // A workaround to add some disabled elements that are still being submitted from the form.
                foreach ($todelete as $del) {
                    $name = explode('_', $del);
                    if ($name[0] == $this->required_param) {
                        foreach ($this->optional_params as $opt) {
                            $todelete[] = $opt . '_' . $name[1];
                            
                            $DB->delete_records_select('program_category_courses', 'programid = :programid AND courseid = :courseid ', array('programid'=>$this->programid, 'courseid'=>$name[1]));
                        }
                    }
                }
                $todelete = array_unique($todelete);
                list($sql, $sqlparams) = $DB->get_in_or_equal($todelete, SQL_PARAMS_NAMED, 'd', true);
                $sqlparams = array_merge(array('critid' => $cid), $sqlparams);
                $DB->delete_records_select('program_criteria_param', 'critid = :critid AND name ' . $sql, $sqlparams);
            }
            
            foreach ($requiredkeys as $key) {
                $name = explode('_', $key);
                if (in_array($key, $existing)) {
                    if($updp = $DB->get_record('program_criteria_param', array('name' => $key, 'critid' => $cid))){
                        $updp->value = $params[$key];
                        
                        if($name[0] == 'bydate'){
                            $updp->value = $params[$key] + DAYSECS - 1;
                        }
                        $DB->update_record('program_criteria_param', $updp, true);
                    }
                } else {
                    $newp = new stdClass();
                    $newp->critid = $cid;
                    $newp->name = $key;
                    $newp->value = $params[$key];
                    
                    if($name[0] == 'bydate'){
                        $newp->value = $params[$key] + DAYSECS - 1;
                    }
                    $DB->insert_record('program_criteria_param', $newp);
                }
            }
        } else {
            $cid = $DB->insert_record('program_criteria', $fordb, true);
            if ($cid) {
                foreach ($requiredkeys as $key) {
                    $newp = new stdClass();
                    $newp->critid = $cid;
                    $newp->name = $key;
                    $newp->value = $params[$key];
                    $name = explode('_', $key);
                    if($name[0] == 'bydate'){
                        $newp->value = $params[$key] + DAYSECS - 1;
                    }
                    $DB->insert_record('program_criteria_param', $newp, false, true);
                }
            }
        }
        $t->allow_commit();
    }

    /**
     * Saves intial criteria records with required parameters set up.
     */
    public function make_clone($newprogramid) {
        global $DB;

        $fordb = new stdClass();
        $fordb->criteriatype = $this->criteriatype;
        $fordb->method = $this->method;
        $fordb->programid = $newprogramid;
        $fordb->description = $this->description;
        $fordb->descriptionformat = $this->descriptionformat;
        if (($newcrit = $DB->insert_record('program_criteria', $fordb, true)) && isset($this->params)) {
            foreach ($this->params as $k => $param) {
                foreach ($param as $key => $value) {
                    $paramdb = new stdClass();
                    $paramdb->critid = $newcrit;
                    $paramdb->name = $key . '_' . $k;
                    $paramdb->value = $value;
                    $DB->insert_record('program_criteria_param', $paramdb);
                }
            }
        }
    }
}
