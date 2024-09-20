<?php
/**
 * Version details.
 *
 * @package    clickap
 * @subpackage program
 * @copyright  2018 Click-AP <elaine@click-ap.com>
 * @license    http://www.click-ap.com/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$orientation = 'L';
$pdf = new PDF($orientation, 'mm', 'A4', true, 'UTF-8', false);

$pdf->SetTitle($program->name);
$pdf->SetProtection(array('modify'));
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->SetAutoPageBreak(false, 0);
$pdf->AddPage();

// Define variables
// Landscape
if ($orientation == 'L') {
    $x = 10;
    $y = 30;
    $sealx = 230;
    $sealy = 150;
    $sigx = 47;
    $sigy = 155;
    $custx = 47;
    $custy = 155;
    $wmarkx = 40;
    $wmarky = 31;
    $wmarkw = 212;
    $wmarkh = 148;
    $brdrx = 0;
    $brdry = 0;
    $brdrw = 297;
    $brdrh = 210;
    $codey = 175;
}
//$pdf->AddFont('wt064', '', 'wt064.ttf');
$pdf->AddFont('times', '', 'times.php');
//$pdf->AddFont('msungstdlight', '', 'msungstdlight.php');
$pdf->AddFont('ukai', '', 'ukai.php');
//

//$pdf->SetFont('msungstdlight', '', 12, '', true); 
// Get font families.
if(isset($user)){
    //$UName = fullname($user);
    $UName = $user->firstname;
}else{
    //$UName = fullname($USER);
    $UName = $USER->firstname;
}
$PName = format_string($program->name);

// Get font families.
$fontsans = 'ukai';//chinese
if(mb_strlen($UName, 'UTF-8') == strlen($UName)){
    $UFont = 'times';//english//times = Times New Roman    
}else{
    $UFont = $fontsans;
}
if(mb_strlen($PName, 'UTF-8') == strlen($PName)){
    $PFont = 'times';//english//times = Times New Roman    
}else{
    $PFont = $fontsans;
}
/*
how to transform tcpdf font 
use *.ttf font to transform tcpdf font
require_once(dirname(__FILE__).'../../../tcpdf.php');
$font = TCPDF_FONTS::addTTFfont('wt064.ttf');
*/
/*font list
droidsansfallback
wt064
wt024
wcl06
msjhbd
msjh
*/
// Add images and lines
//clickap_program_award_print_image($pdf, $program, 'borders', $brdrx, $brdry, $brdrw, $brdrh);
clickap_program_award_print_image($pdf, $program, 'award', $brdrx, $brdry, $brdrw, $brdrh);

// Add text
$pdf->SetAlpha(1);
$pdf->SetTextColor(0, 0, 0);
clickap_program_certificate_print_text($pdf, $x, $y + 60, 'C', $UFont, '', 30, $UName);

$pdf->SetTextColor(27, 65 ,149);
clickap_program_certificate_print_text($pdf, $x + 49 , $y + 90, 'C', $PFont, 'B', 26, $PName, 180);

$pdf->SetTextColor(0, 0, 0);
$issuedate = clickap_program_get_user_issuedate($program, $user->id);
if($issuedate){
    //clickap_program_certificate_print_text($pdf, $x, $y + 134, 'C', $fontsans, '', 18,  $issuedate);
    clickap_program_certificate_print_text($pdf, $x + 55, $y + 134, 'L', $fontsans, '', 20,  get_string('strftimedate_1', 'clickap_program'));
    clickap_program_certificate_print_text($pdf, $x + 135, $y + 134, 'L', $fontsans, '', 20,  get_string('strftimedate_y', 'clickap_program'));
    clickap_program_certificate_print_text($pdf, $x + 175, $y + 134, 'L', $fontsans, '', 20,  get_string('strftimedate_m', 'clickap_program'));
    clickap_program_certificate_print_text($pdf, $x + 215, $y + 134, 'L', $fontsans, '', 20,  get_string('strftimedate_d', 'clickap_program'));

    clickap_program_certificate_print_text($pdf, $x + 95, $y + 134, 'C', $fontsans, '', 20,  $issuedate->y, 40);
    clickap_program_certificate_print_text($pdf, $x + 145, $y + 134, 'C', $fontsans, '', 20,  $issuedate->m, 30);
    clickap_program_certificate_print_text($pdf, $x + 185, $y + 134, 'C', $fontsans, '', 20,  $issuedate->d, 30);
}