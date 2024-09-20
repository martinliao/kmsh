<?php

require_once('../../../../config.php');
require_once($CFG->dirroot . '/admin/clickap/program/lib.php');

$programid = required_param('programid', PARAM_INT);
$categoryid = optional_param('categoryid', null, PARAM_INT);
$current = optional_param('action', 'pcategory', PARAM_TEXT);
$page = optional_param('page', 0, PARAM_INT);
$perpage = optional_param('perpage', 20, PARAM_INT);

require_login();
$program = new program($programid);
$context = $program->get_context();
require_capability('clickap/programs:configurecriteria', $context);
if(empty($categoryid)){
    $sql = "SELECT p.*, count(pc.id) as coursecount
                FROM {program_category} p
                LEFT JOIN {program_category_courses} pc ON pc.categoryid= p.id
                WHERE p.programid = :programid AND p.name =''
                GROUP BY pc.categoryid";
    $category = $DB->get_record_sql($sql, array('programid'=>$programid));
    $categoryid = $category->id;
}else{
    $sql = "SELECT p.*, count(pc.id) as coursecount
                FROM {program_category} p
                LEFT JOIN {program_category_courses} pc ON pc.categoryid= p.id
                WHERE p.programid = :programid AND p.id = :categoryid
                GROUP BY pc.categoryid";
    $category = $DB->get_record_sql($sql, array('programid'=>$programid, 'categoryid'=>$categoryid));
}

$selectedcategoryid = optional_param('selectedcategoryid', null, PARAM_INT);
$courseid = optional_param('courseid', null, PARAM_INT);
$action = optional_param('action', false, PARAM_ALPHA);

$url = new moodle_url('/admin/clickap/program/category/management.php');
$url->param('categoryid', $category->id);
$url->param('programid', $category->programid);
$url->param('page', $page);

$navurl = new moodle_url('/admin/clickap/program/index.php', array('type' => $program->type));
$PAGE->set_pagelayout('admin');
navigation_node::override_active_url($navurl, true);
$PAGE->set_url($url);
$PAGE->set_context($context);
$PAGE->set_heading($program->name);
$PAGE->set_title($program->name);
$PAGE->navbar->add($program->name);

// This is a system level page that operates on other contexts.
require_login();
require_capability('clickap/programs:configurecriteria', $context);
//$PAGE->navbar->add(get_string('coursemgmt', 'admin'), $PAGE->url->out_omit_querystring());
$renderer = $PAGE->get_renderer('clickap_program');

if ($action !== false && confirm_sesskey()) {
    $redirectback = false;
    $redirectmessage = false;
    switch ($action) {
        case 'movecourseup' :
            // They must have specified a category and a course.
            $categoryid = required_param('categoryid', PARAM_INT);
            $courseid = required_param('courseid', PARAM_INT);
            $redirectback = clickap_program_change_course_sortorder_by_one($programid, $categoryid, $courseid, true);
            break;
        case 'movecoursedown' :
            // They must have specified a category and a course.
            $categoryid = required_param('categoryid', PARAM_INT);
            $courseid = required_param('courseid', PARAM_INT);
            $redirectback = clickap_program_change_course_sortorder_by_one($programid, $categoryid, $courseid);
            break;
        case 'movecategoryup' :
            // They must have specified a category.
            $categoryid = required_param('categoryid', PARAM_INT);
            $redirectback = clickap_program_change_category_sortorder_by_one($programid, $categoryid, true);
            break;
        case 'movecategorydown' :
            // They must have specified a category.
            required_param('categoryid', PARAM_INT);
            $categoryid = required_param('categoryid', PARAM_INT);
            $redirectback = clickap_program_change_category_sortorder_by_one($programid, $categoryid);
            break;
        case 'deletecategory':
            // They must have specified a category.
            $categoryid = required_param('categoryid', PARAM_INT);
            $category = $DB->get_record('program_category', array('id'=>$categoryid));
                      
            require_once($CFG->dirroot.'/admin/clickap/program/category/deletecategory_form.php');
            $mform = new clickap_program_deletecategory_form(null, array('programid'=>$programid, 'category'=>$category));
            if ($mform->is_cancelled()) {
                redirect($PAGE->url);
            }

            // Start output.
            /* @var core_course_management_renderer|core_renderer $renderer */
            $renderer = $PAGE->get_renderer('clickap_program');
            echo $renderer->header();
            echo $renderer->heading(get_string('deletecategory', 'moodle', $category->name));
            
            if ($data = $mform->get_data()) {
                if ($data->sure == md5(serialize($categoryid))){
                    if(!empty($data->newparent)) {
                        //move course to new category
                        $sql = "UPDATE {program_category_courses} SET categoryid = :newparent WHERE programid = :programid AND categoryid = :oldcategory";
                        $DB->execute($sql, array('programid'=>$data->programid, 'newparent'=>$data->newparent, 'oldcategory'=>$data->categoryid));
                    }
                    $DB->delete_records_select('program_category', 'programid = :programid AND id = :id', array('programid'=>$data->programid, 'id'=>$data->categoryid));
                    $continueurl = new moodle_url('/admin/clickap/program/category/management.php', array('programid'=>$data->programid));
                    echo $renderer->continue_button($continueurl);
                } else {
                    // Some error in parameters (user is cheating?)
                    $mform->display();
                }
            } else {
                // Display the form.
                $mform->display();
            }
            // Finish output and exit.
            echo $renderer->footer();
            exit();
            break;
        case 'bulkaction':
            // Move courses out of the current category and into a new category.
            // They must have specified a category.
            $oldcategoryid = required_param('categoryid', PARAM_INT);
            $movetoid = required_param('movecoursesto', PARAM_INT);
            $courseids = optional_param_array('bc', false, PARAM_INT);
            if ($courseids === false) {
                break;
            }
            $moveto = $DB->get_record('program_category', array('id'=>$movetoid));
            try {
                // If this fails we want to catch the exception and report it.
                $redirectback = clickap_program_change_course_category($programid, $oldcategoryid, $movetoid, $courseids);
                if ($redirectback) {
                    $a = new stdClass;
                    $a->category = $moveto->name;
                    $a->courses = count($courseids);
                    $redirectmessage = get_string('bulkmovecoursessuccess', 'moodle', $a);
                }
            } catch (moodle_exception $ex) {
                $redirectback = false;
                $notificationsfail[] = $ex->getMessage();
            }
            redirect($PAGE->url, $redirectmessage, 5);
            break;
    }
}

$PAGE->requires->yui_module('moodle-clickap_program-management', 'M.clickap_program.management.init');
$PAGE->requires->strings_for_js(
    array(
        'confirmcoursemove',
        'move',
        'cancel',
        'confirm'
    ),
    'moodle'
);

echo $renderer->header();
//echo $renderer->management_heading($strmanagement, $viewmode, $categoryid);
echo $renderer->print_program_status_box($program);
$renderer->print_program_tabs($programid, $context, $current);
// Start the management form.
$form = array('action' => $PAGE->url->out(), 'method' => 'POST', 'id' => 'coursecat-management');
echo html_writer::start_tag('form', $form);
echo html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'sesskey', 'value' => sesskey()));
echo html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'action', 'value' => 'bulkaction'));

$class = 'columns-2 viewmode-cobmined';
if (!empty($courseid)) {
    $class .= ' course-selected';
}

$class = $class . ' grid-row-r row-fluid';
echo html_writer::start_div($class, array('id'=>'course-category-listings'));
//echo html_writer::start_div('grid-col-5-12 grid-col span5', array('id'=>'category-listing'));
echo $renderer->grid_column_start(5, 'category-listing');

echo $renderer->category_listing($programid, $category);
echo html_writer::end_div();

echo $renderer->grid_column_start(7, 'course-listing');
echo $renderer->course_listing($category, $page, $perpage);
echo html_writer::end_div();

echo html_writer::end_div();
// End of the management form.
echo html_writer::end_tag('form');

echo $renderer->footer();