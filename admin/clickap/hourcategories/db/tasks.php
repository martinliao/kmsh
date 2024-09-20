<?php
/**
 * Task definition for clickap_hourcategories
 *
 * @package    clickap_hourcategories
 * @author     Jack Liou <jack@click-ap.com>
 * @author     Elaine Chen <elaine@click-ap.com>
 * @copyright  2020 Click-AP {@link https://www.click-ap.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$tasks = array(
    array(
        'classname' => '\clickap_hourcategories\task\course_hourcompletions',
        'blocking' => 0,
        'minute' => '55',
        'hour' => '23',
        'day' => '*',
        'month' => '*',
        'dayofweek' => '*',
        'disabled' => 0
    )
);
