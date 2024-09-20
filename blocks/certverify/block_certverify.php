<?php
/**
 * 
 * @package    block_certverify
 * @author     Elaine Chen(CLICK-AP)
 * @copyright  CLICK-AP {@link https://www.click-ap.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

 require_once($CFG->dirroot . '/blocks/certverify/locallib.php');

class block_certverify extends block_base {
    function init() {
        $this->title = get_string('pluginname', 'block_certverify');
    }

    function has_config() {
        return true;
    }

    function get_content() {
        global $CFG, $DB, $USER;

        if($this->content !== NULL) {
            return $this->content;
        }
        $userid = $USER->id;
        $username = $USER->username;

        $this->content = new stdClass();
        $this->content->text = '<ul class="list">';

        $apply = $DB->count_records_select('user_certs', 'userid = :userid AND status IN (0)', array('userid'=>$userid));
        $this->content->text .= '<li class="listentry"><a href="'.$CFG->wwwroot.'/blocks/certverify/apply.php">'.get_string('myapply','block_certverify',$apply).'</a></li>';
        $this->content->text .= '<li class="listentry"><a href="'.$CFG->wwwroot.'/blocks/certverify/apply_history.php">'.get_string('applyhistory','block_certverify').'</a></li>';

        $sql = "SELECT count(*) FROM {user_certs} WHERE status = 0 AND (validators LIKE '$username,%' OR validators LIKE '%,$username' OR validators LIKE '%,$username,%' OR validators = '$username')";
        $verify = $DB->count_records_sql($sql);
        if($verify > 0){
            $this->content->text .= '<li class="listentry"><a href="'.$CFG->wwwroot.'/blocks/certverify/manage.php">'.get_string('myverify','block_certverify',$verify).'</a></li>';
        }

        $verify_history = $DB->count_records_select('user_certs', 'status IN (1,2) AND (validator =:validator)', array('validator'=>$userid));
        if($verify_history > 0){
            $this->content->text .= '<li class="listentry"><a href="'.$CFG->wwwroot.'/blocks/certverify/manage_history.php">'.get_string('verifyhistory','block_certverify').'</a></li>';
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