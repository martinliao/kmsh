<?php
/**
 * @package   block_uploaddoc
 * @copyright 2016 MARY CHEN  {@link http://www.click-ap.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v1 or later
 */

class block_uploaddoc extends block_base {

    function init() {
        $this->title = get_string('pluginname', 'block_uploaddoc');
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

        if ($COURSE->id == SITEID && !has_capability('block/uploaddoc:upload', $this->context)) {
            $this->content->footer = get_string('nocapabilitytousethisservice', 'error');
            return $this->content;
        }
        
        $derberus_config = get_config('derberus');
        if(!empty($derberus_config->view_host) and !empty($derberus_config->client_id) and !empty($derberus_config->access_key)){
            if (has_capability('block/uploaddoc:upload', $this->context)) {
                $icon = '<img src="' . $OUTPUT->image_url('i/report') . '" class="icon" alt="" />';
                $rurl = new moodle_url('/blocks/uploaddoc/report.php', array('courseid'=>$this->page->course->id));
                $this->content->text = html_writer::tag('p', html_writer::link($rurl, $icon . get_string('filereport', 'block_uploaddoc')));
                
                $url = new moodle_url('/blocks/uploaddoc/upload.php', array('courseid'=>$this->page->course->id, 'returnurl' => $PAGE->url->out()));
                $this->content->text .= html_writer::tag('div', $OUTPUT->single_button($url, get_string('uploadfile', 'block_uploaddoc'), 'post'), array('class'=>'uploadbtn'));
    
                /*$this->content->footer = '<br />'.html_writer::link(
                    new moodle_url('/blocks/uploaddoc/upload.php', array('courseid'=>$this->page->course->id, 'returnurl' => $PAGE->url->out())),
                    get_string('uploadfile', 'block_uploaddoc') . '...');
                */
            }
        }else{
            $this->content->footer = get_string('config_error', 'repository_derberus');
        }
        return $this->content;
    }
}