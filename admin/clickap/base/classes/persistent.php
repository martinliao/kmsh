<?php
/**
 * Abstract class for core_competency objects saved to the DB.
 *
 * @package    clickap_base
 * @copyright  2021 Click-AP {@link https://www.click-ap.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace clickap_base;
defined('MOODLE_INTERNAL') || die();

class_alias('core\\invalid_persistent_exception', 'cliakap_org\\invalid_persistent_exception');

abstract class persistent extends \core\persistent {
    /**
     * Magic method to capture getters and setters.
     * This is only available for competency persistents for backwards compatibility.
     * It is recommended to use get('propertyname') and set('propertyname', 'value') directly.
     *
     * @param  string $method Callee.
     * @param  array $arguments List of arguments.
     * @return mixed
     */
    final public function __call($method, $arguments) {
        debugging('Use of magic setters and getters is deprecated. Use get() and set().', DEBUG_DEVELOPER);
        if (strpos($method, 'get_') === 0) {
            return $this->get(substr($method, 4));
        } else if (strpos($method, 'set_') === 0) {
            return $this->set(substr($method, 4), $arguments[0]);
        }
        throw new \coding_exception('Unexpected method call: ' . $method);
    }

}
