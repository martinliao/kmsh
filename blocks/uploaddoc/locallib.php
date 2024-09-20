<?php
/**
 * @package   block_uploaddoc
 * @copyright 2021 MARY CHEN  {@link http://www.click-ap.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v1 or later
 */

defined('MOODLE_INTERNAL') || die;

function block_uploaddoc_get_files($userid, $filters = array()){
    global $DB;

    $config = get_config('derberus');
    
    $params = array('userid' => $userid, 'clienthost' => $config->view_host);
    if(!empty($filters)){
        $sql = "SELECT * ";
    } else {
        $sql = "SELECT count(id) ";
    }
    $sql .= " FROM {derberus_files} 
              WHERE userid = :userid AND course != 'Share' AND upload_host = :clienthost 
              ORDER BY timecreated desc";
    
    if(!empty($filters)){
        $offset = $filters['page'] * $filters['perpage'];
        return $DB->get_records_sql($sql, $params, $offset, $filters['perpage']);
    }
    
    return $DB->count_records_sql($sql, $params);
}

function block_uploaddoc_get_token_fileurl($url) {
    setlocale(LC_ALL, "en_US.UTF-8");
    $parts = parse_url($url);
    $path = $realPath = str_replace('/drive', "", $parts['path']);
    $filname = pathinfo(($path), PATHINFO_FILENAME);
    $path = pathinfo(($path), PATHINFO_DIRNAME);
    $path = $path . '/' . $filname;
    
    $duration = 300;
    $expire = time() + 10*60 + (int)($duration * 1.2);
    $secret = ' enigma';

    $md5 = base64_encode(md5($expire . $path . $secret , true));
    $md5 = strtr($md5, '+/', '-_');
    $md5 = str_replace('=', '', $md5);

    $tokenurl =  $parts['scheme'].'://'. $parts['host'] .'/docs/pdf/' . $md5 .'/'.$expire . $realPath;
    
    return $tokenurl;
}

function block_uploaddoc_template_text($tmp) {
    global $USER;
    
    $pdf = new \assignfeedback_editpdf\pdf();
    $watermarkTxt = $USER->username;
    $skipToc = false;
    $fontFamily = 'msungstdlight';
    $fontSize = '80';
    $textColor = '#3e3e3e';
    // set the source file
    $pageCount = $pdf->setSourceFile($tmp);
    for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
        $skip = false;
        // import page 1
        $tplIdx = $pdf->importPage($pageNo);
        $size = $pdf->getTemplateSize($tplIdx);
        
        $Orientation = $size['height'] > $size['width'] ? 'P' : 'L';
        $pdf->AddPage($Orientation , array($size['width']-1, $size['height']));
        //$pdf->SetMargins(5, 5, 5);
        $pdf->useTemplate($tplIdx);
        if($skipToc == 1){
            if($pageNo == $pageCount OR $pageNo == 1){
                $skip = true;
            }
        }
        else if($skipToc == 2 && $pageNo == 1){
                $skip = true;
        }
        
        if (!$skip ){
            //$pdf->SetFont('stsongstdlight', 'msungstdlight', 'kozgopromedium', 'hysmyeongjostdmedium', 'B', 40);
            $pdf->SetFont($fontFamily, 'B', $fontSize, '', true);
            $hex_color = str_replace('#', '', $textColor);
            $split_hex_color = str_split( $hex_color, 2 );
            $rgb1 = hexdec( $split_hex_color[0] );
            $rgb2 = hexdec( $split_hex_color[1] );
            $rgb3 = hexdec( $split_hex_color[2] );
            $pdf->SetTextColor($rgb1, $rgb2, $rgb3);

            //Text rotated around its origin
            $x = $size['width'] /3 - mb_strlen($watermarkTxt)/2;
            $y = $size['height'] /2;

            $pdf->SetAlpha(0.25);
            $pdf->StartTransform();
            $pdf->Rotate(30, $watermarkTxt, $y);
            //$pdf->Text($x, $y, $txt);
            $pdf->SetXY(0, $y);
            $pdf->Cell($size['width'], 0, $watermarkTxt, 0, 0, 'C');
            $pdf->StopTransform();
        }
    }
    //$pdf->Output('F', $tmp); // -v 2.3.5
    $pdf->Output($tmp, 'F'); // fpdi -v 1.6.2
    $pdf->Close();
}