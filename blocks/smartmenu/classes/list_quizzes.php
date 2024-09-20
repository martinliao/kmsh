<?php
/**
 * @package    block
 * @subpackage course_menu
 * course infomation
 * @copyright  2016 Mary Chen (mary@click-ap.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v2 or later
 */
require_once(dirname(__FILE__) . '/../../../config.php');

$id = required_param('id', PARAM_INT);

if (!$course = $DB->get_record('course', array('id' => $id), '*', MUST_EXIST)) {
    print_error('invalidcourseid');
}

$PAGE->set_pagelayout('course');
$PAGE->set_pagetype('course-view-quizzes');
$PAGE->set_url('/blocks/course_menu/list_quizzes.php', array('id' => $course->id));
require_course_login($course, true);

$context = context_course::instance($course->id, MUST_EXIST);

$config = get_config('block_course_menu');
$allowtypes = explode(',', $config->activitytypes_quizzes);
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

$str_resources = get_string('course_quizzes','block_course_menu');
$str_duedate   = get_string('duedate','block_course_menu');
$str_grade     = get_string('grade','block_course_menu');
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
$table->head       = array($str_section, $str_name, $str_duedate, $str_grade);
$table->align      = array('center', 'left', 'center', 'center');
$table->size       = array("20%","45%","20%","15%");
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
    } else {
        $icon = '<img src="'.$OUTPUT->image_url('icon', $cm->modname).'" class="activityicon" alt="'.get_string('modulename', $cm->modname).'" /> ';
    }
    $download = "";
    $class = $cm->visible ? '' : 'class="dimmed"';

    $resource = $DB->get_record($cm->modname, array('id'=>$cm->instance), '*', MUST_EXIST);
    $viewurl = "<a $class $extra href=\"$CFG->wwwroot/mod/$cm->modname/view.php?id=$cm->id\" target='_blank'>".$icon.format_string($resource->name)."</a>";
    $duedate = '-';
    $grade = '-';
    if($cm->modname == "assign"){
        if(!empty($resource->allowsubmissionsfromdate) && !empty($resource->duedate)){
            $duedate = date('Y-m-d H:i:s',$resource->allowsubmissionsfromdate).'<br />|<br />'.date('Y-m-d H:i:s',$resource->duedate);
        }else if(!empty($resource->allowsubmissionsfromdate)){
            $duedate = date('Y-m-d H:i:s',$resource->allowsubmissionsfromdate).' ~ ';            
        }else if(!empty($resource->duedate)){
            $duedate = ' ~ '.date('Y-m-d H:i:s',$resource->duedate);            
        }
    }else if($cm->modname == "quiz"){
        if(!empty($resource->timeopen) && !empty($resource->timeclose)){
            $duedate = date('Y-m-d H:i:s',$resource->timeopen).'<br />|<br />'.date('Y-m-d H:i:s',$resource->timeclose);
        }else if(!empty($resource->timeopen)){
            $duedate = date('Y-m-d H:i:s',$resource->timeopen).' ~ ';            
        }else if(!empty($resource->timeclose)){
            $duedate = ' ~ '.date('Y-m-d H:i:s',$resource->timeclose);            
        }
    }else if($cm->modname == "workshop"){
        if(!empty($resource->submissionstart) && !empty($resource->submissionend)){
            $duedate = date('Y-m-d H:i:s',$resource->submissionstart).'<br />|<br />'.date('Y-m-d H:i:s',$resource->submissionend);
        }else if(!empty($resource->submissionstart)){
            $duedate = date('Y-m-d H:i:s',$resource->submissionstart).' ~ ';            
        }else if(!empty($resource->submissionend)){
            $duedate = ' ~ '.date('Y-m-d H:i:s',$resource->submissionend);            
        }
        
        require_once($CFG->dirroot.'/mod/workshop/locallib.php');
        $workshop = new workshop($resource, $cm->get_course_module_record(), $course);
        $finalgrades = $workshop->get_gradebook_grades($USER->id);
        if (!empty($finalgrades)) {
            if (!empty($finalgrades->submissiongrade)) {
                $grade .= $finalgrades->submissiongrade->str_long_grade;
            }
            if (!empty($finalgrades->assessmentgrade)) {
                $grade .= '<br />'.$finalgrades->assessmentgrade->str_long_grade;
            }
        }
    }
    
    if($cm->modname != "workshop"){
        require_once($CFG->libdir . '/grade/grade_grade.php');
        $grade_item = $DB->get_records('grade_items', array('courseid'=>$course->id, 'itemmodule'=>$cm->modname, 'iteminstance'=>$resource->id), '', 'id,hidden');
        foreach($grade_item as $item){
            $grade_grade = grade_grade::fetch(array('itemid'=>$item->id,'userid'=>$USER->id));
            if ($item->hidden == 0){
                if(!empty($grade_grade->finalgrade)){
                    $finalgrades = $grade_grade->finalgrade;
                    $grade = round($finalgrades, 2);
                }
            }
        }
    }
    $table->data[] = array ($printsection, $viewurl, $duedate, $grade);
}

echo html_writer::table($table);
echo $OUTPUT->footer();