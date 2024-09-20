<?php
/**
 * Version details.
 *
 * @package    clickap_code
 * @copyright  2021 CLICK-AP {@link https://www.click-ap.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once('../../../config.php');
require_once($CFG->dirroot . '/admin/clickap/code/lib.php');

$id = optional_param('id', null, PARAM_INT);
$type = optional_param('type', null, PARAM_TEXT);
$urlparams = array();
if (!empty($id)) {
    $urlparams['id'] = $id;
}
$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url('/admin/clickap/code/edit.php', $urlparams);
$PAGE->set_pagelayout('admin');
if(!clickap_code_table_exists()) {
    redirect(new moodle_url($CFG->wwwroot.'/'));
}
require_login();
require_capability('clickap/code:manage', $context);

if($id) {
	$codedata = $DB->get_record(CODE_TABLE, array('id'=>$id), '*', MUST_EXIST);
}
else {
	$codedata = array();
}

$editform = new clickap_code\form\edit_form(NULL, array('data'=>$codedata, 'type'=>$type));
if($editform->is_cancelled()) {
	redirect(new moodle_url($CFG->wwwroot.'/admin/clickap/code/index.php'));
} 

else if($data = $editform->get_data()) {
	if(empty($codedata->id)) {
		$code = clickap_code_create($data);
	} else {
		clickap_code_update($data);
	}

    redirect(new moodle_url('/admin/clickap/code/index.php', array('type' => $data->type)));
}

if (!empty($codedata->id)) {
	//$PAGE->navbar->add($streditschool);
	$title = $codedata->name;
} else {
	//$PAGE->navbar->add($straddschool);
	$title = $SITE->fullname;
}

$PAGE->set_title($title);
$PAGE->set_heading($title);

echo $OUTPUT->header();
echo $OUTPUT->heading($title);

$editform->display();

echo $OUTPUT->footer();

