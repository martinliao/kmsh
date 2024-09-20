<?php
/**
 * 
 * @package    enrol
 * @subpackage profile
 * @author     Maria Tan(CLICK-AP)
 * @author     Martin Freeman(CLICK-AP)
 * @copyright  CLICK-AP {@link https://www.click-ap.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$tasks = [
        [
                'classname' => 'enrol_profile\task\processenrolments_task',
                'blocking'  => 0,
                'minute'    => '*/30',
                'hour'      => '*',
                'day'       => '*',
                'month'     => '*',
                'dayofweek' => '*',
                'disabled' => 0
        ],
];