<?php
/**
 * @package   block_yakitory
 * @copyright 2021 MARY CHEN  {@link https://www.click-ap.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v1 or later
 */
require_once('../../config.php');

global $CFG, $DB, $USER;

$file_get = fopen($CFG->tempdir . "/yakitory_notification(get).txt","a+");
$str = "GET Data";
foreach ($_GET as $key => $value){
 $str .= $key."-".$value."\n\r";
}
fwrite($file_get, $str);
fclose($file_get);

$file_post = fopen($CFG->tempdir . "/yakitory_notification(post).txt","a+");
$str = "POST Data";
foreach ($_POST as $key => $value){
 $str .= $key."-".$value."\n\r";
}
fwrite($file_post, $str);
fclose($file_post);
