<?php
/**
 * Version details.
 *
 * @package    block
 * @subpackage program
 * @copyright  2018 Click-AP <elaine@click-ap.com>
 * @license    http://www.click-ap.com/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . "/admin/clickap/program/lib.php");

/**
 * Displays recent badges
 */
class block_programs extends block_base {

    public function init() {
        $this->title = get_string('pluginname', 'block_programs');
    }

    public function instance_allow_multiple() {
        return false;
    }

    public function has_config() {
        return false;
    }

    public function instance_allow_config() {
        return true;
    }

    public function applicable_formats() {
        return array('my' => true, 'user-profile' => true);
    }

    public function specialization() {
        if (empty($this->config->title)) {
            $this->title = get_string('pluginname', 'block_programs');
        } else {
            $this->title = $this->config->title;
        }
    }

    public function get_content() {
        global $CFG, $USER, $PAGE, $OUTPUT;
        require_once($CFG->dirroot . '/admin/clickap/program/lib.php');

        if (!isloggedin() or isguestuser()) {
            return '';// Never useful unless you are logged in as real users
        }
        
        if ($this->content !== null) {
            return $this->content;
        }
        
        if (empty($this->config)) {
            $this->config = new stdClass();
        }

        $userid = optional_param('id', $USER->id, PARAM_INT);
        $page    = optional_param('page', 0, PARAM_INT);
        $perpage = optional_param('perpage', 5 , PARAM_INT);
        
        $params = array('sesskey' => sesskey(), 'page'=>$page, 'perpage'=> $perpage);
        if($userid != $USER->id){
            $params['id'] = $userid;
            $returnurl = new moodle_url('/user/profile.php', $params);
        }else{
            $returnurl = new moodle_url('/my/index.php', $params);
        }
            
        // Create empty content.
        $this->content = new stdClass();
        $this->content->text = '';

        $courses = clickap_program_get_my_courses($userid, 'startdate DESC, sortorder DESC', true);
        $idarry = array();
        $ids = '0';
        if($courses){
            require_once($CFG->libdir . '/completionlib.php');
            $ids = implode(',',array_keys($courses));
            foreach($courses as $key => $course){
                if($CFG->enablecompletion && $course->enablecompletion){// or completion_info::is_enabled_for_site()
                    $completion = new completion_completion(array('userid' =>$userid, 'course' => $course->id));
                    if($completion->is_complete()){
                        $idarry[$course->id] = $course->id;
                    }
                }
            }
        }
        
        $myprograms = programs_get_related_programs($userid, true, $ids);
        $totalcount = sizeof($myprograms);
        if ($totalcount > 0) {
            $programs = programs_get_related_programs($userid, false, $ids, $page, $perpage);
        
            $renderer = $PAGE->get_renderer('clickap_program');
            $this->content->text = $renderer->print_programs_table_list($programs, $userid, $idarry);
            $this->content->text .= $OUTPUT->paging_bar($totalcount, $page, $perpage, $returnurl);
        } else {
            $this->content->text .= get_string('nothingtodisplay', 'block_programs');
        }

        return $this->content;
    }
}