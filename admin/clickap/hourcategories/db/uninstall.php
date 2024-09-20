<?php
/**
 * 
 * @package clickap_hourcategories
 * @author 2018 Mary Tan
 * @copyright CLICK-AP (https://www.click-ap.com)
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

function xmldb_clickap_hourcategories_uninstall() {
    global $DB;

    $dbman = $DB->get_manager();
    
    $table = new xmldb_table('clickap_hourcredit_profile');
    if ($dbman->table_exists($table)) {
        $DB->execute("DROP TABLE {clickap_hourcredit_profile}");
    }
}