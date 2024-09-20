<?php
/**
 * 
 * @package clickap_hourcategories
 * @author 2018 Mary Tan
 * @copyright CLICK-AP (https://www.click-ap.com)
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once('../../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once('locallib.php');

$id = optional_param('id', null, PARAM_INT);
$currentyear = optional_param('year', 0, PARAM_INT);
$action = optional_param('action', "", PARAM_RAW);

$urlparams = array();
if (!empty($currentyear)) {
    $urlparams['year'] = $currentyear;
    $thisYear = $currentyear;
}else{
    $thisYear = date('Y')-1911;
    $urlparams['year'] = $thisYear;
}
admin_externalpage_setup('hourcategories');
$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url('/admin/clickap/hourcategories/index.php', $urlparams);

$PAGE->set_pagelayout('admin');
require_login();
require_capability('clickap/hourcategories:view', $context);

if(!empty($action) && confirm_sesskey()){
    if($action == "automatic"){
        clickap_hourcategories_automatic_create();
    }
}

$canedit = false;
if(has_capability('clickap/hourcategories:manage', $context, $USER)){
    $canedit = true;
}
$title = get_string('pluginname', 'clickap_hourcategories');
$PAGE->set_title($title);
$PAGE->set_heading($title);

$table = new html_table();
$table->id = 'clickap_hourcategories';
$table->width = '100%';
$table->attributes = array('class'=>'admintable generaltable','style'=>'white-space: nowrap; display: table;');//table-layout:fixed;
if($canedit){
    $table->head = array('&nbsp;', get_string('year', 'clickap_hourcategories'), get_string('categoryname', 'clickap_hourcategories'), get_string('condition', 'clickap_hourcategories'), get_string('categoryvisible', 'clickap_hourcategories'), get_string('edit'));
    $table->size = array('10%', '10%', '30%', '20%', '20%', '10%');
    $table->align = array('center', 'center', 'left', 'center', 'center', 'center');
}else{
    $table->head = array('&nbsp;', get_string('year', 'clickap_hourcategories'), get_string('categoryname', 'clickap_hourcategories'), get_string('condition', 'clickap_hourcategories'), get_string('categoryvisible', 'clickap_hourcategories'));
    $table->size = array('10%', '10%', '40%', '20%', '20%');
    $table->align = array('center', 'center', 'left', 'center', 'center');
}

$cnt = 0;
$category = $DB->get_records('clickap_hourcategories', array('year'=>$thisYear), 'year desc, sortorder'); 
foreach($category as $m){
    $data = array();
    $data[] = ++$cnt;
    $data[] = $m->year;
    $data[] = $m->name;
    $data[] = $m->requirement;
    $visible = '';
    switch($m->visible){
        case 0:$visible = get_string('hide','clickap_hourcategories'); break;
        case 1:$visible = get_string('show','clickap_hourcategories'); break;
        default:break;
    }
    $data[] = $visible;
    if($canedit){
        //Edit
        $options = array('title' => get_string('edit'));
        $image = '<img src="'.$OUTPUT->image_url('t/edit').'" alt="'.$options['title'].'" />';
        $function = html_writer::link(new moodle_url('index.php', array('id' => $m->id, 'year' => $thisYear)), $image, $options);
        //Delete
        if($m->type != 1){
            $options = array('title' => get_string('delete'));
            $image = '<img src="'.$OUTPUT->image_url('t/delete').'" alt="'.$options['title'].'" />';
            $function .= '&nbsp;'.html_writer::link(new moodle_url('delete.php', array('id' => $m->id, 'type' => 'category')), $image, $options);
        }
        $data[] = $function;
    }       
    $table->data[] = new html_table_row($data);
}

if($canedit){
    if(!empty($id)){
        $category = $DB->get_record('clickap_hourcategories', array('id'=>$id), '*', MUST_EXIST);
    }else {
        $category = array();
        if($currentyear != NULL){
            $category['year'] = $currentyear;
        }
    }
    
    $editform = new clickap_hourcategories_edit_form(NULL, array('category'=>$category));
    if($editform->is_cancelled()) {
        redirect(new moodle_url($CFG->wwwroot.'/admin/clickap/hourcategories/index.php'), $urlparams);
    }else if($data = $editform->get_data()) {
        if(empty($data->id)) {
            if($id = clickap_hourcategory_create($data)){
                $event = \clickap_hourcategories\event\category_created::create(array('context' => $context, 'objectid' => $id, 
                    'other' => array()));
                $event->trigger();
            }
        } else {
            if(clickap_hourcategory_update($data)){
                $event = \clickap_hourcategories\event\category_updated::create(array('context' => $context, 'objectid' => $data->id, 
                    'other' => array()));
                $event->trigger();
            }
        }
        redirect(new moodle_url($CFG->wwwroot.'/admin/clickap/hourcategories/index.php', $urlparams));
    }
}

echo $OUTPUT->header();
echo $OUTPUT->heading($title);
echo html_writer::start_tag('div', array('class'=>"row-fluid"));
$options = $DB->get_records_sql_menu('SELECT DISTINCT(year), year as value  FROM {clickap_hourcategories} WHERE year != 0 ORDER BY year DESC');
$selectYear = $OUTPUT->single_select(new moodle_url('/admin/clickap/hourcategories/index.php'),'year', $options, $thisYear);
echo html_writer::tag('div', $selectYear, array('class'=>'span6', 'style'=>'display: inline-flex;'));
if($canedit){
    $automatic = $OUTPUT->single_button(new moodle_url('/admin/clickap/hourcategories/index.php?action=automatic'), get_string('createayear', 'clickap_hourcategories'));
    $duplicate = $OUTPUT->single_button(new moodle_url('/admin/clickap/hourcategories/duplicate.php'), get_string('duplicate_categories', 'clickap_hourcategories'));
    echo html_writer::tag('div', $automatic.$duplicate, array('class'=>'span6', 'style'=>'display: inline-flex'));
}
echo html_writer::end_tag('div');
    
echo html_writer::table($table);
//$event = \clickap_hourcategories\event\categories_viewed::create(array('context' => $context, 'other' => array()));
//$event->trigger();

if($canedit){
    $editform->display();
}
echo $OUTPUT->footer();