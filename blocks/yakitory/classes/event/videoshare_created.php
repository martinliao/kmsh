<?php
/**
 * @package   block_yakitory
 * @copyright 2021 MARY CHEN  {@link https://www.click-ap.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v1 or later
 */
namespace block_yakitory\event;
defined('MOODLE_INTERNAL') || die();

class videoshare_created extends \core\event\base {
    protected function init() {
        $this->data['crud'] = 'c';
        $this->data['edulevel'] = self::LEVEL_OTHER;
        $this->data['objecttable'] = 'yakitory_videos';
    }

    public function get_url() {
        return new \moodle_url('/blocks/yakitory/assign.php', array('videoid' => $this->videoid));
    }
}