<?php
/**
 * 
 * @package    block_certverify
 * @author     Elaine Chen(CLICK-AP)
 * @copyright  CLICK-AP {@link https://www.click-ap.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

$ADMIN->add('reports', new admin_externalpage('blockcertverifyreport', get_string('report', 'block_certverify'),
        new moodle_url('/blocks/certverify/report.php'), 'block/certverify:viewreport'));

if ($ADMIN->fulltree) {
    $auths = array();
    $plugins = core_component::get_plugin_list('auth');
    foreach ($plugins as $name => $fulldir) {
        if($name == 'nologin'){continue;}
        if (is_enabled_auth($name)) {
            $auth[$name] = $name;
        }
    }
    $settings->add(new admin_setting_configmultiselect('block_certverify/authmethod', get_string('authmethod', 'block_certverify'),
                       get_string('configmaxattachments', 'block_certverify'), array('manual'), $auth, 'manual'));
    
    $name = 'block_certverify/templatefile';
    $title = get_string('templatefile','block_certverify');
    $setting = new admin_setting_configstoredfile($name, $title, '', 'templatefile', 0, array('maxfiles' => 2));
    $settings->add($setting);
    
    /*
    $option = array(1=>'1',2=>'2',3=>'3',4=>'4',5=>'5');
    $settings->add(new admin_setting_configselect('block_certverify/maxattachments', get_string('maxattachments', 'block_certverify'),
                       get_string('configmaxattachments', 'block_certverify'), 1, $option, 1));
    */
    set_config('maxattachments', 1, 'block_certverify');
         
    if (isset($CFG->maxbytes)) {
        $settings->add(new admin_setting_configselect('block_certverify/maxbytes', get_string('maxattachmentsize', 'block_certverify'),
                           get_string('configmaxbytes', 'block_certverify'), 512000, get_max_upload_sizes($CFG->maxbytes, 0, 0, 0),512000));
    }
    
    $settings->add(new admin_setting_configtext('block_certverify/mail_subject', get_string('mail_subject', 'block_certverify'),
                       get_string('mail_subject_desc', 'block_certverify'), get_string('mail_reject_subject', 'block_certverify')));
    $settings->add(new admin_setting_configtextarea('block_certverify/mail_content', get_string('mail_content', 'block_certverify'),
                       get_string('mail_content_desc', 'block_certverify'), '', PARAM_RAW));
    
    $settings->add(new admin_setting_configduration('block_certverify/duenotify', 
                        get_string('duenotify', 'block_certverify'), get_string('duenotify_desc', 'block_certverify'), 0));
}