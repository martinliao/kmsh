<?php
/**
 * course section 
 * @package    block
 * @subpackage smartmenu
 * 
 * @author     Mary Chen (mary@click-ap.com)
 * @author     Jack (jack@click-ap.com)
 * @copyright  Click-AP {@line https://www.click-ap.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v2 or later
 */
require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->dirroot .'/course/renderer.php');
require_once($CFG->dirroot.'/course/format/lib.php');
global $CFG;

$id = required_param('id', PARAM_INT);
$course = $DB->get_record('course', array('id'=>$id), '*', MUST_EXIST);

$numsections = course_get_format($course)->get_last_section_number();
$context = context_course::instance($course->id, MUST_EXIST);
require_login($course);

$strsection    = get_string('section');
$strsectionname  = get_string('sectionname');
$strname         = get_string('name');
$strintro        = get_string('moduleintro');
$strlastmodified = get_string('lastmodified');

$PAGE->set_pagelayout('course');
$PAGE->set_pagetype('course-view-section');
$PAGE->set_url('/blocks/smartmenu/course_section.php', array('id' => $course->id));
$PAGE->set_title($course->shortname.': '.$strsection);
$PAGE->set_heading($course->fullname);
$PAGE->navbar->add($strsection);
echo $OUTPUT->header();         

//course_create_sections_if_missing($course->id,range(0, $numsections));

if (has_capability('moodle/course:update', $context)) {
    //echo '<div align="right"><a href="'. $CFG->wwwroot . '/blocks/smartmenu/edit_section.php?id='.$id.'">'.get_string('editallsection','block_smartmenu').'</a></div>';
    $editUrl = new moodle_url('/blocks/smartmenu/edit_section.php', array('id' =>$id));
    $editButton = $OUTPUT->single_button($editUrl, get_string('editallsection', 'block_smartmenu'), 'post');
    echo html_writer::tag('div', $editButton, array('class'=>'editbutton'));
}
$table = new html_table();
$table->attributes['class'] = 'generaltable section_index';
$table->head  = array ($strsection, $strsectionname);
$table->align = array ('center', 'left');
$table->width = '100%';
$table->size = array('20%','80%');

$modinfo = get_fast_modinfo($course);

foreach ($modinfo->get_section_info_all() as $section => $thissection) {
    if ($section == 0) {
        continue;
    }
    if ($section > $numsections) {
        continue;
    }

    $name = $thissection->name;
    if(empty($name)){
        $name = get_section_name($course, $section);
    }
    $sectionnum = get_string('sectionnumber', 'block_smartmenu', $section);                   
    $table->data[] = array ($sectionnum, $name);   
}
echo html_writer::table($table);
echo $OUTPUT->footer();