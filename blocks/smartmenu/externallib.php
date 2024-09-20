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
 * Click-AP CouseMenu External course API
 *
 * @package    block_smartmenu
 * @category   external
 * 
 * @author     Mary Chen (mary@click-ap.com)
 * @author     Jack (jack@click-ap.com)
 * @copyright  Click-AP {@line https://www.click-ap.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once("$CFG->libdir/externallib.php");
require_once("lib.php");

/**
 * Course external functions
 *
 * @package    core_course
 * @category   external
 * @copyright  2011 Jerome Mouneyrac
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.2
 */
class block_smartmenu_external extends external_api {

    /**
     * Returns description of method result value
     *
     * @return external_description
     */
    public static function get_course_content_items_returns() {
        return new external_single_structure([
            'content_items' => new external_multiple_structure(
                \core_course\local\exporters\course_content_item_exporter::get_read_structure()
            ),
        ]);
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     */
    public static function get_course_content_items_parameters() {
        return new external_function_parameters([
            'courseid' => new external_value(PARAM_INT, 'ID of the course', VALUE_REQUIRED),
            'allowtypes' => new external_multiple_structure(
                new external_value(PARAM_TEXT, 'resources/activity name'),
                    'Allow resources/activity for format.',
                    VALUE_DEFAULT, array()
            ),
        ]);
    }

    /**
     * Given a course ID fetch all accessible modules for that course
     *
     * @param int $courseid The course we want to fetch the modules for
     * @return array Contains array of modules and their metadata
     */
    public static function get_course_content_items(int $courseid, $allowtypes) {
        global $USER;

        [
            'courseid' => $courseid,
            'allowtypes' => $allowtypes,
        ] = self::validate_parameters(self::get_course_content_items_parameters(), [
            'courseid' => $courseid,
            'allowtypes' => $allowtypes,
        ]);

        $coursecontext = context_course::instance($courseid);
        self::validate_context($coursecontext);
        $course = get_course($courseid);

        //$contentitemservice = \core_course\local\factory\content_item_service_factory::get_content_item_service();
        $contentitemservice = \block_smartmenu\local\factory\content_item_service_factory::get_content_item_service();
        $contentitems = $contentitemservice->get_content_items_for_user_in_course($USER, $course, $allowtypes);
        return ['content_items' => $contentitems];
    }

}
