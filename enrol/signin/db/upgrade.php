<?php
/**
 * This file keeps track of upgrades to the signin enrolment plugin
 * 
 * @package    enrol_signin
 * @copyright  2018 CLICK-AP {@link https://www.click-ap.com/}
 * @author     Wei Chi & Maria Tan
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

function xmldb_enrol_signin_upgrade($oldversion) {
    global $CFG, $DB, $OUTPUT;

    $dbman = $DB->get_manager();

    // Moodle v2.3.0 release upgrade line
    // Put any upgrade step following this

    if ($oldversion < 2012101400) {
        // Set default expiry threshold to 1 day.
        $DB->execute("UPDATE {enrol} SET expirythreshold = 86400 WHERE enrol = 'signin' AND expirythreshold = 0");
        upgrade_plugin_savepoint(true, 2012101400, 'enrol', 'signin');
    }

    if ($oldversion < 2012120600) {
        // Enable new signin enrolments everywhere.
        $DB->execute("UPDATE {enrol} SET customint6 = 1 WHERE enrol = 'signin'");
        upgrade_plugin_savepoint(true, 2012120600, 'enrol', 'signin');
    }


    // Moodle v2.4.0 release upgrade line
    // Put any upgrade step following this


    // Moodle v2.5.0 release upgrade line.
    // Put any upgrade step following this.


    // Moodle v2.6.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2013112100) {
        // Set customint1 (group enrolment key) to 0 if it was not set (null).
        $DB->execute("UPDATE {enrol} SET customint1 = 0 WHERE enrol = 'signin' AND customint1 IS NULL");
        upgrade_plugin_savepoint(true, 2013112100, 'enrol', 'signin');
    }

    // Moodle v2.7.0 release upgrade line.
    // Put any upgrade step following this.

    return true;
}


