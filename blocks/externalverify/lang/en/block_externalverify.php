<?php
/**
 * plugin infomation
 * 
 * @package    block
 * @subpackage externalverify
 * @author     Elaine Chen(CLICK-AP)
 * @copyright  CLICK-AP {@link https://www.click-ap.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['agree'] = 'Agree';
$string['applydate'] = 'Apply date';
$string['applydetail'] = 'Apply Detail';
$string['applyhistory'] = 'Apply history';
$string['applylist'] = 'Apply list';
$string['applylist_desc'] = 'My pending review application list.';
$string['applyuser'] = 'Apply user';
$string['attachments'] = 'Attachment';
$string['authmethod'] = 'Auth Method';
$string['btnagree'] = 'Agree';
$string['btncancel'] = 'Cancel';
$string['btnreject'] = 'Reject';
$string['cancel'] = 'Cancel';
$string['configmaxattachments'] = 'Max attachments';
$string['configmaxbytes'] = 'Max bytes';
$string['confirmusers'] = 'External course confirm';
$string['confirmusers_desc'] = 'Waiting list for you pending review.';
$string['confirmusers_manager'] = 'External course confirm(Manager)';
$string['courseattachments'] = 'Attachment files';
$string['courseattachments_help'] = 'Attached files';
$string['courserequest'] = 'External Course request';
$string['courserequest_success'] = 'Course request success';
$string['course_city'] = 'city';
$string['course_city_help'] = 'Select the city that create the course';
$string['course_credit'] = 'credit';
$string['course_credit_help'] = 'Select the credit of course.';
$string['course_credits'] = 'credit';
$string['course_credits_help'] = '1 credits equal 18 hours.';
$string['course_credits_rule'] = 'must be input integer.';
$string['course_hourcategories'] = 'Hours category';
$string['course_hourcategories_hour_help'] = 'Choose the hours category of course.';
$string['course_hours'] = 'Hours';
$string['course_hours_help'] = 'Learning hours';
$string['course_hours_rule'] = 'input rule';
$string['course_longlearncategory'] = 'Long learn Category';
$string['course_longlearncategory_help'] = 'Choose the long learn category of course';
$string['course_model'] = 'Model';
$string['course_model_help'] = 'Select the model of course.';
$string['course_unit'] = 'unit';
$string['course_unit_help'] = 'Select the unit that create the course';
$string['enddate'] = 'End time';
$string['enddateerror'] = "The end date can not be less than the start date";
$string['externalverify:myaddinstance'] = 'Add a new external course register block to Dashboard';
$string['expense'] = 'Expense';
$string['expense_rule'] = 'Must input an integer';
$string['filename'] = 'Externalcourse_{$a}';
$string['mail_apply'] = '
<p>Please login and confirm this apply form.</p>
Course Name : {$a->fullname}<br/>
Apply Time : {$a->timecreated}<br/>
Apply : {$a->applyuser}<br/>
';
$string['mail_apply_subject'] = 'Notice : external course waiting confirm';
$string['mail_content'] = 'Mail content.';
$string['mail_content_desc'] = 'Apply reject mail content.';
$string['mail_course'] = '
Course Name : {$a->fullname}<br/>
Apply Time : {$a->timecreated}<br/>
Superior : {$a->supervisor}<br/>
Verify Time : {$a->timemodified}<br/>
Verify Status : <font color=blue>Agree</font>
';
$string['mail_course_reject'] = '
Course Name : {$a->fullname}<br/>
Apply Time : {$a->timecreated}<br/>
Superior : {$a->supervisor}<br/>
Verify Time : {$a->timemodified}<br/>
Verify Status : <font color=red>Reject</font><br/>
Reject Reason : {$a->reason}
';
$string['mail_subject'] = 'Mail subject.';
$string['mail_subject_desc'] = 'Apply reject mail subject.';
$string['mail_subject_title'] = 'Notice : External course apply agree/reject';
$string['manager'] = 'Validator(Manager)';
$string['managerverify'] = 'Wait manager verify({$a})';
$string['maxattachments'] = 'Max attachments';
$string['maxattachmentsize'] = 'Max attachment size';
$string['messageprovider:notification'] = 'Verify notification';
$string['missingattachments'] = 'Missing attachments';
$string['missinghourcategories'] = 'Missing hours category';
$string['missinghours'] = 'Missing hours';
$string['missinglonglearncategory'] = 'Missing long learn category';
$string['missingorg'] = 'must be input course unit.';
$string['myapply'] = 'My apply({$a})';
$string['myverify'] = 'Wait verify({$a})';
$string['notallowapply'] = 'Your are not employee, can not apply external course register.';
$string['officialleave'] = 'Official Leave';
$string['org'] = 'org';
$string['org_help'] = 'The department of teaching.';
$string['ownexpense'] = 'Own Expense';
$string['pluginname'] = 'External course register';
$string['privateleave'] = 'Private Leave';
$string['publicexpense'] = 'Public Expense';
$string['reason'] = 'Reason';
$string['reject'] = 'Reject';
$string['reject_subject'] = 'Notice : External course apply reject';
$string['reject-reason'] = 'Reject reason';
$string['requestattatchment'] = 'Attatchment request';
$string['requestdetails'] = 'Course details';
$string['startdate'] = 'Start time';
$string['status'] = 'Status';
$string['superviorname'] = 'Supervior';
$string['templatefile'] = "Template File";
$string['timeverify1'] = 'Verify time(Supervisor)';
$string['timeverify2'] = 'Verfiy time(Manager)';
$string['typesofexpense'] = 'Types of Expense';
$string['typesofleave'] = 'Types of leave';
$string['validator'] = 'Validator';
$string['verifydate'] = 'Verify date';
$string['verifyhistory'] = 'Verify history';
$string['verify-detail'] = 'External course apply Detail - {$a}';
$string['waitingverify'] = 'Waiting for verify';