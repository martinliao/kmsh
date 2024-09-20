<?php
/**
 * 
 * @package    block_certverify
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

function block_certverify_myprofile_navigation(core_user\output\myprofile\tree $tree, $user, $iscurrentuser, $course) {
    global $CFG, $USER;
    require_once($CFG->dirroot.'/blocks/certverify/locallib.php');

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

    $url = new moodle_url('/blocks/certverify/request.php', array('userid' => $user->id));
    $node = new core_user\output\myprofile\node('miscellaneous', 'certverify',
            get_string('certrequest', 'block_certverify'), null, $url);
    $tree->add_node($node);

    $context = context_course::instance($course->id);
    $isManager = block_certverify_isManager($user);
    //if (has_capability('block/certverify:viewreport', $context) && $isManager) {
    if ($isManager) {
        $url = new moodle_url('/blocks/certverify/report.php');
        $node = new core_user\output\myprofile\node('reports', 'reportcertverify',
                get_string('report', 'block_certverify'), null, $url);
        $tree->add_node($node);
    }
    return true;
}

function block_certverify_get_verify_manager(){
    $admins = get_admins();
    foreach($admins as $uid => $user){
        $users[] = $user->username;
    }
    return implode(',', $users);
}
 
function block_certverify_attachments_options($user = null) {//used
    global $USER;
    if(empty($user)){
        $user = $USER;
    }

    $config = get_config('block_certverify');
    if (empty($config->maxattachments) OR empty($config->maxbytes)) {
        return null;
    }
    
    $options = array(
        'maxfiles' => $config->maxattachments,
        'maxbytes' => $config->maxbytes,
        'subdirs' => 0,
        'accepted_types' => ['.jpg', '.png', '.pdf']
    );

    return $options;
}

//template file download
function block_certverify_pluginfile($user,
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
    $fullpath = rtrim('/' . $context->id . '/block_certverify/' . $filearea . '/' .
                      $relativepath, '/');
    $file = $fs->get_file_by_hash(sha1($fullpath));

    if (!$file || $file->is_directory()) {
        return false;
    }
    send_stored_file($file, null, null, true);
}
