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
 * Strings for component 'assignfeedback_editpdf', language 'zh_tw', version '3.9'.
 *
 * @package     assignfeedback_editpdf
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['addtoquicklist'] = '添加到快速列表';
$string['annotationcolour'] = '批註的顏色';
$string['black'] = '黑色';
$string['blue'] = '藍色';
$string['cannotopenpdf'] = '無法開啟這PDF檔。這檔案可能已經損毀，或採不支援的格式。';
$string['clear'] = '清除';
$string['colourpicker'] = '顏色挑選器';
$string['command'] = '指令：';
$string['comment'] = '評論';
$string['commentcolour'] = '評論的顏色';
$string['commentcontextmenu'] = '評論選單';
$string['commentindex'] = '評論的索引';
$string['commentlabel'] = '{$a->pnum}.{$a->cnum}';
$string['couldnotsavepage'] = '無法儲存第{$a}頁';
$string['currentstamp'] = '印章';
$string['default'] = '預設開啟';
$string['default_help'] = '如果設定此項，所有新的作業預設將使用此回饋方法。';
$string['deleteannotation'] = '刪除批註';
$string['deletecomment'] = '刪除評論';
$string['deletefeedback'] = '刪除回饋PDF檔';
$string['downloadablefilename'] = 'feedback.pdf';
$string['downloadfeedback'] = '下載回饋PDF檔';
$string['draftchangessaved'] = '批註的草稿已儲存';
$string['drag'] = '拖拉';
$string['editpdf'] = '批註PDF檔';
$string['editpdf_help'] = '直接在瀏覽器批註學生的作業，並產生一個編輯好的、可下載的PDF檔。';
$string['enabled'] = '啟用批註PDF檔';
$string['enabled_help'] = '若啟用，當教師在批改作業時，可以建立一批註的PDF檔。它允許教師直接在學生的作業上添加評論、畫圖、和蓋印章。
這些批註工作是在瀏覽器上完成而不需要額外的軟體。';
$string['errorgenerateimage'] = 'ghostscript產生圖像時發生錯誤，除錯訊息： {$a}';
$string['errorpdfpage'] = '在產生這一頁面時發生錯誤';
$string['expcolcomments'] = '展開/收疊所有的評論';
$string['filter'] = '過濾評論...';
$string['generatefeedback'] = '產生回饋PDF檔';
$string['generatingpdf'] = '請稍候，正在產生PDF中...';
$string['gotopage'] = '跳到頁面';
$string['green'] = '綠色';
$string['gsimage'] = 'Ghostscript 測試圖像';
$string['highlight'] = '醒目標示';
$string['jsrequired'] = '要批註一個PDF檔，是需要用到JavaScript。請在你的瀏覽器上啟用JavaScript來使用這一功能。';
$string['launcheditor'] = '啟動PDF編輯器';
$string['line'] = '線';
$string['loadingeditor'] = '裝載PDF編輯器';
$string['navigatenext'] = '下一頁';
$string['navigateprevious'] = '前一頁';
$string['output'] = '輸出：';
$string['oval'] = '橢圓形';
$string['pagenumber'] = '第 {$a}頁';
$string['pagexofy'] = '第{$a->page}頁，共 {$a->total}頁';
$string['pathtogspathdesc'] = '請注意，這一註釋的pdf檔需要在{$a}設定到ghostscript的路徑。';
$string['pen'] = '筆';
$string['pluginname'] = '批註的PDF檔';
$string['preparesubmissionsforannotation'] = '準備要批註的作業';
$string['privacy:metadata:colourpurpose'] = '評論或註解的顏色';
$string['privacy:metadata:conversionpurpose'] = '文件將轉換為PDF檔以允許加上註釋';
$string['privacy:metadata:filepurpose'] = '儲存有註釋的PDF檔和給用戶的回饋';
$string['privacy:metadata:rawtextpurpose'] = '儲存快選清單用的原始文字';
$string['privacy:metadata:tablepurpose'] = '儲存教師指定的評論快選清單';
$string['privacy:metadata:userid'] = '用戶編號';
$string['privacy:path'] = '回饋的PDF檔案';
$string['rectangle'] = '長方形';
$string['red'] = '紅色';
$string['result'] = '結果：';
$string['searchcomments'] = '搜尋評論';
$string['select'] = '選擇';
$string['stamp'] = '印章';
$string['stamppicker'] = '印章挑選器';
$string['stamps'] = '印章';
$string['stampsdesc'] = '印章必須是圖像檔(建議大小: 40X40)。這些圖像可以配合印章工具使用，作為批註PDF檔之用。';
$string['test_doesnotexist'] = 'ghostscript 路徑指向一個不存在的檔案';
$string['test_empty'] = 'ghostscript的路徑是空的，請輸入正確的路徑';
$string['test_isdir'] = '這 ghostscript 路徑指向一資料夾，請在你指定的路徑上包含這ghostscript 程式。';
$string['test_notestfile'] = '缺少測試的PDF檔';
$string['test_notexecutable'] = 'ghostscript指向一個不能執行的檔案';
$string['test_ok'] = 'ghostscript路徑似乎沒問題，請查看一下在這圖下方的訊息。';
$string['testgs'] = '測試 ghostscript 路徑';
$string['tool'] = '工具';
$string['toolbarbutton'] = '{$a->tool} {$a->shortcut}';
$string['viewfeedbackonline'] = '檢視有批註的PDF檔';
$string['white'] = '白色';
$string['yellow'] = '黃色';
