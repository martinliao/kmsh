<?php
/**
 * 
 * @package    block_certverify
 * @author     Elaine Chen(CLICK-AP)
 * @copyright  CLICK-AP {@link https://www.click-ap.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
$string['agree'] = 'Agree';
$string['all'] = 'ALL';
$string['applydate'] = 'Apply date';
$string['applyhistory'] = 'Apply history';
$string['applylist'] = 'Apply list';
$string['applylist_desc'] = 'My pending review application list.';
$string['applyuser'] = 'Apply user';
$string['authmethod'] = 'Auth Method';
$string['attachments'] = 'Attachment';
$string['attachments_help'] = 'Certificate file';
$string['btnagree'] = 'Agree';
$string['btncancel'] = 'Cancel';
$string['btnreject'] = 'Reject';
$string['cancel'] = 'Cancel';
$string['certchoose_error'] = 'Please choose certificate';
$string['certname'] = 'Certificate name';
$string['certnumber'] = 'Certificate number';
$string['certrequest'] = 'Certificate request';
$string['certverify:myaddinstance'] = 'Add a new certificate register block to Dashboard';
$string['certverify:viewreport'] = 'View certificate report';
$string['configmaxattachments'] = 'Max attachments';
$string['configmaxbytes'] = 'Max bytes';
$string['confirmusers'] = 'Certificate confirm';
$string['confirmusers_desc'] = 'Waiting list for you pending review.';
$string['dateexpire'] = 'Certificate expire';
$string['dateexpire_error'] = 'Certificate expire date must be greater than the issued date.';
$string['dateissued'] = 'Certificate issued';
$string['deptname'] = 'Unit';
$string['download_cert'] = "Download certificate";
$string['duenotify'] = 'Expiration notify';
$string['duenotify_desc'] = 'Send a mail to notify the user when certificate Expiring soon.';
$string['expire'] = 'Expire';
$string['expirenotifytask'] = 'Certificate expire notify task';
$string['filename'] = 'Certificate_{$a}';
$string['keyword'] = 'Certificate name or number';
$string['mail_apply_subject'] = 'Notice : Certificate waiting confirm';
$string['mail_apply'] = '
<p>Please login and confirm this apply form.</p>
Certificate Name : {$a->certname}<br/>
Apply Date : {$a->timecreated}<br/>
Apply User: {$a->applyuser}<br/>
';
$string['mail_content'] = 'Mail content.';
$string['mail_content_desc'] = 'Apply reject mail content.';
$string['mail_duenotify_subject'] = 'Notice : Certificate expire soon';
$string['mail_duenotify'] = '
<p>{$a->applyuser} , you register certificate expire soon.</p>
Certificate Name : {$a->certname}<br/>
Certificate No : {$a->certnumber}<br/>
Expire Date : {$a->dateexpire}<br/>
';
$string['mail_verify'] = '
Certificate Name : {$a->certname}<br/>
Validator : {$a->validator}<br/>
Verify Date : {$a->timemodified}<br/>
Verify Status : <font color=blue>Agree</font>
';
$string['mail_verify_reject'] = '
Certificate Name : {$a->certname}<br/>
Validator : {$a->validator}<br/>
Verify Date : {$a->timemodified}<br/>
Verify Status : <font color=red>Reject</font><br/>
Reject Reason : {$a->reason}
';
$string['mail_reject_subject'] = 'Notice : Certificate apply reject';
$string['mail_subject'] = 'Mail subject.';
$string['mail_subject_desc'] = 'Apply reject mail subject.';
$string['mail_subject_title'] = 'Notice : Certificate apply agree/reject';
$string['maxattachments'] = 'Max attachments';
$string['maxattachmentsize'] = 'Max attachment size';
$string['messageprovider:notification'] = 'Verify notify';
$string['missingattachments'] = 'Missing certificate file';
$string['missingcertnumber'] = 'Missing certificate number.';
$string['myapply'] = 'My apply({$a})';
$string['myverify'] = 'Wait verify({$a})';
$string['nopermissionstoviewreport'] = 'You lack the permissions required to view this report';
$string['notallowapply'] = 'Your are not employee, can not apply certificate register.';
$string['pluginname'] = 'Certificate register';
$string['reason'] = 'Reason';
$string['reject'] = 'Reject';
$string['reject_reason'] = 'Reject reason';
$string['remark'] = 'Remark';
$string['report'] = 'Certificate report';
$string['requestattatchment'] = 'Attatchment request';
$string['requestdetails'] = 'Certificate details';
$string['request_success'] = 'Certificate register success';
$string['status'] = 'Status';
$string['templatefile'] = "Template File";
$string['timeverify'] = 'Verify date';
$string['vaild'] = 'Vaild';
$string['validator'] = 'Validator';
$string['validators'] = 'Validators';
$string['verifyhistory'] = 'Verify history';
