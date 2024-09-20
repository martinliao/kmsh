<?php
/**
 * @package    block
 * @subpackage smartmenu
 * 
 * @author     Mary Chen (mary@click-ap.com)
 * @author     Jack (jack@click-ap.com)
 * @copyright  Click-AP {@line https://www.click-ap.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v2 or later
 */
require_once(dirname(__FILE__) . '/../../config.php');

defined('MOODLE_INTERNAL') || die();

$plugin->version   = 2022042701;
$plugin->requires  = 2020061501; // 3.9.1
$plugin->component = 'block_smartmenu';
$plugin->release  = '3.9.3';
$plugin->dependencies = array('local_mooccourse' => '2016093001');