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

$type = optional_param('type', NULL, PARAM_TEXT);
$action = optional_param('action', NULL, PARAM_TEXT);

$urlparams = array();
if (!empty($type)) {
    $urlparams['type'] = $type;
}
$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url('/admin/clickap/code/index.php');
$PAGE->set_pagelayout('admin');

if(!clickap_code_table_exists()) {
	redirect(new moodle_url($CFG->wwwroot.'/'));
}
require_login();
require_capability('clickap/code:view', $context);

$canedit = false;
if(has_capability('clickap/code:manage', $context, $USER)){
    $canedit = true;
}

$title = get_string('pluginname', 'clickap_code');

if (!empty($action) && confirm_sesskey()) {
    $returnurl = new moodle_url('/admin/clickap/code/index.php?type='.$type);
    switch ($action) {
        case 'moveup' :
            clickap_code_moveup(required_param('id', PARAM_INT), $returnurl);
            break;
        case 'movedown' :
            clickap_code_movedown(required_param('id', PARAM_INT), $returnurl);            
            break; 
    }
    redirect($returnurl);
}

$PAGE->set_title($SITE->shortname.':'.$title);
echo $OUTPUT->header();
$OUTPUT->heading($title);
 
$mform = new \clickap_code\form\filter_form(null, array('type'=>$type));
$mform->display();

$url = new moodle_url('/admin/clickap/code/edit.php', array('type'=>$type));
$action = html_writer::link($url, get_string('createnewcode', 'clickap_code'));
echo html_writer::div(($action), 'listing-actions');

if (!empty($type)) {
    if ($formdata = $mform->get_data() or !empty($type)) {
        $table = clickap_code_get_list($type, $canedit);
        echo '<div class="clearer">&nbsp;</div>';
        echo html_writer::table($table);    
    }
	
}
echo $OUTPUT->footer();