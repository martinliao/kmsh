<?php
/**
 * Capabilities
 *
 * @package     report
 * @subpackage  course_files
 * @author      James & Mary <service@click-ap.com>
 * @copyright   Click-AP <service@click-ap.com>
 * @license     http://www.click-ap.com GNU GPL v2 or later
 */

use core_reportbuilder\system_report_factory;

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once(dirname(__FILE__) . '/locallib.php');
require_once($CFG->libdir.'/adminlib.php');

$filters['user'] = optional_param('user', null, PARAM_TEXT);
$filters['certid'] = optional_param('certid', 0, PARAM_INT);
$filters['status'] = optional_param('status', null, PARAM_INT);
$download = optional_param('download', '', PARAM_ALPHA);

require_login();

$context = context_system::instance();
//require_capability('block/certverify:viewreport', $context);
$PAGE->set_context($context);
$returnurl = new moodle_url('/blocks/certverify/report.php', $filters);
$PAGE->set_url($returnurl);
$title = get_string('report', 'block_certverify');

$args = array();
$isManager = block_certverify_isManager($USER);
if(has_capability('block/certverify:viewreport', $context)){
    admin_externalpage_setup('blockcertverifyreport', '', null, '', array('pagelayout'=>'report'));

    $depts = block_certverify_get_depts();
    $args = array('depts' => $depts);
}else if(!$isManager) {
    echo $OUTPUT->header();
    echo $OUTPUT->heading($title);
    echo $OUTPUT->notification(get_string('nopermissionstoviewreport','block_certverify'), 'notifyproblem');
    echo $OUTPUT->footer();
}

$mform = new block_certverify_filter_form(null, $args); 
if ($mform->is_cancelled()) {
    unset($SESSION->certverify);
    redirect($returnurl);
}
else if ($formdata = $mform->get_data()) {
    $mform->set_data($formdata);

    unset($SESSION->certverify);
    $SESSION->certverify['user'] = $formdata->user;
    $SESSION->certverify['certid'] = $formdata->certid;
    $SESSION->certverify['status'] = $formdata->status;
    if(isset($formdata->depts)){
        $SESSION->certverify['depts'] = implode(',', $formdata->depts);
    }
}

if(isset($SESSION->certverify)){
    if(isset($SESSION->certverify['user'])){
        $filters['user'] = $SESSION->certverify['user'];
    }
    if(isset($SESSION->certverify['depts'])){
        $filters['certid'] = $SESSION->certverify['certid'];
    }
    if(isset($SESSION->certverify['status'])){
        $filters['status'] = $SESSION->certverify['status'];
    }
    if(isset($SESSION->certverify['depts'])){
        $filters['depts'] = $SESSION->certverify['depts'];
    }
    $mform->set_data($filters);
}

$renderer = $PAGE->get_renderer('block_certverify');
$table = new \block_certverify\output\report_table('block-certverify-report-table', $filters);
$table->is_downloading($download, get_string('filename', 'block_certverify', date('Ymd', time())));

if (!$table->is_downloading()) {
    $PAGE->set_heading(format_string($title));
    $PAGE->set_title(format_string($title));

    echo $OUTPUT->header();
    echo $OUTPUT->heading($title);

    $mform->display();
}

$table->define_baseurl($PAGE->url);
$table->out(20, false);

echo $renderer->render($table);

if (!$table->is_downloading()) {
    echo $OUTPUT->footer();
}
