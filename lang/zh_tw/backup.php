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
 * Strings for component 'backup', language 'zh_tw', version '3.9'.
 *
 * @package     backup
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['asyncgeneralsettings'] = '非同步備份/還原';
$string['asyncrestorecompletebutton'] = '繼續';
$string['asyncreturn'] = '回到課程';
$string['autoactivedescription'] = '選擇是否進行自動備份。如果選擇人工手動，那麼只能經由自動備份CLI程式做備份。

您可以用手工在命令列模式執行，或是透過 cron 排程執行。';
$string['autoactivedisabled'] = '已停用';
$string['autoactiveenabled'] = '已啟用';
$string['autoactivemanual'] = '人工';
$string['automatedbackupschedule'] = '時程';
$string['automatedbackupschedulehelp'] = '選擇每一週的那幾天執行自動備份工作。';
$string['automatedbackupsinactive'] = '網站管理員沒有啟動備份排程';
$string['automatedbackupstatus'] = '自動備份時程狀態';
$string['automateddeletedays'] = '刪除早於此日期的備份';
$string['automatedmaxkept'] = '最多保留幾個備份';
$string['automatedmaxkepthelp'] = '當每一課程都會定期自動備份時，請指定要保留幾個備份，較早的備份會自動被刪除。';
$string['automatedminkept'] = '最少保留幾個備份';
$string['automatedminkepthelp'] = '若備份因為超過某一指定的天數而被刪除時，那可能造成那些停用的課程會沒有備份。要防止這現象，請指定最少要保留幾個備份。';
$string['automatedsettings'] = '自動備份設定';
$string['automatedsetup'] = '自動備份的設定';
$string['automatedstorage'] = '自動備份儲存';
$string['automatedstoragehelp'] = '選擇自動備份檔案的儲存路徑';
$string['backupactivity'] = '備份活動：{$a}';
$string['backupcourse'] = '備份課程：{$a}';
$string['backupcoursedetails'] = '課程詳情';
$string['backupcoursesection'] = '學習單元：{$a}';
$string['backupcoursesections'] = '課程學習單元';
$string['backupdate'] = '備份日期';
$string['backupdetails'] = '備份細節';
$string['backupdetailsnonstandardinfo'] = '所選出的檔案不是標準的Moodle 備份檔案。

回存過程會嘗試將它轉換為標準格式再回存它。';
$string['backupformat'] = '格式';
$string['backupformatimscc1'] = 'IMS Common Cartridge 1.0';
$string['backupformatimscc11'] = 'IMS Common Cartridge 1.1';
$string['backupformatmoodle1'] = 'Moodle1格式';
$string['backupformatmoodle2'] = 'Moodle 2格式';
$string['backupformatunknown'] = '未知的格式';
$string['backuplog'] = '技術訊息和警告';
$string['backupmode'] = '模式';
$string['backupmode10'] = '一般';
$string['backupmode20'] = '匯入';
$string['backupmode30'] = '課程資料中心';
$string['backupmode40'] = '相同網站';
$string['backupmode50'] = '自動的';
$string['backupmode60'] = '已轉換過';
$string['backupsection'] = '備份課程學習單元：{$a}';
$string['backupsettings'] = '備份設定';
$string['backupsitedetails'] = '網站細節';
$string['backupstage16action'] = '繼續';
$string['backupstage1action'] = '下一個';
$string['backupstage2action'] = '下一個';
$string['backupstage4action'] = '執行備份';
$string['backupstage8action'] = '繼續';
$string['backuptype'] = '類型';
$string['backuptypeactivity'] = '活動';
$string['backuptypecourse'] = '課程';
$string['backuptypesection'] = '學習單元';
$string['backupversion'] = '備份版本';
$string['cannotfindassignablerole'] = '在備份檔的這{$a}角色，無法對應到你允許指派的任何角色。';
$string['choosefilefromactivitybackup'] = '活動備份區';
$string['choosefilefromactivitybackup_help'] = '當使用預設設定來備份各種活動時，備份檔將儲存在此';
$string['choosefilefromautomatedbackup'] = '自動備份';
$string['choosefilefromautomatedbackup_help'] = '包含自動產生的備份檔。';
$string['choosefilefromcoursebackup'] = '課程備份區';
$string['choosefilefromcoursebackup_help'] = '當使用預設設定來備份課程時，備份檔將儲存在此';
$string['choosefilefromuserbackup'] = '用戶私人備份區';
$string['choosefilefromuserbackup_help'] = '包含匿名用戶訊息的備份檔，將會儲存在此';
$string['config_keep_groups_and_groupings'] = '預設為維持現在的群組和分群';
$string['config_keep_roles_and_enrolments'] = '預設為維持現在的角色和選課狀態';
$string['config_overwrite_conf'] = '允許用戶覆寫目前課程的配置';
$string['config_overwrite_course_fullname'] = '預設上是會以備份檔裡的全名覆寫這課程全名。這需要勾選"覆寫課程配置"，並且目前的用戶要有更改課程全名的權限(moodle/course:changefullname)。';
$string['config_overwrite_course_shortname'] = '預設上是會以備份檔裡的簡稱覆寫這課程簡稱。這需要勾選"覆寫課程配置"，並且目前的用戶要有更改課程簡稱的權限(moodle/course:changeshortlname)。';
$string['config_overwrite_course_startdate'] = '預設上是會取備份檔裡的日期覆寫這課程的開始日期。這需要勾選"覆寫課程配置"，並且目前的用戶要有更改選課日期的權限(moodle/restore:rolldates)';
$string['configgeneralactivities'] = '預設為將各種活動包含在備份檔。';
$string['configgeneralanonymize'] = '預設為將與用戶有關的所有訊息變成匿名。';
$string['configgeneralbadges'] = '預設備份中包含獎章';
$string['configgeneralblocks'] = '預設為將各種區塊包含在備份檔。';
$string['configgeneralcalendarevents'] = '設定要包含行事曆事件在備份檔中';
$string['configgeneralcomments'] = '預設為將各種評語包含在備份檔。';
$string['configgeneralcompetencies'] = '設定要包含核心能力在備份檔中';
$string['configgeneralfilters'] = '預設為將各種過濾器包含在備份檔。';
$string['configgeneralgroups'] = '設定預設值，以便在備份時包含全組和分群。';
$string['configgeneralhistories'] = '預設為將用戶活動日誌包含在備份檔。';
$string['configgenerallogs'] = '預設為將日誌包含在備份檔。';
$string['configgeneralquestionbank'] = '若啟用，預設將題庫包含在備份檔裡面。請注意：關閉這一設定時，要同時關閉用到這題庫的活動(如測驗模組)的備份。';
$string['configgeneralroleassignments'] = '預設為將各種角色指派包含在備份檔。';
$string['configgeneralusers'] = '預設為將所有用戶包含在備份檔。';
$string['configgeneraluserscompletion'] = '若啟用，會預設為把用戶完成課程資訊包含在備份檔。';
$string['configloglifetime'] = '請指定一個備份日誌要保留的時間。超過此時間的舊日誌將會被自動刪除。

建議你將此時間縮短，因為備份日誌所紀錄的訊息非常多。';
$string['configrestoreactivities'] = '為回存活動設定預設值';
$string['configrestorebadges'] = '為回存獎章設定預設值';
$string['configrestoreblocks'] = '為回存區塊設定預設值';
$string['configrestorecalendarevents'] = '為回存行事曆事件設定預設值';
$string['configrestorecomments'] = '為回存評論設定預設值';
$string['configrestorecompetencies'] = '為回存核心能力設定預設值';
$string['configrestoreenrolments'] = '為回存選課方式設定預設值';
$string['configrestorefilters'] = '為回存過濾器設定預設值';
$string['configrestoregroups'] = '為是否回存"群組與分群"設定預設值，若它們有包含在備份檔裏頭。';
$string['configrestorehistories'] = '為是否回存"用戶歷史"設定預設值，若它們有包含在備份檔裏頭。';
$string['configrestorelogs'] = '為是否回存"日誌檔"設定預設值，若它們有包含在備份檔裏頭。';
$string['configrestoreroleassignments'] = '為是否回存"角色指派"設定預設值，若它們有包含在備份檔裏頭。';
$string['configrestoreusers'] = '為是否回存"用戶"設定預設值，若它們有包含在備份檔裏頭。';
$string['confirmcancel'] = '取消備份';
$string['confirmcancelimport'] = '取消匯入';
$string['confirmcancelno'] = '不要取消';
$string['confirmcancelquestion'] = '你確定你要取消？任何你輸入的訊息將會遺失。';
$string['confirmcancelrestore'] = '取消回存';
$string['confirmcancelyes'] = '取消備份';
$string['confirmnewcoursecontinue'] = '新課程警告';
$string['confirmnewcoursecontinuequestion'] = '在課程還原過程中，將會建立一暫時性(隱藏的)課程。

要放棄還原，請擊點"取消"。

請不要在還原過程中關閉瀏覽器。';
$string['coursecategory'] = '課程還原時的分類';
$string['courseid'] = '原初課程編號';
$string['coursesettings'] = '課程設定';
$string['coursetitle'] = '標題';
$string['currentstage1'] = '初始設定';
$string['currentstage16'] = '完成';
$string['currentstage2'] = '架構設定';
$string['currentstage4'] = '確認和檢查';
$string['currentstage8'] = '執行備份';
$string['enterasearch'] = '輸入一搜尋';
$string['error_block_for_module_not_found'] = '在課程模組(id: {$a->mid})找到孤兒區塊 (id: {$a->bid})實例。這一區塊將不會包含在備份檔中。';
$string['error_course_module_not_found'] = '找到孤兒課程模組(編號:{$a})，這模組不會加入備份檔。';
$string['errorcopyingbackupfile'] = '在回存之前，無法複製這備份檔到這暫時資料夾。';
$string['errorfilenamemustbezip'] = '你輸入的檔案必須是zip壓縮檔，並以 .mbz 為副檔名。';
$string['errorfilenamerequired'] = '你必須為這一備份輸入一有效的檔案名稱。';
$string['errorfilenametoolong'] = '檔名的長度必須少於255字母';
$string['errorinvalidformat'] = '未知的備份格式';
$string['errorinvalidformatinfo'] = '這選出的檔案不是有效的Moodle備份檔，且無法還原。';
$string['errorminbackup20version'] = '這一備份檔是以Moodle備份模組的測試版本({$a->backup})建立。最低需求是 {$a->min}，因此無法被還原。';
$string['errorrestorefrontpagebackup'] = '你在這首頁上只可以回存首頁的備份';
$string['executionsuccess'] = '備份檔已經成功建立。';
$string['filealiasesrestorefailures'] = '以別名回存失敗';
$string['filealiasesrestorefailures_help'] = '別名檔是指向其他檔案的象徵性鏈結，包括那些儲存在外部倉儲的。

在某些情況下，Moodle無法儲存它們，舉例來說，把備份檔回存到另一個網站，或指向的檔案已經不存在。

失敗的更多細節和真正的理由，可以在回存日誌檔中找到。';
$string['filealiasesrestorefailures_link'] = 'restore/filealiases';
$string['filealiasesrestorefailuresinfo'] = '某些包含在備份檔的別名檔案無法被回存。

以下清單包含它們在原始網站中被期待的位置和指向的資源檔。';
$string['filename'] = '檔案名稱';
$string['filereferencesincluded'] = '備份包包含了引用外部內容的檔案，它們在其他網站無法作用。';
$string['filereferencesnotsamesite'] = '這一備份檔是來自不同的網站，因此檔案的參照無法被恢復';
$string['filereferencessamesite'] = '這一備份是來自這一網站，所以檔案的參照可以被恢復';
$string['generalactivities'] = '包含活動和資源';
$string['generalanonymize'] = '匿名訊息';
$string['generalbackdefaults'] = '一般備份預設';
$string['generalbadges'] = '包含獎章';
$string['generalblocks'] = '包含區塊';
$string['generalcalendarevents'] = '包含行事曆事件';
$string['generalcomments'] = '包含回應';
$string['generalcompetencies'] = '包含核心能力';
$string['generalenrolments'] = '包含選課方法';
$string['generalfilters'] = '包含過濾器';
$string['generalgradehistories'] = '包含成績歷史紀錄';
$string['generalgroups'] = '包含群組和分群';
$string['generalhistories'] = '包含歷史紀錄';
$string['generallogs'] = '包含日誌';
$string['generalquestionbank'] = '包含題庫';
$string['generalrestoredefaults'] = '一般回存預設';
$string['generalrestoresettings'] = '一般回存設定';
$string['generalroleassignments'] = '包含角色指派';
$string['generalsettings'] = '一般備份設定';
$string['generalusers'] = '包含用戶';
$string['generaluserscompletion'] = '包含用戶完成訊息';
$string['hidetypes'] = '隱藏類型的選項';
$string['importbackupstage16action'] = '繼續';
$string['importbackupstage1action'] = '下一個';
$string['importbackupstage2action'] = '下一個';
$string['importbackupstage4action'] = '執行匯入';
$string['importbackupstage8action'] = '繼續';
$string['importcurrentstage0'] = '課程選擇';
$string['importcurrentstage1'] = '初始設定';
$string['importcurrentstage16'] = '完成';
$string['importcurrentstage2'] = '架構設定';
$string['importcurrentstage4'] = '確認和檢查';
$string['importcurrentstage8'] = '進行匯入';
$string['importfile'] = '匯入一備份檔';
$string['importgeneralduplicateadminallowed'] = '允許管理員解決衝突';
$string['importgeneralduplicateadminallowed_desc'] = '若這網站有一帳號的用戶名稱是"admin"，那麼試圖回存一個包含有一帳號的用戶號名稱是"admin"的備份檔，就會造成衝突。若啟用這一設定，系統將會更改備份的用戶名稱為"admin_xyz"，以解決這一衝突。';
$string['importgeneralmaxresults'] = '一次最多可以匯入多少個課程';
$string['importgeneralmaxresults_desc'] = '此項用來控制於匯入的第一個步驟中顯示多少個課程';
$string['importgeneralsettings'] = '一般匯入預設';
$string['importrootsettings'] = '匯入設定';
$string['importsettings'] = '一般匯入設定';
$string['importsuccess'] = '匯入已經完成，請擊點繼續以回到課程。';
$string['includeactivities'] = '包含：';
$string['includeditems'] = '包含項目：';
$string['includefilereferences'] = '檔案引用到外部內容';
$string['includesection'] = '學習單元{$a}';
$string['includeuserinfo'] = '用戶資料';
$string['jumptofinalstep'] = '跳到最後一步驟';
$string['keep'] = '保留';
$string['locked'] = '已被封鎖';
$string['lockedbyconfig'] = '此設定被預設的備份設定鎖住。';
$string['lockedbyhierarchy'] = '因相依關係而被鎖定';
$string['lockedbypermission'] = '你無足夠權限去更改這一設定';
$string['loglifetime'] = '保持日誌檔';
$string['managefiles'] = '管理備份檔';
$string['mergerestoredefaults'] = '當併入另一課程時，回存預設';
$string['missingfilesinpool'] = '在備份過程中，有些檔案無法被儲存，因此備份檔將無法回存它們。';
$string['module'] = '模組';
$string['moodleversion'] = 'Moodle版本';
$string['morecoursesearchresults'] = '多於 {$a} 個課程已被找到，正在顯示前面 {$a} 個結果';
$string['moreresults'] = '有太多結果，請輸入更精確詳細的搜尋詞。';
$string['nomatchingcourses'] = '沒有課程可以顯示';
$string['norestoreoptions'] = '這兒沒有類別或現有課程讓你回存。';
$string['originalwwwroot'] = '備份檔的網址';
$string['overwrite'] = '覆蓋';
$string['preparingdata'] = '準備資料中';
$string['preparingui'] = '準備顯示頁面中';
$string['previousstage'] = '先前的';
$string['privacy:metadata:backup_controllers'] = '備份操作過程的清單';
$string['privacy:metadata:backup_controllers:itemid'] = '課程的編號';
$string['qcategory2coursefallback'] = '這試題類別"{$a->name}"原來是在備份檔的系統/課程類別情境中，在回存時將會建立在課程處境中。';
$string['qcategorycannotberestored'] = '這試題類別 "{$a->name}" 無法以還原建立';
$string['question2coursefallback'] = '這試題類別"{$a->name}"原來是在備份檔的系統/課程類別情境中，在回存時將會建立在課程處境中。';
$string['questionegorycannotberestored'] = '這試題"{$a->name}" 無法經由還原建立。';
$string['replacerestoredefaults'] = '預設為當回存到另一課程時刪除該課程內容';
$string['restoreactivity'] = '還原活動';
$string['restorecourse'] = '還原課程';
$string['restorecoursesettings'] = '課程設定';
$string['restoreexecutionsuccess'] = '這課程已經成功地回存，擊點以下"繼續"按鈕，將帶你檢視你回存的課程。';
$string['restorefileweremissing'] = '某些檔案無法被回存，因為它們在備份檔中找不到。';
$string['restorenewcoursefullname'] = '新課程名稱';
$string['restorenewcourseshortname'] = '新課程簡稱';
$string['restorenewcoursestartdate'] = '新開始日期';
$string['restorerolemappings'] = '還原角色對應';
$string['restorerootsettings'] = '還原設定';
$string['restoresection'] = '還原學習單元';
$string['restorestage1'] = '確認';
$string['restorestage16'] = '檢視';
$string['restorestage16action'] = '執行還原';
$string['restorestage1action'] = '下一個';
$string['restorestage2'] = '目的地';
$string['restorestage2action'] = '下一個';
$string['restorestage32'] = '處理';
$string['restorestage32action'] = '繼續';
$string['restorestage4'] = '各種設定';
$string['restorestage4action'] = '下一個';
$string['restorestage64'] = '完成';
$string['restorestage64action'] = '繼續';
$string['restorestage8'] = '架構';
$string['restorestage8action'] = '下一步';
$string['restoretarget'] = '還原的目標';
$string['restoretocourse'] = '還原到課程：';
$string['restoretocurrentcourse'] = '還原到此課程：';
$string['restoretocurrentcourseadding'] = '合併這備份課程到此課程';
$string['restoretocurrentcoursedeleting'] = '刪除此課程的內容，然後用備份檔還原';
$string['restoretoexistingcourse'] = '還原到一個現有課程';
$string['restoretoexistingcourseadding'] = '合併這備份課程到這一現有課程';
$string['restoretoexistingcoursedeleting'] = '刪除這現有課程的內容，然後用備份檔還原';
$string['restoretonewcourse'] = '還原成新課程';
$string['restoringcourse'] = '課程還原進行中';
$string['restoringcourseshortname'] = '正在還原中';
$string['rootenrolmanual'] = '還原為手動選課';
$string['rootsettingactivities'] = '包含各種活動和資源';
$string['rootsettinganonymize'] = '用戶訊息匿名化';
$string['rootsettingbadges'] = '包含獎章';
$string['rootsettingblocks'] = '包含各種區塊';
$string['rootsettingcalendarevents'] = '包含行事曆事件';
$string['rootsettingcomments'] = '包含評語';
$string['rootsettingcompetencies'] = '包含核心能力';
$string['rootsettingenrolments'] = '包含選課方法';
$string['rootsettingenrolments_always'] = '是的，永遠如此';
$string['rootsettingenrolments_never'] = '不，以手動選課方式回存用戶';
$string['rootsettingenrolments_withusers'] = '是的，但只有在有包含用戶時';
$string['rootsettingfilters'] = '包含過濾器';
$string['rootsettinggradehistories'] = '包含成績紀錄';
$string['rootsettinggroups'] = '包含群組和分群';
$string['rootsettingimscc1'] = '轉換到IMS共同容器 1.0';
$string['rootsettingimscc11'] = '轉換到IMS共同容器 1.1';
$string['rootsettinglogs'] = '包含課程日誌';
$string['rootsettingquestionbank'] = '包含題庫';
$string['rootsettingroleassignments'] = '包含用戶角色指派';
$string['rootsettings'] = '備份設定';
$string['rootsettingusers'] = '包含有選課的用戶';
$string['rootsettinguserscompletion'] = '包含用戶學習進度細節';
$string['sectionactivities'] = '各種活動';
$string['sectioninc'] = '包含在備份檔(不含用戶訊息)';
$string['sectionincanduser'] = '包含在備份檔，伴隨用戶訊息';
$string['selectacategory'] = '選擇類別';
$string['selectacourse'] = '選擇課程';
$string['setting_course_fullname'] = '課程名稱';
$string['setting_course_shortname'] = '課程簡稱';
$string['setting_course_startdate'] = '課程開始日期';
$string['setting_keep_groups_and_groupings'] = '保持當前的群組和分類';
$string['setting_keep_roles_and_enrolments'] = '保持當前的角色和環境';
$string['setting_overwrite_conf'] = '覆蓋課程的配置';
$string['setting_overwrite_course_fullname'] = '覆蓋課程的全稱';
$string['setting_overwrite_course_shortname'] = '覆蓋課程的簡稱';
$string['setting_overwrite_course_startdate'] = '覆蓋課程的開始日期';
$string['showtypes'] = '顯示類型的選項';
$string['sitecourseformatwarning'] = '這是一個首頁的備份，注意它只能回存在首頁上。';
$string['skiphidden'] = '跳過隱藏課程';
$string['skiphiddenhelp'] = '選擇是否要跳過隱藏的課程';
$string['skipmodifdays'] = '跳過有幾天沒有修改過的課程';
$string['skipmodifdayshelp'] = '選擇要跳過超過幾天沒有修改的課程';
$string['skipmodifprev'] = '跳過從上一次備份後就沒修改的課程';
$string['skipmodifprevhelp'] = '選擇是否要跳過自從上次自動備份之後，就沒有被修改過的課程。這設定需要配合日誌的啟用。';
$string['storagecourseandexternal'] = '課程備份檔案區和指定的目錄';
$string['storagecourseonly'] = '課程備份檔案區';
$string['storageexternalonly'] = '為自動備份指定目錄';
$string['timetaken'] = '所用時間';
$string['title'] = '標題';
$string['totalcategorysearchresults'] = '類別總數：{$a}';
$string['totalcoursesearchresults'] = '課程總數：{$a}';
$string['unnamedsection'] = '未命名的單元';
$string['userinfo'] = '用戶資訊';
