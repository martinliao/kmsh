<?php
/**
 * Version details.
 *
 * @package    clickap
 * @subpackage program
 * @copyright  2018 Click-AP <elaine@click-ap.com>
 * @license    http://www.click-ap.com/copyleft/gpl.html GNU GPL v3 or later
 */
namespace clickap_program\task;

/**
 * A scheduled task class for LDAP user sync.
 *
 * @copyright  2015 Vadim Dvorovenko <Vadimon@mail.ru>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class programs_cron_task extends \core\task\scheduled_task {

    /**
     * Get a descriptive name for this task (shown to admins).
     *
     * @return string
     */
    public function get_name() {
        return get_string('crontask', 'clickap_program');
    }

    /**
     * Run users sync.
     */
    public function execute() {
        global $CFG;
        require_once($CFG->dirroot . '/admin/clickap/program/cron.php');
        clickap_program_cron();
    }

}
