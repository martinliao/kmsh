<?php
/**
 * 
 * @package    block
 * @subpackage externalverify
 * @author     Elaine Chen(CLICK-AP)
 * @copyright  CLICK-AP {@link https://www.click-ap.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->dirroot . '/blocks/externalverify/locallib.php');

require_login();
$apply_id = required_param('id', PARAM_INT);
$stage = optional_param('stage', null, PARAM_INT);
if (!$apply = $DB->get_record("course_external", array("id"=>$apply_id))) {
    print_error("invalidapplyid");
}

/*
if (($USER->username != $apply->supervisor && !is_siteadmin($USER)) && $USER->id != $apply->userid) {
    print_error("invalidsupervioruserid");
}
*/
$user = $DB->get_record('user', array('id'=>$apply->userid));
profile_load_data($user);

$context = context_user::instance($user->id);
$PAGE->set_context($context);
$PAGE->set_pagetype('my-index-externalverify');
$PAGE->set_pagelayout('mydashboard');
$PAGE->set_url('/blocks/externalverify/detail.php', array('id' => $apply->id));
$PAGE->set_title(get_string("summaryof", "", fullname($user)));
$title = get_string('verify-detail', 'block_externalverify', fullname($user));
$PAGE->set_heading($title);
$PAGE->navbar->add($title);

$params = array('id'=>$apply->id);
$returnurl = $CFG->wwwroot.'/blocks/externalverify/manage.php';
if(!empty($stage)){
    $params['stage'] = $stage;
    $returnurl = new moodle_url('/blocks/externalverify/manage.php?stage=2');
}
$url = new moodle_url('/blocks/externalverify/detail.php', $params);

$args = array(
    'id' => $apply->id,
    'stage'=>$stage
);
$form = new block_externalverify_detail_form($url, $args);
if ($form->is_cancelled()){
    redirect($returnurl);
} else if ($data = $form->get_data()) {
    require_once($CFG->dirroot . '/blocks/externalverify/locallib.php');
    block_external_verify_course($data);
    redirect($returnurl);
}
echo $OUTPUT->header();
echo $OUTPUT->box_start('generalbox');

$table = new html_table();
$table->width = '100%';
//$table->attributes = array('class'=>'admintable generaltable','style'=>'white-space: nowrap; display: table;');//table-layout:fixed;
$table->attributes = array('class'=>'admintable generaltable','style'=>'display: table;');//table-layout:fixed;
$table->head  = array('', '');
$table->align  = array('left', 'left');
$table->size  = array('20%', '80%');

$table->data[] = new html_table_row(array(get_string('firstname'), fullname($user)));
$table->data[] = new html_table_row(array(get_string('username'), $user->username));
$table->data[] = new html_table_row(array(get_string('department'), $user->profile_field_DeptName));
$table->data[] = new html_table_row(array(get_string('institution'), $user->profile_field_InstitutionName));
$table->data[] = new html_table_row(array(get_string('fullnamecourse'), $apply->fullname));
$table->data[] = new html_table_row(array(get_string('org', 'block_externalverify'), $apply->org));
$table->data[] = new html_table_row(array(get_string('expense', 'block_externalverify'), $apply->expense));
/*
$leavetype = ($apply->leavetype==0) ? get_string('officialleave', 'block_externalverify') : get_string('privateleave', 'block_externalverify');
$expensetype = ($apply->expensetype==0) ? get_string('publicexpense', 'block_externalverify') : get_string('ownexpense', 'block_externalverify');
$table->data[] = new html_table_row(array(get_string('typesofleave', 'block_externalverify'), $leavetype));
$table->data[] = new html_table_row(array(get_string('typesofexpense', 'block_externalverify'), $expensetype));
$table->data[] = new html_table_row(array(get_string('expense', 'block_externalverify'), $apply->expense));
*/
$table->data[] = new html_table_row(array(get_string('summary'), $apply->summary));

$plugins = get_plugin_list('clickap');
if(array_key_exists("hourcategories", $plugins)){
    $hourcategory = '';
    if(!empty($apply->hourcategories)){
        $hourcategories = explode(',', $apply->hourcategories);
        foreach($hourcategories as $c){
            if(empty($c)){
                continue;
            }
            if(!empty($hourcategory)){
                $hourcategory .= '<br />';
            }
            $hourcategory .= $DB->get_field('clickap_hourcategories', 'name', array('id'=>$c));
        }
        $table->data[] = new html_table_row(array(get_string('course_hourcategories', 'block_externalverify'), $hourcategory));
    }
}
$table->data[] = new html_table_row(array(get_string('course_hours', 'block_externalverify'), $apply->hours));

if(array_key_exists("longlearn_categories", $plugins)){
    $llcategory = $DB->get_field('longlearn_categories', 'name', array('id'=>$apply->longlearn_category));
    $table->data[] = new html_table_row(array(get_string('course_longlearncategory', 'block_externalverify'),$llcategory));
}
if(array_key_exists("code", $plugins)){
    $model = $DB->get_field('clickap_code', 'name', array('id'=>$apply->model));
    $table->data[] = new html_table_row(array(get_string('course_model', 'block_externalverify'),$model));
    //$table->data[] = new html_table_row(array(get_string('course_hours', 'block_externalverify'), $apply->hours));
    $credit = $DB->get_field('clickap_code', 'name', array('id'=>$apply->credit));
    $table->data[] = new html_table_row(array(get_string('course_credit', 'block_externalverify'), $credit));
    $unit = $DB->get_field('clickap_code', 'name', array('id'=>$apply->unit));
    $table->data[] = new html_table_row(array(get_string('course_unit', 'block_externalverify'), $unit));
    $city = $DB->get_field('clickap_code', 'name', array('id'=>$apply->city));
    $table->data[] = new html_table_row(array(get_string('course_city', 'block_externalverify'), $city));
}
$table->data[] = new html_table_row(array(get_string('startdate', 'block_externalverify'), block_externalverify_get_date_format($apply->startdate)));
$table->data[] = new html_table_row(array(get_string('enddate', 'block_externalverify'), block_externalverify_get_date_format($apply->enddate)));
$table->data[] = new html_table_row(array(get_string('applydate', 'block_externalverify'), block_externalverify_get_date_format($apply->timecreated)));

$attachment = '';
$fs = get_file_storage();
$files = $fs->get_area_files($context->id, 'user', 'externalcourse_attachments', $apply->id, 'sortorder DESC, id ASC', false);
if (count($files) >= 1) {
    foreach($files as $file){
        if(!empty($attachment)){
            $attachment .= '<br />';
        }
        $url = $CFG->wwwroot. '/pluginfile.php/'.$context->id.'/user/externalcourse_attachments/'.$apply->id.'/'.$file->get_filename().'?forcedownload=1';
        $attachment .= '<a href ="'.$url.'">'.$file->get_filename().'</a>';
        
    }
    $table->data[] = new html_table_row(array(get_string('attachments', 'block_externalverify'), $attachment));
}

$supervisors = explode(',', $apply->supervisor);
if($apply->status != 0){
    if(!empty($apply->validator)){
        $verifyuser = $DB->get_record('user', array('id'=>$apply->validator));
        $table->data[] = new html_table_row(array(get_string('timeverify1', 'block_externalverify'), block_externalverify_get_date_format($apply->timeverify1).'('.fullname($verifyuser).')'));
    }

    $managerVerify = get_config('block_externalverify', 'managerverify');
    if($managerVerify AND !empty($apply->manager)){
        $manager = $DB->get_record('user', array('id'=>$apply->manager));
        $table->data[] = new html_table_row(array(get_string('timeverify2', 'block_externalverify'), block_externalverify_get_date_format($apply->timeverify2).'('.fullname($manager).')'));
    }

    $table->data[] = new html_table_row(array(get_string('status'), block_externalverify_get_verify_status($apply->status)));
    $table->data[] = new html_table_row(array(get_string('reason', 'block_externalverify'), $apply->reason));
}

echo html_writer::table($table);
echo $OUTPUT->box_end();

$supervisors = explode(',', $apply->supervisor);
if((in_array($USER->username,$supervisors)) AND $apply->status == 0){
    $form->display();
}else if(($apply->status == 4) AND is_siteadmin($USER)) {
    $form->display();
}

echo $OUTPUT->footer();