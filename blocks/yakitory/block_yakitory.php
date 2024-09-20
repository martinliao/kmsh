<?php
/**
 * @package   block_yakitory
 * @copyright 2021 MARY CHEN  {@link https://www.click-ap.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v1 or later
 */

class block_yakitory extends block_base {

    function init() {
        $this->title = get_string('pluginname', 'block_yakitory');
    }

    function specialization() {
    }

    function applicable_formats() {
        return array('course-view' => true, 'my' => true);
    }

    function instance_allow_multiple() {
        return false;
    }
    function has_config() {
        return false;
    }
    function get_content() {
        global $CFG, $USER, $COURSE, $PAGE, $OUTPUT;

        if ($this->content !== NULL) {
            return $this->content;
        }
        if (empty($this->instance)) {
            return null;
        }
        
        $this->content = new stdClass();
        $this->content->text = '';
        $this->content->footer = '';

        if (!isloggedin() or isguestuser()) {
            return $this->content;
        }

        if ($COURSE->id == SITEID && !has_capability('block/yakitory:upload', $this->context)) {
            $this->content->footer = get_string('nocapabilitytousethisservice', 'error');
            return $this->content;
        }
        
        $yakitory_config = get_config('yakitory');
        if(!empty($yakitory_config->video_host) and !empty($yakitory_config->client_id) and !empty($yakitory_config->access_key)){
            if (has_capability('block/yakitory:upload', $this->context)) {
                $icon = '<img src="' . $OUTPUT->image_url('i/report') . '" class="icon" alt="" />';
                $rurl = new moodle_url('/blocks/yakitory/report.php', array('courseid'=>$this->page->course->id));
                $this->content->text = html_writer::tag('p', html_writer::link($rurl, $icon . get_string('videoreport', 'block_yakitory')));
                
                $url = new moodle_url('/blocks/yakitory/upload.php', array('courseid'=>$this->page->course->id, 'returnurl' => $PAGE->url->out()));
                $this->content->text .= html_writer::tag('div', $OUTPUT->single_button($url, get_string('uploadfile', 'block_yakitory'), 'post'), array('class'=>'uploadbtn'));
            }
        }else{
            $this->content->footer = get_string('config_error', 'repository_yakitory');
        }
        return $this->content;
    }
}