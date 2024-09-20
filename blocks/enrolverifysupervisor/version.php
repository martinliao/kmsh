<?php
/**
 * plugin infomation
 *
 * @package    block_enrolverifysupervisor
 * @copyright  2020 CLICK-AP  {@link https://www.click-ap.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

$plugin->version   = 2020051501;
$plugin->requires  = 2014050800;
$plugin->release   = '1.1';
$plugin->component = 'block_enrolverifysupervisor';
$plugin->dependencies = array('enrol_supervisor' => ANY_VERSION);