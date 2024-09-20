<?php
/**
 * @package    clickap
 * @subpackage legacy
 * @copyright  2024 CLICK-AP {@https://www.click-ap.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

$plugin->version   = 2017041002;
$plugin->requires  = 2011091600;
$plugin->release = '2.0.1';
$plugin->component = 'clickap_legacy';
$plugin->dependencies = array('clickap_code' => ANY_VERSION
, 'clickap_hourcategories' => ANY_VERSION
, 'clickap_longlearn_categories' => ANY_VERSION);