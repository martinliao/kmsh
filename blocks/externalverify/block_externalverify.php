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

require_once($CFG->dirroot . '/blocks/externalverify/locallib.php');

class block_externalverify extends block_base {
    function init() {
        $this->title = get_string('pluginname', 'block_externalverify');
    }

    function has_config() {
        return true;
    }

    function get_content() {
        global $CFG, $USER, $DB, $OUTPUT;
        $managerVerify = get_config('block_externalverify', 'managerverify');

        if($this->content !== NULL) {
            return $this->content;
        }

        $this->content = new stdClass();
        $this->content->text = '<ul class="list">';

        $apply = $DB->count_records_select('course_external', 'userid = :userid AND status IN (0,4)', array('userid'=>$USER->id));
        $this->content->text .= '<li class="listentry"><a href="'.$CFG->wwwroot.'/blocks/externalverify/apply.php">'.get_string('myapply','block_externalverify',$apply).'</a></li>';
        $this->content->text .= '<li class="listentry"><a href="'.$CFG->wwwroot.'/blocks/externalverify/apply_history.php">'.get_string('applyhistory','block_externalverify').'</a></li>';

        $sql = "SELECT count(*) FROM {course_external} WHERE status = 0 AND (supervisor LIKE '$USER->username,%' OR supervisor LIKE '%,$USER->username' OR supervisor LIKE '%,$USER->username,%' OR supervisor = '$USER->username')";
        $verify = $DB->count_records_sql($sql);
        if($verify > 0){
            $this->content->text .= '<li class="listentry"><a href="'.$CFG->wwwroot.'/blocks/externalverify/manage.php">'.get_string('myverify','block_externalverify',$verify).'</a></li>';
        }

        if(is_siteadmin($USER) AND $managerVerify){
            $verify = $DB->count_records_select('course_external', 'status IN (4)', array());
            $this->content->text .= '<li class="listentry"><a href="'.$CFG->wwwroot.'/blocks/externalverify/manage.php?stage=2">'.get_string('managerverify','block_externalverify',$verify).'</a></li>';
        }

        $verify_history = $DB->count_records_select('course_external', 'status IN (1,2,4) AND (validator =:validator OR manager =:manager)', array('validator'=>$USER->id, 'manager'=>$USER->id));
        if($verify_history > 0){
            $this->content->text .= '<li class="listentry"><a href="'.$CFG->wwwroot.'/blocks/externalverify/manage_history.php">'.get_string('verifyhistory','block_externalverify').'</a></li>';
        }
        
        $this->content->text .= '</ul>';
        $this->content->footer = '';
        return $this->content;
    }

    public function applicable_formats() {
        return array('my' => true);
    }
    
    /**
     * Returns the role that best describes the course list block.
     *
     * @return string
     */
    public function get_aria_role() {
        return 'navigation';
    }
}