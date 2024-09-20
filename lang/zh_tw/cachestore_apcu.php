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
 * Strings for component 'cachestore_apcu', language 'zh_tw', version '3.9'.
 *
 * @package     cachestore_apcu
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['clusternotice'] = '請注意 APCu快取方式只適合用在單一節點的網站，或可以儲存在本地端的快取。細節請看<a href="{$a}">APC用戶快取文件</a>';
$string['notice'] = '注意';
$string['pluginname'] = 'APC用戶快取(APCu)';
$string['prefix'] = '字首';
$string['prefix_help'] = '預設上會使用資料庫的字首。';
$string['prefixinvalid'] = '你所選的字首是無效的。你只可以選 a-z A-Z 0-9-_。';
$string['prefixnotunique'] = '你所選的字首不是獨一無二的。請充新選一個獨一的。';
$string['testperformance'] = '測試效能';
$string['testperformance_desc'] = '若啟用，當檢視測試效能頁面時，將會包含APCu的效能。不建議你在正式運作的網站上啟用它。';
