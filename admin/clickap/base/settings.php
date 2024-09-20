<?php
/**
 * Plugin version info
 *
 * @package    clickap_base
 * @author     Jack Liou <jack@click-ap.com>
 * @author     Elaine Chen
 * @copyright  2023 Click-AP {@link https://www.click-ap.com}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig ) {
    $ADMIN->add('root', new admin_category('clickapsettings', new lang_string('clickap','clickap_base')), 'users');
    //$settings = new admin_settingpage('clickapsettingsbase', 'ClickAP-Setting', 'clickap/base:config', false);
    //$yesno = array(new lang_string('no'), new lang_string('yes'));
    //$settings->add(new admin_setting_configselect('clickap_base/debugmode', 'Debug method', '', 0, $yesno ));
    // $ADMIN->add('clickap', $setting);
    $settings = null;
} else {
    $settings = null;
}