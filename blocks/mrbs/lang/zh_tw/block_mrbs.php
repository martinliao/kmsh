<?php

// This file is part of Moodle - http://moodle.org/
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
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Strings for component 'block_mrbs', language 'zh_tw', branch 'MOODLE_31_STABLE'
 *
 * @package   block_mrbs
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['about_mrbs'] = '關於場地預約';
$string['accessdenied'] = '限制存取';
$string['accessmrbs'] = '預約場地';
$string['addarea'] = '新增場地';
$string['addentry'] = '新增';
$string['addroom'] = '新增教室';
$string['advanced_search'] = '進階搜尋:依預約者帳號、姓名及說明欄位搜尋';
$string['all_day'] = '整天';
$string['area_admin_email'] = '場地管理者email';
$string['areas'] = '場地名稱';
$string['backadmin'] = '回到管理頁';
$string['both'] = '兩者';
$string['bgcolor'] = '背景顏色';
$string['blockname'] = '場地預約';
$string['booking_users'] = '可以預約的用戶';
$string['booking_users_help'] = "設定允許預約此場地的用戶信箱,多筆資料請以逗號(,)分隔.如未填寫則開放所有用戶皆可預約.";
$string['bookingmoved'] = '場地預約已被移除';
$string['bookingmovedmessage'] = '{$a->name} 您的 {$a->oldroom} 預約已移至 {$a->newroom} - {$a->area} 的 {$a->date}, {$a->starttime}.<br />
{$a->oldroom} 此場地已變更供 {$a->newbookingname} 使用.<br /> 
注意: 此變更為系統自動標註,請於使用者確認您的預約資料.';
$string['bookingmovedshort'] = '{$a->name} 已移至 {$a->newroom}';
$string['bookingmoveerror'] = '場地預約衝突通知';
$string['bookingmoveerrormessage'] = '{$a->name} 的預約單與新的預約(強制預約)有衝突，請管理員手動調整. <br/>
預約教室:{$a->area} {$a->oldroom}<br/>
預約時間:<a href="{$a->link}">{$a->date}</a> <br/>
新預約者:{$a->newbookingname}  <br/>
';
$string['bookingmoveerrorshort'] = '原申請人 {$a->name} 的預約申請變更要求,已發信通知場地管理員.';
$string['bookingsfor'] = '預約';
$string['bookingsforpost'] = '--未使用字串--';
$string['brief_description'] = '簡要說明.';
$string['browserlang'] = '您的瀏覽器設為';
$string['capacity'] = '容量';
$string['charset'] = 'utf-8';
$string['clashemailbody'] = '根據最新匯入的時間表，您的預約有個衝突：在 {$a->time}時, {$a->oldbooking} 及 {$a->newbooking}都訂了{$a->room}教室。請自己解決此項問題，預先避開不必要的困擾。我們只提醒這個衝突一次。如您忽略此電郵，將為您產生問題。此訊息由預訂系統自動產生，如您認為您因錯誤而收到此電郵，請聯絡{$a->admin}';
$string['clashemailnotsent'] = '無法寄送email至老師';
$string['clashemailsent'] = 'email寄至';
$string['click_to_reserve'] = '點選日期進行預約登記';
$string['config_admin'] = '管理者名稱';
$string['config_admin_email'] = '管理者信箱';
$string['config_area_list_format'] = '場地顯示';
$string['config_area_list_format2'] = '此區應以清單顯示或以下拉選單顯示?';
$string['config_dateformat'] = '日期格式';
$string['config_dateformat2'] = '使用的日期格式';
$string['config_default_report_days'] = '報告期間(日)';
$string['config_default_report_days2'] = '預設報告期間(日)';
$string['config_default_view'] = '預設檢視';
$string['config_default_room'] = '預設人數';
$string['config_enable_periods'] = '使用週期';
$string['config_eveningends2'] = '一天結束的時間(以時計)。您必須取消週期方能使用此選項。';
$string['config_eveningends_min'] = '結束分鐘';
$string['config_eveningends_min2'] = '一天結束的時間(以分計)。您必須取消週期方能使用此選項。';
$string['config_highlight_method'] = '顯示樣式';
$string['config_highlight_method2'] = '選擇某種突顯的方法：背景顏色，Class，或混合亦可。';
$string['config_mail_admin_on_bookings2'] = '以信件通知管理者有一個新的預約';
$string['config_mail_admin_on_delete2'] = '以信件通知管理者有刪除的預約';
$string['config_mail_area_admin_on_bookings2'] = '以信件通知場地管理者有一個新的預約.';
$string['config_mail_booker2'] = '發送信件通知給預約者';
$string['config_mail_cc'] = '信件副本給';
$string['config_mail_details'] = '信件明細';
$string['config_mail_from'] = '信件寄件者';
$string['config_mail_admin_on_bookings'] = '發送預約通知信給管理者';
$string['config_mail_area_admin_on_bookings'] = '發送預約通知信給場地管理者';
$string['config_mail_room_admin_on_bookings'] = '發送預約通知信給教室管理者';
$string['config_mail_admin_on_delete'] = '發送預約取消通知信給管理者';
$string['config_mail_admin_all'] = '所有事件皆發送信件給管理者';
$string['config_mail_booker'] = '通知預約者';
$string['config_mail_recipients'] = '其它收件人';
$string['config_max_rep_entrys'] = '允許最大數循環數';
$string['config_monthly_view_entries_details'] = '月報告';
$string['config_morningstarts'] = '開始時間';
$string['config_morningstarts_min'] = '開始分鐘';
$string['config_periods'] = '自訂時段';
$string['config_eveningends'] = '結束時間';
$string['config_new_window'] = '視窗';
$string['config_refresh_rate'] = '頁面刷新時間';
$string['config_resolution'] = '時間區段';
$string['config_resolution2'] = '時間區塊需加以規劃。週期必須取消方能使用此選項。';
$string['config_search_count'] = '每頁搜尋結果';
$string['config_times_right_side'] = '時段位置';
$string['config_timeformat'] = '時間格式';
$string['config_timeformat2'] = '時間格式。';
$string['config_times_right_side2'] = '在行程表以日、週檢視方式查詢，選擇"否"時段僅顯示在左側；選擇"是"時段將顯示在左右兩側';
$string['config_view_week_number'] = '檢視第幾週';
$string['config_weeklength'] = '一週長度';
$string['config_weekstarts'] = '一週開始';
$string['config_weekstarts2'] = '選擇一週開始日期';
$string['config_max_advance_days'] = 'N天前才開放預約';
$string['confirmdel'] = '您確定要刪除此記錄?';
$string['conflict'] = '此時段已被預約';
$string['createdby'] = '預約者';
$string['ctrl_click'] = '用Ctrl+滑鼠右鍵可以複選';
$string['ctrl_click_type'] = '使用Ctrl+滑鼠右鍵可以複選多個教室';
$string['computerroom'] = '機房';
$string['database'] = '資料庫';
$string['dayafter'] = '下一天';
$string['daybefore'] = '上一天';
$string['days'] = '天';
$string['delarea'] = '<p>您必須先刪除場地內的所有教室</p>';
$string['deleteentry'] = '刪除';
$string['deletefollowing'] = '這個動作會刪除相關的預約紀錄';
$string['deleteseries'] = '整批刪除';
$string['delete_user'] = '刪除使用者';
$string['dontshowoccupied'] = '不顯示已預約的教室';
$string['doublebookefailbody'] = '下列訊息未能發送給{$a}';
$string['doublebookesubject'] = '重複預約通知';
$string['downloadquery'] = '下載報表';
$string['duration'] = '持續時間';
$string['editarea'] = '新增類型';
$string['editentry'] = '修改';
$string['editroom'] = '修改教室';
$string['editroomarea'] = '編修教室描述';
$string['editseries'] = '整批修改';
$string['edit_user'] = '編輯使用者';
$string['email_failed'] = '未能送出的件信';
$string['end_date'] = '結束時間';
$string['entries_found'] = '找到預約';
$string['entry'] = '登錄';
$string['entry_found'] = '找到預約';
$string['entryid'] = '登記序號';
$string['error_area'] = '錯誤: 場地';
$string['error_room'] = '錯誤: 教室';
$string['error_send_email'] = '錯誤: 送信給{$a}出了問題';
$string['eventbookingcreated'] = '已預約';
$string['eventbookingupdated'] = '預約已更新';
$string['external'] = '外部使用';
$string['failed_connect_db'] = '嚴重錯誤: 無法連上資料庫';
$string['failed_to_acquire'] = '無法存取資料庫';
$string['filename'] = '場地預約報表';
$string['finishedimport'] = '處理完畢，共用時間: {$a}秒';
$string['for_any_questions'] = ',關於任何在這裡找不到答案的問題.';
$string['forciblybook'] = '強制預訂場地';
$string['forciblybook2'] = '強制預約';
$string['fulldescription'] = '說明(單位,用途)';
$string['goroom'] = '查詢';
$string['goto'] = '查詢';
$string['gotoroom'] = '至';
$string['gotothismonth'] = '本月';
$string['gotothisweek'] = '本週';
$string['gototoday'] = '本日';
$string['help_wildcard'] = '注意：使用%符號於所有文字方格中當萬用字元';
$string['highlight_line'] = '加強顯示這行';
$string['hours'] = '小時';
$string['hybrid'] = '混合';
$string['idontcare'] = '強制重複預約此場地';
$string['importedbooking'] = '匯入預約';
$string['importedbookingmoved'] = '匯入預約(已編輯)';
$string['importlog'] = '匯入記錄';
$string['in'] = '在';
$string['include'] = '包含';
$string['internal'] = '內部使用';
$string['invalid_booking'] = '預約無效';
$string['invalid_entry_id'] = '身份無效';
$string['invalid_search'] = '空的或不合法的搜尋字串.';
$string['invalid_series_id'] = '序列號錯誤.';
$string['mail_body_changed_entry'] = '一項目已被修正，明細於此。';
$string['mail_body_del_entry'] = '一項目已被刪除，明細於此。';
$string['mail_body_new_entry'] = '已預約一新項目，明細於此。';
$string['mail_changed_entry'] = '一項目已被修正，明細於此。';
$string['mail_deleted_entry'] = '一項目已被刪除，明細於此。';
$string['mail_subject'] = '主題';
$string['mail_subject_delete'] = '場地預約-刪除{$a->date}, {$a->room} (來自{$a->user})';
$string['mail_subject_entry'] = '場地預約-變更{$a->date}, {$a->room} (來自{$a->user})';
$string['mail_subject_newentry'] = '場地預約-新增{$a->date}, {$a->room} (來自{$a->user})';
$string['match_area'] = '符合的場地';
$string['match_descr'] = '符合全部簡述';
$string['match_entry'] = '符合部份的簡述';
$string['match_room'] = '符合部份的教室簡述';
$string['match_type'] = '符合的類型';
$string['menu'] = '選單';
$string['mincapacity'] = '最小的容量';
$string['minutes'] = '分';
$string['month'] = '月';
$string['monthafter'] = '下一月';
$string['monthbefore'] = '上一月';
$string['movedto'] = '移至';
$string['mrbs'] = '場地預約管理';
$string['mrbsadmin'] = '場地管理者';
$string['mrbs:administermrbs'] = '修改場地預約設定';
$string['mrbs:doublebook'] = '重複預約場地';
$string['mrbs:editmrbs'] = '編輯場地預約';
$string['mrbseditor'] = '場地預約者';
$string['mrbs:forcebook'] = '強制預約場地(自動移除先前預約)';
$string['mrbs:myaddinstance'] = '新增場地預約區塊';
$string['mrbs:viewalltt'] = '檢視所有用戶時間表';
$string['mrbs:viewmrbs'] = '檢視場地預約';
$string['mrbsviewer'] = '場地檢視者';
$string['mustlogin'] = '要存取場地預約日曆區塊您須先登入';
$string['namebooker'] = '預約者';
$string['newwindow'] = '新視窗'; //see MDL-15952
$string['noarea'] = '尚未選擇場地';
$string['noareas'] = '沒有場地資料';
$string['norights'] = '您無權利修改此筆記錄!';
$string['norooms'] = '尚無教室資料.';
$string['noroomsfound'] = '查無相關的教室';
$string['no_rooms_for_area'] = '這個場地沒有建置教室資料';
$string['no_user_with_email'] = '查詢無相關用戶與信箱{$a}有關聯,輸入的管理者信箱必須與平台現有用戶有所關聯.';
$string['not_found'] = '找不到資料';
$string['not_php3'] = 'WARNING: This probably doesn\'t work with PHP3';
$string['of'] = 'of';
$string['password_twice'] = 'If you wish to change the password, please type the new password twice';
$string['period'] = '時段';
$string['periods'] = '時';
$string['please_contact'] = '請聯絡';
$string['ppreview'] = '預覽列印';
$string['pagewindow'] = '相同視窗';
$string['records'] = '紀錄';
$string['rep_dsp'] = '顯示在報表';
$string['rep_dsp_dur'] = '持續時間';
$string['rep_dsp_end'] = '結束時間';
$string['repeat_id'] = '重複ID';
$string['rep_end_date'] = '結束重複的日期';
$string['rep_for_nweekly'] = '(每週)';
$string['rep_for_weekly'] = '(每週)';
$string['rep_freq'] = '頻率';
$string['rep_num_weeks'] = '重複幾週';
$string['report_and_summary'] = '明細和加總';
$string['report'] = '報表查詢';
$string['report_end'] = '報表結束日';
$string['report_on'] = '場地預約報表';
$string['report_only'] = '只要明細';
$string['report_start'] = '報表起始日';
$string['rep_rep_day'] = '重複的星期';
$string['rep_type'] = '重複預約';
$string['rep_type_0'] = '不重複';
$string['rep_type_1'] = '每天';
$string['rep_type_2'] = '每週';
$string['rep_type_3'] = '每月';
$string['rep_type_4'] = '每年';
$string['rep_type_5'] = '每月對應的日期';
$string['rep_type_6'] = '(每週)';
$string['returncal'] = '查看日程表';
$string['returnprev'] = '回前一頁';
$string['requestvacate'] = '請求移轉場地預約';
$string['requestvacatemessage']= '{$a->user} 請求移轉預約:
於場地-{$a->room} {$a->description} , {$a->datetime} 
聯絡方式:
原因:

若有相關問題，請與該員聯繫.';
$string['requestvacatemessage_html']= '{$a->user} 請求移轉預約: <br />
於場地- {$a->room} {$a->description} , <a href="{$a->href}">{$a->datetime}</a><br />
聯絡方式:<br />
原因:<br /><br />
若有相關問題，請與該員聯繫.';
$string['rights'] = '權限';
$string['room'] = '教室';
$string['room_admin_email'] = '教室管理者信箱';
$string['rooms'] = '教室名稱';
$string['roomchange'] = '標記為教室變更';
$string['sched_conflict'] = '時段衝突';
$string['search_for'] = '預約搜尋';
$string['search_results'] = '搜尋結果';
$string['seconds'] = '秒';
$string['slot'] = '時段';
$string['show_my_entries'] = '顯示全部我的預約';
$string['sort_rep'] = '排序';
$string['sort_rep_time'] = '起始日/時';
$string['start_date'] = '起始時間';
$string['submitquery'] = '產生報表';
$string['sum_by_creator'] = '預約者';
$string['sum_by_descrip'] = '簡述';
$string['summarize_by'] = '加總項目';
$string['summary_header'] = '總共預約(小時)';
$string['summary_header_per'] = '總共預約(次)';
$string['summary_only'] = '只要加總';
$string['sure'] = '您確定嗎?';
$string['system'] = '系統';
$string['through'] = '經由';
$string['too_may_entrys'] = '這個選擇造成太多輸入.<br>請重新選擇!';
$string['type'] = '類型';
$string['unknown'] = '未知的';
$string['update_area_failed'] = '場地更新失敗:';
$string['update_room_failed'] = '教室更新失敗:';
$string['useful_n-weekly_value'] = '可以提供預約的星期.';
$string['valid_room'] = '教室.';
$string['valid_time_of_day'] = '可以預約的時間.';
$string['viewday'] = '查看日期';
$string['viewmonth'] = '月顯示';
$string['viewweek'] = '週顯示';
$string['weekafter'] = '下一週';
$string['weekbefore'] = '上一週';
$string['weeks'] = '星期';
$string['you_are'] = '您是';
$string['you_have_not_entered'] = '您沒有輸入資料';
$string['you_have_not_selected'] = '您沒有選資料';
$string['findroom'] = '教室搜尋(完全符合)';
$string['capacity'] = '人數';
$string['unconfirmedbooking'] = '未確認';
$string['manage'] = '場地管理';
$string['pluginname'] = '場地預約';