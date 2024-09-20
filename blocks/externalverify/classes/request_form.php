<?php
/**
 * plugin infomation
 * 
 * @package    block
 * @subpackage externalverify
 * @author     Elaine Chen(CLICK-AP)
 * @copyright  CLICK-AP {@link https://www.click-ap.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');
}

require_once($CFG->libdir.'/formslib.php');
require_once($CFG->dirroot.'/blocks/externalverify/lib.php');

class block_externalverify_request_form extends moodleform {
    function definition() {
        global $CFG, $DB, $USER;

        $filesoptions = $this->_customdata['filesoptions'];
        
        $mform =& $this->_form;

        $mform->addElement('header','coursedetails', get_string('requestdetails', 'block_externalverify'));

        $mform->addElement('text', 'fullname', get_string('fullnamecourse'), 'maxlength="254" size="50"');
        $mform->addHelpButton('fullname', 'fullnamecourse');
        $mform->addRule('fullname', get_string('missingfullname'), 'required', null, 'client');
        $mform->setType('fullname', PARAM_TEXT);
        
        $mform->addElement('text', 'org', get_string('org', 'block_externalverify'), 'maxlength="254" size="50"');
        $mform->addHelpButton('org', 'org', 'block_externalverify');
        $mform->addRule('org', get_string('missingorg', 'block_externalverify'), 'required', null, 'client');
        $mform->setType('org', PARAM_TEXT);
        
        $mform->addElement('text', 'expense', get_string('expense', 'block_externalverify'), 'maxlength="10" size="50"');
        $mform->addRule('expense', null, 'numeric', null, 'client');
        $mform->setType('expense', PARAM_INT);

        $mform->addElement('date_time_selector', 'startdate', get_string('startdate', 'block_externalverify'));
        $mform->setDefault('startdate', time());
        
        $mform->addElement('date_time_selector', 'enddate', get_string('enddate', 'block_externalverify'));
        $mform->setDefault('enddate', time());
        /*
        $leavearray=array();
        $leavearray[] = $mform->createElement('radio', 'leavetype', '', get_string('officialleave', 'block_externalverify'), 0);
        $leavearray[] = $mform->createElement('radio', 'leavetype', '', get_string('privateleave', 'block_externalverify'), 1);
        $mform->addGroup($leavearray, 'leavetype', '', null, false);
        $mform->setDefault('leavetype', '0');
        
        $expensearray=array();
        $expensearray[] = $mform->createElement('radio', 'expensetype', '', get_string('publicexpense', 'block_externalverify'), 0);
        $expensearray[] = $mform->createElement('radio', 'expensetype', '', get_string('ownexpense', 'block_externalverify'), 1);
        $mform->addGroup($expensearray, 'expensetype', '', null, false); 
        $mform->setDefault('expensetype', '0');
        
        $mform->addElement('text', 'expense', get_string('expense', 'block_externalverify'), 'maxlength="10" size="10"');
        $mform->addRule('expense', get_string('expense_rule','block_externalverify'), 'numeric', null, 'client');
        $mform->setType('expense', PARAM_INTEGER);
        */
        $mform->addElement('textarea', 'summary', get_string('summary'), array('rows'=>'5', 'cols'=>'50'));
        $mform->setType('summary', PARAM_TEXT);
        
        $clickap = get_plugin_list('clickap');
        if(array_key_exists("longlearn_categories", $clickap)){
            require_once($CFG->dirroot. '/admin/clickap/longlearn_categories/lib.php');
            $lc = clickap_longlearn_categories_get_list();
            $mform->addElement('select', 'longlearn_category', get_string('course_longlearncategory', 'clickap_base'), $lc);
            $mform->addHelpButton('longlearn_category', 'course_longlearncategory', 'clickap_base');
        }
        
        /*
        if(array_key_exists("hourcategories", $clickap)){
            $thisYear = date('Y', time()) - 1911;
            $sql = 'SELECT id, CONCAT(year, name) as name FROM {clickap_hourcategories} WHERE visible = 1 AND type = 0 AND year >= :thisyear ORDER BY year,sortorder';
            $hc = $DB->get_records_sql_menu($sql, array('thisyear'=>$thisYear));
            $hcgroup = array();
            foreach($hc as $key => $text){
                $hcgroup[] =& $mform->createElement('checkbox', $key, null, $text);
            }
            $mform->addGroup($hcgroup, 'hourcategory', get_string('course_hourcategories', 'clickap_base'),null,true);
            $mform->addRule('hourcategory', get_string('missinghourcategories','clickap_base'), 'required', null, 'client');
        }
        */
        if(array_key_exists("hourcategories", $clickap)){
            $thisYear = date('Y', time()) - 1911;
            
            $sql = 'SELECT id, year, name FROM {clickap_hourcategories} 
                    WHERE year >= :thisyear AND visible = 1 AND type = 0
                    ORDER BY year,sortorder';
            $hcs = $DB->get_records_sql($sql, array('thisyear'=>$thisYear-1));
            $optionsYear = $optionsName = array();
            foreach($hcs as $hc){
                $optionsYear[$hc->year] = $hc->year;
                $optionsName[$hc->year][$hc->id] = $hc->name;
            }
            $attributes = array('size' => '5');
            $hier = &$mform->addElement('hierselect', 'hourcategory', get_string('course_hourcategories', 'clickap_base'), $attributes);
            //$mform->setDefault('format', $courseconfig->format);
            //$hier->setOptions(array($optionsYear, $optionsName));
            $hier->setMainOptions($optionsYear);
            $hier->setSecOptions($optionsName);
            $els =& $hier->getElements();
            $els[1]->updateAttributes(array('multiple' => 'multiple'));
        }
        
        if(array_key_exists("code", $clickap)){
            $model = $DB->get_records_menu('clickap_code', array('status'=>'1', 'type'=>'model'), 'sortorder', 'id,name');
            $mform->addElement('select', 'model', get_string('course_model', 'clickap_base'), $model);
            $mform->addHelpButton('model', 'course_model', 'clickap_base');

            if($unit = $DB->get_records_menu('clickap_code', array('status'=>'1', 'type'=>'unit'), 'sortorder', 'id,name')){
                $mform->addElement('select', 'unit', get_string('course_unit', 'clickap_base'), $unit);
                $mform->addHelpButton('unit', 'course_unit', 'clickap_base');
            }

            if(array_key_exists("longlearn_categories", $clickap)){
                if($credit = $DB->get_records_menu('clickap_code', array('status'=>'1', 'type'=>'credit'), 'sortorder', 'id,name')){
                    $mform->addElement('select', 'credit', get_string('course_credit', 'clickap_base'), $credit);
                    $mform->addHelpButton('credit', 'course_credit', 'clickap_base');
                }
                if($city = $DB->get_records_menu('clickap_code', array('status'=>'1', 'type'=>'city'), 'sortorder', 'id,name')){
                    $mform->addElement('select', 'city', get_string('course_city', 'clickap_base'), $city);
                    $mform->addHelpButton('city', 'course_city', 'clickap_base');
                }
            }
        }
        
        $mform->addElement('text','hours', get_string('course_hours', 'clickap_base'), 'maxlength="3"  size="5"');
        $mform->addHelpButton('hours', 'course_hours' ,'clickap_base');
        $mform->addRule('hours', get_string('course_hours_rule','clickap_base'), 'numeric', null, 'client');
        $mform->addRule('hours', get_string('missinghours','clickap_base'), 'required', null, 'client');
        $mform->setType('hours', PARAM_NUMBER);
        
        $mform->addElement('header','courseattatchments', get_string('requestattatchment', 'block_externalverify'));
        $fs = get_file_storage();
        $context = context_system::instance();
        $files = $fs->get_area_files($context->id, 'block_externalverify', 'templatefile', false, 'filename', false);
        if (count($files) > 0) {
            $templatefile = '';
            foreach ($files as $file) {
                $url = file_encode_url("$CFG->wwwroot/pluginfile.php",
                        '/'. $file->get_contextid(). '/'. $file->get_component(). '/'.
                        $file->get_filearea(). $file->get_filepath(). $file->get_itemid().'/'.$file->get_filename());
                $templatefile .= '<a href="'.$url.'">'.$file->get_filename().'</a>&nbsp;&nbsp;';
            }
            if(!empty($templatefile)){
                $mform->addElement('static', 'templatefile', get_string('templatefile', 'block_externalverify'), $templatefile);
            }
        }
        
        if ($filesoptions) {
            $mform->addElement('filemanager', 'attachments_filemanager', get_string('attachments', 'block_externalverify'), null, $filesoptions);
            $mform->addHelpButton('attachments_filemanager', 'courseattachments', 'block_externalverify');
            $mform->addRule('attachments_filemanager', get_string('missingattachments', 'block_externalverify'), 'required', null, 'client');
        }
        
        $this->add_action_buttons(true, get_string('submit'));
    }

    function validation($data, $files) {
        global $DB;

        $errors = parent::validation($data, $files);
        
        if(empty($data['hours'])){
            $errors['hours'] = get_string('missinghours', 'block_externalverify');
        }
        
        if($data['enddate'] <= $data['startdate']){
            $errors['enddate'] = get_string('enddateerror', 'block_externalverify');
        }
        
        if(!empty($data['hours'])){
            if($data['hours'] <= 0){//if($data['hours'] < 0 || $data['hours'] >= 20){
                $errors['hours'] = get_string('course_hours_rule','clickap_base');
            }
        }
        if(isset($data['hourcategory'])){
            if(!isset($data['hourcategory'][1])){
                $errors['hourcategory'] = get_string('missinghourcategories', 'clickap_base');
            }else if ($data['hourcategory'][0] != (date('Y', $data['startdate'])-1911)){
                $errors['hourcategory'] = get_string('hourcategorymismatch', 'clickap_base');
            }
        }
        
        return $errors;
    }
}