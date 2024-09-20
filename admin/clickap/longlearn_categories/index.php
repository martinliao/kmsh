<?php
/**
 * @package    clickap
 * @subpackage longlearn_categories
 * @copyright  2024 CLICK-AP {@https://www.click-ap.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->libdir.'/csvlib.class.php');
require_once($CFG->dirroot.'/user/profile/lib.php');
require_once($CFG->dirroot.'/user/lib.php');
require_once($CFG->dirroot.'/group/lib.php');
require_once($CFG->dirroot.'/cohort/lib.php');
require_once('locallib.php');
require_once('upload_form.php');

$iid = optional_param('iid', '', PARAM_INT);

require_login();
$context = context_course::instance(SITEID);
$PAGE->set_context($context);
$PAGE->set_pagelayout('admin');
$PAGE->set_url('/admin/clickap/longlearn_categories/index.php', array());
$PAGE->set_title(get_string('uploadcategories','clickap_longlearn_categories'));
    
if (empty($iid)) {
    global $DB;
    $mform1 = new upload_form1();
    
    if ($formdata = $mform1->get_data()) {
        
        $iid = csv_import_reader::get_new_iid('uploadlonglearncategories');
        $cir = new csv_import_reader($iid, 'uploadlonglearncategories');
        $content = $mform1->get_file_content('categoriesfile');
        $readcount = $cir->load_csv_content($content, $formdata->encoding, $formdata->delimiter_name);
        $cir->init();

        print_r($cir->get_columns());
        $data = array();
        $i = 0;
        while ($row = $cir->next()) {

            $record = new stdClass();
            $record->id = $row[3];
            $record->newid  = $row[3];
            $record->idnumber = $row[0];
            $record->name = trim($row[1]);
            $record->depth = $row[4];
            $record->parent = $row[5];
            $record->path = $row[6];
            $record->timemodified = time();
            $DB->insert_record('longlearn_categories', $record);
            //insert_data($record);
            $i++;
        }
        echo $OUTPUT->header();
        echo $OUTPUT->footer();
    } else {
        echo $OUTPUT->header();

        $mform1->display();
        echo $OUTPUT->footer();
        die;
    }
} else {
    $cir = new csv_import_reader($iid, 'uploadlonglearncategories');
    // $filecolumns = uu_validate_user_upload_columns($cir, $STD_FIELDS, $PRF_FIELDS, $returnurl);
}
