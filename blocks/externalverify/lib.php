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

 /**
 * Add nodes to myprofile page.
 *
 * @param \core_user\output\myprofile\tree $tree Tree object
 * @param stdClass $user user object
 * @param bool $iscurrentuser
 * @param stdClass $course Course object
 *
 * @return bool
 */

function block_externalverify_myprofile_navigation(core_user\output\myprofile\tree $tree, $user, $iscurrentuser, $course) {
    global $USER;
    if (isguestuser() or !isloggedin()) {
        return;
    }

    if (empty($course)) {
        // We want to display these reports under the site context.
        $course = get_fast_modinfo(SITEID)->get_course();
    }
    
    if (\core\session\manager::is_loggedinas() or $USER->id != $user->id) {
        // No peeking at somebody else's sessions!
        return;
    }
    $url = new moodle_url('/blocks/externalverify/request.php', array('userid' => $user->id));
    $node = new core_user\output\myprofile\node('miscellaneous', 'externalverify',
            get_string('courserequest', 'block_externalverify'), null, $url);
    $tree->add_node($node);
    
    return true;
}
 
function block_externalverify_get_verify_supervisor($user){
	global $CFG;
    require_once($CFG->dirroot.'/user/profile/lib.php');
	
    $userprofile = (array)profile_user_record($user->id, false);
    $supervisor = [];
    if(isset($userprofile['Supervisor']) && (!empty($userprofile['Supervisor']))){
        $supervisor[] = $userprofile['Supervisor'];
    }
    else{
        $admins = get_admins();
        foreach($admins as $uid => $user){
            $supervisor[] = $user->username;
        }
    }

    return implode(',', $supervisor);
}

function block_externalverify_get_verify_manager(){
    $admins = get_admins();
    foreach($admins as $uid => $user){
        $users[] = $user->username;
    }
    return implode(',', $users);
}

/*
function block_externalverify_get_supervisor($user){
    global $CFG, $DB;
    require_once($CFG->dirroot.'/admin/clickap/org/lib.php');

    $managers = array();
    $deptids = explode(';', $user->department);
    if(count($deptids) > 1){
        foreach($deptids as $key => $value){
            $thismanagers = org_get_manager($value);
            if(!empty($thismanagers)){
                $managers += $thismanagers;
            }
        }
    } else {
        $managers = org_get_manager($user->department);
    }
    
    if(count($managers) > 0){
        return implode(',', $managers);
    }else{
        $users = array();
        $admins = get_admins();
        foreach($admins as $uid => $user){
            $users[] = $user->username;
        }
        return implode(',', $users);
    }
    return '';
}
*/
 
function block_externalverify_course_attachments_options($user = null) {
    global $CFG, $USER;
    if(empty($user)){
        $user = $USER;
    }

    $config = get_config('block_externalverify');
    if (empty($config->maxattachments) OR empty($config->maxbytes)) {
        return null;
    }
    
    $options = array(
        'maxfiles' => $config->maxattachments,
        'maxbytes' => $config->maxbytes,
        'subdirs' => 0,
        'accepted_types' => '*'
    );
    //$options['context'] = context_user::instance($user->id);
    return $options;
}

/*
function externalverify_pluginfile($user,
                              $cm,
                              $context,
                              $filearea,
                              array $args,
                              $forcedownload,
                              array $options=array()) {
    global $CFG, $DB, $USER;

    require_once(dirname(__FILE__) . '/locallib.php');
    if ($context->contextlevel != CONTEXT_USER) {
        return false;
    }

    if ($filearea !== 'externalcourse_attachments') {
        return false;
    }
    
    $fs = get_file_storage();
    $relativepath = implode('/', $args);
    $fullpath = rtrim('/' . $context->id . '/user/' . $filearea . '/' .
                      $relativepath, '/');
    $file = $fs->get_file_by_hash(sha1($fullpath));

    if (!$file || $file->is_directory()) {
        return false;
    }
    send_stored_file($file);
}
*/
function block_externalverify_pluginfile($user,
                              $cm,
                              $context,
                              $filearea,
                              array $args,
                              $forcedownload,
                              array $options=array()) {

    require_once(dirname(__FILE__) . '/locallib.php');
    if ($context->contextlevel != CONTEXT_SYSTEM) {
        return false;
    }

    if ($filearea !== 'templatefile') {
        return false;
    }
    
    $fs = get_file_storage();
    $relativepath = implode('/', $args);
    $fullpath = rtrim('/' . $context->id . '/block_externalverify/' . $filearea . '/' .
                      $relativepath, '/');
    $file = $fs->get_file_by_hash(sha1($fullpath));

    if (!$file || $file->is_directory()) {
        return false;
    }
    send_stored_file($file, null, null, true);
}