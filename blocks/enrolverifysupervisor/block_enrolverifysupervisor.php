<?php
/**
 * plugin infomation
 *
 * @package    block_enrolverifysupervisor
 * @copyright  2020 CLICK-AP  {@link https://www.click-ap.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
include_once($CFG->dirroot . '/blocks/enrolverifysupervisor/locallib.php');

class block_enrolverifysupervisor extends block_base {
    function init() {
        $this->title = get_string('pluginname', 'block_enrolverifysupervisor');
    }

    function has_config() {
        return false;
    }

    function get_content() {
        global $CFG, $USER, $DB, $OUTPUT;

        if($this->content !== NULL) {
            return $this->content;
        }

        $this->content = new stdClass();
        $this->content->text = '<ul class="list">';

        $verify = $verify_history = 0;
        if(is_siteadmin($USER)){
            $verify += $DB->count_records('enrol_supervisor', array('status'=>0));
            $sql = "SELECT COUNT(id) FROM {enrol_supervisor} WHERE (status=1 OR status=2)";
            $verify_history = $DB->count_records_sql($sql, array());
        }else{
            $like1 = $USER->username.',%';
            $like2 = '%,'.$USER->username;

            $sql_v = "SELECT COUNT(id) FROM {enrol_supervisor} WHERE (verifyuser like :censor1 OR verifyuser like :censor2 OR verifyuser = :verifyuser) AND status=0";
            $verify += $DB->count_records_sql($sql_v, array('censor1'=>$like1, 'censor2'=>$like2, 'verifyuser'=>$USER->username));

            $sql_vh = "SELECT COUNT(id) FROM {enrol_supervisor} WHERE(verifyuser like :censor1 OR verifyuser like :censor2 OR verifyuser = :verifyuser) AND (status=1 OR status=2)";
            $verify_history = $DB->count_records_sql($sql_vh, array('censor1'=>$like1, 'censor2'=>$like2, 'verifyuser'=>$USER->username));
        }
        $apply = $DB->count_records('enrol_supervisor', array('userid'=>$USER->id, 'status'=>0));
        $this->content->text .= '<li class="listentry"><a href="'.$CFG->wwwroot.'/blocks/enrolverifysupervisor/apply.php">'.get_string('myapply','block_enrolverifysupervisor', $apply).'</a></li>';
        $this->content->text .= '<li class="listentry"><a href="'.$CFG->wwwroot.'/blocks/enrolverifysupervisor/apply_history.php">'.get_string('applyhistory','block_enrolverifysupervisor').'</a></li>';

        if($verify > 0){
            $this->content->text .= '<li class="listentry"><a href="'.$CFG->wwwroot.'/blocks/enrolverifysupervisor/manage.php">'.get_string('myverify','block_enrolverifysupervisor',$verify).'</a></li>';
        }
        if($verify_history > 0){
            $this->content->text .= '<li class="listentry"><a href="'.$CFG->wwwroot.'/blocks/enrolverifysupervisor/manage_history.php">'.get_string('verifyhistory','block_enrolverifysupervisor').'</a></li>';
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