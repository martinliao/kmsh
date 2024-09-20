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
 * Performs course category management ajax actions.
 *
 * Please note functions may throw exceptions, please ensure your JS handles them as well as the outcome objects.
 *
 * @package    core_course
 * @copyright  2013 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('AJAX_SCRIPT', true);

require_once('../../../../../config.php');
require_once($CFG->dirroot.'/admin/clickap/program/lib.php');

$programid = required_param('programid', PARAM_INT);

$action = required_param('action', PARAM_ALPHA);
require_sesskey(); // Gotta have the sesskey.
require_login(); // Gotta be logged in (of course).
$PAGE->set_context(context_system::instance());

// Prepare an outcome object. We always use this.
$outcome = new stdClass;
$outcome->error = false;
$outcome->outcome = false;

echo $OUTPUT->header();

switch ($action) {
    case 'movecourseup' :
        $categoryid = required_param('categoryid', PARAM_INT);
        $courseid = required_param('courseid', PARAM_INT);
        $outcome->outcome = clickap_program_change_course_sortorder_by_one($programid, $categoryid, $courseid, true);
        break;
    case 'movecoursedown' :
        $categoryid = required_param('categoryid', PARAM_INT);
        $courseid = required_param('courseid', PARAM_INT);
        $outcome->outcome = clickap_program_change_course_sortorder_by_one($programid, $categoryid, $courseid);
        break;
    case 'movecategoryup' :
        $categoryid = required_param('categoryid', PARAM_INT);
        $outcome->outcome = clickap_program_change_category_sortorder_by_one($programid, $categoryid, true);
        break;
    case 'movecategorydown' :
        $categoryid = required_param('categoryid', PARAM_INT);
        $outcome->outcome = clickap_program_change_category_sortorder_by_one($programid, $categoryid);
        break;
    case 'movecourseafter' :
        $categoryid = required_param('categoryid', PARAM_INT);
        
        $courseid = required_param('courseid', PARAM_INT);
        $moveaftercourseid = required_param('moveafter', PARAM_INT);
        $movepreviouscourseid = required_param('previous', PARAM_INT);
        
        $outcome->outcome = clickap_program_action_course_change_sortorder_after_course(
            $categoryid, $courseid, $moveaftercourseid, $movepreviouscourseid);
        break;
    case 'movecourseintocategory':
        $courseid = required_param('courseid', PARAM_INT);
        $categoryid = required_param('categoryid', PARAM_INT);
        $course = $DB->get_record('program_category_courses', array('programid'=>$programid, 'courseid'=>$courseid));
        $outcome->outcome = clickap_program_change_course_category($programid, $course->categoryid, $categoryid, $courseid);

        $perpage = (int)get_user_preferences('coursecat_management_perpage', $CFG->coursesperpage);
        $totalcourses = $DB->count_records('program_category_courses', array('programid'=>$programid, 'categoryid'=>$course->categoryid));
        $totalpages = ceil($totalcourses / $perpage);
        if ($totalpages == 0) {
            $str = get_string('nocoursesyet');
        } else if ($totalpages == 1) {
            $str = get_string('showingacourses', 'moodle', $totalcourses);
        } else {
            $a = new stdClass;
            $a->start = ($page * $perpage) + 1;
            $a->end = min((($page + 1) * $perpage), $totalcourses);
            $a->total = $totalcourses;
            $str = get_string('showingxofycourses', 'moodle', $a);
        }
        $outcome->totalcatcourses = $DB->count_records('program_category_courses', array('programid'=>$programid, 'categoryid'=>$categoryid));
        $outcome->fromcatcoursecount = $totalcourses;
        $outcome->paginationtotals = $str;
        break;
}

echo json_encode($outcome);
echo $OUTPUT->footer();
// Thats all folks.
// Don't ever even consider putting anything after this. It just wouldn't make sense.
// But you already knew that, you smart developer you.
exit;