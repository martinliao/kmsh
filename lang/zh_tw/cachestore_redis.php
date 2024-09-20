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
 * Strings for component 'cachestore_redis', language 'zh_tw', version '3.9'.
 *
 * @package     cachestore_redis
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['password'] = '密碼';
$string['password_help'] = '用來設定 Redis 伺服器的密碼';
$string['pluginname'] = 'Redis';
$string['prefix'] = '鍵名的前綴';
$string['prefix_help'] = 'Redis伺服器所用的鍵名的前綴
*若您只有一個Moodle網站使用這伺服器，你可以使用預設值而不管它。
*最大只能使用5個字元。';
$string['prefixinvalid'] = '無效的前綴。你只可以使用 a-z A-Z 0-9-_ 。';
$string['serializer_igbinary'] = 'igbinary 串聯器';
$string['serializer_php'] = '預設的PHP串聯器';
$string['server'] = '伺服器';
$string['server_help'] = 'Reids伺服器所用的主機名稱或IP位址';
$string['test_password'] = '測試伺服器密碼';
$string['test_password_desc'] = 'Redis 測試伺服器密碼';
$string['test_serializer'] = '串聯器';
$string['test_serializer_desc'] = '用來測試的串聯器';
$string['test_server'] = '測試伺服器';
$string['test_server_desc'] = '測試用的Redis伺服器';
$string['useserializer'] = '使用串聯器';
$string['useserializer_help'] = '指定要用來串連的串聯器。有效的串聯器是Redis::SERIALIZER_PHP 或 Redis::SERIALIZER_IGBINARY。
後者只有在 phpredis 被配置為---啟用 redis-igbinary 選項，且這igbinary擴展已經裝載了。';
