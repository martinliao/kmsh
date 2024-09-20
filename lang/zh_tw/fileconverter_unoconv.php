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
 * Strings for component 'fileconverter_unoconv', language 'zh_tw', version '3.9'.
 *
 * @package     fileconverter_unoconv
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pathtounoconv'] = '到 unoconv 文件轉換器的路徑';
$string['pathtounoconv_help'] = 'unoconv 是一個可執行的程式，他能夠轉換 LibreOffice 軟體的文件格式。這不是必要的，但是一旦指定，Moodle將會用它來轉換各種文件格式。這是要用來支援各式各樣的輸入檔案，使之能使用PDF檔案格式的作業註記功能。';
$string['pluginname'] = 'Unoconv';
$string['test_unoconv'] = '測試 unoconv 路徑';
$string['test_unoconvdoesnotexist'] = '這 unoconv 路徑沒有指向 unoconv 程式。請檢查你的路徑設定。';
$string['test_unoconvdownload'] = '下載已轉換的 pdf 測試檔';
$string['test_unoconvempty'] = '沒有設定Unoconv的徑路，請重新檢視你的徑路設定。';
$string['test_unoconvisdir'] = '這 unoconv 路徑是指向一個資料夾，請在你指定的路徑上包含這unoconv 程式。';
$string['test_unoconvnotestfile'] = '找不到要轉換成pdf檔的測試文件';
$string['test_unoconvnotexecutable'] = '這 unoconv 路徑指向一個不能執行的檔案';
$string['test_unoconvok'] = '這 unoconv 路徑看起來已經配置妥當';
$string['test_unoconvversionnotsupported'] = '本系統不支援你所安裝的 unoconv 版本，Moodle的作業評分功能需要用到0.7或以上的版本。';
