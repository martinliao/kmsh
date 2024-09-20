<?php
/**
 * @package   block_uploaddoc
 * @copyright 2016 MARY CHEN  {@link http://www.click-ap.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v1 or later
 */
defined('MOODLE_INTERNAL') || die();

$plugin->version   = 2021052501;
$plugin->requires  = 2014051217;
$plugin->release = '1.1.0';
$plugin->component = 'block_uploaddoc';
$plugin->dependencies = array('repository_derberus' => 2021010100, 'mod_pdfolder' =>'2021021000');