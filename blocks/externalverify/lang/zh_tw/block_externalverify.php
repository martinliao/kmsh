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

$string['agree'] = '審核同意';
$string['applydate'] = '申請日期';
$string['applydetail'] = '檢視';
$string['applyhistory'] = '申請歷史';
$string['applylist'] = '我的外訓課程登錄申請';
$string['applylist_desc'] = '等待審核中的申請單.';
$string['applyuser'] = '員工編號';
$string['attachments'] = '附件';
$string['authmethod'] = '申請者身分認證';
$string['btnagree'] = '同意';
$string['btncancel'] = '取消申請';
$string['btnreject'] = '駁回';
$string['cancel'] = '自行取消';
$string['configmaxattachments'] = '允許使用者上傳的最大附件數';
$string['configmaxbytes'] = '允許使用者上傳的附件最大容量';
$string['confirmusers'] = '外訓登錄審核';
$string['confirmusers_desc'] = '等待您審核的外訓課程登錄清單.';
$string['confirmusers_manager'] = '外訓登錄管理員複審';
$string['courseattachments'] = '附件';
$string['courseattachments_help'] = '請提供外訓課程登錄相關文件';
$string['courserequest'] = '外訓課程登錄申請';
$string['courserequest_success'] = '外訓課程登錄申請已送出';
$string['course_city'] = '上課縣市';
$string['course_city_help'] = '請選擇課程上課縣市';
$string['course_credit'] = '學位學分';
$string['course_credit_help'] = '請選擇課程學位學分.';
$string['course_credits'] = '課程學分數';
$string['course_credits_rule'] = '必須輸入整數.';
$string['course_credits_help'] = '1學分等於18小時.';
$string['course_hourcategories'] = '時數類別';
$string['course_hourcategories_help'] = '請選擇此課程之年度時數類別(可複選).';
$string['course_hours'] = '學習時數';
$string['course_hours_help'] = '請輸入課程完成後取得的學習時數.';
$string['course_hours_rule'] = '必須輸入整數.';
$string['course_longlearncategory'] = '終身學習類別';
$string['course_longlearncategory_help'] = '請勾選課程所屬終身學習類別.';
$string['course_model'] = '課程主題';
$string['course_model_help'] = '請選擇課程主題類別.';
$string['course_unit'] = '訓練單位';
$string['course_unit_help'] = '請選擇課程訓練單位';
$string['enddate'] = '課程結束時間';
$string['enddateerror'] = "結束時間必須大於開始時間";
$string['externalverify:myaddinstance'] = '新增外訓課程登錄至儀表板';
$string['expense'] = '費用金額';
$string['expense_rule'] = '必須輸入整數';
$string['filename'] = '外訓課程登錄清單_{$a}';
$string['mail_apply'] = '
<p>請登入系統，並進入我的首頁進行簽核.</p>
課程名稱: {$a->fullname}<br/>
申請時間: {$a->timecreated}<br/>
申請人員: {$a->applyuser}<br/>
';
$string['mail_apply_subject'] = '注意:外訓課程申請待審核';
$string['mail_content'] = '通知信內容';
$string['mail_content_desc'] = '外訓補登申請同意/駁回通知信信件的內文.';
$string['mail_course'] = '
課程名稱:{$a->fullname}<br/>
申請時間:{$a->timecreated}<br/>
部門主管:{$a->supervisor}<br/>
審核時間:{$a->timemodified}<br/>
審核狀態:<font color=blue>同意</font>
';
$string['mail_course_reject'] = '
課程名稱:{$a->fullname}<br/>
申請時間:{$a->timecreated}<br/>
部門主管:{$a->supervisor}<br/>
審核時間:{$a->timemodified}<br/>
審核狀態:<font color=red>駁回</font><br/>
駁回原因:{$a->reason}
';
$string['mail_subject'] = '通知信主旨';
$string['mail_subject_desc'] = '外訓補登申請同意/駁回通知信信件的主旨.';
$string['mail_subject_title'] = '注意:外訓課程申請同意/駁回通知';
$string['manager'] = '審核者(管理員)';
$string['managerverify'] = '管理員複審專區({$a})';
$string['maxattachments'] = '附件數';
$string['maxattachmentsize'] = '附件容量';
$string['messageprovider:notification'] = 'Verify notification';
$string['missingattachments'] = '缺乏附件';
$string['missinghourcategories'] = '缺乏時數類別';
$string['missinghours'] = '缺乏學習時數';
$string['missinglonglearncategory'] = '缺乏首頁開課單位';
$string['missingorg'] = '缺乏授課單位';
$string['myapply'] = '我的外訓登錄({$a})';
$string['myverify'] = '主管審核專區({$a})';
$string['notallowapply'] = '您不是內部人員，無法進行外訓補登作業.';
$string['officialleave'] = '公假';
$string['org'] = '授課單位';
$string['org_help'] = '請輸入課程開課單位.';
$string['ownexpense'] = '自費';
$string['pluginname'] = '外訓課程登錄/審核';
$string['privateleave'] = '自假';
$string['publicexpense'] = '公費';
$string['reason'] = '原因';
$string['reject'] = '審核不同意';
$string['reject_subject'] = '注意:外訓課程申請同意/駁回通知';
$string['reject-reason'] = '駁回原因';
$string['requestattatchment'] = '上傳佐證文件';
$string['requestdetails'] = '課程資訊';
$string['startdate'] = '課程開始時間';
$string['status'] = '狀態';
$string['superviorname'] = '主管';
$string['templatefile'] = "表單範本檔";
$string['timeverify1'] = '審核時間(主管)';
$string['timeverify2'] = '審核時間(管理者)';
$string['typesofexpense'] = '費用';
$string['typesofleave'] = '假別';
$string['validator'] = '審核者(直屬主管)';
$string['verifydate'] = '最後更新日期';
$string['verifyhistory'] = '審核歷史';
$string['verify-detail'] = '外訓登錄申請明細 - {$a}';
$string['waitingverify'] = '等待管理者複審';