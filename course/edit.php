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
 * Edit course settings
 *
 * @package    core_course
 * @copyright  1999 onwards Martin Dougiamas (http://dougiamas.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../config.php');
require_once('lib.php');
require_once('edit_form.php');

$id = optional_param('id', 0, PARAM_INT); // Course id.
$categoryid = optional_param('category', 0, PARAM_INT); // Course category - can be changed in edit form.
$returnto = optional_param('returnto', 0, PARAM_ALPHANUM); // Generic navigation return page switch.
$returnurl = optional_param('returnurl', '', PARAM_LOCALURL); // A return URL. returnto must also be set to 'url'.

if ($returnto === 'url' && confirm_sesskey() && $returnurl) {
    // If returnto is 'url' then $returnurl may be used as the destination to return to after saving or cancelling.
    // Sesskey must be specified, and would be set by the form anyway.
    $returnurl = new moodle_url($returnurl);
} else {
    if (!empty($id)) {
        $returnurl = new moodle_url($CFG->wwwroot . '/course/view.php', array('id' => $id));
    } else {
        $returnurl = new moodle_url($CFG->wwwroot . '/course/');
    }
    if ($returnto !== 0) {
        switch ($returnto) {
            case 'category':
                $returnurl = new moodle_url($CFG->wwwroot . '/course/index.php', array('categoryid' => $categoryid));
                break;
            case 'catmanage':
                $returnurl = new moodle_url($CFG->wwwroot . '/course/management.php', array('categoryid' => $categoryid));
                break;
            case 'topcatmanage':
                $returnurl = new moodle_url($CFG->wwwroot . '/course/management.php');
                break;
            case 'topcat':
                $returnurl = new moodle_url($CFG->wwwroot . '/course/');
                break;
            case 'pending':
                $returnurl = new moodle_url($CFG->wwwroot . '/course/pending.php');
                break;
        }
    }
}

$PAGE->set_pagelayout('admin');
if ($id) {
    $pageparams = array('id' => $id);
} else {
    $pageparams = array('category' => $categoryid);
}
if ($returnto !== 0) {
    $pageparams['returnto'] = $returnto;
    if ($returnto === 'url' && $returnurl) {
        $pageparams['returnurl'] = $returnurl;
    }
}
$PAGE->set_url('/course/edit.php', $pageparams);

// Basic access control checks.
if ($id) {
    // Editing course.
    if ($id == SITEID){
        // Don't allow editing of  'site course' using this from.
        print_error('cannoteditsiteform');
    }

    // Login to the course and retrieve also all fields defined by course format.
    $course = get_course($id);
    require_login($course);
    $course = course_get_format($course)->get_course();

    $category = $DB->get_record('course_categories', array('id'=>$course->category), '*', MUST_EXIST);
    $coursecontext = context_course::instance($course->id);
    require_capability('moodle/course:update', $coursecontext);

} else if ($categoryid) {
    // Creating new course in this category.
    $course = null;
    require_login();
    $category = $DB->get_record('course_categories', array('id'=>$categoryid), '*', MUST_EXIST);
    $catcontext = context_coursecat::instance($category->id);
    require_capability('moodle/course:create', $catcontext);
    $PAGE->set_context($catcontext);

} else {
    // Creating new course in default category.
    $course = null;
    require_login();
    $category = core_course_category::get_default();
    $catcontext = context_coursecat::instance($category->id);
    require_capability('moodle/course:create', $catcontext);
    $PAGE->set_context($catcontext);
}

// Prepare course and the editor.
$editoroptions = array('maxfiles' => EDITOR_UNLIMITED_FILES, 'maxbytes'=>$CFG->maxbytes, 'trusttext'=>false, 'noclean'=>true);
$overviewfilesoptions = course_overviewfiles_options($course);
if (!empty($course)) {
    // Add context for editor.
    $editoroptions['context'] = $coursecontext;
    $editoroptions['subdirs'] = file_area_contains_subdirs($coursecontext, 'course', 'summary', 0);
    $course = file_prepare_standard_editor($course, 'summary', $editoroptions, $coursecontext, 'course', 'summary', 0);
    if ($overviewfilesoptions) {
        file_prepare_standard_filemanager($course, 'overviewfiles', $overviewfilesoptions, $coursecontext, 'course', 'overviewfiles', 0);
    }

    // Inject current aliases.
    $aliases = $DB->get_records('role_names', array('contextid'=>$coursecontext->id));
    foreach($aliases as $alias) {
        $course->{'role_'.$alias->roleid} = $alias->name;
    }

    // Populate course tags.
    $course->tags = core_tag_tag::get_item_tags_array('core', 'course', $course->id);

} else {
    // Editor should respect category context if course context is not set.
    $editoroptions['context'] = $catcontext;
    $editoroptions['subdirs'] = 0;
    $course = file_prepare_standard_editor($course, 'summary', $editoroptions, null, 'course', 'summary', null);
    if ($overviewfilesoptions) {
        file_prepare_standard_filemanager($course, 'overviewfiles', $overviewfilesoptions, null, 'course', 'overviewfiles', 0);
    }
}

if (isset($course->id)) {
    $dbman = $DB->get_manager();
    $table = new xmldb_table('clickap_course_categories');
    if($dbman->table_exists($table)){
        $sql = "SELECT c.id, hc.year, c.hcid FROM {clickap_course_categories} c
                LEFT JOIN {clickap_hourcategories} hc ON c.hcid = hc.id
                WHERE c.courseid = :courseid";
        $hcs = $DB->get_records_sql($sql, array('courseid'=>$course->id));
        $options = array();
        foreach($hcs as $val){
            $options[0]=$val->year;
            $options[1][]=$val->hcid;
        }
        $course->hourcategory = $options;
    }
    $table = new xmldb_table('clickap_course_info');
    if($dbman->table_exists($table)){
        $info = $DB->get_record('clickap_course_info', array('courseid'=>$course->id));
        $course->unit = !empty($info->unit) ? $info->unit : null;
        $course->model = !empty($info->model) ? $info->model : null;
        $course->credit = !empty($info->credit) ? $info->credit : null;
        $course->city = !empty($info->city) ? $info->city : null;
        $course->hours = !empty($info->hours) ? $info->hours : null;
        $course->longlearn_category = !empty($info->longlearn_category) ? $info->longlearn_category : null;
    }
}

// First create the form.
$args = array(
    'course' => $course,
    'category' => $category,
    'editoroptions' => $editoroptions,
    'returnto' => $returnto,
    'returnurl' => $returnurl
);
$editform = new course_edit_form(null, $args);
if ($editform->is_cancelled()) {
    // The form has been cancelled, take them back to what ever the return to is.
    redirect($returnurl);
} else if ($data = $editform->get_data()) {
    // Process data if submitted.
    if (empty($course->id)) {
        // In creating the course.
        $course = create_course($data, $editoroptions);

        // Get the context of the newly created course.
        $context = context_course::instance($course->id, MUST_EXIST);

        if (!empty($CFG->creatornewroleid) and !is_viewing($context, NULL, 'moodle/role:assign') and !is_enrolled($context, NULL, 'moodle/role:assign')) {
            // Deal with course creators - enrol them internally with default role.
            // Note: This does not respect capabilities, the creator will be assigned the default role.
            // This is an expected behaviour. See MDL-66683 for further details.
            enrol_try_internal_enrol($course->id, $USER->id, $CFG->creatornewroleid);
        }

        // The URL to take them to if they chose save and display.
        $courseurl = new moodle_url('/course/view.php', array('id' => $course->id));

        // If they choose to save and display, and they are not enrolled take them to the enrolments page instead.
        if (!is_enrolled($context) && isset($data->saveanddisplay)) {
            // Redirect to manual enrolment page if possible.
            $instances = enrol_get_instances($course->id, true);
            foreach($instances as $instance) {
                if ($plugin = enrol_get_plugin($instance->enrol)) {
                    if ($plugin->get_manual_enrol_link($instance)) {
                        // We know that the ajax enrol UI will have an option to enrol.
                        $courseurl = new moodle_url('/user/index.php', array('id' => $course->id, 'newcourse' => 1));
                        break;
                    }
                }
            }
        }
    } else {
        // Save any changes to the files used in the editor.
        update_course($data, $editoroptions);
        // Set the URL to take them too if they choose save and display.
        $courseurl = new moodle_url('/course/view.php', array('id' => $course->id));
    }

    /*click-ap*/
    if(isset($data->hcenable) && $data->hcenable ==1){
        if(isset($data->hourcategory[1]) && !empty($data->hourcategory[1])){
            $data->hourcategory = $data->hourcategory[1];
            $hourcategories = $DB->get_records_menu('clickap_course_categories', array('courseid'=>$course->id), '', 'id, hcid');
            
            $add = array_diff($data->hourcategory, $hourcategories);
            $del = array_diff($hourcategories, $data->hourcategory);
            if(!empty($add)){
                $hc = new stdClass();
                foreach($add as $key => $hcid){
                    $hc->courseid = $course->id;
                    $hc->hcid = $hcid;
                    $hc->timemodified = time();
                    $hc->usermodified = $USER->id;
                    $DB->insert_record('clickap_course_categories', $hc);
                }
            }
            
            if(!empty($del)){
               foreach($del as $key => $hcid){
                   $DB->delete_records('clickap_course_categories', array('hcid'=>$hcid,'courseid'=>$course->id));
                   $DB->delete_records('clickap_hourcompletions', array('hcid'=>$hcid,'courseid'=>$course->id));
               }
            }
        }
    }else{
        $sql = "DELETE FROM {clickap_course_categories} WHERE courseid = :courseid";
        $DB->execute($sql, array('courseid'=>$course->id));
        
        $sql = "DELETE FROM {clickap_hourcompletions} WHERE courseid = :courseid";
        $DB->execute($sql, array('courseid'=>$course->id));
    }
    
    $dbman = $DB->get_manager();
    $table = new xmldb_table('clickap_course_info');
    if ($dbman->table_exists($table)) {
        $doupdate = false;
        $info = $DB->get_record('clickap_course_info',array('courseid'=>$course->id));
        if(!$info){
            $info = new stdClass();
            $info->courseid = $course->id;
        }else{
            $doupdate = true;
        }
        
        if(isset($data->hcenable) && $data->hcenable ==1){
            if(isset($data->hours) && !empty($data->hours)){
                $info->hours = $data->hours;
            }
        }else{
            $info->hours = 0;
        }

        if(isset($data->model)){
            $info->model = $data->model;
        }
        if(isset($data->credit)){
            $info->credit = $data->credit;
        }
        if(isset($data->unit)){
            $info->unit = $data->unit;
        }
        if(isset($data->city)){
            $info->city = $data->city;
        }
        if(isset($data->longlearn_category)){
            $info->longlearn_category = $data->longlearn_category;
        }
        
        $info->usermodified = $USER->id;
        $info->timemodified = time();
        
        if($doupdate){
            $DB->update_record('clickap_course_info', $info);
        }else{
            $DB->insert_record('clickap_course_info', $info);
        }
    }
    /*click-ap end*/

    if (isset($data->saveanddisplay)) {
        // Redirect user to newly created/updated course.
        redirect($courseurl);
    } else {
        // Save and return. Take them back to wherever.
        redirect($returnurl);
    }
}

// Print the form.

$site = get_site();

$streditcoursesettings = get_string("editcoursesettings");
$straddnewcourse = get_string("addnewcourse");
$stradministration = get_string("administration");
$strcategories = get_string("categories");

if (!empty($course->id)) {
    // Navigation note: The user is editing a course, the course will exist within the navigation and settings.
    // The navigation will automatically find the Edit settings page under course navigation.
    $pagedesc = $streditcoursesettings;
    $title = $streditcoursesettings;
    $fullname = $course->fullname;
} else {
    // The user is adding a course, this page isn't presented in the site navigation/admin.
    // Adding a new course is part of course category management territory.
    // We'd prefer to use the management interface URL without args.
    $managementurl = new moodle_url('/course/management.php');
    // These are the caps required in order to see the management interface.
    $managementcaps = array('moodle/category:manage', 'moodle/course:create');
    if ($categoryid && !has_any_capability($managementcaps, context_system::instance())) {
        // If the user doesn't have either manage caps then they can only manage within the given category.
        $managementurl->param('categoryid', $categoryid);
    }
    // Because the course category management interfaces are buried in the admin tree and that is loaded by ajax
    // we need to manually tell the navigation we need it loaded. The second arg does this.
    navigation_node::override_active_url($managementurl, true);

    $pagedesc = $straddnewcourse;
    $title = "$site->shortname: $straddnewcourse";
    $fullname = $site->fullname;
    $PAGE->navbar->add($pagedesc);
}

$PAGE->set_title($title);
$PAGE->set_heading($fullname);

echo $OUTPUT->header();
echo $OUTPUT->heading($pagedesc);

$editform->display();

echo $OUTPUT->footer();
