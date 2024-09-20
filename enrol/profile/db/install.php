<?php
/**
 *
 *  @package    enrol
 * @subpackage profile
 * @author     Maria Tan(CLICK-AP)
 * @author     Martin Freeman(CLICK-AP)
 * @copyright  CLICK-AP {@link https://www.click-ap.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

function xmldb_enrol_profile_install() {
    global $DB;
    
    //transfer enrol_attributes to enrol_profile
    $DB->execute("UPDATE {enrol} SET enrol = 'profile' WHERE enrol = 'attributes'");

    return true;
}