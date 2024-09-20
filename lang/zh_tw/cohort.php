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
 * Strings for component 'cohort', language 'zh_tw', version '3.9'.
 *
 * @package     cohort
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['addcohort'] = '建立新的校定班級群組';
$string['allcohorts'] = '所有同期學生';
$string['anycohort'] = '任何';
$string['assign'] = '指派';
$string['assigncohorts'] = '指派同期生成員';
$string['assignto'] = '校定班級“{$a}”的成員';
$string['backtocohorts'] = '回到校定班級群組';
$string['bulkadd'] = '加到校定班級群組';
$string['bulknocohort'] = '找不到可用的校定班級群組';
$string['categorynotfound'] = '沒有找到 <b>{$a}</b>類別，或你沒有權限在此建立一群同期生。將會使用預設的脈絡。';
$string['cohort'] = '校定班級群組';
$string['cohorts'] = '校定班級群組';
$string['cohortsin'] = '{$a}：可用的校定班級';
$string['component'] = '來源';
$string['contextnotfound'] = '沒有找到 <b>{$a}</b>脈絡，或你沒有權限在此建立一群同期生。將會使用預設的脈絡。';
$string['csvcontainserrors'] = '在CSV資料發現有錯誤。請見以下細節。';
$string['csvcontainswarnings'] = '在CSV資料發現有警告。請見以下細節。';
$string['csvextracolumns'] = '將會忽略<b>{$a}</b>欄位。';
$string['currentusers'] = '目前的用戶';
$string['currentusersmatching'] = '目前符合的用戶';
$string['defaultcontext'] = '預設脈絡';
$string['delcohort'] = '刪除校定班級群組';
$string['delconfirm'] = '您真的要刪除校定班級群組“{$a}”嗎？';
$string['description'] = '描述';
$string['displayedrows'] = '已顯示{$a->displayed}行，共有 {$a->total}行。';
$string['duplicateidnumber'] = '已經存在有相同編號的校定班級群組';
$string['editcohort'] = '編輯校定班級群組';
$string['editcohortidnumber'] = '編輯同級生編號';
$string['editcohortname'] = '編輯同級生編號';
$string['eventcohortcreated'] = '校定班級群組已建立';
$string['eventcohortdeleted'] = '校定班級群組已刪除';
$string['eventcohortmemberadded'] = '加入校定班級群組的用戶';
$string['eventcohortmemberremoved'] = '從校定班級群組中移除的用戶';
$string['eventcohortupdated'] = '校定班級群組已更新';
$string['external'] = '外部的校定班級群組';
$string['idnumber'] = '編號';
$string['invalidtheme'] = '同期群組主題不存在';
$string['memberscount'] = '校定班級群組大小';
$string['name'] = '名稱';
$string['namecolumnmissing'] = '這CVS檔案的格式有些錯誤。請檢查它所包含的欄位名稱。要添加用戶到一同期群組，請到網站管理頁面去"上傳用戶"。';
$string['namefieldempty'] = '欄位名稱不可以是空的';
$string['newidnumberfor'] = '同期群組{$a}的新編號';
$string['newnamefor'] = '同級生{$a}的新編號';
$string['nocomponent'] = '手動建立';
$string['potusers'] = '可能用戶';
$string['potusersmatching'] = '可能符合的用戶';
$string['preview'] = '預覽';
$string['privacy:metadata:cohort_members'] = '關於這用戶的同期群組的訊息';
$string['privacy:metadata:cohort_members:cohortid'] = '同期群組的編號';
$string['privacy:metadata:cohort_members:timeadded'] = '用來指明這用戶在何時加入同期群組的時間戳記';
$string['privacy:metadata:cohort_members:userid'] = '在這同期群組中，此一用戶的編號';
$string['removeuserwarning'] = '從校定班級群組刪除用戶可能會導致取消該用戶在多個課程的選課，也就是說，它會刪除此用戶在這些課程上的個人設定、成績、分組和其他用戶訊息。';
$string['search'] = '搜尋';
$string['searchcohort'] = '搜尋校定班級群組';
$string['selectfromcohort'] = '從校定班級群組選擇成員';
$string['systemcohorts'] = '系統同期生';
$string['unknowncohort'] = '未知的校定班級群組({$a})！';
$string['uploadcohorts'] = '上傳同期生';
$string['uploadcohorts_help'] = '同期生也可以經由純文字檔上傳。檔案的格式應該如下面：

*這檔案的每一行只包含一筆紀錄。
*每一筆紀錄是以逗點(或其他分隔符號)隔開的一系列資料。
*第一筆紀錄包含一欄位名稱的清單，用以定義這檔暗渠於部分的
*
*';
$string['uploadedcohorts'] = '上傳的{$a}同期生';
$string['useradded'] = '用戶已經加入校定班級群組"{$a}"';
$string['visible'] = '是否顯示';
$string['visible_help'] = '任何同期生可以在這同期生脈絡被擁有\'moodle/cohort:view\' 權限的用戶所檢視。<br/>
可見的同期生也可以被以下課程的用戶所檢視。';
