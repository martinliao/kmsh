<?php
/**
 * freshmanhours block settings
 *
 * @package    block_freshmanhours
 * @copyright  2016 Mary Chen(http://www.click-ap.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
include_once($CFG->dirroot . '/course/lib.php');
require_once($CFG->dirroot.'/blocks/freshmanhours/locallib.php');

class block_freshmanhours extends block_base {
    
    function init() {
        global $USER;
        $this->currentyear = date('Y') - 1911;
        $this->title = get_string('title', 'block_freshmanhours');
    }

    function has_config() {
        return false;
    }

    function get_content() {
        global $CFG, $USER, $DB, $PAGE;
        require_once($CFG->dirroot . '/user/profile/lib.php');

        $userid = optional_param('id', 0, PARAM_INT);

        if($userid != 0){
            $user = $DB->get_record('user', array('id'=>$userid));
            
            $userprofile = (array)profile_user_record($user->id, false);
            $arrivaldate = $userprofile['ArrivalDate'];
        }else{
            $user = $USER;
            $arrivaldate = $USER->profile['ArrivalDate'];
        }

        if($this->content !== NULL) {
            return $this->content;
        }

        $this->content = new stdClass();
        $this->content->text = '';
        $this->content->footer = '';
        if($user->auth != 'kmsh' || empty($arrivaldate)){
            return $this->content;
        }

        $calculatedate = strtotime("+3 months", strtotime((int)$arrivaldate)) + DAYSECS -1;
        $this->content->text .= '<p>'.get_string('arrivaldate', 'block_freshmanhours', $arrivaldate).'</p>';
        $this->content->text .= '<p>'.get_string('calculatedate', 'block_freshmanhours', date('Ymd', $calculatedate)).'</p>';
        
        $this->content->text .= block_freshmanhours_list_hour($user->id, $calculatedate);

        return $this->content;
    }

    public function applicable_formats() {
        return array('my' => true, 'user-profile' => true);
    }
    
    public function get_aria_role() {
        return 'navigation';
    }
}