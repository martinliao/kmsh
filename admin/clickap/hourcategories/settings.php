<?php
/**
 * 
 * @package clickap_hourcategories
 * @author 2018 Mary Tan
 * @copyright CLICK-AP (https://www.click-ap.com)
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

$systemcontext = context_system::instance();
if ($hassiteconfig
    or has_capability('clickap/hourcategories:view', $systemcontext)){

    $subplugins = false;
    foreach (core_plugin_manager::instance()->get_plugins_of_type('hourcredit') as $plugin) {
        $subplugins = true;continue;
    }

    if($subplugins){
        $ADMIN->add('clickapsettings', new admin_category('clickaphourcategories', new lang_string('hourcategory_manage', 'clickap_hourcategories')));
        $ADMIN->add('clickaphourcategories', new admin_externalpage('hourcategories', get_string('pluginname', 'clickap_hourcategories'), "$CFG->wwwroot/admin/clickap/hourcategories/index.php",'clickap/hourcategories:view'));
    }
    else{
        $ADMIN->add('clickapsettings', new admin_externalpage('hourcategories', get_string('pluginname', 'clickap_hourcategories'), "$CFG->wwwroot/admin/clickap/hourcategories/index.php",'clickap/hourcategories:view'));
    }
    
    foreach (core_plugin_manager::instance()->get_plugins_of_type('hourcredit') as $plugin) {
        /** @var \clickap_hourcategories\plugininfo\hourcredit $plugin */
        $plugin->load_settings($ADMIN, 'clickaphourcategories', $hassiteconfig);
        $editpage = $CFG->dirroot."/".$CFG->admin."/clickap/hourcategories/credit/".$plugin->name."/edit.php";
        if(file_exists($editpage)){
            $ADMIN->add('hourcredit', new admin_externalpage('hourcredit'.$plugin->name, 
                get_string('editingheading', 'hourcredit_'.$plugin->name),
                 "$CFG->wwwroot/$CFG->admin/clickap/hourcategories/credit/".$plugin->name."/edit.php", 'clickap/hourcategories:view'));
        }
    }
}
$settings = null;