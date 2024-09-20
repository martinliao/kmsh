<?php
/**
 * Class for exporting path_node data.
 *
 * @package    clickap_base
 * @copyright  2021 Click-AP {@link https://www.click-ap.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace clickap_base;
defined('MOODLE_INTERNAL') || die();

use context_system;

/**
 * Class for exporting path_node data.
 *
 * @copyright  2016 Issam Taboubi <issam.taboubi@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class path_node_exporter extends \core\external\exporter {

    /**
     * Constructor - saves the persistent object, and the related objects.
     *
     * @param mixed $data The data.
     * @param array $related Array of relateds.
     */
    public function __construct($data, $related = array()) {
        if (!isset($related['context'])) {
            // Previous code was automatically using the system context which was not always correct.
            // We let developers know that they must fix their code without breaking anything,
            // and fallback on the previous behaviour. This should be removed at a later stage: Moodle 3.5.
            debugging('Missing related context in path_node_exporter.', DEBUG_DEVELOPER);
            $related['context'] = context_system::instance();
        }
        parent::__construct($data, $related);
    }

    /**
     * Return the list of properties.
     *
     * @return array
     */
    protected static function define_related() {
        return [
            'context' => 'context'
        ];
    }

    /**
     * Return the list of properties.
     *
     * @return array
     */
    protected static function define_properties() {
        return [
            'id' => [
                'type' => PARAM_INT,
                'null' => NULL_ALLOWED
            ],
            'name' => [
                'type' => PARAM_TEXT
            ],
            'first' => [
                'type' => PARAM_BOOL
            ],
            'last' => [
                'type' => PARAM_BOOL
            ],
            'position' => [
                'type' => PARAM_INT
            ]
        ];
    }
}
