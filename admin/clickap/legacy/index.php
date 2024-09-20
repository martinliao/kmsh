<?php
/**
 * @package    clickap
 * @subpackage legacy
 * @copyright  2024 CLICK-AP {@https://www.click-ap.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->libdir.'/csvlib.class.php');
require_once('locallib.php');
require_once('upload_form.php');

admin_externalpage_setup('clickap_legacy');

$iid = optional_param('iid', '', PARAM_INT);

require_login();
$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_pagelayout('admin');
$PAGE->set_url('/admin/clickap/legacy/index.php', array());
$PAGE->set_title(get_string('uploadusersresult','clickap_legacy'));
require_capability('clickap/legacy:manage', $context);
    
if (empty($iid)) {
    global $DB;
    $mform1 = new clickap_legacy_upload_form();
    
    if ($formdata = $mform1->get_data()) {
        echo $OUTPUT->header();
        echo $OUTPUT->heading(get_string('uploadusersresult', 'clickap_legacy'));
    
        $iid = csv_import_reader::get_new_iid('uploadlegacy');
        $cir = new csv_import_reader($iid, 'uploadlegacy');
        $content = $mform1->get_file_content('csvfile');
        $readcount = $cir->load_csv_content($content, $formdata->encoding, $formdata->delimiter_name);
        $cir->init();
        //print_r($cir->get_columns());
        
        $hourcategories = '';
        if(!empty($formdata->hourcategory)){
            foreach($formdata->hourcategory as $key => $value){
                $hourcategories .= $key.',';
            }
        }
        
        $data = array();
        $linenum = $skipped = $created = $errors = 0;
        $upt = new legacy_progress_tracker();
        $upt->start();
        
        while ($row = $cir->next()) {
            $upt->flush();
            $linenum++;
            $upt->track('line', $linenum);
            $upt->track('id', $row[0], 'normal');
            $upt->track('course', s($row[1]), 'normal');
            
            if(empty($row[0])){
                $upt->track('status', get_string('missingfield', 'error', 'idnumber'), 'error');
                $skipped++;
                continue;
            }
            $userid = $DB->get_field('user', 'id', array('idnumber'=>$row[0]));
            if(empty($userid)){
                $upt->track('status', get_string('usernotexist', 'clickap_legacy'), 'error');
                $skipped++;
                continue;
            }
            if(empty($row[1])){
                $upt->track('status', get_string('missingfield', 'error', 'course'), 'error');
                $skipped++;
                continue;
            }
            $record = new stdClass();
            $record->userid = $userid;
            $record->hourcategories = $hourcategories;
            $record->fullname = trim($row[1]);

            $credit = $DB->get_field('clickap_code', 'id', array('type'=>'credit', 'idnumber'=>$row[7]));
            $llcategory = $DB->get_field('longlearn_categories', 'id', array('idnumber'=>$row[8]));
            $city = $DB->get_field('clickap_code', 'id', array('type'=>'city', 'idnumber'=>$row[9]));
            $unit = $DB->get_field('clickap_code', 'id', array('type'=>'unit', 'idnumber'=>$row[12]));
            $model = $DB->get_field('clickap_code', 'id', array('type'=>'model', 'idnumber'=>$row[17]));
            if(empty($credit) || empty($llcategory) || empty($city) || empty($unit) || empty($model)){
                $upt->track('status', get_string('missingcode', 'clickap_legacy'), 'error');
                $skipped++;
                continue;
            }
                    
            $record->longlearn_category = isset($llcategory) ? $llcategory : 0;
            $record->model = $model;
            $record->credit = isset($credit) ? $credit : 0;
            $record->city = isset($city) ? $city : 0;
            $record->unit = $unit;
            
            if(empty($row[11])){
                $upt->track('status', get_string('missinghours', 'clickap_legacy'), 'error');
                $skipped++;
                continue;
            }
            $record->hours = $row[11];
            $record->idnumber = $row[20];
            
            if(empty($row[21]) || empty($row[22]) || empty($row[23]) || empty($row[24])){
                $upt->track('status', get_string('missingdate', 'clickap_legacy'), 'error');
                $skipped++;
                continue;
            }
            //103-03-18 12:30
            $date1 = explode('-', $row[21]);
            $sdate = $date1[0]+1911 .'-'.$date1[1].'-'.$date1[2] .' '.$row[22].':00';
            $record->startdate = strtotime($sdate);
            $date2 = explode('-', $row[23]);
            $edate = $date2[0]+1911 .'-'.$date2[1].'-'.$date2[2] .' '.$row[24].':00';
            $record->enddate = strtotime($edate);
            $record->timemodified = time();
            if($DB->insert_record('clickap_legacy', $record)){
                $created++;
            }else{
                $errors++;
            }
        }
        $upt->close(); // close table

        $cir->close();
        $cir->cleanup(true);
        
        echo $OUTPUT->box_start('boxwidthnarrow boxaligncenter generalbox', 'uploadresults');
        echo '<p>';
        echo get_string('created', 'clickap_legacy').': '.$created.'<br />';
        echo get_string('skipped', 'clickap_legacy').': '.$skipped.'<br />';
        echo get_string('errors', 'clickap_legacy').': '.$errors.'</p>';
        echo $OUTPUT->box_end();

        echo $OUTPUT->footer();
        die;
    } else{
        echo $OUTPUT->header();

        $mform1->display();
        echo $OUTPUT->footer();
        die;
    }
} else {
    $cir = new csv_import_reader($iid, 'uploadlegacy');
}