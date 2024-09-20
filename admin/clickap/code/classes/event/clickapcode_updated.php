<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Provides {@link clickap_code\event\clickapcode_updated} class.
 *
 * @package    clickap_code
 * @copyright  CLICK-AP (https://www.click-ap.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace clickap_code\event;

use core\event\base;

defined('MOODLE_INTERNAL') || die();

class clickapcode_updated extends \core\event\base {

    /**
     * Initialise the event.
     */
    protected function init() {
        $this->data['objecttable'] = 'clickap_code';
        $this->data['crud'] = 'u';
        $this->data['edulevel'] = self::LEVEL_OTHER;
    }

    /**
     * Returns event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('event_clickapcode_updated', 'clickap_code');
    }

    /**
     * Get the event description.
     *
     * @return string
     */
    public function get_description() {
        return "The code '{$this->other['code']}' was updated.";
    }
}