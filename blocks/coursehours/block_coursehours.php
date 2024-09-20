<?php
/**
 * coursehours block settings
 *
 * @package    block_coursehours
 * @copyright  2016 Mary Chen(http://www.click-ap.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
include_once($CFG->dirroot . '/course/lib.php');
require_once($CFG->dirroot.'/blocks/coursehours/table.php');
require_once($CFG->dirroot.'/blocks/coursehours/locallib.php');

class block_coursehours extends block_base {
    
    function init() {
        global $USER;
        $this->currentyear = date('Y') - 1911;
        $this->title = get_string('title', 'block_coursehours');
    }

    function has_config() {
        return false;
    }

    function get_content() {
        global $CFG, $USER, $DB, $PAGE, $OUTPUT;
        
        $userid = optional_param('id', 0, PARAM_INT);
        if($userid != 0){
            $user = $DB->get_record('user', array('id'=>$userid));
        }else{
            $user = $USER;
        }
        if($this->content !== NULL) {
            return $this->content;
        }

        $this->content = new stdClass();
        $this->content->text = '';
        $this->content->footer = '';
        
        $renderer = $PAGE->get_renderer('block_coursehours');
        $CurrentYear = date('Y',time())-1911;
        $selectyear = optional_param('selectyear', $CurrentYear, PARAM_INT);
        
        if($selectyear != 999){
            $currentyear = $selectyear;
        }else{
            $currentyear = 999;
            $this->content->text .= $renderer->editing_bar_head($currentyear, $user->id);
            return $this->content;
        }
        
        //select year
        //$this->content->text .= $renderer->editing_bar_head($currentyear, $user->id);
        $selectYear = html_writer::tag('div', $renderer->editing_bar_head($currentyear, $user->id),
                array('class' => ''));
        //exceldownload
        //$this->content->text .= $renderer->excel_download($currentyear, $user->id);
        $url = new moodle_url('/blocks/coursehours/download.php',array('currentyear'=>$currentyear, 'userid'=>$user->id));
        $downloadBtn = html_writer::tag('div', $OUTPUT->render(new single_button($url, get_string('download')))
            , array('class' => 'ml-auto d-flex', 'style' => 'text-align:right;margin-top: 10px;'));
        $this->content->text .= html_writer::tag('div', $selectYear.$downloadBtn, array('class' => 'd-flex flex-wrap'));
            
        //display year rules
        $this->content->text .= block_coursehours_list_myhours($currentyear, $user);
        $this->content->text .= $OUTPUT->notification(get_string('notification', 'block_coursehours'), 'notifymessage');
        $this->content->text .= html_writer::tag('div', '&nbsp;'.get_string('enrolledcourses', 'block_coursehours')
            , array('class'=>'title fa fa-briefcase'));
        $this->content->text .= '<hr>';
        
        //my all enrol course
        $table = new blocks_coursehours_manage_table($currentyear, $user->id);
        $table->collapsible(false);//disable Field hide
        $url = $PAGE->url;
        $url->param('selectyear', $currentyear);
        $table->define_baseurl($url);
        $this->content->text .= $renderer->manage_page($table);
        
        return $this->content;
    }

    public function applicable_formats() {
        return array('my' => true, 'user-profile' => true);
    }
    
    public function get_aria_role() {
        return 'navigation';
    }
}