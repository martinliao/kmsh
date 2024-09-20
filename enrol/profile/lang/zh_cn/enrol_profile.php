<?php
/**
 *
 * @package    enrol
 * @subpackage profile
 * @author     Maria Tan(CLICK-AP)
 * @author     Martin Freeman(CLICK-AP)
 * @copyright  CLICK-AP {@link https://www.click-ap.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = '科室/职称选课';
$string['defaultrole'] = '预设角色';
$string['defaultrole_desc'] = 'Default role used to enrol people with this plugin (each instance can override this).';
$string['attrsyntax'] = '科室选课条件';
$string['attrsyntax_help'] = '<p>符合此条件的使用者会自动加选至课程中</p>';
$string['profile:config'] = 'Configure plugin instances';
$string['profile:manage'] = 'Manage enrolled users';
$string['profile:unenrol'] = 'Unenrol users from the course';
$string['profile:unenrolself'] = 'Unenrol self from the course';
$string['ajax-error'] = 'An error occured';
$string['ajax-okpurged'] = '成功, 使用者已被清空';
$string['ajax-okforced'] = '成功, {$a} 使用者已加选进课程';
$string['purge'] = '清空选课';
$string['force'] = '加选使用者';
$string['confirmforce'] = '将会根据条件加选使用者';
$string['confirmpurge'] = '将移除透过批次选课加选的使用者';
$string['mappings'] = 'Shibboleth mappings';
$string['mappings_desc'] = 'When using Shibboleth authentification, this plugin can automatically update a user\'s profile upon each login.<br><br>For instance, if you want to update the user\'s <code>homeorganizationtype</code> profile field with the Shibboleth attribute <code>Shib-HomeOrganizationType</code> (provided that is the environment variable available to the server during login), you can enter on one line: <code>Shib-HomeOrganizationType:homeorganizationtype</code><br>You may enter as many lines as needed.<br><br>To not use this feature or if you don\'t use Shibboleth authentification, simple leave this empty.';
$string['profilefields'] = '个人资料栏位';
$string['profilefields_desc'] = '为设定课程的科室职称选课时，可使用哪些个人资料栏位作为条件
如果您没有在这选择任何栏位，此模组将无法在课程使用。';
$string['removewhenexpired'] = '当使用者不符合条件时，将使用者退选';
$string['removewhenexpired_help'] = '每次登入时检查使用者是否符合条件，当使用者不符合条件时，自动将他退选';

$string['sendcoursewelcomemessage'] = '传送课程的欢迎讯息';
$string['sendcoursewelcomemessage_help'] = '若启用，会透过email收到一封欢迎的信。';
$string['welcometocourse'] = '欢迎来到 {$a}';
$string['welcometocoursetext'] = '欢迎来到 {$a->coursename}！

即日起即可登入平台，进入课程开始学习:

  {$a->courseurl}';
$string['customwelcomemessage'] = '自订欢迎讯息';
$string['customwelcomemessage_help'] = '自订的欢迎讯息可以以纯文字加入或是Moodle自动格式，包含Html标前及多语系标籤。

包含以下佔位符号的讯息：

*课程名称{$a->coursename}
*连接到用户的个人资料页面{$a->profileurl}';
$string['anyrule'] = "所有用户";
$string['notice'] = "注项事项";
$string['notice_explain'] = "<p>新增或变更选课条件后,请先按下'储存变更'钮储存设定,</p>
<p>并重新回到选课编修页面,执行'加选使用者'将人员加入课程参与名单.</p>
<p>如要将条件不符人员从课程内退选,请勾选'当使用者不符合条件时,将使用者退选',或先执行'清空选课';</p>
<p>完成后再执行'加选使用者'重新更新选课名单.</p>";