<?php
/**
 * Version details.
 *
 * @package    clickap_code
 * @copyright  2021 CLICK-AP {@link https://www.click-ap.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die;

if ($hassiteconfig ) {
    $ADMIN->add('clickapsettings', new admin_externalpage('clickapcode', get_string('pluginname', 'clickap_code'), "$CFG->wwwroot/admin/clickap/code/index.php",'clickap/code:view'));
}
$settings = null;