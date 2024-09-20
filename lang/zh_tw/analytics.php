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
 * Strings for component 'analytics', language 'zh_tw', version '3.9'.
 *
 * @package     analytics
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['analysablenotused'] = '可分析的{$a->analysableid} 未被使用: {$a->errors}';
$string['analysablenotvalidfortarget'] = '分析法{$a->analysableid} 對這目標無效:  {$a->result}';
$string['analysisinprogress'] = '還在被先前執行的程式繼續分析中';
$string['analytics'] = '分析';
$string['analyticslogstore'] = '用來分析的日誌檔儲存';
$string['analyticslogstore_help'] = '這一日誌檔儲存，將會被分析法應用程式用來讀取用戶的活動';
$string['analyticssettings'] = '分析法的設定';
$string['analyticssiteinfo'] = '網站資訊';
$string['defaultpredictionsprocessor'] = '預設的預測處理器';
$string['defaulttimesplittingmethods'] = '模型評估的預設分析時距';
$string['disabledmodel'] = '關閉的模組';
$string['erroralreadypredict'] = '檔案{$a}已經被用來產生預測';
$string['errorcannotreaddataset'] = '資料集檔案 {$a} 無法讀取。';
$string['errorcannotwritedataset'] = '資料集檔案 {$a} 無法被寫入。';
$string['errorexportmodelresult'] = '這機器學習模組無法匯出。';
$string['errorinvalidindicator'] = '無效的 {$a} 指標';
$string['errorinvalidtarget'] = '無效的{$a} 目標';
$string['errorinvalidtimesplitting'] = '無效的分析間隔時間；請確定你有加入的完整班級的班級名稱';
$string['errornoexportconfig'] = '在匯出模組的配置時發生問題';
$string['errornoindicators'] = '這一模組沒有任何指標';
$string['errornopredictresults'] = '預測處理器沒有傳回結果。請檢查輸出資料夾以找出原因。';
$string['errornoroles'] = '沒有界定學生和教師的角色。請在分析法設定頁界定他們角色';
$string['errornotarget'] = '這一模組沒有任何目標';
$string['errornotimesplittings'] = '這一模組沒有任何分析間隔';
$string['errorpredictioncontextnotavailable'] = '這一預測脈絡已經無法再使用';
$string['errorpredictionformat'] = '錯誤的預測計算格式';
$string['errorpredictionnotfound'] = '沒找到預測';
$string['errorpredictionsprocessor'] = '預測處理器錯誤：{$a}';
$string['errorpredictwrongformat'] = '預測處理器回傳資料無法被解碼："{$a}"';
$string['errorprocessornotready'] = '被選出的預測處理器還沒準備好：{$a}';
$string['errorsamplenotavailable'] = '這預測的樣本已經無法再使用';
$string['errorunexistingmodel'] = '不存在的模組 {$a}';
$string['errorunexistingtimesplitting'] = '所選用的時間分割方式無法使用';
$string['errorunknownaction'] = '未知的動作';
$string['eventinsightsviewed'] = '分析報告已被檢視';
$string['eventpredictionactionstarted'] = '預測處理已開始';
$string['fixedack'] = '接受';
$string['incorrectlyflagged'] = '錯誤標記';
$string['insightinfomessagehtml'] = '系統為您提供了分析報告。';
$string['insightinfomessageplain'] = '這系統為你產生一些分析報告：{$a}';
$string['insightmessagesubject'] = '"{$a}"的新分析報告';
$string['invalidanalysablefortimesplitting'] = '它無法使用{$a}時距來分析';
$string['invalidtimesplitting'] = '編號{$a}的模組需要有一個分析間隔法才能夠被用來做訓練';
$string['modeloutputdir'] = '模組輸出的資料夾';
$string['modeloutputdirinfo'] = '預測處理器用來儲存所有評估的訊息的資料夾。它對於程式除錯和進行研究很有用處。';
$string['modeltimelimit'] = '每一模組的分析時間限制';
$string['modeltimelimitinfo'] = '這是設定每一模組用來分析網站內容的時間限制。';
$string['nocourses'] = '沒有課程可分析';
$string['nodata'] = '沒有資料可分析';
$string['noevaluationbasedassumptions'] = '基於假定的模組無法被評鑑';
$string['noinsights'] = '沒有分析結果可報告';
$string['noinsightsmodel'] = '這一模組沒有產生分析結果';
$string['nonewdata'] = '';
$string['nonewranges'] = '';
$string['nopredictionsyet'] = '尚無預測可用';
$string['noranges'] = '還沒有預測';
$string['notapplicable'] = '不適用';
$string['notrainingbasedassumptions'] = '基於假定的模組，不需要訓練';
$string['notuseful'] = '沒有用處';
$string['novaliddata'] = '沒有有效資料可以使用';
$string['novalidsamples'] = '沒有有效樣本可以使用';
$string['onlycli'] = '分析處理只能經由指令行執行';
$string['onlycliinfo'] = '分析過程，比如評鑑模組、訓練機械學習演算法，或取得預測，都需要花一些時間。他們將會以定期執行任務方式進行，而且它們可以經由指令行強制執行。
若你要網站管理員藉由網頁介面執行這些過程，請關閉這一設定。';
$string['predictionsprocessor'] = '預測處理器';
$string['predictionsprocessor_help'] = '預測處理器是具有機械學習功能的後端，它能處理由計算模組的指標和目標所產生的資料集。
如果你改換用其他預測處理器，所有的演算法和預測結果將會被刪除。';
$string['privacy:metadata:analytics:indicatorcalc:contextid'] = '情境';
$string['privacy:metadata:analytics:indicatorcalc:endtime'] = '計算結束時間';
$string['privacy:metadata:analytics:indicatorcalc:sampleid'] = '樣本編號';
$string['privacy:metadata:analytics:indicatorcalc:sampleorigin'] = '樣本的原始表格';
$string['privacy:metadata:analytics:indicatorcalc:starttime'] = '計算開始時間';
$string['privacy:metadata:analytics:indicatorcalc:timecreated'] = '當做了預測時';
$string['privacy:metadata:analytics:predictionactions'] = '預測的動作';
$string['privacy:metadata:analytics:predictionactions:userid'] = '做這動作的用戶';
$string['privacy:metadata:analytics:predictions'] = '預測';
$string['privacy:metadata:analytics:predictions:calculations'] = '指標計算';
$string['privacy:metadata:analytics:predictions:contextid'] = '情境';
$string['privacy:metadata:analytics:predictions:modelid'] = '模組編號';
$string['privacy:metadata:analytics:predictions:prediction'] = '預測';
$string['privacy:metadata:analytics:predictions:predictionscore'] = '預測的分數';
$string['privacy:metadata:analytics:predictions:sampleid'] = '樣本編號';
$string['privacy:metadata:analytics:predictions:timeend'] = '計算時間結束';
$string['privacy:metadata:analytics:predictions:timestart'] = '計算時間開始';
$string['processingsitecontents'] = '處理網站內容';
$string['successfullyanalysed'] = '已成功分析';
$string['timesplittingmethod'] = '分析的時距';
$string['timesplittingmethod_help'] = '這分析間隔將會把整個課程的進行切隔成幾個時段；預測引擎將會在每個時段的尾端執行。建議你只啟用你想要用的時間切割方式。這評估歷程將會根據所有啟用的時間切割方式反覆執行，因此啟用的時間切割方式愈多，評估歷程將會越慢。';
$string['timesplittingmethod_link'] = 'Time_splitting_methods';
$string['useful'] = '有用的';
$string['viewinsight'] = '檢視分析報告';
$string['viewinsightdetails'] = '檢視分析報告細節';
$string['viewprediction'] = '檢視預測詳情';
