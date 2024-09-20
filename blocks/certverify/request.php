<?php
/**
 * 
 * @package    block_certverify
 * @author     Elaine Chen(CLICK-AP)
 * @copyright  CLICK-AP {@link https://www.click-ap.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/filelib.php');
require_once('locallib.php');
require_once('lib.php');

require_login(null, false);
if (isguestuser()) {
    throw new require_login_exception('Guests are not allowed here.');
}

$userid = optional_param('userid', 0, PARAM_INT);
if (empty($userid)) {
    $userid = $USER->id;   
}
$user = $DB->get_record('user', array('id'=>$userid));

$context = context_user::instance($user->id);
require_capability('moodle/course:request', $context);

$url = new moodle_url('/blocks/certverify/request.php?userid='.$userid);
$returnurl = new moodle_url('/my', array());
$PAGE->set_context($context);
$PAGE->set_url($url);

$config = get_config('block_certverify');
$allowauth = explode(',', $config->authmethod);
if(!in_array($user->auth, $allowauth)){
    notice(get_string('notallowapply', 'block_certverify'), $returnurl);
}

$title = get_string('certrequest', 'block_certverify');
$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->set_pagelayout('standard');
$PAGE->navbar->add(get_string('myhome'), $returnurl);// Create the breadcrumb.
$PAGE->navbar->add($title);
 
$filesoptions = block_certverify_attachments_options($user);
if ($filesoptions) {
    file_prepare_standard_filemanager($user, 'attachments', $filesoptions, $context, 'user', 'cert_attachments', null);
}
$args = array('filesoptions' => $filesoptions);
$requestform = new block_certverify_request_form($url, $args);
    
if ($requestform->is_cancelled()){
    redirect($returnurl);
} else if ($data = $requestform->get_data()) {
    $apply = block_certverify_create_certificate($data, $user, $filesoptions);

    notice(get_string('request_success','block_certverify'), $returnurl);
}
echo $OUTPUT->header();
echo $OUTPUT->heading($title);
// Show the request form.
$requestform->display();
echo $OUTPUT->footer();
