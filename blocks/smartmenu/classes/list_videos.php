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
$PAGE->set_pagetype('course-view-videos');
$PAGE->set_url('/blocks/course_menu/list_videos.php', array('id' => $course->id));
require_course_login($course, true);

$context = context_course::instance($course->id, MUST_EXIST);

$config = get_config('block_course_menu');
$allowtypes = explode(',', $config->activitytypes_videos);
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

$str_resources = get_string('course_videos', 'block_course_menu');
$str_section   = get_string('section');
$str_name      = get_string('name');

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

foreach ($resources as $modname=>$instances) {
    $additionalfields = '';
    if (plugin_supports('mod', $modname, FEATURE_MOD_INTRO)) {
        $additionalfields = ',intro,introformat';
    }
    $resources[$modname] = $DB->get_records_list($modname, 'id', $instances, 'id', 'id,name'.$additionalfields);
}

$table             = new html_table();
$table->attributes = array('class'=>'admintable generaltable','style'=>'white-space: nowrap; display: table;');//table-layout:fixed;
//$table->head       = array($str_section, $str_name, get_string('download'));
$table->head       = array($str_section, $str_name);
$table->align      = array('center', 'left');
$table->size       = array("20%","70%");
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
    if (!empty($cm->icon) && ($cm->icon=="f/mpeg-24")) {
        $icon = '<img src="'.$OUTPUT->image_url($cm->icon).'" class="activityicon" alt="'.get_string('modulename', $cm->modname).'" style ="height:24px;weight:24px"/> ';
    } else if($cm->modname=="videofile" or $cm->modname=="videoquiz") {
        $icon = '<img src="'.$OUTPUT->image_url('icon', $cm->modname).'" class="activityicon" alt="'.get_string('modulename', $cm->modname).'" style ="height:24px;weight:24px"/> ';
    } else if (!empty($cm->icon)) {
        $icon = '<img src="'.$OUTPUT->image_url($cm->icon).'" class="activityicon" alt="'.get_string('modulename', $cm->modname).'" style ="height:24px;weight:24px"/> ';
    } else {
        $icon = '<img src="'.$OUTPUT->image_url('icon', $cm->modname).'" class="activityicon" alt="'.get_string('modulename', $cm->modname).'" style ="height:24px;weight:24px"/> ';
    }
    
    $download = "";
    $class = $cm->visible ? '' : 'class="dimmed"';
    if($cm->modname=="videofile"){
        $resource = $DB->get_record('videofile', array('id'=>$cm->instance), '*', MUST_EXIST); 
        $fs = get_file_storage();
        $moudles_context = context_module::instance($cm->id);  
        $files = $fs->get_area_files($moudles_context->id, 'mod_videofile', 'videos', 0, 'sortorder DESC, id ASC', true);
        $viewresource = "<a $class $extra href=\"$CFG->wwwroot/mod/$cm->modname/view.php?id=$cm->id\" target='_blank'>".$icon.format_string($resource->name)."</a>";
        if (count($files) >= 1) {    
            //require_once($CFG->dirroot.'/mod/videofile/locallib.php');    
            $file = reset($files);
            $path = 'pluginfile.php/'.$moudles_context->id.'/mod_videofile/videos/'.$resource->responsive.$file->get_filepath().$file->get_filename();
            $download = '<a href="'.$CFG->wwwroot.'/'.$path.'" target=\"_blank"\">Download</a>';
        }
        unset($files);
    } else{
        $viewresource="<a $class $extra href=\"$CFG->wwwroot/mod/$cm->modname/view.php?id=$cm->id\" target='_blank'>".$icon.format_string($resources[$cm->modname][$cm->instance]->name)."</a>";
    }
    //$table->data[] = array ($printsection, $viewresource, $download);
    $table->data[] = array ($printsection, $viewresource);
    $beforesection = $cm->sectionnum;
}
echo html_writer::table($table);
echo $OUTPUT->footer();