<?php
/**
 * Version details.
 *
 * @package    block
 * @subpackage program
 * @copyright  2018 Click-AP <elaine@click-ap.com>
 * @license    http://www.click-ap.com/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

$plugin->version   = 2019032001;
$plugin->requires  = 2016051900;
$plugin->component = 'block_programs';
$plugin->dependencies = array('clickap_program' => ANY_VERSION);