<?php
/**
 * 
 * @package    block_certverify
 * @author     Elaine Chen(CLICK-AP)
 * @copyright  CLICK-AP {@link https://www.click-ap.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
$string['agree'] = '審核同意';
$string['all'] = '全部';
$string['applydate'] = '申請日期';
$string['applyhistory'] = '申請歷史';
$string['applylist'] = '我的證照登錄申請';
$string['applylist_desc'] = '已送出登錄申請，正在等待審核的申請單.';
$string['applyuser'] = '申請者';
$string['authmethod'] = '允許登錄者的身分要求';
$string['attachments'] = '證照圖檔';
$string['attachments_help'] = '已取得的證照圖檔';
$string['btnagree'] = '同意';
$string['btncancel'] = '取消';
$string['btnreject'] = '駁回';
$string['cancel'] = '自行取消';
$string['certchoose_error'] = '請選擇要登錄的證照名稱';
$string['certname'] = '證照名稱';
$string['certnumber'] = '證照號碼';
$string['certrequest'] = '證照登錄申請';
$string['certverify:myaddinstance'] = '新增證照登錄區塊至儀表板';
$string['certverify:viewreport'] = '檢視證照登錄報表';
$string['configmaxattachments'] = '允許上傳的最大附件數';
$string['configmaxbytes'] = '允許上傳附件的最大容量';
$string['confirmusers'] = '證照登錄審核';
$string['confirmusers_desc'] = '等待您審核的證照登錄清單.';
$string['dateexpire'] = '證照到期日';
$string['dateexpire_error'] = '證照到期日必需大於發證日.';
$string['dateissued'] = '證照發證日';
$string['deptname'] = '單位';
$string['download_cert'] = "下載證照";
$string['duenotify'] = '證照到期通知';
$string['duenotify_desc'] = '自動於證照登錄之到期日期前n日發送信件通知用戶';
$string['expire'] = '過期的';
$string['expirenotifytask'] = '證照到期通知工作';
$string['filename'] = '證照登錄清單_{$a}';
$string['keyword'] = '證照名稱或號碼';
$string['mail_apply_subject'] = '通知:證照登錄待審核';
$string['mail_apply'] = '
<p>請登入平台並進入儀表板頁面進行證照審核.</p>
證照名稱 : {$a->certname}<br/>
登錄人員 : {$a->applyuser}<br/>
登錄日期 : {$a->timecreated}<br/>
';
$string['mail_content'] = '通知信內容';
$string['mail_content_desc'] = '證照登錄申請，審核同意/駁回的通知信件的內容';
$string['mail_duenotify_subject'] = '通知:證照即將到期';
$string['mail_duenotify'] = '
<p>{$a->applyuser} 您好，您登錄的證照即將到期，資訊如下：</p>
證照名稱 : {$a->certname}<br/>
證照號碼 : {$a->certnumber}<br/>
到期日期 : {$a->dateexpire}<br/>
';
$string['mail_verify'] = '
證照名稱 : {$a->certname}<br/>
審核人員 : {$a->validator}<br/>
審核日期 : {$a->timemodified}<br/>
審核狀態 : <font color=blue>同意</font>
';
$string['mail_verify_reject'] = '
證照名稱 : {$a->certname}<br/>
審核人員 : {$a->validator}<br/>
審核日期 : {$a->timemodified}<br/>
審核狀態 : <font color=red>駁回</font><br/>
駁回原因 : {$a->reason}
';
$string['mail_reject_subject'] = '通知:證照申請同意/駁回通知';
$string['mail_subject'] = '通知信主旨';
$string['mail_subject_desc'] = '證照登錄申請，審核同意/駁回通知信件的主旨';
$string['mail_subject_title'] = '通知:證照申請同意/駁回通知';
$string['maxattachments'] = '附件數';
$string['maxattachmentsize'] = '附件容量限制';
$string['messageprovider:notification'] = '證照登錄通知';
$string['missingattachments'] = '缺少證照圖檔';
$string['missingcertnumber'] = '缺少證照號碼.';
$string['myapply'] = '證照登錄申請({$a})';
$string['myverify'] = '管理員審核專區({$a})';
$string['nopermissionstoviewreport'] = '抱歉，你目前沒有權限去查詢此報表.';
$string['notallowapply'] = '您的身分不允許進行證照登錄，如有疑問請聯繫管理員.';
$string['pluginname'] = '證照登錄';
$string['reason'] = '原因';
$string['reject'] = '審核不同意';
$string['reject_reason'] = '駁回原因';
$string['remark'] = '備註';
$string['report'] = '證照登錄報表';
$string['requestattatchment'] = '上傳證照';
$string['requestdetails'] = '證照資訊';
$string['request_success'] = '證照登錄申請已送出';
$string['status'] = '狀態';
$string['templatefile'] = "範本檔";
$string['timeverify'] = '審核日期';
$string['vaild'] = '有效的';
$string['validator'] = '審核人員';
$string['validators'] = '審核者';
$string['verifyhistory'] = '審核歷史';
