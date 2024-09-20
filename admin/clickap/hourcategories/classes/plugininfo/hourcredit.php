<?php
/**
 * 
 *
 * @package    hourcredit_profile
 * @author     Jack Liou <jack@click-ap.com>
 * @author     Elaine Chen <elaine@click-ap.com>
 * @copyright  2019 Click-AP {@link https://www.click-ap.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace clickap_hourcategories\plugininfo;

use core\plugininfo\base, moodle_url, part_of_admin_tree, admin_settingpage;

defined('MOODLE_INTERNAL') || die();
class hourcredit extends base {
    public function get_settings_section_name() {
        return 'hourcreditsetting' . $this->name;
    }

    public function load_settings(part_of_admin_tree $adminroot, $parentnodename, $hassiteconfig) {
        global $CFG, $USER, $DB, $OUTPUT, $PAGE; // In case settings.php wants to refer to them.
        $ADMIN = $adminroot; // May be used in settings.php.
        $section = $this->get_settings_section_name();

        if (!$this->is_installed_and_upgraded()) {
            return;
        }

        if (!$hassiteconfig or !file_exists($this->full_path('settings.php'))) {
            return;
        }

        $settings = new admin_settingpage($section, $this->displayname, 'clickap/hourcategories:view', $this->is_enabled() === false);
        include($this->full_path('settings.php'));

        if ($settings) {
            $ADMIN->add($parentnodename, $settings);
        }
    }
}

