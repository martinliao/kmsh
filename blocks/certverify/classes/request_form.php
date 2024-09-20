<?php
/**
 * 
 * @package    block_certverify
 * @author     Elaine Chen(CLICK-AP)
 * @copyright  CLICK-AP {@link https://www.click-ap.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');
}

require_once($CFG->libdir.'/formslib.php');
require_once($CFG->dirroot.'/blocks/certverify/locallib.php');

class block_certverify_request_form extends moodleform {
    function definition() {
        global $CFG, $DB, $USER;

        $filesoptions = $this->_customdata['filesoptions'];
        
        $mform =& $this->_form;

        $mform->addElement('header','certdetails', get_string('requestdetails', 'block_certverify'));

        //$clickapplugins = core_component::get_plugin_list('clickap');
        
        $options = array(0=>get_string('choose')) + block_certverify_get_certs();
        $mform->addElement('select', 'certid', get_string('certname', 'block_certverify'), $options);
        $mform->setType('certid', PARAM_INT);
        
        $mform->addElement('text', 'certnumber', get_string('certnumber', 'block_certverify'), 'maxlength="30"  size="20"');
        $mform->addRule('certnumber', get_string('missingcertnumber','block_certverify'), 'required', null, 'client');
        $mform->setType('certnumber', PARAM_TEXT);

        $mform->addElement('date_selector', 'dateissued', get_string('dateissued', 'block_certverify'));
        $mform->setDefault('dateissued', time());
        
        $mform->addElement('date_selector', 'dateexpire', get_string('dateexpire', 'block_certverify'), array('optional' => true));
        $mform->setDefault('dateexpire', time()+YEARSECS);
        
        $mform->addElement('textarea', 'remark', get_string('remark', 'block_certverify'), array('rows'=>'5', 'cols'=>'50'));
        $mform->setType('remark', PARAM_TEXT);       
        
        $mform->addElement('header','certattatchment', get_string('requestattatchment', 'block_certverify'));
        $fs = get_file_storage();
        $context = context_system::instance();
        $files = $fs->get_area_files($context->id, 'block_certverify', 'templatefile', false, 'filename', false);
        if (count($files) > 0) {
            $templatefile = '';
            foreach ($files as $file) {
                $url = file_encode_url("$CFG->wwwroot/pluginfile.php",
                        '/'. $file->get_contextid(). '/'. $file->get_component(). '/'.
                        $file->get_filearea(). $file->get_filepath(). $file->get_itemid().'/'.$file->get_filename());
                $templatefile .= '<a href="'.$url.'">'.$file->get_filename().'</a>&nbsp;&nbsp;';
            }
            if(!empty($templatefile)){
                $mform->addElement('static', 'templatefile', get_string('templatefile', 'block_certverify'), $templatefile);
            }
        }
        
        $mform->addElement('filemanager', 'attachments_filemanager', get_string('attachments', 'block_certverify'), null, $filesoptions);
        $mform->addHelpButton('attachments_filemanager', 'attachments', 'block_certverify');
        $mform->addRule('attachments_filemanager', get_string('missingattachments', 'block_certverify'), 'required', null, 'client');
        
        $mform->addElement('hidden', 'userid', $USER->id);
        $mform->setType('userid', PARAM_INT);

        $this->add_action_buttons(true, get_string('submit'));
    }

    function validation($data, $files) {
        $errors = parent::validation($data, $files);

        if(empty($data['certid'])){
            $errors['certid'] = get_string('certchoose_error','block_certverify');
        }

        if(!empty($data['dateexpire'])){
            if($data['dateexpire'] <= $data['dateissued']){
                $errors['dateexpire'] = get_string('dateexpire_error','block_certverify');
            }
        }
        
        return $errors;
    }
}