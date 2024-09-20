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

$string['agree'] = '审核同意';
$string['applydate'] = '申请日期';
$string['applydetail'] = '检视';
$string['applyhistory'] = '申请历史';
$string['applylist'] = '我的外训课程登录申请';
$string['applylist_desc'] = '等待审核中的申请单.';
$string['applyuser'] = '员工编号';
$string['attachments'] = '附件';
$string['authmethod'] = '申请者身分认证';
$string['btnagree'] = '同意';
$string['btncancel'] = '取消申请';
$string['btnreject'] = '驳回';
$string['cancel'] = '自行取消';
$string['configmaxattachments'] = '允许使用者上传的最大附件数';
$string['configmaxbytes'] = '允许使用者上传的附件最大容量';
$string['confirmusers'] = '外训登录审核';
$string['confirmusers_desc'] = '等待您审核的外训课程登录清单.';
$string['confirmusers_manager'] = '外训登录管理员复审';
$string['courseattachments'] = '附件';
$string['courseattachments_help'] = '请提供外训课程登录相关文件';
$string['courserequest'] = '外训课程登录申请';
$string['courserequest_success'] = '外训课程登录申请已送出';
$string['course_city'] = '上课县市';
$string['course_city_help'] = '请选择课程上课县市';
$string['course_credit'] = '学位学分';
$string['course_credit_help'] = '请选择课程学位学分.';
$string['course_credits'] = '课程学分数';
$string['course_credits_rule'] = '必须输入整数.';
$string['course_credits_help'] = '1学分等于18小时.';
$string['course_hourcategories'] = '时数类别';
$string['course_hourcategories_help'] = '请选择此课程之年度时数类别(可复选).';
$string['course_hours'] = '学习时数';
$string['course_hours_help'] = '请输入课程完成后取得的学习时数.';
$string['course_hours_rule'] = '必须输入整数.';
$string['course_longlearncategory'] = '终身学习类别';
$string['course_longlearncategory_help'] = '请勾选课程所属终身学习类别.';
$string['course_model'] = '课程主题';
$string['course_model_help'] = '请选择课程主题类别.';
$string['course_unit'] = '训练单位';
$string['course_unit_help'] = '请选择课程训练单位';
$string['enddate'] = '课程结束时间';
$string['enddateerror'] = "结束时间必须大于开始时间";
$string['externalverify:myaddinstance'] = '新增外训课程登录至仪表板';
$string['expense'] = '费用金额';
$string['expense_rule'] = '必须输入整数';
$string['filename'] = '外训课程登录清单_{$a}';
$string['mail_apply'] = '
<p>请登入系统，并进入我的首页进行签核.</p>
课程名称: {$a->fullname}<br/>
申请时间: {$a->timecreated}<br/>
申请人员: {$a->applyuser}<br/>
';
$string['mail_apply_subject'] = '注意:外训课程申请待审核';
$string['mail_content'] = '通知信内容';
$string['mail_content_desc'] = '外训补登申请同意/驳回通知信信件的内文.';
$string['mail_course'] = '
课程名称:{$a->fullname}<br/>
申请时间:{$a->timecreated}<br/>
部门主管:{$a->supervisor}<br/>
审核时间:{$a->timemodified}<br/>
审核状态:<font color=blue>同意</font>
';
$string['mail_course_reject'] = '
课程名称:{$a->fullname}<br/>
申请时间:{$a->timecreated}<br/>
部门主管:{$a->supervisor}<br/>
审核时间:{$a->timemodified}<br/>
审核状态:<font color=red>驳回</font><br/>
驳回原因:{$a->reason}
';
$string['mail_subject'] = '通知信主旨';
$string['mail_subject_desc'] = '外训补登申请同意/驳回通知信信件的主旨.';
$string['mail_subject_title'] = '注意:外训课程申请同意/驳回通知';
$string['manager'] = '审核者(管理员)';
$string['managerverify'] = '管理员复审专区({$a})';
$string['maxattachments'] = '附件数';
$string['maxattachmentsize'] = '附件容量';
$string['messageprovider:notification'] = 'Verify notification';
$string['missingattachments'] = '缺乏附件';
$string['missinghourcategories'] = '缺乏时数类别';
$string['missinghours'] = '缺乏学习时数';
$string['missinglonglearncategory'] = '缺乏首页开课单位';
$string['missingorg'] = '缺乏授课单位';
$string['myapply'] = '我的外训登录({$a})';
$string['myverify'] = '主管审核专区({$a})';
$string['notallowapply'] = '您不是内部人员，无法进行外训补登作业.';
$string['officialleave'] = '公假';
$string['org'] = '授课单位';
$string['org_help'] = '请输入课程开课单位.';
$string['ownexpense'] = '自费';
$string['pluginname'] = '外训课程登录/审核';
$string['privateleave'] = '自假';
$string['publicexpense'] = '公费';
$string['reason'] = '原因';
$string['reject'] = '审核不同意';
$string['reject_subject'] = '注意:外训课程申请同意/驳回通知';
$string['reject-reason'] = '驳回原因';
$string['requestattatchment'] = '上传佐证文件';
$string['requestdetails'] = '课程资讯';
$string['startdate'] = '课程开始时间';
$string['status'] = '状态';
$string['superviorname'] = '主管';
$string['templatefile'] = "表单范本档";
$string['timeverify1'] = '审核时间(主管)';
$string['timeverify2'] = '审核时间(管理者)';
$string['typesofexpense'] = '费用';
$string['typesofleave'] = '假别';
$string['validator'] = '审核者(直属主管)';
$string['verifydate'] = '最后更新日期';
$string['verifyhistory'] = '审核历史';
$string['verify-detail'] = '外训登录申请明细 - {$a}';
$string['waitingverify'] = '等待管理者复审';