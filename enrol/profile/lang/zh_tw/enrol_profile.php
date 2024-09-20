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

$string['pluginname'] = '科室/職稱選課';
$string['defaultrole'] = '預設角色';
$string['defaultrole_desc'] = 'Default role used to enrol people with this plugin (each instance can override this).';
$string['attrsyntax'] = '科室選課條件';
$string['attrsyntax_help'] = '<p>符合此條件的使用者會自動加選至課程中</p>';
$string['profile:config'] = 'Configure plugin instances';
$string['profile:manage'] = 'Manage enrolled users';
$string['profile:unenrol'] = 'Unenrol users from the course';
$string['profile:unenrolself'] = 'Unenrol self from the course';
$string['ajax-error'] = 'An error occured';
$string['ajax-okpurged'] = '成功, 使用者已被清空';
$string['ajax-okforced'] = '成功, {$a} 使用者已加選進課程';
$string['purge'] = '清空選課';
$string['force'] = '加選使用者';
$string['confirmforce'] = '將會根據條件加選使用者';
$string['confirmpurge'] = '將移除透過批次選課加選的使用者';
$string['mappings'] = 'Shibboleth mappings';
$string['mappings_desc'] = 'When using Shibboleth authentification, this plugin can automatically update a user\'s profile upon each login.<br><br>For instance, if you want to update the user\'s <code>homeorganizationtype</code> profile field with the Shibboleth attribute <code>Shib-HomeOrganizationType</code> (provided that is the environment variable available to the server during login), you can enter on one line: <code>Shib-HomeOrganizationType:homeorganizationtype</code><br>You may enter as many lines as needed.<br><br>To not use this feature or if you don\'t use Shibboleth authentification, simple leave this empty.';
$string['profilefields'] = '個人資料欄位';
$string['profilefields_desc'] = '為設定課程的科室職稱選課時，可使用哪些個人資料欄位作為條件
如果您沒有在這選擇任何欄位，此模組將無法在課程使用。';
$string['removewhenexpired'] = '當使用者不符合條件時，將使用者退選';
$string['removewhenexpired_help'] = '每次登入時檢查使用者是否符合條件，當使用者不符合條件時，自動將他退選';

$string['sendcoursewelcomemessage'] = '傳送課程的歡迎訊息';
$string['sendcoursewelcomemessage_help'] = '若啟用，會透過email收到一封歡迎的信。';
$string['welcometocourse'] = '歡迎來到 {$a}';
$string['welcometocoursetext'] = '歡迎來到 {$a->coursename}！

即日起即可登入平台，進入課程開始學習:

  {$a->courseurl}';
$string['customwelcomemessage'] = '自訂歡迎訊息';
$string['customwelcomemessage_help'] = '自訂的歡迎訊息可以以純文字加入或是Moodle自動格式，包含Html標前及多語系標籤。

包含以下佔位符號的訊息：

*課程名稱{$a->coursename}
*連接到用戶的個人資料頁面{$a->profileurl}';
$string['anyrule'] = "所有用戶";
$string['notice'] = "注項事項";
$string['notice_explain'] = "<p>新增或變更選課條件後,請先按下'儲存變更'鈕儲存設定,</p>
<p>並重新回到選課編修頁面,執行'加選使用者'將人員加入課程參與名單.</p>
<p>如要將條件不符人員從課程內退選,請勾選'當使用者不符合條件時,將使用者退選',或先執行'清空選課';</p>
<p>完成後再執行'加選使用者'重新更新選課名單.</p>";