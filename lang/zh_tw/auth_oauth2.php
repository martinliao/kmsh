<?php
// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Strings for component 'auth_oauth2', language 'zh_tw', version '3.9'.
 *
 * @package     auth_oauth2
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['accountexists'] = '在這網站已經有一個人使用這一用戶名稱，若這是您的帳號，請輸入您的用戶名稱和密碼，並透過您的個人偏好頁面，把它加入成為連結式登入。';
$string['alreadylinked'] = '外部帳戶已經綁定到本站的一個帳戶了';
$string['auth_oauth2description'] = '依據 OAuth 2 標準的認證';
$string['auth_oauth2settings'] = 'OAuth 2 的認證設定';
$string['confirmaccountemail'] = '{$a->fullname} 您好,

在\'{$a->sitename}\' 有人使用您的電子郵件申請了一個新帳號。

要確認您的新帳戶，請連結到此網址：
{$a->link}

在大多數郵件程式中，應該顯示為您可以點按的藍色連結。 如果不行，請將超連結剪下並貼到您的瀏覽器視窗上方的位址欄中。

如果您需要幫助，請聯繫網站管理員，
{$a->admin}';
$string['confirmaccountemailsubject'] = '{$a}：帳號確認';
$string['confirmationinvalid'] = '確認的超連結無效或已過期。 請再次登入，系統會再傳送新的確認電子郵件。';
$string['confirmationpending'] = '此帳號正等待電子郵件確認';
$string['confirmlinkedloginemail'] = '{$a->fullname}，您好

我們收到您要求綁定 {$a->issuername} 帳號 {$a->linkedemail} 作為您登入 \'{$a->sitename}\' 的帳號和電子郵件信箱。

若確定您真的要求綁定這些登入資訊，請點按下列網址連結，即可完成綁定：

{$a->link}

在大多數郵件程式中，應該顯示為您可以點按的藍色連結。 如果不行，請將超連結剪下並貼到您的瀏覽器視窗上方的位址欄中。

如果您需要幫助，請聯繫網站管理員，
{$a->admin}

如果不是您要這麼做，則可能是其他人試圖破壞您的帳戶。
請立即網站管理員聯繫。';
$string['confirmlinkedloginemailsubject'] = '{$a}：綁定登入帳號的資訊';
$string['createnewlinkedlogin'] = '綁定一個新帳戶 ({$a})';
$string['emailconfirmlink'] = '綁定您的帳戶';
$string['emailconfirmlinksent'] = '<p>發現此電子郵件地址屬於現有帳戶的，但是尚未完成綁定。</p>
   <p>在您可以登入前，帳戶必須先完成綁定確認。</p>
   <p>一封電子郵件應該已發送到您的信箱<b>{$a}</b>。</p>
   <p>它包含簡易的指引如何連結綁定到您的帳戶。</p>
   <p>如果您有任何困難，請與網站管理員聯繫。</p>';
$string['emailpasswordchangeinfosubject'] = '{$a} ：更改密碼的資訊';
$string['info'] = '外部帳戶';
$string['issuer'] = 'OAuth 2 服務';
$string['issuernologin'] = '這一發行者無法用來登入';
$string['linkedlogins'] = '綁定登入方式';
$string['linkedloginshelp'] = '綁定登入方式的輔助說明';
$string['loginerror_authenticationfailed'] = '認證過程已失敗';
$string['loginerror_cannotcreateaccounts'] = '找不到有你的 email 位址的帳號';
$string['loginerror_invaliddomain'] = '在這一網站不允許使用這一電子郵件地址';
$string['loginerror_nouserinfo'] = '沒有回傳用戶訊息，這OAuth 2服務可能配置錯誤。';
$string['noissuersavailable'] = '所有配置的 OAuth 2 服務都不允許你連結到登入帳號';
$string['notenabled'] = '抱歉，OAuth 2 認證外掛程式沒有被啟用';
$string['notloggedindebug'] = '登入失敗。理由：{$a}';
$string['oauth2:managelinkedlogins'] = '管理自己綁定的登入帳戶';
$string['pluginname'] = 'OAuth 2';
