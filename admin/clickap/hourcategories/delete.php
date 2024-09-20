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

$id = required_param('id', PARAM_INT);
$delete = optional_param('delete', '', PARAM_ALPHANUM);

$urlparams = array();
if (!empty($id)) {
    $data = $DB->get_record('clickap_hourcategories',array('id'=>$id));
    $urlparams['id'] = $id;
}
admin_externalpage_setup('hourcategories');
$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url('/admin/clickap/hourcategories/delete.php', $urlparams);
$PAGE->set_pagelayout('admin');
require_login();
require_capability('clickap/hourcategories:manage', $context);

$returnurl = new moodle_url('/admin/clickap/hourcategories/index.php', array('year'=>$data->year));
if (isset($data) && $delete === md5($data->timemodified)) {
    require_sesskey();
    if(clickap_hourcategory_delete($id)){
        $context = context_system::instance();
        $event = \clickap_hourcategories\event\category_deleted::create(array('context' => $context, 'objectid' => $id, 
            'other' => array()));
        $event->trigger();
    }
    redirect($returnurl);
}

if(isset($data) && $data->type == 0) {
    echo $OUTPUT->header();
    
    $candelete = true;
    $dbman = $DB->get_manager();
    $table = new xmldb_table('clickap_hourcredit_profile');
    if ($dbman->table_exists($table)) {
        if($DB->record_exists('clickap_hourcredit_profile', array('hcid'=>$id))){
            $candelete = false;
            $message = get_string('categoryused_profile', 'clickap_hourcategories');
            echo $OUTPUT->notification($message, \core\output\notification::NOTIFY_INFO);
            echo $OUTPUT->continue_button($returnurl);
        }
    }
    
    if($candelete){
        $message = "";
        if($DB->record_exists('clickap_course_categories', array('hcid'=>$id))){
            $message .= get_string('categoryused', 'clickap_hourcategories'). "<br />";
        }
        $message .= get_string('confirm-delete','clickap_hourcategories', $data->name);
        
        $continueurl = new moodle_url('/admin/clickap/hourcategories/delete.php', array('id' => $id, 'delete' => md5($data->timemodified)));
        
        $PAGE->set_title("$SITE->fullname: $data->name");
        $PAGE->set_heading($SITE->fullname);
        
        echo $OUTPUT->confirm($message, $continueurl, $returnurl);
    }
    echo $OUTPUT->footer();
}