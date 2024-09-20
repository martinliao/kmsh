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
 * Strings for component 'antivirus_clamav', language 'zh_tw', version '3.9'.
 *
 * @package     antivirus_clamav
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['clamfailed'] = 'Clam AV 執行時發生錯誤。傳回的錯誤訊息是{$a}。下面是Clam輸出的資訊：';
$string['clamfailureonupload'] = 'ClamAV 上傳失敗';
$string['configclamactlikevirus'] = '把檔案視為病毒';
$string['configclamdonothing'] = '把檔案視為沒問題';
$string['configclamfailureonupload'] = '若你有配置掃毒軟體clam來掃描上傳的檔案，但若配置錯誤，或因為不明理由而無法執行，那它會怎樣？若你選擇：

*"把檔案視為病毒"--檔案將被移到隔離區或被刪除。
*"把檔案視為沒問題"--檔案將會正常地一到目的地資料夾中。
無論是何種方式，管理員都會被警告說掃毒程式clam已經失敗。

如果你選了"把檔案視為病毒"，且因些理由掃毒程式無法執行(通常是因為你輸入的到clam的路徑錯誤)，所有的上傳的檔案都會被移到指定的隔離區或被刪除。設定時請小心。';
$string['errorcantopensocket'] = '連結到Unix域名插口導致錯誤{$a}';
$string['errorclamavnoresponse'] = 'ClamAV 沒有反應；請檢察 daemon 執行狀態';
$string['errornounixsocketssupported'] = '這一系統不支援Unix域名插口的傳輸。請使用"指令行"的選項代替';
$string['invalidpathtoclam'] = 'Moodle已設定執行Clam檢查上傳的檔案，但是提供給Clam AV的路徑{$a}，是無效的。';
$string['pathtoclam'] = '指令行';
$string['pathtoclamdesc'] = '若執行方式設定為"指令行"，請在此輸入到ClamAV的路徑。在Linix，它應該像  /usr/bin/clamscan 或 /usr/bin/clamdscan';
$string['pathtounixsocket'] = 'Unix 域名插口';
$string['pathtounixsocketdesc'] = '若運作方式設定為 "Unix domain socket"，請在此輸入到 ClamAV Unix的插口。 在Debian Linux 這將會是 /var/run/clamav/clamd.ctl。請確定這 clamav daemon 對於上傳的檔案有讀取的權力，最簡單的方式是是添加一個 \'clamav\' 用戶到你的網頁伺服器群組 (\'www-data\' on Debian Linux)。';
$string['pluginname'] = 'ClamAV 防毒軟體';
$string['privacy:metadata'] = '這一ClamAV 防毒外掛程式沒有儲存個人資料';
$string['quarantinedir'] = '隔離用目錄';
$string['runningmethod'] = '執行方式';
$string['runningmethodcommandline'] = '指令行';
$string['runningmethoddesc'] = '執行ClamAV的方式。預設上是使用"指令行"，然而在Unix系統上，使用系統叉口可以地到更好的表現';
$string['runningmethodunixsocket'] = 'Unix域名插口';
$string['unknownerror'] = 'Clam 發生了不明的錯誤。';
