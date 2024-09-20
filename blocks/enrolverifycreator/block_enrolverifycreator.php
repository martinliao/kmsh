<?php
/**
 * plugin infomation
 *
 * @package    block_enrolverifycreator
 * @copyright  2017 Mary Chen {@link http://www.click-ap.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
include_once($CFG->dirroot . '/blocks/enrolverifycreator/locallib.php');

class block_enrolverifycreator extends block_base {
    function init() {
        $this->title = get_string('pluginname', 'block_enrolverifycreator');
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
            $verify += $DB->count_records('enrol_creator', array('status'=>0));
            $sql = "SELECT COUNT(id) FROM {enrol_creator} WHERE (status=1 OR status=2)";
            $verify_history = $DB->count_records_sql($sql, array());
        }else{
            $like1 = $USER->id.',%';
            $like2 = '%,'.$USER->id;

            $sql_v = "SELECT COUNT(id) FROM {enrol_creator} WHERE (verifyuser like :censor1 OR verifyuser like :censor2 OR verifyuser = :verifyuser) AND status=0";
            $verify += $DB->count_records_sql($sql_v, array('censor1'=>$like1, 'censor2'=>$like2, 'verifyuser'=>$USER->id));

            $sql_vh = "SELECT COUNT(id) FROM {enrol_creator} WHERE(verifyuser like :censor1 OR verifyuser like :censor2 OR verifyuser = :verifyuser) AND (status=1 OR status=2)";
            $verify_history = $DB->count_records_sql($sql_vh, array('censor1'=>$like1, 'censor2'=>$like2, 'verifyuser'=>$USER->id));
        }
        $apply = $DB->count_records('enrol_creator', array('userid'=>$USER->id, 'status'=>0));
        $this->content->text .= '<li class="listentry"><a href="'.$CFG->wwwroot.'/blocks/enrolverifycreator/apply.php">'.get_string('myapply','block_enrolverifycreator', $apply).'</a></li>';
        $this->content->text .= '<li class="listentry"><a href="'.$CFG->wwwroot.'/blocks/enrolverifycreator/apply_history.php">'.get_string('applyhistory','block_enrolverifycreator').'</a></li>';

        if($verify > 0){
            $this->content->text .= '<li class="listentry"><a href="'.$CFG->wwwroot.'/blocks/enrolverifycreator/manage.php">'.get_string('myverify','block_enrolverifycreator',$verify).'</a></li>';
        }
        if($verify_history > 0){
            $this->content->text .= '<li class="listentry"><a href="'.$CFG->wwwroot.'/blocks/enrolverifycreator/manage_history.php">'.get_string('verifyhistory','block_enrolverifycreator').'</a></li>';
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