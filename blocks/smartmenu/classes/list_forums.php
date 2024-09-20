<?php
/**
 * @package    block
 * @subpackage course_menu
 * course infomation
 * @copyright  2016 Mary Chen (mary@click-ap.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v2 or later
 */
require_once(dirname(__FILE__) . '/../../../config.php');
require_once("$CFG->libdir/resourcelib.php");

$id = required_param('id', PARAM_INT);

if (!$course = $DB->get_record('course', array('id' => $id), '*', MUST_EXIST)) {
    print_error('invalidcourseid');
}
$PAGE->set_pagelayout('course');
$PAGE->set_pagetype('course-view-forums');
$PAGE->set_url('/blocks/course_menu/list_forums.php', array('id' => $course->id));
require_course_login($course, true);

$context = context_course::instance($course->id, MUST_EXIST);

$config = get_config('block_course_menu');
$allowtypes = explode(',', $config->activitytypes_forums);
$component = "''";
foreach($allowtypes as $type){
    $component .= ",'".$type."'";
}
$whereselect = "visible=1 AND name in (".$component.")";
$allmodules = $DB->get_records_select('modules', $whereselect);

$modules = array();
foreach ($allmodules as $key=>$module) {
    $modname = $module->name;
    $libfile = "$CFG->dirroot/mod/$modname/lib.php";
    if (!file_exists($libfile)) {
        continue;
    }
    $modules[$modname] = get_string('modulename', $modname);
}

$str_resources   = get_string('course_forums', 'block_course_menu');
$str_intro       = get_string('intro', 'block_course_menu');
$str_discussions = get_string('discussions', 'block_course_menu');
$str_section     = get_string('section');
$str_name        = get_string('name');

$PAGE->set_title($course->shortname.': '.$str_resources);
$PAGE->set_heading($course->fullname);
$PAGE->navbar->add($str_resources);
echo $OUTPUT->header();

$modinfo = get_fast_modinfo($course);
$usesections = course_format_uses_sections($course->format);
$cms = array();
$resources = array();
foreach ($modinfo->cms as $cm) {
    if (!$cm->uservisible) {
        continue;
    }
    if (!array_key_exists($cm->modname, $modules)) {
        continue;
    }
    if (!$cm->has_view()) {
        continue;
    }
    $cms[$cm->id] = $cm;
    $resources[$cm->modname][] = $cm->instance;
}

// preload instances
foreach ($resources as $modname=>$instances) {
    $additionalfields = '';
    if (plugin_supports('mod', $modname, FEATURE_MOD_INTRO)) {
        $additionalfields = ',intro,introformat';
    }
    $resources[$modname] = $DB->get_records_list($modname, 'id', $instances, 'id', 'id,name'.$additionalfields);
}

$table             = new html_table();
$table->attributes = array('class'=>'admintable generaltable','style'=>'white-space: nowrap; display: table;');//table-layout:fixed;
$table->head       = array($str_section, $str_name, $str_intro, $str_discussions);
$table->align      = array('center', 'left', 'left', 'center');
$table->size       = array("20%","40%","30%","10%");
$table->width      = '100%';

$beforesection='';
$currentsection = '';
foreach ($cms as $cm) {
    if (!isset($resources[$cm->modname][$cm->instance])) {
        continue;
    }
    if (!$cm->visible) {
        continue;
    }
    $printsection = '';
    if ($usesections) {
        if($cm->sectionnum == 0 || $cm->sectionnum > 52){
                continue; 
        } else {
            $printsection = get_section_name($course, $cm->sectionnum);         
        }
        if($beforesection == $cm->sectionnum){
            $printsection = '';   
        }else{
            $beforesection = $cm->sectionnum;
        }
    }
    $icon = "";
    $extra = empty($cm->extra) ? '' : $cm->extra;
    if (!empty($cm->icon)) {
        $icon = '<img src="'.$OUTPUT->image_url($cm->icon).'" class="activityicon" alt="'.get_string('modulename', $cm->modname).'" /> ';
    }
    $discussionlink = '&nbsp;';
    $intro = "";
    $class = $cm->visible ? '' : 'class="dimmed"';
    if($cm->modname=="forum"){
        $resource = $DB->get_record('forum', array('id'=>$cm->instance), '*', MUST_EXIST); 
        $count = forum_count_discussions($resource, $cm, $course);
        $viewresource="<a $class $extra href=\"$CFG->wwwroot/mod/$cm->modname/view.php?id=$cm->id\" target='_blank'>".$icon.format_string($resource->name)."</a>";
        $intro = $resource->intro;   
        $discussionlink = "<a href=\"$CFG->wwwroot/mod/forum/view.php?f=$resource->id\" $class>".$count."</a>"; 
    }
    $table->data[] = array ($printsection, $viewresource, $intro, $discussionlink);
    $beforesection = $cm->sectionnum;
}
echo html_writer::table($table);
echo $OUTPUT->footer();