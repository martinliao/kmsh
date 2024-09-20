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
 * Strings for component 'block_xp', language 'zh_tw', version '3.9'.
 *
 * @package     block_xp
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['actions'] = '行為';
$string['activityoresourceis'] = '此活動或資源是 {$a}';
$string['addacondition'] = '添加一條件';
$string['addarule'] = '添加一規則';
$string['addrulesformhelp'] = '最後一欄代表當經驗累積到標準時所獲得的經驗值總量';
$string['admindefaultrulesintro'] = '在一個區塊被添加，以下的則規將被用作為課程的預設';
$string['admindefaultsettingsintro'] = '當區塊剛加入到課程，下列的設定將被用作為課程的預設';
$string['admindefaultvisualsintro'] = '以下將會被用作為預設，當有新的區塊加入到課程';
$string['anonymity'] = '匿名';
$string['anonymity_help'] = '此設定控制參加者是否可以看到彼此的名稱和頭像。';
$string['awardaxpwhen'] = '<strong>{$a}</strong> 經驗值會增加當：';
$string['basexp'] = '演算法基底';
$string['blockappearance'] = '禁止出現';
$string['cachedef_filters'] = '等級濾器';
$string['cachedef_ruleevent_eventslist'] = '一些事件的清單';
$string['cannotshowblockconfig'] = '我間中會在此顯示外觀設定，但不能夠尋找你的區塊。如要改變這區塊的外觀，返回 [這裹]({$a})(或你添加區塊的那裹)，打開編緝模式，及跟據"配置"設項中的區塊下拉列表。如果無未能找到區塊，請再次加入區塊到你的課程。';
$string['changelevelformhelp'] = '如果更改級別數目，自訂級別獎章將被暫時禁用，以防止出現沒有獎章的級別。如果更改了級別數目，當你儲存這個表格後，可以到視覺效果頁面重新啟用自訂獎章。';
$string['cheatguard'] = '防作弊守衛';
$string['coefxp'] = '演算法係數';
$string['colon'] = '{$甲->甲}: {$甲->乙}';
$string['configdescription'] = '附加說明';
$string['configdescription_help'] = '在學生的級別，一個簡短的介紹會顯示在區塊中。學生們有能力消除這訊息，在他們不希望再次看到這訊息的情況下';
$string['configheader'] = '設定';
$string['configrecentactivity'] = '顯示目前獎勵';
$string['configrecentactivity_help'] = '當啟用，方塊便會顯示一張包含現時活動的清單，並給予學生分數作狀獎勵';
$string['configtitle'] = '標題';
$string['configtitle_help'] = '題目的區塊';
$string['congratulationsyouleveledup'] = '恭喜!';
$string['coolthanks'] = '太酷了，謝謝!';
$string['courselog'] = '課程紀錄';
$string['coursereport'] = '課程報告';
$string['courserules'] = '課程規則';
$string['coursesettings'] = '課程設定';
$string['coursevisuals'] = '課程視覺效果';
$string['customizelevels'] = '自訂等級';
$string['dangerzone'] = '危險區域';
$string['defaultlevels'] = '預設等級';
$string['defaultrules'] = '預設規則';
$string['defaultrulesformhelp'] = '這是外掛程式提供的預設規則，這些規則會自動給予預設經驗值並忽略一些冗餘事件，你自訂的規則優先於他們。';
$string['defaultsettings'] = '預設的設定值';
$string['defaultvisuals'] = '預設的視覺效果';
$string['deletecondition'] = '刪除條件';
$string['deleterule'] = '刪除規則';
$string['description'] = '說明';
$string['difference'] = '差異';
$string['discoverlevelupplus'] = '發現升級了！';
$string['dismissnotice'] = '停用通知';
$string['displayeveryone'] = '顯示所有人';
$string['displaynneighbours'] = '顯示 {$a} 邻居';
$string['displayoneneigbour'] = '顯示一位邻居';
$string['displayparticipantsidentity'] = '顯示參加者身份';
$string['displayrank'] = '顯示排名';
$string['displayrelativerank'] = '顯示相對排名';
$string['enablecheatguard'] = '允許防作弊守衛';
$string['enablecheatguard_help'] = '防作弊守衛提供一個簡單、便宜的機制去防止學生明顯地誤用這系統，例如不斷地重新整理同一頁面，或不斷的重複相同的動作';
$string['enableinfos'] = '啟用資訊頁';
$string['enableinfos_help'] = '當設定為"否"時，學生將無法檢視資訊頁。';
$string['enableladder'] = '啟用階梯';
$string['enableladder_help'] = '當設定為"否"時，學生將無法檢視階梯。';
$string['enablelevelupnotif'] = '啟用升級提示';
$string['enablelevelupnotif_help'] = '當設定為"是"時，學生將會看到一個祝賀他們達到新等級的彈出式視窗。';
$string['enablelogging'] = '啟用登入';
$string['enablexpgain'] = '啟用經驗值獲取';
$string['enablexpgain_help'] = '當設定為"否"的時候，沒有人會在此課程中獲得經驗值。這有效於凍結所獲得的經驗，或在某個時間啟用它。

請注意，你也可以使用功能 “block / xp：earnxp” 更精細地控制。';
$string['errorformvalues'] = '表單數值還是有些問題，請修正它們。';
$string['errorlevelsincorrect'] = '等級最小值為2';
$string['errornotalllevelsbadgesprovided'] = '您仍有一些等級獎章尚未提供。 少掉了: {$a}';
$string['errorunknownevent'] = '錯誤：未知的事件';
$string['errorunknownmodule'] = '錯誤：未知的模組';
$string['errorxprequiredlowerthanpreviouslevel'] = '所需的經驗值小於或等於前一個等級';
$string['event_user_leveledup'] = '使用者升級';
$string['eventis'] = '這事件是{$a}';
$string['eventname'] = '事件名稱';
$string['eventproperty'] = '事件性質';
$string['eventtime'] = '事件時間';
$string['for1day'] = '一天';
$string['for1month'] = '一個月';
$string['for1week'] = '一周';
$string['for3days'] = '三天';
$string['forever'] = '永遠';
$string['forthewholesite'] = '在整個網站中';
$string['give'] = '給予';
$string['hideparticipantsidentity'] = '隱藏參加者身份';
$string['hiderank'] = '隱藏排名';
$string['incourses'] = '在課程中';
$string['infos'] = '資訊';
$string['instructions'] = '更多資訊';
$string['invalidxp'] = '無效的經驗值';
$string['keeplogs'] = '保留紀錄';
$string['ladder'] = '階梯';
$string['ladderadditionalcols'] = '附加的列';
$string['ladderadditionalcols_help'] = '這設定限定了附加的列被顯示在階梯上。按CLRT或CMD KEY當選擇多於一列，或取消選擇一已選擇的列';
$string['level'] = '等級';
$string['levelbadges'] = '等級徽章';
$string['levelbadgesformhelp'] = '為檔案取名 [level].[file extension]，舉例來說： 1.png, 2.jpg, 等等... 推薦的影像大小為100x100。';
$string['levelcount'] = '等級計算';
$string['leveldesc'] = '等級說明';
$string['levels'] = '等級';
$string['levelswillbereset'] = '警告! 儲存這一表格將會重新計算所有人的等級!';
$string['levelup'] = '升級了!';
$string['levelupplus'] = '升級了！';
$string['levelx'] = '等級 #{$a}';
$string['likenotice'] = '<strong>您喜歡這外掛嗎？</strong> 請花一些時間到 Moodle.org<a href="{$a->moodleorg}" target="_blank">把它加到您的最愛</a> 並在 <a href="{$a->github}" target="_blank"> GitHub給它星號</a>。';
$string['limitparticipants'] = '限制參加者';
$string['limitparticipants_help'] = '此設定控制在排行榜中顯示的人。 邻居是在當前用戶之上和之下排名的參與者。 例如，當選擇 “顯示兩個邻居” 時，將只會顯示直接高於和低於當前用戶排名的兩個參與者。';
$string['logging'] = '登入';
$string['maxactionspertime'] = '在時間間隔中的最大動作量';
$string['maxactionspertime_help'] = '在所給的時間間隔中會用來計算經驗值的最大動作量，任何後續的動作將會被忽略。';
$string['movecondition'] = '移動條件';
$string['moverule'] = '移動規則';
$string['navinfos'] = '資訊';
$string['navladder'] = '階梯';
$string['navlevels'] = '等級';
$string['navlog'] = '紀錄';
$string['navpromo'] = '增加';
$string['navreport'] = '報告';
$string['navrules'] = '規則';
$string['navsettings'] = '設定';
$string['navvisuals'] = '視覺效果';
$string['participant'] = '參與';
$string['participatetolevelup'] = '參與這個課程以賺取經驗點值並升級';
$string['pickaconditiontype'] = '挑選一條件類型';
$string['pluginname'] = '升級!';
$string['pointsintimelinker'] = '每';
$string['pointsrequired'] = '分數的要求';
$string['progress'] = '進度';
$string['promocontactintro'] = '聯絡我們取得更多資訊。我們會盡快回覆。';
$string['promocontactus'] = '保持聯繫';
$string['promoemailusat'] = '電郵我們予_levelup@branchup.tech_';
$string['promoerrorsendingemail'] = '噢！我們未能發送這訊息⋯⋯請直接電郵我們予: {$a}.謝謝。';
$string['promoifpreferemailusat'] = '如您願意，請直接電郵我們予: {$a}.';
$string['promointro'] = '這是 升級！外掛程式，充分發揮其潛力吧！';
$string['promoyourmessagewassent'] = '謝謝，你的訊息已經送出。我們將會再次與你聯繁。';
$string['property:action'] = '事件動作';
$string['property:component'] = '事件組件';
$string['property:crud'] = '事件的CRUD';
$string['property:eventname'] = '事件名稱';
$string['property:target'] = '事件目標';
$string['rank'] = '排名';
$string['ranking'] = '名次';
$string['ranking_help'] = '排名是當前用戶在階梯中的絕對位置。相對排名是用戶與其鄰邻居之間的經驗值相差。';
$string['reallyresetdata'] = '真的要重設在這一課程中所有人的等級以及經驗值嗎？';
$string['reallyresetgroupdata'] = '真的要重設在這一群組中所有人的等級以及經驗值嗎？';
$string['recentrewards'] = '最近獎勵';
$string['requires'] = '要求';
$string['resetcoursedata'] = '重設課程資料';
$string['resetgroupdata'] = '重設群組資料';
$string['reverttopluginsdefaults'] = '恢復為外掛程式預設值';
$string['reverttopluginsdefaultsintro'] = '如果你希望恢復為外掛程式預設值，請接下面的按扭';
$string['reward'] = '獎勵';
$string['rule'] = '規則';
$string['rule:contains'] = '包含';
$string['rule:eq'] = '等於';
$string['rule:eqs'] = '嚴格等於';
$string['rule:gt'] = '大於';
$string['rule:gte'] = '大於或等於';
$string['rule:lt'] = '小於';
$string['rule:lte'] = '小於或等於';
$string['rule:regex'] = '符合正則表達式';
$string['rulecm'] = '活動或資源';
$string['rulecmdesc'] = '這活動或資源是 \'{$a->contextname}\'';
$string['ruleevent'] = '特定事件';
$string['ruleeventdesc'] = '這事件是\'{$a->eventname}\'';
$string['ruleproperty'] = '事件屬性';
$string['rulepropertydesc'] = '這屬性 \'{$a->property}\' {$a->compare} \'{$a->value}\'.';
$string['ruleset'] = '條件的集合';
$string['ruleset:all'] = '所有條件皆屬實';
$string['ruleset:any'] = '任何條件皆屬實';
$string['ruleset:none'] = '沒有條件屬實';
$string['rulesformhelp'] = '<p>這個外掛是利用事件來將學生進行的動作變成經驗值。你可以使用下面的表單來增加你自己的規則，以及檢視預設的規則。</p>
<p>建議您檢查此外掛之<a href="{$a->log}">日誌檔</a>來找出當你在課程中進行的動作時會觸發什麼事件，並且也對事件本身作進一步了解： <a href="{$a->list}">所有事件清單</a>, <a href="{$a->doc}">開發者憑證</a>.</p>
<p>最後，請注意這個外掛總是忽略：
<ul>
    <li>來自管理員、訪客或沒有登入之用戶所做的動作。</li>
    <li>不具有<em>block/xp:earnxp</em>權限的用戶所做的動作。</li>
    <li>在一個短暫的時間區間中重複的動作來避免作弊。</li>
    <li>教育等級的事件不等於 <em>參與</em>。</li>
</ul>
</p>';
$string['send'] = '傳送';
$string['someoneelse'] = '其他人';
$string['somethinghappened'] = '發生了一些事';
$string['taskcollectionloggerpurge'] = '清除收集日誌';
$string['timebetweensameactions'] = '進行相同動作之間所需的時間';
$string['timebetweensameactions_help'] = '以秒計算，在兩個相同動作之間所需的最短間隔時間。一個動作若與前一個動作有相同的情境和物件，就被視為是相同的動作，如果重覆閱讀同一則討論區貼文，閱讀討論區的貼文會被視為相同動作。';
$string['timeformaxactions'] = '最大動作量的時間限制';
$string['timeformaxactions_help'] = '時間限制(秒)，在此期間用戶不應該超過最大動作量。';
$string['tinytimenow'] = '現在';
$string['total'] = '總共';
$string['updateandpreview'] = '更新及預覽';
$string['usealgo'] = '使用演算法';
$string['usecustomlevelbadges'] = '使用自訂的等級獎章';
$string['usecustomlevelbadges_help'] = '當設定為"是"，你必須要為每個等級提供一個圖像';
$string['value'] = '值';
$string['valuessaved'] = '這些數值已順利儲存';
$string['viewtheladder'] = '檢視階梯';
$string['visualsintro'] = '上傳圖像以自訂級別的外觀';
$string['wewillreplyat'] = '我們將會回覆在: _{$a}_.';
$string['when'] = '當';
$string['wherearexpused'] = '經驗值要用在哪裡？';
$string['wherearexpused_desc'] = '當你設定為"在課程中"，獲得的經驗值將只會用在有添加這區塊的課程中。若你設定為"在整個網站上"，則用戶將會在網站升級，使用他在整個網站上所獲得的經驗值，而不是限制在選定的單一課程上。';
$string['whoops'] = '哎呦！';
$string['xp'] = '經驗值';
$string['xp:addinstance'] = '增加一個經驗值區塊';
$string['xp:earnxp'] = '賺取經驗值';
$string['xp:myaddinstance'] = '添加一個區塊到我的儀表板';
$string['xp:view'] = '檢視這區塊和與它有關的頁面';
$string['xpgaindisabled'] = '無法得到經驗值';
$string['xprequired'] = '所需經驗值';
$string['youreachedlevela'] = '您已經達到等級 {$a}!';
$string['yourmessage'] = '你的訊息';
$string['yourownrules'] = '你自己的規則';
