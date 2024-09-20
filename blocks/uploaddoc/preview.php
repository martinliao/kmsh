<?php 

require_once('../../config.php');
require_once($CFG->libdir . '/filelib.php');
require_once($CFG->dirroot . '/blocks/uploaddoc/locallib.php');

$userid = required_param('userid', PARAM_INT);
$id = required_param('fid', PARAM_INT);

if($USER->id != $userid){
    print_error('user not match');
    exit;
}

$PAGE->set_context(context_user::instance($userid));
$file = $DB->get_record('derberus_files', array('id'=>$id, 'userid'=>$userid));
$url = $file->upload_host.$file->streaming_url;
$path = block_uploaddoc_get_token_fileurl($url);

require_once($CFG->libdir . '/pdflib.php');

$filename = basename($path);
$fileContents = file_get_contents($path);
$renderer = $PAGE->get_renderer('mod_pdfolder');
$cloneFile = $renderer->pdfolder_copy_content_to_temp($fileContents, '');

if(!isset($cloneFile)) {
    return false;
}
block_uploaddoc_template_text($cloneFile);

header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename='.$filename);
header('Content-Transfer-Encoding: binary');
header('Accept-Ranges: bytes');
@readfile($cloneFile);
unlink($cloneFile);
