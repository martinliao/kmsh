<?php
/**
 * 
 * @package clickap_hourcategories
 * @author 2018 Mary Tan
 * @copyright CLICK-AP (https://www.click-ap.com)
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once('../../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once('locallib.php');

admin_externalpage_setup('hourcategories');
$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url('/admin/clickap/hourcategories/duplicate.php');

$PAGE->set_pagelayout('admin');
require_login();
require_capability('clickap/hourcategories:manage', $context);

$title = get_string('duplicate_categories', 'clickap_hourcategories');
$PAGE->set_title($title);
$PAGE->set_heading($title);
   
$editform = new clickap_hourcategories_duplicate_form(NULL, array());
if($editform->is_cancelled()) {
    redirect(new moodle_url($CFG->wwwroot.'/admin/clickap/hourcategories/index.php'));
}else if($data = $editform->get_data()) {
    clickap_hourcategories_duplicate_categories($data);
    redirect(new moodle_url($CFG->wwwroot.'/admin/clickap/hourcategories/index.php', array('year'=>$data->dest)), get_string('duplicate_success', 'clickap_hourcategories'));
}
echo $OUTPUT->header();
echo $OUTPUT->heading($title);
$editform->display();
echo $OUTPUT->footer();