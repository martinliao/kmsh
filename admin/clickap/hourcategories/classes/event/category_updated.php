<?php
/**
 * updated event
 *
 * @package clickap_hourcategories
 * @author 2018 Mary Tan
 * @copyright CLICK-AP (https://www.click-ap.com)
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace clickap_hourcategories\event;

defined('MOODLE_INTERNAL') || die();

class category_updated extends \core\event\base {

    /**
     * Init method.
     */
    protected function init() {
        $this->data['objecttable'] = 'clickap_hourcategories';
        $this->data['crud'] = 'u';
        $this->data['edulevel'] = self::LEVEL_OTHER;
    }

    /**
     * Returns localised general event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('eventcategoryupdated', 'clickap_hourcategories');
    }
}