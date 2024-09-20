<?php
/**
 * plugin infomation
 * 
 * @package    block
 * @subpackage externalverify
 * @author     Elaine Chen(CLICK-AP)
 * @copyright  CLICK-AP {@link https://www.click-ap.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/course/lib.php');
require_once('lib.php');

require_login(null, false);
if (isguestuser()) {
    throw new require_login_exception('Guests are not allowed here.');
}
$userid = optional_param('userid', $USER->id, PARAM_INT);
$user = $DB->get_record('user', array('id'=>$userid));
$context = context_user::instance($user->id);
require_capability('moodle/course:request', $context);

$url = new moodle_url('/blocks/externalverify/request.php?userid='.$userid);
//$return = optional_param('return', null, PARAM_ALPHANUMEXT);
$returnurl = new moodle_url('/my', array());
$PAGE->set_context($context);
$PAGE->set_url($url);


$config = get_config('block_externalverify');
$allowauth = explode(',', $config->authmethod);
if(!in_array($user->auth, $allowauth)){
    notice(get_string('notallowapply', 'block_externalverify'), $returnurl);
}

$strtitle = get_string('courserequest', 'block_externalverify');
$PAGE->set_title($strtitle);
$PAGE->set_heading($strtitle);
$PAGE->set_pagelayout('standard');
// Create the breadcrumb.
$PAGE->navbar->add(get_string('myhome'), $returnurl);
$PAGE->navbar->add($strtitle);
 
$filesoptions = block_externalverify_course_attachments_options($user);
if ($filesoptions) {
    file_prepare_standard_filemanager($user, 'attachments', $filesoptions, $context, 'user', 'externalcourse_attachments', null);
}
$args = array(
    'filesoptions' => $filesoptions,
);
$requestform = new block_externalverify_request_form($url, $args);
    
if ($requestform->is_cancelled()){
    redirect($returnurl);
} else if ($data = $requestform->get_data()) {
    require_once($CFG->dirroot . '/blocks/externalverify/locallib.php');
    $apply = block_external_create_course($data, $user, $filesoptions);

    notice(get_string('courserequest_success','block_externalverify'), $returnurl);
}
echo $OUTPUT->header();
echo $OUTPUT->heading($strtitle);
// Show the request form.
$requestform->display();
echo $OUTPUT->footer();
