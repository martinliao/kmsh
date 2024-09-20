<?php
/**
 * Version details.
 *
 * @package    blocks
 * @subpackage smartmenu
 * 
 * @author     Mary Chen (mary@click-ap.com)
 * @author     Jack (jack@click-ap.com)
 * @copyright  Click-AP {@line https://www.click-ap.com}
 * @license    http://www.click-ap.com/copyleft/gpl.html GNU GPL v3 or later
 */
require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->dirroot .'/course/renderer.php');
require_once('lib.php');

$id = required_param('id', PARAM_INT);
if (!$course = $DB->get_record("course", array("id"=>$id))) {
    print_error("invalidcourseid");
}

$context = context_course::instance($course->id);
require_login($course->id);
$PAGE->set_course($course);
$PAGE->set_pagelayout('course');
$PAGE->set_pagetype('course-view-information');
$PAGE->set_url('/blocks/smartmenu/information.php', array('id' => $course->id));
$PAGE->set_title(get_string("summaryof", "", $course->fullname));
$PAGE->set_heading(get_string('courseinfo'));
$PAGE->navbar->add(get_string('summary'));
echo $OUTPUT->header();

if(has_capability('moodle/course:update',$context)){         
    $editUrl = new moodle_url('/local/mooccourse/editinfo.php', array('id' =>$course->id));
    $editButton = $OUTPUT->single_button($editUrl, get_string('editcourseinfo', 'local_mooccourse'), 'post');
    echo html_writer::tag('div', $editButton, array('class'=>'editbutton'));
}

if(!empty($course->semester)){
    $dbman = $DB->get_manager();
    $table = new xmldb_table('clickap_excludedeadline');
    if($dbman->table_exists($table)){
        if($deadline = $DB->get_field('clickap_excludedeadline', 'deadline',array('semester'=>$course->semester))){
            echo html_writer::tag('div', get_string('notification_uploaddeadline', 'local_mooccourse', date('Y/m/d H:i',$deadline)), array('class'=>'editbutton'));
        }
    }
    /**
    * if exclude norm_status or outline_status = 1, then show the info
    */
    $clickaps = get_plugin_list('clickap');
    if(array_key_exists("excludecourse", $clickaps)){
        if($exclude = $DB->get_record('clickap_excludecourses',array('idnumber'=>$course->idnumber))){
            if($exclude->norm_status == 1 && $exclude->outline_status == 1){
                $outputStr = get_string('notification_exclude','local_mooccourse');
            }else if($exclude->norm_status == 1){
                $outputStr = get_string('notification_exclude_norm','local_mooccourse');
            }else if($exclude->outline_status == 1){
                $outputStr = get_string('notification_exclude_outline','local_mooccourse');
            }
            if(isset($outputStr)){
                echo html_writer::tag('div', $outputStr, array('class'=>'editbutton'));
            }
        }
    }
}

$renderer = $PAGE->get_renderer('local_mooccourse');
$renderable = new \local_mooccourse\output\course_info($course, false);

echo $renderer->render($renderable);

block_smartmenu_information_standard_log_view($course);

echo $OUTPUT->footer();
