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
 * Strings for component 'block_openai_chat', language 'zh_tw', version '3.9'.
 *
 * @package     block_openai_chat
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['apikey'] = 'OpenAI的API 金鑰';
$string['apikeydesc'] = '您OpenAI帳戶的API 金鑰';
$string['apikeymissing'] = '請將您OpenAI的API 金鑰加入區塊全域設定。';
$string['askaquestion'] = '請問個問題...';
$string['blocktitle'] = '區塊標題';
$string['defaultprompt'] = '以下是使用者與機器人在進行線上學習的Moodle網頁的對話';
$string['defaultusername'] = '用戶';
$string['erroroccurred'] = '發生錯誤！請稍候重試。';
$string['maxlength'] = '最大長度';
$string['maxlengthdesc'] = '能生成的最大標記數。請求可以使用最多 2,048 或 4,000 個標記，在下指令和文字填詞間共用。確切的限制因模型而異。（一個標記大致相當於 4 個普通英文字元）';
$string['openai_chat'] = 'OpenAI聊天';
$string['openai_chat:addinstance'] = '新增一個新的OpenAI聊天區塊';
$string['openai_chat:myaddinstance'] = '新增一個新的OpenAI聊天區塊到我的Moodle頁面';
$string['pluginname'] = 'OpenAI聊天區塊';
$string['privacy:metadata'] = 'OpenAI聊天區塊不會儲存個人用戶數據，預設情況下也不會將個人數據發送到OpenAI。然而，用戶提交的聊天訊息會完整地發送到OpenAI，在OpenAI隱私政策（https://openai.com/api/policies/privacy/）的約束下，可能會存儲訊息以提升API。';
$string['restrictusage'] = '聊天的使用僅限於已登入的使用者';
$string['restrictusagedesc'] = '如果此格被勾選，只有已登入的使用者可以使用聊天框';
$string['sourceoftruthdesc'] = '儘管AI非常有能力，但如果它不知道答案，它更有可能自信地給出錯誤信息而非拒絕回答。在此文字框中，您可以添加常見問題及對應的答案供AI使用。請按以下格式放置問題和答案：
<pre>Q: 問題 1<br />A: 答案 1<br /><br />Q: 問題 2<br />A: 答案 2</pre>';
$string['temperature'] = '隨機生成程度';
$string['username'] = '使用者名稱';
