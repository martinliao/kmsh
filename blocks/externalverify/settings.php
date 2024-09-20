<?php
/**
 * plugin infomation
 * 
 * @package    block
 * @subpackage externalverify
 * @author     Elaine Chen(CLICK-AP)
 * @copyright  CLICK-AP {@link https://www.click-ap.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    $auths = array();
    $plugins = core_component::get_plugin_list('auth');
    foreach ($plugins as $name => $fulldir) {
        if($name == 'nologin'){continue;}
        if (is_enabled_auth($name)) {
            $auth[$name] = $name;
        }
    }
    $settings->add(new admin_setting_configmultiselect('block_externalverify/authmethod', get_string('authmethod', 'block_externalverify'),
                       get_string('configmaxattachments', 'block_externalverify'), array('manual'), $auth, 'manual'));
    
    $name = 'block_externalverify/templatefile';
    $title = get_string('templatefile','block_externalverify');
    $setting = new admin_setting_configstoredfile($name, $title, '', 'templatefile', 0, array('maxfiles' => 5));
    $settings->add($setting);
    
    $option = array(1=>'1',2=>'2',3=>'3',4=>'4',5=>'5',6=>'6',7=>'7',8=>'8',9=>'9',10=>'10');
    $settings->add(new admin_setting_configselect('block_externalverify/maxattachments', get_string('maxattachments', 'block_externalverify'),
                       get_string('configmaxattachments', 'block_externalverify'), 5, $option, 5));
                       
    if (isset($CFG->maxbytes)) {
        $settings->add(new admin_setting_configselect('block_externalverify/maxbytes', get_string('maxattachmentsize', 'block_externalverify'),
                           get_string('configmaxbytes', 'block_externalverify'), 512000, get_max_upload_sizes($CFG->maxbytes, 0, 0, 0),512000));
    }
    
    $settings->add(new admin_setting_configtext('block_externalverify/mail_subject', get_string('mail_subject', 'block_externalverify'),
                       get_string('mail_subject_desc', 'block_externalverify'), get_string('reject_subject', 'block_externalverify')));
    $settings->add(new admin_setting_configtextarea('block_externalverify/mail_content', get_string('mail_content', 'block_externalverify'),
                       get_string('mail_content_desc', 'block_externalverify'), '', PARAM_RAW));
}