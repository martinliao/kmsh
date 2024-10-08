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
require_once('award_criteria_course.php');
require_once($CFG->libdir . '/completionlib.php');
require_once($CFG->dirroot . '/grade/querylib.php');
require_once($CFG->libdir . '/gradelib.php');

/**
 * Program award criteria -- award on courseset completion
 *
 */
class program_award_criteria_courseset extends program_award_criteria {

    /* @var int Criteria [PROGRAM_CRITERIA_TYPE_COURSESET] */
    public $criteriatype = PROGRAM_CRITERIA_TYPE_COURSESET;

    public $required_param = 'course';
    public $optional_params = array('grade', 'bydate');

    /**
     * Get criteria details for displaying to users
     *
     * @return string
     */
    public function get_details($short = '') {
        global $DB, $OUTPUT;
        $output = array();
        foreach ($this->params as $p) {
            $coursename = $DB->get_field('course', 'fullname', array('id' => $p['course']));
            if (!$coursename) {
                $str = $OUTPUT->error_text(get_string('error:nosuchcourse', 'clickap_program'));
            } else {
                $str = html_writer::tag('b', '"' . $coursename . '"');
                if (isset($p['bydate'])) {
                    $str .= get_string('criteria_descr_bydate', 'clickap_program', userdate($p['bydate'], get_string('strftimedate', 'core_langconfig')));
                }
                if (isset($p['grade'])) {
                    $str .= get_string('criteria_descr_grade', 'clickap_program', $p['grade']);
                }
            }
            $output[] = $str;
        }

        if ($short) {
            return implode(', ', $output);
        } else {
            return html_writer::alist($output, array(), 'ul');
        }
    }

    public function get_courses(&$mform) {
        global $DB, $CFG;
        require_once($CFG->dirroot . '/course/lib.php');
        $buttonarray = array();

        // Get courses with enabled completion.
        $courses = $DB->get_records('course', array('enablecompletion' => COMPLETION_ENABLED));
        if (!empty($courses)) {
            $list = core_course_category::make_categories_list();

            $select = array();
            $selected = array();
            foreach ($courses as $c) {
                $select[$c->id] = $list[$c->category] . ' / ' . format_string($c->fullname, true, array('context' => context_course::instance($c->id)));
            }

            if ($this->id !== 0) {
                $selected = array_keys($this->params);
            }
            $settings = array('multiple' => 'multiple', 'size' => 20, 'style' => 'width:300px');
            $mform->addElement('select', 'courses', get_string('addcourse', 'clickap_program'), $select, $settings);
            $mform->addRule('courses', get_string('requiredcourse', 'clickap_program'), 'required');
            $mform->addHelpButton('courses', 'addcourse', 'clickap_program');

            $buttonarray[] =& $mform->createElement('submit', 'submitcourse', get_string('addcourse', 'clickap_program'));
            $buttonarray[] =& $mform->createElement('submit', 'cancel', get_string('cancel'));
            $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);

            $mform->addElement('hidden', 'addcourse', 'addcourse');
            $mform->setType('addcourse', PARAM_TEXT);
            if ($this->id !== 0) {
                $mform->setDefault('courses', $selected);
            }
            $mform->setType('agg', PARAM_INT);
        } else {
            $mform->addElement('static', 'nocourses', '', get_string('error:nocourses', 'clickap_program'));
            $buttonarray[] =& $mform->createElement('submit', 'cancel', get_string('continue'));
            $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
        }
    }

    public function add_courses($params = array()) {
        global $DB, $USER;
        $t = $DB->start_delegated_transaction();
        if ($this->id !== 0) {
            $critid = $this->id;
        } else {
            $fordb = new stdClass();
            $fordb->criteriatype = $this->criteriatype;
            $fordb->method = PROGRAM_CRITERIA_AGGREGATION_ALL;
            $fordb->programid = $this->programid;
            $critid = $DB->insert_record('program_criteria', $fordb, true, true);
        }
        if ($critid) {
            $categoryid = $DB->get_field('program_category', 'id', array('programid'=>$this->programid, 'name'=>''));
            $max_sort = $DB->get_field_sql('SELECT max(sortorder) FROM {program_category_courses} WHERE categoryid=:categoryid', array('categoryid'=>$categoryid));
            foreach ($params as $p) {
                $newp = new stdClass();
                $newp->critid = $critid;
                $newp->name = 'course_' . $p;
                $newp->value = $p;
                if (!$DB->record_exists('program_criteria_param', array('critid' => $critid, 'name' => $newp->name))) {
                    $DB->insert_record('program_criteria_param', $newp, false, true);

                    $newp->programid = $this->programid;
                    $newp->categoryid = $categoryid;
                    $newp->courseid = $p;
                    $newp->sortorder = ++$max_sort;
                    $newp->timemodified = time();
                    $newp->usermodified = $USER->id;
                    $DB->insert_record('program_category_courses', $newp);
                }
            }
        }
        $t->allow_commit();
        return $critid;
    }

    /**
     * Add appropriate new criteria options to the form
     *
     */
    public function get_options(&$mform) {
        global $DB;
        $none = true;

        $mform->addElement('header', 'first_header', $this->get_title());
        $mform->addHelpButton('first_header', 'criteria_' . $this->criteriatype, 'clickap_program');

        if ($courses = $DB->get_records('course', array('enablecompletion' => COMPLETION_ENABLED))) {
            $mform->addElement('submit', 'addcourse', get_string('addcourse', 'clickap_program'), array('class' => 'addcourse'));
        }

        // In courseset, print out only the ones that were already selected.
        foreach ($this->params as $p) {
            if ($course = $DB->get_record('course', array('id' => $p['course']))) {
                $coursecontext = context_course::instance($course->id);
                $param = array(
                        'id' => $course->id,
                        'checked' => true,
                        'name' => format_string($course->fullname, true, array('context' => $coursecontext)),
                        'error' => false
                );

                if (isset($p['bydate'])) {
                    $param['bydate'] = $p['bydate'];
                }
                if (isset($p['grade'])) {
                    $param['grade'] = $p['grade'];
                }
                $this->config_options($mform, $param);
                $none = false;
            } else {
                $this->config_options($mform, array('id' => $p['course'], 'checked' => true,
                        'name' => get_string('error:nosuchcourse', 'clickap_program'), 'error' => true));
            }
        }

        // Add aggregation.
        if (!$none) {
            $mform->addElement('header', 'aggregation', get_string('method', 'clickap_program'));
            $agg = array();
            $agg[] =& $mform->createElement('radio', 'agg', '', get_string('allmethodcourseset', 'clickap_program'), 1);
            $agg[] =& $mform->createElement('radio', 'agg', '', get_string('anymethodcourseset', 'clickap_program'), 2);

            $mform->addGroup($agg, 'methodgr', '', array('<br/>'), false);
            if ($this->id !== 0) {
                $mform->setDefault('agg', $this->method);
            } else {
                $mform->setDefault('agg', PROGRAM_CRITERIA_AGGREGATION_ANY);
            }
        }

        return array($none, get_string('noparamstoadd', 'clickap_program'));
    }

    /**
     * Review this criteria and decide if it has been completed
     *
     * @param int $userid User whose criteria completion needs to be reviewed.
     * @param bool $filtered An additional parameter indicating that user list
     *        has been reduced and some expensive checks can be skipped.
     *
     * @return bool Whether criteria is complete
     */
    public function review($userid, $filtered = false) {
        foreach ($this->params as $param) {
            $course =  new stdClass();
            $course->id = $param['course'];

            $info = new completion_info($course);
            $check_grade = true;
            $check_date = true;

            if (isset($param['grade'])) {
                $grade = grade_get_course_grade($userid, $course->id);
                $check_grade = ($grade->grade >= $param['grade']);
            }

            if (!$filtered && isset($param['bydate'])) {
                $cparams = array(
                        'userid' => $userid,
                        'course' => $course->id,
                );
                $completion = new completion_completion($cparams);
                $date = $completion->timecompleted;
                $check_date = ($date <= $param['bydate']);
            }

            $overall = false;
            if ($this->method == PROGRAM_CRITERIA_AGGREGATION_ALL) {
                if ($info->is_course_complete($userid) && $check_grade && $check_date) {
                    $overall = true;
                    continue;
                } else {
                    return false;
                }
            } else {
                if ($info->is_course_complete($userid) && $check_grade && $check_date) {
                    return true;
                } else {
                    $overall = false;
                    continue;
                }
            }
        }

        return $overall;
    }

    /**
     * Returns array with sql code and parameters returning all ids
     * of users who meet this particular criterion.
     *
     * @return array list($join, $where, $params)
     */
    public function get_completed_criteria_sql() {
        $join = '';
        $where = '';
        $params = array();

        if ($this->method == PROGRAM_CRITERIA_AGGREGATION_ANY) {
            foreach ($this->params as $param) {
                $coursedata[] = " cc.course = :completedcourse{$param['course']} ";
                $params["completedcourse{$param['course']}"] = $param['course'];
            }
            if (!empty($coursedata)) {
                $extraon = implode(' OR ', $coursedata);
                $join = " JOIN {course_completions} cc ON cc.userid = u.id AND
                          cc.timecompleted > 0 AND ({$extraon})";
            }
            return array($join, $where, $params);
        } else {
            foreach ($this->params as $param) {
                $join .= " LEFT JOIN {course_completions} cc{$param['course']} ON
                          cc{$param['course']}.userid = u.id AND
                          cc{$param['course']}.course = :completedcourse{$param['course']} AND
                          cc{$param['course']}.timecompleted > 0 ";
                $where .= " AND cc{$param['course']}.course IS NOT NULL ";
                $params["completedcourse{$param['course']}"] = $param['course'];
            }
            return array($join, $where, $params);
        }
    }
}
