<?php
/**
 * Version details.
 *
 * @package    blocks
 * @subpackage smartmenu
 * 
 * @author     Mary Chen (mary@click-ap.com)
 * @author     Jack (jack@click-ap.com)
 * @copyright  Click-AP {@line https://www.click-ap.com}
 * @license    http://www.click-ap.com/copyleft/gpl.html GNU GPL v3 or later
 */
function block_smartmenu_information_standard_log_view($course) {
    $eventdata = array();
    $eventdata['objectid'] = $course->id;
    $eventdata['context'] = context_course::instance($course->id);
    //$eventdata['other'] = array();
    $event = \block_smartmenu\event\information_viewed::create($eventdata);
    $event->trigger();
}
?>