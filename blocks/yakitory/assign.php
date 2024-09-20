<?php
/**
 * @package   block_yakitory
 * @copyright 2021 MARY CHEN  {@link https://www.click-ap.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v1 or later
 */
require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/' . $CFG->admin . '/roles/lib.php');
require_once($CFG->dirroot . '/blocks/yakitory/classes/user_selector.php');

$id       = required_param('id', PARAM_INT);
$courseid = required_param('courseid', PARAM_INT);
$userid   = optional_param('userid', null, PARAM_INT);
$returnto = optional_param('returnto', null, PARAM_ALPHANUMEXT);

if(!$userid){
    $userid = $USER->id;
    $username = $USER->username;
}

$config = get_config('yakitory');
$sql = "SELECT * FROM {yakitory_videos} 
        WHERE id = :id AND username = :username AND client_host =:host AND client_id =:clientid
              AND course !='Share' AND state = 'completed'";
$videos = $DB->get_record_sql($sql, array('id' => $id, 'username' => $username, 'host' => $config->video_host, 'clientid' => $config->client_id));

if(!$videos){
    echo $OUTPUT->header();
    $returnto = $CFG->wwwroot . '/blocks/yakitory/report.php?courseid='.$courseid;
    redirect($returnto, get_string('videoshare_error', 'block_yakitory'), null, \core\output\notification::NOTIFY_ERROR);
    echo $OUTPUT->footer();
    exit;
}

$course = get_course($courseid);
$context =  context_course::instance($courseid);
require_login($course, false);
require_capability('block/yakitory:upload', $context,$userid);
$PAGE->set_context($context);
$PAGE->set_pagelayout('report');
$PAGE->set_url('/blocks/yakitory/assign.php', array('id' => $id, 'courseid' => $courseid));

if ($videos) {
    $options = array('videoid' => $videos->videoid, 'courseid' => $courseid);

    $potentialuserselector = new block_yakitory_potential_users_selector('addselect', $options);
    $currentuserselector = new block_yakitory_existing_user_holders('removeselect', $options);

    if (optional_param('add', false, PARAM_BOOL) && confirm_sesskey()) {
        $userstoassign = $potentialuserselector->get_selected_users();
        if (!empty($userstoassign)) {
           foreach ($userstoassign as $adduser) {
                if(!$DB->record_exists('yakitory_videos', array('client_id'=>$videos->client_id, 'username'=>$adduser->username, 'videoid'=>$videos->videoid, 'course'=>'Share'))){
                    $add = new stdClass();
                    $add = clone($videos);
                    unset($add->id);
                    $add->username     = $adduser->username;
                    $add->supplier     = $username;
                    $add->course       = 'Share';
                    $add->timecreated  = time();
                    $add->timemodified = time();
                    $DB->insert_record('yakitory_videos', $add);
                    
                    $event = \block_yakitory\event\videoshare_created::create(array(
                        'courseid' => $courseid,
                        'objectid' => $videos->videoid ,
                        'context'  => $context,
                        'other'    => array(
                            'orign'    => $videos->id,
                            'adduser'  => $adduser->username,
                            'supplier' => $username
                        )
                    ));
                    $event->trigger();
                }
            }

            $potentialuserselector->invalidate_selected_users();
            $currentuserselector->invalidate_selected_users();

            // Counts have changed, so reload.
            list($assignableroles, $assigncounts, $nameswithcounts) = get_assignable_roles($context, ROLENAME_BOTH, true);
        }
    }

    // Process incoming unassignments.
    if (optional_param('remove', false, PARAM_BOOL) && confirm_sesskey()) {
        $userstounassign = $currentuserselector->get_selected_users();
        if (!empty($userstounassign)) {
            foreach ($userstounassign as $removeuser) {
                // Unassign only roles that are added manually, no messing with other components!!!
                if($remove = $DB->get_record('yakitory_videos', array('client_id'=>$videos->client_id, 'client_host'=>$videos->client_host, 'videoid'=>$videos->videoid, 'username'=>$removeuser->username, 'course'=>'Share'))){
                    $DB->delete_records('yakitory_videos', array('id'=>$remove->id));

                    $event = \block_yakitory\event\videoshare_deleted::create(array(
                        'courseid' => $courseid,
                        'objectid' => $videos->videoid,
                        'context'  => $context,
                        'other'    => array(
                            'orign'      => $remove->id,
                            'removeuser' => $removeuser->id,
                            'supplier'   => $username
                        )
                    ));
                    $event->trigger();
                }
            }

            $potentialuserselector->invalidate_selected_users();
            $currentuserselector->invalidate_selected_users();

            // Counts have changed, so reload.
            list($assignableroles, $assigncounts, $nameswithcounts) = get_assignable_roles($context, ROLENAME_BOTH, true);
        }
    }
}

if (!empty($user) && ($user->id != $userid)) {
    $PAGE->navigation->extend_for_user($user);
    $PAGE->navbar->includesettingsbase = true;
}

$pluginname = get_string('pluginname', 'block_yakitory');
$reporttitle = get_string('videoreport', 'block_yakitory');
$title      = get_string('videoshare', 'block_yakitory');
//$PAGE->navbar->add($pluginname, new moodle_url('/blocks/yakitory/upload.php', array('courseid'=>$course->id, 'returnurl'=>$returnurl)));
$PAGE->navbar->add($reporttitle, new moodle_url('/blocks/yakitory/report.php', array('courseid'=>$course->id)));
$PAGE->navbar->add($title);
$PAGE->set_title("$course->fullname: $title");
$PAGE->set_heading($course->fullname);

echo $OUTPUT->header();
echo $OUTPUT->heading_with_help($videos->filename .'-'. $title, 'videoshare', 'block_yakitory');

if ($videos->id) {
    // Show UI for assigning a particular role to users.
    // Print a warning if we are assigning system roles.
    if ($context->contextlevel == CONTEXT_SYSTEM) {
        echo $OUTPUT->notification(get_string('globalroleswarning', 'core_role'));
    }

    // Print the form.
    $assignurl = new moodle_url($PAGE->url, array('id'=>$videos->id));
    if ($returnto !== null) {
        $assignurl->param('return', $returnto);
    }
?>
<form id="assignform" method="post" action="<?php echo $assignurl ?>"><div>
  <input type="hidden" name="sesskey" value="<?php echo sesskey() ?>" />
  <input type="hidden" name="videoid" value="<?php echo $videos->videoid ?>" />
  <input type="hidden" name="context" value="<?php echo $context->id ?>" />
  <table id="assigningrole" summary="" class="admintable roleassigntable generaltable" cellspacing="0">
    <tr>
      <td id="existingcell">
          <p><label for="removeselect"><?php print_string('extusers', 'core_role'); ?></label></p>
          <?php $currentuserselector->display() ?>
      </td>
      <td id="buttonscell">
          <div id="addcontrols">
              <input name="add" id="add" type="submit" value="<?php echo $OUTPUT->larrow().'&nbsp;'.get_string('add'); ?>" title="<?php print_string('add'); ?>" /><br />
          </div>

          <div id="removecontrols">
              <input name="remove" id="remove" type="submit" value="<?php echo get_string('remove').'&nbsp;'.$OUTPUT->rarrow(); ?>" title="<?php print_string('remove'); ?>" />
          </div>
      </td>
      <td id="potentialcell">
          <p><label for="addselect"><?php print_string('potusers', 'core_role'); ?></label></p>
          <?php $potentialuserselector->display() ?>
      </td>
    </tr>
  </table>
</div></form>

<?php
    $PAGE->requires->js_init_call('M.core_role.init_add_assign_page');

}
echo $OUTPUT->footer();
