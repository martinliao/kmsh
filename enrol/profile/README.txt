support Moodle3.1(scheduled task)
modify date : 2019/01/15

依使用者個人資料欄位(含客制個人資料欄位)篩選符合之用戶，並自動加選至課程內。
選課歡迎通知信發送。
選課用戶不提供自行退選功能。

【mod33】
upgrade scheduled task (processenrolments_task)
	*透過排程同步課程設定的條件，自動"加選"課程內參與者。
	*無法自動將資格不符合的名單退選，須進入課程手動執行下列其中一個步驟:
	1.執行'清空選課'再執行'加選使用者'。
	2.或勾選'當使用者不符合條件時，將使用者退選 當使用者不符合條件時，將使用者退選'，並執行'加選使用者'。
fix 條件內含用戶客制欄位時，產生的sql會有問題($arraysql2)
fix button css icon跑掉問題
add 編修頁加註說明

ps.如果有客制欄位，請自行在moodle.php增加語言包(string為欄位名稱，所以不可以有大寫、空隔)
//auth_changyee extension user profile , used in enrol_attributes(enrol_profile)
$string['companycode'] = 'Company';
$string['depid'] = 'Dept Id';
$string['id'] = 'Dept Code';
$string['memid'] = 'Member ID';


【SCHEMA說明(mdl_enrol)】
customint1 = Use group enrolment keys;
customint2 = Unenrol inactive after;
customint3 = max enrolled
customint4 = send course welcome message
customint5 = cohort
customint6 = Allow new enrolments;
customtext1 => rolues
customtext2 => welcome message