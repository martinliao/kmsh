<?php

/**
 * This file keeps track of upgrades to the session enrolment plugin
 *
 * @package    enrol_session
 * @copyright  2012 Petr Skoda {@link http://skodak.org
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

function xmldb_enrol_session_upgrade($oldversion) {
    global $CFG, $DB, $OUTPUT;

    $dbman = $DB->get_manager();

    // Moodle v2.3.0 release upgrade line
    // Put any upgrade step following this

    if ($oldversion < 2012101400) {
        // Set default expiry threshold to 1 day.
        $DB->execute("UPDATE {enrol} SET expirythreshold = 86400 WHERE enrol = 'session' AND expirythreshold = 0");
        upgrade_plugin_savepoint(true, 2012101400, 'enrol', 'session');
    }

    if ($oldversion < 2012120600) {
        // Enable new session enrolments everywhere.
        $DB->execute("UPDATE {enrol} SET customint6 = 1 WHERE enrol = 'session'");
        upgrade_plugin_savepoint(true, 2012120600, 'enrol', 'session');
    }


    // Moodle v2.4.0 release upgrade line
    // Put any upgrade step following this


    // Moodle v2.5.0 release upgrade line.
    // Put any upgrade step following this.


    // Moodle v2.6.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2013112100) {
        // Set customint1 (group enrolment key) to 0 if it was not set (null).
        $DB->execute("UPDATE {enrol} SET customint1 = 0 WHERE enrol = 'sssion' AND customint1 IS NULL");
        upgrade_plugin_savepoint(true, 2013112100, 'enrol', 'session');
    }

    // Moodle v2.7.0 release upgrade line. Put any upgrade step following this.
    if ($oldversion < 2014051202) {
        
        $table = new xmldb_table('enrol_session');
        if (!$dbman->table_exists($table)) {
            // Adding fields.
            $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null);
            $table->add_field('courseid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', null);
            $table->add_field('instanceid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', null);
            $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, null, null, null );
            $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', null);
            $table->add_field('sessdate', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', null);
            $table->add_field('duration', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', null);
            $table->add_field('timeupdated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', null);
            //<FIELD NAME="trackid" TYPE="int" LENGTH="10" NOTNULL="false" DEFAULT="0" SEQUENCE="false"/>
            //<FIELD NAME="groupid" TYPE="int" LENGTH="10" NOTNULL="false" DEFAULT="0" SEQUENCE="false"/>
            
            // Adding key.
            $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
            $table->add_key('courseid', XMLDB_KEY_FOREIGN, array('courseid'), 'course', array('id'));
            
            $table->add_index('instanceid', XMLDB_INDEX_NOTUNIQUE, array('instanceid'));
            $table->add_index('sessdate', XMLDB_INDEX_NOTUNIQUE, array('sessdate'));
            
            $dbman->create_table($table); // no return value
        }
        upgrade_plugin_savepoint(true, 2014051202, 'enrol', 'session');
    }
    
    if ($oldversion < 2014051203) {
        
        //if (!$dbman->field_exists($table, $field)) {
        
        $table = new xmldb_table('enrol_session');
        if ($dbman->table_exists($table)) {
            // Adding fields.
            
            $field = new xmldb_field('addmultiply', XMLDB_TYPE_INTEGER, '1', null, null, null, null);
            $dbman->add_field($table, $field);
            $field = new xmldb_field('period', XMLDB_TYPE_INTEGER, '10', null, null, null, '0');
            $dbman->add_field($table, $field);
            $field = new xmldb_field('sdays', XMLDB_TYPE_CHAR, '255', null, null, null, null);
            $dbman->add_field($table, $field);
            $field = new xmldb_field('sessenddate', XMLDB_TYPE_INTEGER, '10', null, null, null, '0');
            $dbman->add_field($table, $field);
            
        }
        upgrade_plugin_savepoint(true, 2014051203, 'enrol', 'session');
    }

    return true;
}



