<?php
/**
 * Language strings
 *
 * @package    local
 * @subpackage cwbadmin
 * @copyright  2014 Jack Liou <jack@click-ap.com>
 * @copyright  2014 Elaine Chen <elaine@click-ap.com>
 * @license    http://www.click-ap.com/copyleft/gpl.html GNU GPL v3 or later
 */
$string['pluginname'] = '系列課程';

$string['programs:manageglobalsettings'] = '管理全域設定';
$string['programs:viewprogram'] = '檢視系列課程';
$string['programs:manageownprogram'] = '管理自已的系列課程';
$string['programs:viewotherprogram'] = '管理其它人的系列課程';
$string['programs:earnprogram'] = '獲得系列課程';
$string['programs:createprogram'] = '建立系列課程';
$string['programs:deleteprogram'] = '刪除系列課程';
$string['programs:configuredetails'] = '編修細節';
$string['programs:configurecriteria'] = '編修完成規則';
$string['programs:configuremessages'] = '編修訊息內容';
$string['programs:awardprogram'] = '獲得系列課程證書';
$string['programs:viewawarded'] = '檢視取得的系列課程';

$string['activate'] = '啟用存取';
$string['activatesuccess'] = '此系列課程已經順利啟用.';
$string['addprogram'] = '新增系列課程';
$string['addcriteria'] = '新增獲得判斷規則';
$string['addprogramcriteria'] = '增加判斷規則';
$string['addcriteriatext'] = '增加判斷規則，請從下拉選單中選擇一個選項.';
$string['addcourse'] = '增加一組課程';
$string['addcourse_help'] = '選出這一個系列課程要求的所有課程。按下CTRL鍵可選擇多個項目.';
$string['after'] = '在頒授的日期之後.';
$string['all'] = '全部';
$string['any'] = '任何';
$string['anymethodcourseset'] = '完成任何選出的課程';
$string['archiveprogram'] = '你想要刪除系列課程\'{$a}\'，但保留已頒發出去的系列課程證書?';
$string['archiveconfirm'] = '刪除並保留已頒發出去的系列課程證書';
$string['archivehelp'] = '<p>這一選項表示這系列課程將會被標示為"撤回"，且將不會出現在系列課程的列表中。用戶將不再獲得此系列課程，但是現有的系列課程證書獲獎人仍然能夠在他們的個人資料頁上顯示已取得此系列課程.</p>
<p>若您希望您的用戶仍然能存取他獲得的系列課程，您應該選擇這一選項，而不是完全刪除系列課程</p>';

$string['allmethodcourseset'] = '完成全部選出的課程';
$string['awards'] = '頒發用戶';

$string['bcriteria'] = '判斷規則';
$string['boverview'] = '綜覽';
$string['bdetails'] = '編輯細節';
$string['bawards'] = '頒發用戶 ({$a})';
$string['bmessage'] = '訊息';
$string['bydate'] = '完成於';

$string['copyof'] = '{$a}複製';
$string['create'] = '新增系列課程';
$string['createbutton'] = '新增系列課程';
$string['crontask'] = '系列課程排程工作';
$string['criteria_descr_bydate'] = '於日期 <em>{$a}</em> 完成課程';
$string['criteria_descr_grade'] = '要達到最低成績 <em>{$a}</em> ';
$string['criteria_0'] = '這一個系列課程是用來獎勵...';
$string['criteria_1'] = '活動完成';
$string['criteria_1_help'] = '允許頒發系列課程證書給在一課程中完成一組活動的客戶。';
$string['criteria_2'] = '依據角色手動頒發';
$string['criteria_2_help'] = '允許手動頒發系列課程證書給在這網站或課程中有特殊角色的用戶。';
$string['criteria_3'] = '社會參與';
$string['criteria_3_help'] = '社交';
$string['criteria_4'] = '完成課程';
$string['criteria_4_help'] = '允許頒發系列課程給完成這課程的用戶。這判斷規準可以加上附帶條件，例如，最低分數或課程完成日期。';
$string['criteria_5'] = '完成一組課程';
$string['criteria_5_help'] = '允許頒發系列課程給完成一組課程的用戶。每一課程可以加上附帶條件，例如，最低分數或課程完成日期。';
$string['criteria_6'] = '完成個人資料表';
$string['criteria_6_help'] = '允許頒發系列課程給有填寫個人資料表上特定欄位的用戶。你可以從預設和自訂的個人資料表欄位上選擇。';
$string['criteriacreated'] = '已成功建立系列課程判斷規準';
$string['criteriadeleted'] = '已成功刪除系列課程判斷規準';
$string['criteriaupdated'] = '已成功更新系列課程判斷規準';
$string['criteria_descr'] = '用戶完成以下條件，即可獲頒這一個系列課程。';
$string['criteria_descr_0'] = '用戶若完成<strong>{$a}</strong>個列表中的條件，即可獲頒這一個系列課程。';
$string['criteria_descr_1'] = '已經完成<strong>{$a}</strong>個下列活動：';
$string['criteria_descr_2'] = '此系列課程已經頒發給有<strong>{$a}</strong> 角色的用戶，
有下列角色：';
$string['criteria_descr_4'] = '用戶必須完成這課程';
$string['criteria_descr_5'] = '必須至少完成<strong>{$a}</strong>個下列課程：';
$string['criteria_descr_6'] = '必須至少完成<strong>{$a}</strong>個下列用戶資料表欄位：';

$string['criteria_descr_short0'] = '完成 <strong>{$a}</strong> 個，共有：';
$string['criteria_descr_short1'] = '完成 <strong>{$a}</strong> 個，共有：';
$string['criteria_descr_short2'] = '頒獎者： <strong>{$a}</strong> 於:';
$string['criteria_descr_short4'] = '完成這課程';
$string['criteria_descr_short5'] = '完成<strong>{$a}</strong> 的：';
$string['criteria_descr_short6'] = '完成<strong>{$a}</strong> 的：';
$string['criteria_descr_single_1'] = '必須完成下列活動：';
$string['criteria_descr_single_2'] = '這一獎章必須頒授給有下列角色的用戶：';
$string['criteria_descr_single_4'] = '用戶必須完成這一課程';
$string['criteria_descr_single_5'] = '必須完成下列課程：';
$string['criteria_descr_single_6'] = '必須完成下列用戶資料欄位：';
$string['criteria_descr_single_short1'] = '完成：';
$string['criteria_descr_single_short2'] = '頒獎者：';
$string['criteria_descr_single_short4'] = '完成這課程';
$string['criteria_descr_single_short5'] = '完成：';
$string['criteria_descr_single_short6'] = '完成：';

$string['deactivate'] = '關閉存取';
$string['deactivatesuccess'] = '存取系列課程的功能已經順利關閉.';
$string['delprogram'] = '您想要刪除系列課程\'{$a}\' ，並移除所有已核發出去的系列課程證書嗎？';
$string['delconfirm'] = '刪除並移除已經頒發出去的系列課程';
$string['delcritconfirm'] = '你確定你要刪除這一判斷規則嗎?';
$string['deletehelp'] = '<p>完全刪除此系列課程表示它的所有資訊和判斷規則將被完全移除。獲得這一個系列課程的用戶將不再能夠存取它和在他們的個人資料頁上顯示它。 </p>';
$string['description'] = '描述';

$string['error:invalidexpireperiod'] = '失效期限不可以設定為負數或0.';
$string['error:duplicatename'] = '在這系統中已經存有這一名稱的系列課程.';
$string['error:invalidexpiredate'] = '失效日期必須設定在未來時間.';
$string['error:nosuchcourse'] = '警告：這一課程已經無法使用.';
$string['error:nocourses'] = '在這網站上沒有任何課程啟用課程完成記錄功能，所以無法顯示。你可以在課程設定中啟用課程完成紀錄功能.';
$string['error:parameter'] = '警告：至少要選出一個附加條件以確保正確的系列課程頒授流程.';

$string['expirydate'] = '失效日期';
$string['expirydate_help'] = '你可自行決定，讓系列課程在指定的日期即失效，或是獎章頒發給用戶多久之後即失效。';
$string['fixed'] = '固定的日期';


$string['manageprograms'] = '管理系列課程';
$string['method'] = '這一判斷規準已完成，當...';
$string['messagesubject'] = '恭喜! 您剛獲得一個系列課程證書';
$string['messagebody'] = '<p>您已經被頒發了一個系列課程證書 "%programname%"!</p>
<p>相關訊息您瀏覽 %programlink% 系列課程說明頁面.</p>';
$string['mingrade'] = '最低成績要求';

$string['newprogram'] = '新增系列課程';
$string['never'] = '從不';
$string['nocourses'] = '無可用的課程.';
$string['noprograms'] = '無可用的系列課程.';
$string['noparamstoadd'] = '這裡沒有附加條件可以加到這一個系列課程的要求條件中.';
$string['nocriteria'] = '此系列課程的判斷規準尚未設定.';
$string['nothingtoadd'] = '無可用的判斷規準可以添加.';

$string['issuancedetails'] = '系列課程到期';
$string['program'] = '系列課程';
$string['program_help'] = '系列課程只可以因完成課程活動而頒發給用戶,這包括完成一組課程.';
$string['programbanner'] = '橫幅圖像';
$string['programaward'] = '證書底圖';
$string['programdetails'] = '系列課程細節';
$string['programsettings'] = '系列課程設定';
$string['programstoearn'] = '系列課程數量: {$a}';
$string['programimage'] = '形象圖像';
$string['programimage_help'] = '這是代表這一系列課程的圖像.

To add a new image, browse and select an image (in JPG or PNG format) then click "Save changes". The image will be cropped to a square and resized to match program image requirements. ';
$string['programtitle'] = '系列課程名稱 : ';
$string['programstatus_0'] = '用戶不可以使用';
$string['programstatus_1'] = '用戶可以使用';
$string['programstatus_2'] = '用戶不可以使用';
$string['programstatus_3'] = '用戶可以使用';
$string['programstatus_4'] = '存檔';

$string['relative'] = '相對日期';
$string['requiredcourse'] = '至少要添加一個課程到課程組合的判斷規準.';
$string['reviewprogram'] = '變更系列課程的存取狀態';
$string['reviewconfirm'] = '<p>這將讓用戶看到你的系列課程，並讓使用者可以開始取得證書 </p>

<p>可能有些用戶已經符合這獎章的判斷規準，並在您啟用它之後立即獲頒此系列課程</p>

<p>一旦有系列課程被頒發出去，它將會被<strong> 鎖定</strong> ---某些設定，包括判斷規準和失效日期都無法再更改。
</p>

<p>你確定你要啟用這系列課程"{$a}"的存取嗎？</p>';
$string['save'] = '儲存';
$string['status'] = '系列課程狀態';
$string['status_help'] = '系列課程的狀態會決定它在這系統裡的動作：

* **可用的** -- 表示用戶可以獲得此系列課程，一旦系列課程被頒授給用戶，它的判斷規準就不能被修改了。

* **無法使用** -- 表示這系列課程還無法被獲得或人工的頒授。如果此系列課程以前從未頒授過，那它的判斷規準還可以被更改。

一個系列課程一旦被頒授過，它會自動被**鎖住**，被鎖住的系列課程還可以繼續頒發，但是它的判斷規準就不再可以更改。如果你需要修改一個被鎖住系列課程的細節或判斷規準，你可以複製這一個系列課程，然後作所有需要的修改。';
$string['statusmessage_0'] = '此系列課程目前用戶無法取用。若您要讓用戶可以獲得此系列課程，請啟動存取功能。';
$string['statusmessage_1'] = '此系列課程目前用戶可以取用。請關閉存取功能來做更改。';
$string['statusmessage_2'] = '此系列課程目前用戶無法取用，且它的判斷規準已被鎖定。若您要讓用戶可以贏得此系列課程，請啟動存取功能。';
$string['statusmessage_3'] = '此系列課程現在用戶已經可以使用，且它的判斷規準已經鎖定。';
$string['statusmessage_4'] = '此系列課程目前已被存檔';

$string['eventprogramawarded'] = '已頒授系列課程';
$string['subject'] = '訊息主旨';
$string['message'] = '訊息';
$string['notification'] = '通知系列課程的建立者';
$string['notification_help'] = '這一設定用來控制要如何通知系列課程建立者，讓他們知道有多少系列課程頒授出去。

有五個選項可以使用:

* **從不**
* **每一次**
* **每天**
* **每週**
* **每月**';
$string['notifydaily'] = '每日';
$string['notifyevery'] = '每次';
$string['notifymonthly'] = '每月';
$string['notifyweekly'] = '每週';
$string['configuremessage'] = '系列課程訊息';
$string['variablesubstitution'] = '訊息內容中可替換的變數.';
$string['variablesubstitution_help'] = '在系列課程的訊息中，你可以在訊息的主旨或內文中插入某些特定的變項，這樣在訊息送出時會將變項替換成真實的資料。這些變項要一字不變地插在文字中，它包括：

%programname%
: 這變項將替換成系列課程的完整名稱.

%username%
: 這變項將替換成收件者的全名.

%programlink%
: 這變項將替換成公開的網址，裡面有這頒授系列課程的訊息.';

$string['attachment'] = '在訊息上附加檔案';
$string['attachment_help'] = '若勾選，一個頒發的系列課程證書將會附加在收件者的email上以便下載。(你必須透過網站管理 >外掛 > 訊息輸出 > 電子郵件，來啟用附件)';
$string['noawards'] = '尚未有人取得此系列課程證書.';
$string['dateawarded'] = '頒發的日期';
$string['viewprogram'] = '檢視獲得的系列課程證書';
$string['creatorbody'] = '<p>{$a->user} 已經完成此系列課程所要求的條件，並已經被頒授證書。請在 {$a->link}檢視獲得的系列課程</p>';
$string['creatorsubject'] = '\'{$a}\' 已取得!';

$string['expiredate'] = '此系列課程在{$a}失效.';
$string['expireperiod'] = '此系列課程在頒授後{$a}天失效.';
$string['expireperiodh'] = '此系列課程在頒授後{$a}小時失效.';
$string['expireperiodm'] = '此系列課程在頒授後{$a}分鐘失效.';
$string['expireperiods'] = '此系列課程在頒授後{$a}秒失效.';
$string['noexpiry'] = '此系列課程未設定失效日期.';

$string['strftimedate_0'] = '中&nbsp;華&nbsp;民&nbsp;國';
$string['strftimedate_1'] = '西&nbsp;&nbsp;&nbsp;&nbsp;元';
$string['strftimedate_y'] = '年';
$string['strftimedate_m'] = '月';
$string['strftimedate_d'] = '日';

$string['download'] = '下載證書';

$string['programname'] = '系列課程名稱';
$string['awarddate'] = '頒發日期';
$string['borderstyle'] = '證書底圖';
$string['notcategorised'] = '未分類';
$string['program_category'] = '課程分類';
$string['delcritconfirm'] = '你確定你要刪除這一判斷規準嗎?';
$string['degreeofcompletion'] = '完成度';
$string['unfinished'] = '未完成';