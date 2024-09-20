<?php
/**
 *
 * @package    enrol
 * @subpackage profile
 * @author     Maria Tan(CLICK-AP)
 * @author     Martin Freeman(CLICK-AP)
 * @copyright  CLICK-AP {@link https://www.click-ap.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace enrol_profile\task;

class processenrolments_task extends \core\task\scheduled_task {

    /**
     * Get a descriptive name for this task (shown to admins).
     *
     * @return string
     */
    public function get_name() {
        return 'Process all rules and enrolments for Enrol by user profile fields';
    }

    /**
     * Run cron.
     */
    public function execute() {
        global $CFG;
        require_once ($CFG->dirroot . '/enrol/profile/lib.php');
        \enrol_profile_plugin::process_enrolments();
    }
}