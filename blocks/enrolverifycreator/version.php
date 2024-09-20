<?php
/**
 * plugin infomation
 *
 * @package    block_enrolverifycreator
 * @copyright  2017 Mary Chen {@link http://www.click-ap.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

$plugin->version   = 2020033001;
$plugin->requires  = 2014050800;
$plugin->release   = '1.2.3';
$plugin->component = 'block_enrolverifycreator';
$plugin->dependencies = array('enrol_creator' => ANY_VERSION);