<?php

defined('MOODLE_INTERNAL') || die();

function clickap_program_get_user_issuedate($program, $userid) {
    global $DB, $USER;

    if (empty($userid)) {
        $userid = $USER->id;
    }
    $dateissued = '';
    if($DB->record_exists('program_issued', array('programid'=>$program->id, 'userid'=>$userid))){
        $gettime = $DB->get_field('program_issued', 'dateissued', array('programid'=>$program->id, 'userid'=>$userid));
        
        $dt = new stdClass();
        $dt->y = date('Y',$gettime);
        $dt->m = date('m', $gettime);
        $dt->d = date('d', $gettime);
        return $dt;
        //$dateissued = get_string('strftimedate2', 'certificate', $dt);
    }
    return $dateissued;
}

/**
 * Sends text to output given the following params.
 *
 * @param stdClass $pdf
 * @param int $x horizontal position
 * @param int $y vertical position
 * @param char $align L=left, C=center, R=right
 * @param string $font any available font in font directory
 * @param char $style ''=normal, B=bold, I=italic, U=underline
 * @param int $size font size in points
 * @param string $text the text to print
 * @param int $width horizontal dimension of text block
 */
function clickap_program_certificate_print_text($pdf, $x, $y, $align, $font='freeserif', $style, $size = 10, $text, $width = 0) {
    $pdf->setFont($font, $style, $size);
    $pdf->SetXY($x, $y);
    $pdf->writeHTMLCell($width, 0, '', '', $text, 0, 0, 0, true, $align);
}

/**
 * Prints border images from the borders folder in PNG or JPG formats.
 *
 * @param stdClass $pdf
 * @param stdClass $certificate
 * @param string $type the type of image
 * @param int $x x position
 * @param int $y y position
 * @param int $w the width
 * @param int $h the height
 */
function clickap_program_award_print_image($pdf, $program, $type, $x, $y, $w, $h) {
    global $CFG;

    $path = '';
    switch($type) {
        case 'borders' :
            $attr = 'borderstyle';
            $path = "$CFG->dirroot/admin/clickap/program/pix/$type/$program->borderstyle";
            $uploadpath = "$CFG->dataroot/admin/clickap/program/pix/pix/$type/$program->borderstyle";
            break;

        case 'award' :
            $attr = 'borderstyle';
            $certfile = clickap_program_award_get_image($program->id);
            foreach ($certfile as $file) {
                $isimage = $file->is_valid_image();
                if($isimage){
                    $url = file_encode_url($CFG->wwwroot . "/pluginfile.php",
                            '/'. $file->get_contextid(). '/'. $file->get_component(). '/'.
                            $file->get_filearea(). $file->get_filepath() . $program->id .'/'.$file->get_filename(),'');
                    $path = file_encode_url($url,'');
                    $program->borderstyle = $attr;
                }
            }
            break;
    }

    if (!empty($path)) {
        if (file_exists($path)) {
            $pdf->Image($path, $x, $y, $w, $h);
        }else{
            $img = file_get_contents($path);
            if($img){
                $pdf->Image($path, $x, $y, $w, $h);
            }
        }
    }
}

function clickap_program_award_get_image($programid = null) {
    global $CFG,$DB;
    if ($programid){
        require_once($CFG->libdir. '/filestorage/file_storage.php');

        $fs = get_file_storage();
        $context = context_system::instance();
        $files = $fs->get_area_files($context->id, 'clickap_program', 'award', $programid, 'filename', false);
        if (count($files)) {
            foreach ($files as $key => $file) {
                $isimage = $file->is_valid_image();
                if (!$isimage) {
                    unset($files[$key]);
                }
            }
        }
        return $files; 
    }
    return '';
}

function clickap_program_award_get_images($type) {
    global $CFG;

    switch($type) {
        case 'borders' :
            $path = "$CFG->dirroot/admin/clickap/program/pix/borders";
            $uploadpath = "$CFG->dataroot/admin/clickap/program/pix/borders";
            break;
    }
    // If valid path
    if (!empty($path)) {
        $options = array();
        $options += clickap_program_award_scan_image_dir($path);
        $options += clickap_program_award_scan_image_dir($uploadpath);

        // Sort images
        ksort($options);

        // Add the 'no' option to the top of the array
        $options = array_merge(array('0' => get_string('no')), $options);

        return $options;
    } else {
        return array();
    }
}

function clickap_program_award_scan_image_dir($path) {
    // Array to store the images
    $options = array();

    // Start to scan directory
    if (is_dir($path)) {
        $iterator = new DirectoryIterator($path);
        foreach ($iterator as $fileinfo) {
            $filename = $fileinfo->getFilename();
            $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            if ($fileinfo->isFile() && in_array($extension, array('png', 'jpg', 'jpeg'))) {
                $options[$filename] = pathinfo($filename, PATHINFO_FILENAME);
            }
        }
    }
    return $options;
}