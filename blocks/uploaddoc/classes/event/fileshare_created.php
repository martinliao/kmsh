<?php
/**
 * @package   block_uploaddoc
 * @copyright 2018 MARY CHEN  {@link http://www.click-ap.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v1 or later
 */
namespace block_uploaddoc\event;
defined('MOODLE_INTERNAL') || die();

class fileshare_created extends \core\event\base {
    protected function init() {
        $this->data['crud'] = 'c';
        $this->data['edulevel'] = self::LEVEL_OTHER;
        $this->data['objecttable'] = 'derberus_files';
    }

    public function get_url() {
        return new \moodle_url('/blocks/uploaddoc/assign.php', array('fileid' => $this->fileid));
    }
}