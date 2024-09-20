<?php
/**
 * smartmenu
 *
 * @package    block
 * @subpackage block_smartmenu
 * @copyright  2017
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v2 or later
 */
namespace block_smartmenu\event;
defined('MOODLE_INTERNAL') || die();

class information_viewed extends \core\event\base {

    /**
     * Init method.
     */
    protected function init() {
        $this->data['crud'] = 'r';
        $this->data['edulevel'] = self::LEVEL_PARTICIPATING;
        $this->data['objecttable'] = 'course';
    }

    /**
     * Returns non-localised description of what happened.
     *
     * @return string
     */
    public function get_description() {
        return 'User with id ' . $this->userid . ' viewed course information with instanceid ' .
            $this->objectid;
    }

    /**
     * Returns localised general event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('eventinformationviewed', 'block_smartmenu');
    }

    /**
     * Get URL related to the action
     *
     * @return \moodle_url
     */
    public function get_url() {
        return new \moodle_url('/blocks/smartmenu/information.php', array('id' => $this->objectid));
    }

    /**
     * Replace add_to_log() statement.
     *
     * @return array of parameters to be passed to legacy add_to_log() function.
     */
    protected function get_legacy_logdata() {
        return array($this->courseid, 'smartmenu', 'blocks', 'information.php?id=' . $this->objectid,
            $this->objectid, $this->contextinstanceid);
    }
    
    public static function get_objectid_mapping() {
        return array('db' => 'course', 'restore' => 'course');
    }
}
