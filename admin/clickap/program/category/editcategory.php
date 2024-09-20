<?php

require_once('../../../../config.php');
require_once($CFG->dirroot.'/admin/clickap/program/category/editcategory_form.php');

$id = optional_param('id', 0, PARAM_INT);

require_login();

$url = new moodle_url('/admin/clickap/program/category/editcategory.php');
$context = context_system::instance();
$PAGE->set_context($context);

if ($id) {
    $category = $DB->get_record('program_category', array('id'=>$id), '*', MUST_EXIST);
    $programid = $category->programid;
    $url->param('id', $id);
    $strtitle = get_string('editcategorysettings');
    $title = $strtitle;
    $fullname = format_string($category->name);

} else {
    $programid = required_param('programid', PARAM_INT);
    $url->param('programid', $programid);

    $category = new stdClass();
    $category->id = 0;
    $category->programid = $programid;
    $strtitle = get_string("addnewcategory");
    $title = "$SITE->shortname: ".get_string('addnewcategory');
    $fullname = $SITE->fullname;
}

//require_capability('moodle/category:manage', $context);
$PAGE->set_url($url);
$PAGE->set_pagelayout('admin');
$PAGE->set_title($title);
$PAGE->set_heading($fullname);

$mform = new clickap_program_editcategory_form(null, array(
    'category' => $category,
    'programid' => $category->programid,
    'context' => $context
));

$manageurl = new moodle_url('/admin/clickap/program/category/management.php');
$manageurl->param('programid', $programid);
if ($mform->is_cancelled()) {
    if ($id) {
        $manageurl->param('categoryid', $id);
    }
    redirect($manageurl);
} else if ($data = $mform->get_data()) {
    
    $data->usermodified = $USER->id;
    $data->timemodified = time();
    
    if (!empty($data->id)) {
        $DB->update_record('program_category', $data);
        //$coursecat->update($data);
    } else {
        $max_sort = $DB->get_field_sql('SELECT max(sortorder) FROM {program_category} WHERE programid=:programid', array('programid'=>$programid));
        $data->sortorder = ++$max_sort;
        $DB->insert_record('program_category', $data);
    }
    $manageurl->param('categoryid', $category->id);
    redirect($manageurl);
}

echo $OUTPUT->header();
echo $OUTPUT->heading($strtitle);
$mform->display();
echo $OUTPUT->footer();
