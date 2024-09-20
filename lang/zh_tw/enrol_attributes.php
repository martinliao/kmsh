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
 * Strings for component 'enrol_attributes', language 'zh_tw', version '3.9'.
 *
 * @package     enrol_attributes
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['addcondition'] = '新增條件';
$string['addgroup'] = '新增群組條件';
$string['ajax-error'] = '發生錯誤';
$string['ajax-okforced'] = '好的，{$a}用戶已經完成選課';
$string['ajax-okpurged'] = '好的，選課註冊的資料已經清除';
$string['attributes:config'] = '設定外掛應用實例';
$string['attributes:manage'] = '管理已選課用戶';
$string['attributes:unenrol'] = '從課程中退選用戶';
$string['attributes:unenrolself'] = '從課程中自己退選';
$string['attrsyntax'] = '個人資料欄位規則';
$string['attrsyntax_help'] = '<p>這些規則只能使用自訂的個人資料欄位。</p>';
$string['confirmforce'] = '這將為符合此規則的所有用戶(重新)註冊選課。';
$string['confirmpurge'] = '這將(移除)所有符合此規則的註冊選課。';
$string['defaultrole'] = '刪除角色';
$string['defaultrole_desc'] = '使用此外掛註冊選課的人員預設角色（每個應用實例都可以覆蓋此角色）。';
$string['defaultwhenexpired'] = '資料屬性到期後的預設作為';
$string['defaultwhenexpired_desc'] = '不再符合屬性規則的用戶該怎麼辦？可以在每個註冊實例中取代此設定。';
$string['deletecondition'] = '刪除條件';
$string['force'] = '立即選課註冊';
$string['mappings'] = 'Shibboleth對應';
$string['observelogins'] = '登入時立即將用戶加入課程';
$string['observelogins_desc'] = '在用戶登入時立即註冊選課可能會對網站產生性能影響，如果大量用戶同時登入並且一次被註冊選課，會成為運作瓶頸，請停用此功能。';
$string['pluginname'] = '由用戶資料篩選入課';
$string['privacy:metadata'] = '按用戶資料屬性選課的外掛，使用時不會儲存任何個人資料。';
$string['profilefields'] = '個人資料欄位已列入選擇器';
$string['purge'] = '清除選課註冊';
$string['removewhenexpired'] = '屬性資料過期後是否取消選課？';
$string['removewhenexpired_help'] = '如果用戶不再符合屬性規則，則在登入時取消用戶的選課。';
$string['whenexpired'] = '屬性資料過期後的作為';
$string['whenexpired_help'] = '用戶不再符合屬性資料規則時怎麼辦';
$string['whenexpireddonothing'] = '保留用戶選課狀態';
$string['whenexpiredremove'] = '將用戶退選';
$string['whenexpiredsuspend'] = '將用戶停權';
