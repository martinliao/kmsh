<?php
/**
 * analyze course hour completion task
 *
 * @package    clickap_hourcategories
 * @author     Jack Liou <jack@click-ap.com>
 * @author     Elaine Chen <elaine@click-ap.com>
 * @copyright  2020 Click-AP {@link https://www.click-ap.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace clickap_hourcategories\task;

defined('MOODLE_INTERNAL') || die();

class course_hourcompletions extends \core\task\scheduled_task {

    /**
     * Name for this task.
     *
     * @return string
     */
    public function get_name() {
        return get_string('completionhourstask', 'clickap_hourcategories');
    }

    /**
     * Run task for synchronising users.
     */
    public function execute() {
        @ini_set('max_execution_time', 0);
        raise_memory_limit("256M");
        
        $currentyear = date('Y', time()) - 1911;

        $processor = new \clickap_hourcategories\statsbase($currentyear);
        $plugin1 = get_plugin_list('clickap');
        if (array_key_exists("hourcategories", $plugin1)){
            $processor->execute_hourcategories();
        }
        
        $plugin2 = get_plugin_list('block');
        if (array_key_exists("externalverify", $plugin2)){
            $processor->execute_externalverify();
        }
        
        $plugin3 = get_plugin_list('clickap');
        if (array_key_exists("legacy", $plugin3)){
            $processor->execute_legacy();
        }
    }
}