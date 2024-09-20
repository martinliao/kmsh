<?php
/**
 * coursehours block settings
 *
 * @package    block_coursehours
 * @copyright  2016 Mary Chen(http://www.click-ap.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class block_coursehours_renderer extends plugin_renderer_base {
    public function manage_page($table) {
        ob_start();
        $this->manage_table($table);
        $output = ob_get_contents();
        ob_end_clean();
        return $output;
    }
    
    public function editing_bar_head($currentyear, $userid = null) {
        global $CFG, $DB, $USER, $PAGE;
        $output = $this->output->box_start('notice');
		if(empty($userid)){
           $userid = $USER->id; 
        }
        
        $selectYear = (date('Y', time()) - 1911) - 5;
        $options = array('999'=>get_string('choosedots'));
        //$years = block_coursehours_enrol_get_my_courses_year($userid);
        $sql = "SELECT DISTINCT year, year as value FROM {clickap_hourcategories}
                WHERE year !=0 AND visible = 1 AND year > :year";
        $years = $DB->get_records_sql_menu($sql, array('year' => $selectYear));
        foreach($years as $key=>$value){
            $options[$key] = $key;
        }
        krsort($options);
        /*
        if($currentyear == 999){
            $currentyear = array_slice($options,2);
        }
        */
        $select = new single_select($PAGE->url, 'selectyear', $options, $currentyear, array());
        $output .= $this->output->render($select);

        $output .= $this->output->box_end();
        return $output;
    }
    
    public function manage_table($table) {
        $columns = array(
            'coursefullname',
            //'category',
            'startdate',
            'model',
            'hours',
            'hourcategories',
            'enrolmethod',
            'status',
            'finalgrade',
            'lastcourseaccess');
        $headers = array(
            get_string('coursefullname', 'block_coursehours'),
            //get_string('coursecategory', 'block_coursehours'),
            get_string('startdate', 'block_coursehours'),
            get_string('model', 'block_coursehours'),
            get_string('hours', 'block_coursehours'),
            get_string('hourcategories', 'block_coursehours'),
            get_string('enrolmethod', 'block_coursehours'),
            get_string('status', 'block_coursehours'),
            get_string('finalgrade', 'grades'),
            get_string('lastcourseaccess')
            );
        $table->define_columns($columns);
        $table->define_headers($headers);
        $table->set_attribute('class', 'admintable generaltable');
        //$table->column_class('status', 'numcol');
        $table->sortable(true, 'startdate', SORT_DESC);
        $table->out(5, true);
    }
    
    /*
    public function excel_download($currentyear, $userid = null) {
        global $CFG, $USER;
        $output = $this->output->box_start('notice');
        if(empty($userid)){
           $userid = $USER->id; 
        }
        $options = array('999'=>get_string('choosedots'),'xls' => get_string('download_coursedetail', 'block_coursehours'));
        $url = new moodle_url('/blocks/coursehours/download.php',array('currentyear'=>$currentyear, 'userid'=>$userid));
        $select = new single_select($url, 'excel', $options, '', array());
        $output .= $this->output->render($select);

        $output .= $this->output->box_end();
        return $output;
    }
    */
}