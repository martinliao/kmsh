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
 * Strings for component 'attendance', language 'zh_tw', version '3.9'.
 *
 * @package     attendance
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['Aacronym'] = '缺';
$string['Afull'] = '缺席';
$string['Eacronym'] = '假';
$string['Efull'] = '請假';
$string['Lacronym'] = '遲';
$string['Lfull'] = '遲到';
$string['Pacronym'] = '參';
$string['Pfull'] = '出席';
$string['absenteereport'] = '缺席報告';
$string['acronym'] = '縮寫';
$string['add'] = '新增';
$string['addmultiplesessions'] = '新增多段上課時間';
$string['addsession'] = '新增上課時段';
$string['adduser'] = '新增用戶';
$string['all'] = '全員';
$string['allcourses'] = '所有課程';
$string['allpast'] = '全數通過';
$string['allsessions'] = '所有課堂';
$string['attendance:addinstance'] = '新增一項出席活動';
$string['attendance:canbelisted'] = '出現於點名單';
$string['attendance:changeattendances'] = '更改出缺席';
$string['attendance:changepreferences'] = '更改偏好設定';
$string['attendance:export'] = '匯出報表';
$string['attendance:manageattendances'] = '管理出席狀況';
$string['attendance:managetemporaryusers'] = '管理臨時學生';
$string['attendance:takeattendances'] = '記錄出缺席狀況';
$string['attendance:view'] = '檢視出席情況';
$string['attendance:viewreports'] = '檢視報告';
$string['attendance:viewsummaryreports'] = '檢視課程摘要報告';
$string['attendance_already_submitted'] = '您的出勤已經設置好了。';
$string['attendancedata'] = '出缺席資料';
$string['attendanceforthecourse'] = '本課程出缺席狀況';
$string['attendancegrade'] = '出缺席成績';
$string['attendancenotset'] = '您必須設定出席情況';
$string['attendancenotstarted'] = '此一課程還未開始記錄出缺席狀況';
$string['attendancepercent'] = '出缺席比率';
$string['attendancereport'] = '出缺席報告';
$string['attendanceslogged'] = '出席紀錄';
$string['attendancestaken'] = '出席了';
$string['attendancesuccess'] = '已成功紀綠出缺席狀況';
$string['attendanceupdated'] = '出席狀況已更新';
$string['attforblockdirstillexists'] = '舊的mod/attforblock目錄 － 仍然出現 －您必須在伺服器中刪除此目錄再可以進行更新運作。';
$string['attrecords'] = '出席記錄';
$string['autoassignstatus'] = '自動選擇可用的最高狀態';
$string['autoassignstatus_help'] = '如果選擇此項，學生將被自動分配到可用的最高等級。';
$string['automark'] = '自動標記';
$string['automark_help'] = '允許自動完成標記。
如果“是”，學生在他們第一次進入課程時將自動被標記。
如果“在課程結束時設為未標記”，任何未標記出勤的學生將被設定為所選的未標記狀態。';
$string['automarkall'] = '是';
$string['autorecorded'] = '系統自動記錄';
$string['averageattendance'] = '平均出席率';
$string['averageattendancegraded'] = '平均出席率';
$string['below'] = '低於 {$a}%';
$string['calclose'] = '結束';
$string['calendarevent'] = '為上課時段建立行事曆事件';
$string['calendarevent_help'] = '如果啟用，將為此上課時段建立行事曆事件。
如果禁用，此上課時段的任何現有行事曆事件都將被刪除。';
$string['caleventcreated'] = '已成功建立連線的行事曆事件';
$string['caleventdeleted'] = '已順利刪除連線的行事曆事件';
$string['calmonths'] = '一月，二月，三月，四月，五月，六月，七月，八月，九月，十月，十一月，十二月，';
$string['calshow'] = '選擇日期';
$string['caltoday'] = '今天';
$string['calweekdays'] = '日, 一, 二, 三, 四, 五, 六';
$string['cannottakeforgroup'] = '你不能參加{$a}群組課程';
$string['categoryreport'] = '課程類目報告';
$string['changeattendance'] = '更改出席狀況';
$string['changeduration'] = '更改的期限';
$string['changesession'] = '變更上課時段';
$string['checkweekdays'] = '選擇會落在你選擇的上課日期範圍內的平日。';
$string['column'] = '欄';
$string['columns'] = '欄';
$string['commonsession'] = '共同';
$string['commonsessions'] = '共同';
$string['confirm'] = '確認';
$string['confirmdeletehiddensessions'] = '你確定要刪除安排在此課程開始日期({$a->date})之前的 {$a->count}個上課時間嗎？';
$string['confirmdeleteuser'] = '您確定要刪除用戶 \'{$a->fullname}\' ({$a->email}) 嗎?<br/>他的全部出缺席紀錄將被永久刪除掉。';
$string['copyfrom'] = '複製出缺席資料';
$string['countofselected'] = '選出個數';
$string['course'] = '課程';
$string['coursemessage'] = '以簡訊通知課程用戶';
$string['coursesummary'] = '課程摘要報告';
$string['createmultiplesessions'] = '建立多段上課時間';
$string['createmultiplesessions_help'] = '您可使用此項功能來一步建立多重上課時間。

 * <strong>上課開始日期</strong>: 選擇上課開始日期
 * <strong>上課結束日期</strong>:選擇上課結束日期 (您想點名的最後一天).
 * <strong>上課日</strong>: 選擇上課日(例如星期二星期五等等)
 * <strong>頻率</strong>: 於此設定頻率。如每週上課選 1，隔週下課選2，等等。';
$string['createonesession'] = '新建此課程的一個上課時段';
$string['date'] = '日期';
$string['days'] = '日';
$string['defaultdisplaymode'] = '預設顯示模式';
$string['defaults'] = '預設';
$string['defaultstatus'] = '預設狀態集';
$string['delete'] = '刪除';
$string['deletedgroup'] = '有關此時段的群組經已被刪除';
$string['deletehiddensessions'] = '刪除所有隱藏的上課時段';
$string['deletelogs'] = '刪除出缺席資料';
$string['deleteselected'] = '刪除所選';
$string['deletesession'] = '刪除上課時段';
$string['deletesessions'] = '刪除所有上課時段';
$string['deleteuser'] = '刪除用戶';
$string['deletingsession'] = '刪除此課程的上課時段';
$string['deletingstatus'] = '刪除課程之狀態';
$string['description'] = '描述';
$string['display'] = '顯示';
$string['displaymode'] = '顯示模式';
$string['donotusepaging'] = '不使用分頁';
$string['downloadexcel'] = '以Excel格式下載';
$string['downloadooo'] = '以OpenOfficel格式下載';
$string['downloadtext'] = '以文字格式下載';
$string['duration'] = '持續時間';
$string['editsession'] = '編輯上課時段';
$string['edituser'] = '編輯用戶';
$string['emailcontent'] = '電子郵件內容';
$string['emailsubject'] = '電子郵件主旨';
$string['emptyacronym'] = '縮寫不允許是空白。狀態紀錄未被更新。';
$string['emptydescription'] = '描述不允許是空白。狀態紀錄未被更新。';
$string['enablecalendar'] = '建立行事曆事件';
$string['enablecalendar_desc'] = '如果啟用，將為每個上課時段建立一個行事曆事件。 更改此設置後，您應該執行重設行事曆報告。';
$string['endofperiod'] = '期程結束';
$string['endtime'] = '連線結束時間';
$string['enrolmentend'] = '用戶註冊結束{$a}';
$string['enrolmentstart'] = '用戶註冊開始{$a}';
$string['enrolmentsuspended'] = '註冊暫停';
$string['errorgroupsnotselected'] = '選出一個或多個群組';
$string['errorinaddingsession'] = '增加上課期間有誤';
$string['erroringeneratingsessions'] = '產生上課期間誤';
$string['eventdurationupdated'] = '上課期間已更新';
$string['eventreportviewed'] = '出缺席報告已檢視';
$string['eventscreated'] = '行事曆事件已建立';
$string['eventsdeleted'] = '行事曆事件已刪除';
$string['eventsessionadded'] = '上課時段已添加';
$string['eventsessiondeleted'] = '上課時段已刪除';
$string['eventsessionupdated'] = '上課時段已更新';
$string['eventstatusadded'] = '已新增狀態';
$string['eventstatusupdated'] = '狀態已更新';
$string['eventtaken'] = '已點名';
$string['eventtakenbystudent'] = '由學生點名';
$string['export'] = '匯出';
$string['extrarestrictions'] = '額外限制';
$string['from'] = '從：';
$string['gradebookexplanation'] = '成績單上的分數';
$string['gradebookexplanation_help'] = '出缺席模組表示您至今日的出席成績，依您所獲得的點數及您理當獲得的點數而定。此成績不含未來上課時間。成績單上您的出席成績則是依您出席(包含未來上課)及整個課程您可能獲得的點數的百分比計算。准此，出現在出缺席模組及成績單上的出席成績可能點數不同，但百分比是相同的。例如您已由10點中獲得8點(80%出席率)而整個課程的出席率值50點，則出缺席模組會出示8/10而成績單則顯示40/50。您雖未取得40點但是40等於您目前出席率的80%。您於出席模組取得的點數絕不會變少，因為它只算到目前的出席日數。但是您績單上的點數基於您未來的出席可能有增減，因為它是計算整個課程的。';
$string['gridcolumns'] = '網格欄位';
$string['groupsession'] = '群組';
$string['hiddensessions'] = '隱藏的上課時段';
$string['hiddensessions_help'] = '如果上課日早於課程開始日，上課日即會看不見。

您可以利用此項功能來隱藏早先的上課日，而不需要去刪除它們。
請記得只有看得到的上課日才會出現在成績簿中。';
$string['hiddensessionsdeleted'] = '所有隱藏的上課時段已被刪除';
$string['identifyby'] = '辨識學生用';
$string['includeall'] = '選擇全部上課時段';
$string['includenottaken'] = '包含未點名的上課時段';
$string['includeremarks'] = '含備註';
$string['indetail'] = '詳細...';
$string['indicator:cognitivedepthdef_link'] = 'Learning_analytics_indicators#Cognitive_depth';
$string['indicator:socialbreadthdef_link'] = 'Learning_analytics_indicators#Social_breadth';
$string['invalidaction'] = '你必須選擇一動作';
$string['invalidsessionenddate'] = '上課結束日期不能早於開始日期';
$string['invalidsessionendtime'] = '完結時間必須大過開始時間';
$string['iptimemissing'] = '釋放分鐘數無效';
$string['jumpto'] = '跳至';
$string['maxpossiblepercentage'] = '可得最大百分比';
$string['maxpossiblepoints'] = '可得最大分數';
$string['mergeuser'] = '合併用戶';
$string['modulename'] = '出缺席';
$string['modulename_help'] = '出席活動可使老師點名，並讓學生觀看自己之出席記錄。
教師可建立多段上課時間，並註記"出席"，"缺席"，"遲到"，"請假"，或修改之以符合自己的需求。
可以匯出個別學生之報告表或全班的。';
$string['modulenameplural'] = '出缺席';
$string['months'] = '月';
$string['moreattendance'] = '此頁點名已完成';
$string['mustselectusers'] = '必須選出要匯出的用戶';
$string['newdate'] = '新日期';
$string['newduration'] = '新上課期間';
$string['newstatusset'] = '狀態的新設定';
$string['noattendanceusers'] = '不可能滙出任何資料因為尚未有學生就讀此課程。';
$string['noattforuser'] = '此用戶沒有出缺席記錄';
$string['nodescription'] = '正常班級上課時段';
$string['noeventstoreset'] = '沒有需要更新的行事曆事件。';
$string['nogroups'] = '您不能新增群組上課時段。此課程裡並無群組。';
$string['noguest'] = '訪客無法看出席狀況';
$string['noofdaysabsent'] = '缺席日數';
$string['noofdaysexcused'] = '因故請假日數';
$string['noofdayslate'] = '遲到日數';
$string['noofdayspresent'] = '出席日數';
$string['nosessiondayselected'] = '未選出上課時段';
$string['nosessionexists'] = '此課程沒有上課時段';
$string['nosessionsselected'] = '尚未選取上課時段';
$string['notfound'] = '此課程找不到出缺席紀錄!';
$string['notmember'] = 'not&nbsp; 成員';
$string['noupgradefromthisversion'] = '出缺席模組不能在您attforblock的版本上升級。安裝新的出缺席模組前請刪除attforblock或更新至最新的版面。';
$string['olddate'] = '舊日期';
$string['onlyselectedusers'] = '匯出特定用戶';
$string['participant'] = '參加';
$string['percentage'] = '百分比';
$string['percentageallsessions'] = '總上課次數百分比';
$string['percentagesessionscompleted'] = '出席次數百分比';
$string['pluginadministration'] = '出缺席管理';
$string['pluginname'] = '出缺席紀錄';
$string['points'] = '分數';
$string['pointsallsessions'] = '所有上課次數的分數';
$string['pointssessionscompleted'] = '出席次數的分數';
$string['preferences_desc'] = '對狀態集的更改將影響現有的出席會議，並可能影響評分．';
$string['preventsharedip'] = '防止學生共享IP位址';
$string['priorto'] = '由於時段早於課程開始日期({$a}) ，所以在此日期前的新時段將會隱藏（不能存取）。您可以隨時更改課程的開始日期（查看課程設定）以存取較早的時段。<br><br>請變更時段日期或點擊「新增時段」接鍵確認';
$string['remark'] = '{a}的備註';
$string['remarks'] = '備註';
$string['repeatasfollows'] = '如下方一樣重複時段';
$string['repeatevery'] = '重複在每個';
$string['repeaton'] = '重複在';
$string['repeatuntil'] = '重複直至';
$string['report'] = '報告';
$string['required'] = '必需*';
$string['requiredentries'] = '暫時性紀錄覆蓋參加者的出缺席紀錄';
$string['requiredentry'] = '暫時用戶合併協助指南';
$string['requiredentry_help'] = '<p align="center"><b>出缺席紀錄</b></p>
<p align="left"><strong>合併帳戶</strong></p>
<p align="left">
<table border="2" cellpadding="4">
<tr>
<th>Moodle 用戶</th>
<th>暫時用戶</th>
<th>動作</th>
</tr>
<tr>
<td>出缺席資料</td>
<td>出缺席資料</td>
<td>暫時用戶將覆蓋Moodle用戶</td>
</tr>
<tr>
<td>沒有出缺席資料</td>
<td>出缺席資料</td>
<td>暫時用戶的出缺席紀錄將傳送到Moodle用戶</td>
</tr>
<tr>
<td>出缺席資料</td>
<td>沒有出缺席資料</td>
<td>暫時用戶將會被刪除</td>
</tr>
<tr>
<td>沒有出缺席資料</td>
<td>沒有出缺席資料</td>
<td>暫時用戶將會被刪除</td>
</tr>
</table>

</p>
<p align="left"><strong>暫時用戶將會在會在合併後被刪除</strong></p>';
$string['requiresubnet'] = '學生只可以在這些電腦上記錄自己的出缺席';
$string['requiresubnet_help'] = '你可以藉著輸入一個以逗點分隔的IP位址清單，來限制只在內部網路中記錄出缺席';
$string['resetcalendar'] = '重設行事曆';
$string['resetdescription'] = '請記得，刪除出缺席資料將會從資料庫中清掉所有訊息。你可以利用更改這一課程的開始日期來隱藏先前的上課時段的資料。';
$string['resetstatuses'] = '狀態還原為預設';
$string['restoredefaults'] = '還原為預設狀態';
$string['resultsperpage'] = '每頁人數';
$string['resultsperpage_desc'] = '單一頁面可以顯示的學生數';
$string['save'] = '儲存出缺席狀況';
$string['session'] = '上課時段';
$string['session_help'] = '上課時段';
$string['sessionadded'] = '成功新增上課時段';
$string['sessionalreadyexists'] = '此日期已有課';
$string['sessiondate'] = '上課日期';
$string['sessiondays'] = '上課日子';
$string['sessiondeleted'] = '成功刪除上課時段';
$string['sessionexist'] = '未新增上課時段(早就有了)';
$string['sessiongenerated'] = '已成功產生一個上課時段';
$string['sessions'] = '上課時段';
$string['sessionscompleted'] = '已出席的時段';
$string['sessionsgenerated'] = '已成功產生{$a}個上課時段';
$string['sessionsids'] = '上課時段的編號';
$string['sessionsnotfound'] = '在此選定期間內沒有上課時段';
$string['sessionstartdate'] = '上課開始日期';
$string['sessionstotal'] = '總上課節數';
$string['sessiontype'] = '上課時段類型';
$string['sessiontype_help'] = '有二種添加上課節數的方式：全部、群組。是否能添加不同形式端賴活動群組之模式如何。

* 群組模式裡的"不分群組"，您只能為全部學生增加上課節數。
* 在群組模式的"可視群組"，你可以為全部及群組增加節數。
* 在群組模式的"分離群組"，你只可以為群組增加上課節數。';
$string['sessiontypeshort'] = '類型';
$string['sessionupdated'] = '成功更新上課時段';
$string['set_by_student'] = '自行紀錄的';
$string['setallstatuses'] = '設定所有人的狀態';
$string['setallstatusesto'] = '設定所有人的狀態為«{$a}»';
$string['setperiod'] = '訂定幾分鐘的時間後釋放 IP';
$string['settings'] = '設定';
$string['setunmarked'] = '未標記時自動設置';
$string['showdefaults'] = '顯示預設值';
$string['showduration'] = '顯示期間';
$string['sortedgrid'] = '分類網格';
$string['sortedlist'] = '分類表';
$string['startofperiod'] = '開始期間';
$string['status'] = '狀態';
$string['statusdeleted'] = '狀態已刪除';
$string['statuses'] = '狀態';
$string['statusset'] = '狀態集 {$a}';
$string['statussetsettings'] = '狀態集';
$string['strftimedm'] = '%m.%d';
$string['strftimedmy'] = '%Y.%m.%d';
$string['strftimedmyhm'] = '%Y.%m.%d %H.%M';
$string['strftimedmyw'] = '%y.%m.%d (%a)';
$string['strftimehm'] = '%H.%M';
$string['strftimeshortdate'] = '%Y.%m.%d';
$string['studentavailability'] = '學生自行登記的時限(分鐘)';
$string['studentid'] = '學生編號';
$string['studentmarking'] = '學生紀錄';
$string['studentrecordingexpanded'] = '展開學生紀錄';
$string['studentrecordingexpanded_desc'] = '建立新的出缺席時，預設將“學生紀錄”的設定顯示為展開。';
$string['studentscanmark'] = '允許學生登錄自己出缺席情況';
$string['studentscanmark_desc'] = '若勾選，教師將可允許學生登錄自己的出缺席';
$string['studentscanmark_help'] = '若勾選，學生將可以更改他們自己在這一上課時段的出缺席狀態。';
$string['submitattendance'] = '登記出缺席';
$string['subnetwrong'] = '學生只可以從某些特定的位置上紀錄出缺席，而這電腦不在允許使用的清單上。';
$string['summary'] = '摘要';
$string['tablerenamefailed'] = '無法將出缺席活動的attforblock 資料表重新命名。';
$string['tactions'] = '動作';
$string['takeattendance'] = '點名';
$string['takensessions'] = '出席時段';
$string['tcreated'] = '已建立';
$string['tempaddform'] = '新增臨時學生';
$string['tempexists'] = '這兒已經有一個臨時用戶登記這一email地址';
$string['temptable'] = '臨時學生清單';
$string['tempuser'] = '臨時學生';
$string['tempusermerge'] = '合併臨時學生';
$string['tempusers'] = '臨時學生';
$string['tempusersedit'] = '編輯臨時學生';
$string['tempuserslist'] = '臨時學生';
$string['thiscourse'] = '此課程';
$string['time'] = '時間';
$string['timeahead'] = '不能建立超過一年的多重時段，請調整開始及結束日期。';
$string['to'] = '致：';
$string['todate'] = '迄今為止';
$string['tuseremail'] = '電子郵件';
$string['tusername'] = '全名';
$string['unknowngroup'] = '不明的群組';
$string['update'] = '更新';
$string['usedefaultsubnet'] = '使用預設值';
$string['userexists'] = '這兒已經有一真實用戶使用這一email地址。';
$string['users'] = '要匯出的用戶';
$string['usestatusset'] = '狀態集';
$string['variable'] = '變數';
$string['variablesupdated'] = '成功更新變數';
$string['versionforprinting'] = '列印版本';
$string['viewmode'] = '檢視模式';
$string['warningdesc'] = '這些警告將自動增加到任何新的出勤活動中。 如果同時觸發多個警告，則只發送警告門檻值較低的警告。';
$string['warningupdated'] = '更新的警告';
$string['week'] = '週';
$string['weeks'] = '週';
$string['youcantdo'] = '什麼都不能做';
