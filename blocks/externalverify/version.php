<?php
/**
 * plugin infomation
 * 
 * @package    block
 * @subpackage externalverify
 * @author     Elaine Chen(CLICK-AP)
 * @copyright  CLICK-AP {@link https://www.click-ap.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

$plugin->version   = 2024091800;
$plugin->requires  = 2014050800;
$plugin->release   = '3.9.2';
$plugin->component = 'block_externalverify';
$plugin->dependencies = array('clickap_hourcategories' => 2020051500);