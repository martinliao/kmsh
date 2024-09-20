<?php
/**
 * coursehours block settings
 *
 * @package    block_coursehours
 * @copyright  2016 Mary Chen(http://www.click-ap.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

$plugin->version   = 2024080801;
$plugin->requires  = 2014050800;
$plugin->component = 'block_coursehours';
$plugin->release   = '1.4.2';
$plugin->dependencies = array('clickap_hourcategories' => ANY_VERSION);