<?php
/**
 * @package   block_uploaddoc
 * @copyright 2018 MARY CHEN  {@link http://www.click-ap.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v1 or later
 */
require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/' . $CFG->admin . '/roles/lib.php');
require_once($CFG->dirroot . '/blocks/uploaddoc/classes/user_selector.php');

$id       = required_param('id', PARAM_INT);
$courseid = required_param('courseid', PARAM_INT);
$userid   = optional_param('userid', null, PARAM_INT);
$returnto = optional_param('returnto', null, PARAM_ALPHANUMEXT);

if(!$userid){
    $userid = $USER->id;
}

$config = get_config('derberus');
$sql = "SELECT * FROM {derberus_files} 
        WHERE id = :id AND userid = :userid AND upload_host =:host AND client_id =:clientid
              AND course !='Share' AND state = 'completed'";
$files = $DB->get_record_sql($sql, array('id' => $id, 'userid' => $userid, 'host' => $config->view_host, 'clientid' => $config->client_id));

if(!$files){
    echo $OUTPUT->header();
    $returnto = $CFG->wwwroot . '/blocks/uploaddoc/report.php?courseid='.$courseid;
    redirect($returnto, get_string('fileshare_error', 'block_uploaddoc'), null, \core\output\notification::NOTIFY_ERROR);
    echo $OUTPUT->footer();
    exit;
}

$course = get_course($courseid);
$context =  context_course::instance($courseid);
require_login($course, false);
require_capability('block/uploaddoc:upload', $context,$userid);
$PAGE->set_context($context);
$PAGE->set_pagelayout('report');
$PAGE->set_url('/blocks/uploaddoc/assign.php', array('id' => $id, 'courseid' => $courseid));

if ($files) {
    $options = array('fileid' => $files->fileid, 'courseid' => $courseid);

    $potentialuserselector = new block_uploaddoc_potential_users_selector('addselect', $options);
    $currentuserselector = new block_uploaddoc_existing_user_holders('removeselect', $options);

    if (optional_param('add', false, PARAM_BOOL) && confirm_sesskey()) {
        $userstoassign = $potentialuserselector->get_selected_users();
        if (!empty($userstoassign)) {
           foreach ($userstoassign as $adduser) {
                if(!$DB->record_exists('derberus_files', array('client_id'=>$files->client_id, 'userid'=>$adduser->id, 'fileid'=>$files->fileid, 'course'=>'Share'))){
                    $add = new stdClass();
                    $add = clone($files);
                    unset($add->id);
                    $add->userid       = $adduser->id;
                    $add->supplier     = $userid;
                    $add->course       = 'Share';
                    $add->timecreated  = time();
                    $add->timemodified = time();
                    $DB->insert_record('derberus_files', $add);
                    
                    $event = \block_uploaddoc\event\fileshare_created::create(array(
                        'courseid' => $courseid,
                        'objectid' => $files->fileid ,
                        'context'  => $context,
                        'other'    => array(
                            'orign'    => $files->id,
                            'adduser'  => $adduser->id,
                            'supplier' => $userid
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
                if($remove = $DB->get_record('derberus_files', array('client_id'=>$files->client_id, 'upload_host'=>$files->upload_host, 'fileid'=>$files->fileid, 'userid'=>$removeuser->id, 'course'=>'Share'))){
                    $DB->delete_records('derberus_files', array('id'=>$remove->id));

                    $event = \block_uploaddoc\event\fileshare_deleted::create(array(
                        'courseid' => $courseid,
                        'objectid' => $files->fileid,
                        'context'  => $context,
                        'other'    => array(
                            'orign'      => $remove->id,
                            'removeuser' => $removeuser->id,
                            'supplier'   => $userid
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

$pluginname = get_string('pluginname', 'block_uploaddoc');
$reporttitle = get_string('filereport', 'block_uploaddoc');
$title      = get_string('fileshare', 'block_uploaddoc');
//$PAGE->navbar->add($pluginname, new moodle_url('/blocks/uploaddoc/upload.php', array('courseid'=>$course->id, 'returnurl'=>$returnurl)));
$PAGE->navbar->add($reporttitle, new moodle_url('/blocks/uploaddoc/report.php', array('courseid'=>$course->id)));
$PAGE->navbar->add($title);
$PAGE->set_title("$course->fullname: $title");
$PAGE->set_heading($course->fullname);

echo $OUTPUT->header();
echo $OUTPUT->heading_with_help($files->filename .'-'. $title, 'fileshare', 'block_uploaddoc');

if ($files->id) {
    // Show UI for assigning a particular role to users.
    // Print a warning if we are assigning system roles.
    if ($context->contextlevel == CONTEXT_SYSTEM) {
        echo $OUTPUT->notification(get_string('globalroleswarning', 'core_role'));
    }

    // Print the form.
    $assignurl = new moodle_url($PAGE->url, array('id'=>$files->id));
    if ($returnto !== null) {
        $assignurl->param('return', $returnto);
    }
?>
<form id="assignform" method="post" action="<?php echo $assignurl ?>"><div>
  <input type="hidden" name="sesskey" value="<?php echo sesskey() ?>" />
  <input type="hidden" name="fileid" value="<?php echo $files->fileid ?>" />
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
