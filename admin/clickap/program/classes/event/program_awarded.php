<?php
/**
 * Version details.
 *
 * @package    clickap
 * @subpackage program
 * @copyright  2018 Click-AP <elaine@click-ap.com>
 * @license    http://www.click-ap.com/copyleft/gpl.html GNU GPL v3 or later
 */

namespace clickap_program\event;
defined('MOODLE_INTERNAL') || die();

class program_awarded extends \core\event\base {

    /**
     * Set basic properties for the event.
     */
    protected function init() {
        $this->data['objecttable'] = 'program';
        $this->data['crud'] = 'c';
        $this->data['edulevel'] = self::LEVEL_TEACHING;
    }

    /**
     * Returns localised general event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('eventprogramawarded', 'clickap_program');
    }

    /**
     * Returns non-localised event description with id's for admin use only.
     *
     * @return string
     */
    public function get_description() {
        return "The user with id '$this->relateduserid' has been awarded the program with id '".$this->objectid."'.";
    }

    /**
     * Returns relevant URL.
     * @return \moodle_url
     */
    public function get_url() {
        return new \moodle_url('/admin/clickap/program/edit.php', array('id' => $this->objectid));
    }

    /**
     * Custom validations.
     *
     * @throws \coding_exception
     * @return void
     */
    protected function validate_data() {
        parent::validate_data();

        if (!isset($this->relateduserid)) {
            throw new \coding_exception('The \'relateduserid\' must be set.');
        }

        if (!isset($this->objectid)) {
            throw new \coding_exception('The \'objectid\' must be set.');
        }
    }

    public static function get_objectid_mapping() {
        return array('db' => 'program', 'restore' => 'program');
    }

    public static function get_other_mapping() {
        $othermapped = array();
        $othermapped['programissuedid'] = array('db' => 'program_issued', 'restore' => base::NOT_MAPPED);

        return $othermapped;
    }
}
