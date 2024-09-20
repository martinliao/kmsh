<?php
/**
 * freshmanhours block settings
 *
 * @package    block_freshmanhours
 * @copyright  2016 Mary Chen(http://www.click-ap.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

$plugin->version   = 2024081200;
$plugin->requires  = 2014050800;
$plugin->component = 'block_freshmanhours';
$plugin->release   = '1.1.0';
$plugin->dependencies = array('clickap_hourcategories' => ANY_VERSION);