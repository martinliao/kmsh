<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * This file keeps track of upgrades to the supervisor enrolment plugin
 *
 * @package    enrol_supervisor
 * @copyright  2020 CLICK-AP  {@link https://www.click-ap.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

function xmldb_enrol_supervisor_upgrade($oldversion) {
    global $CFG, $DB;

    if ($oldversion < 2016052301) {
        // Get roles with manager archetype.
        $managerroles = get_archetype_roles('manager');
        if (!empty($managerroles)) {
            // Remove wrong CAP_PROHIBIT from supervisor:holdkey.
            foreach ($managerroles as $role) {
                $DB->execute("DELETE
                                FROM {role_capabilities}
                               WHERE roleid = ? AND capability = ? AND permission = ?",
                        array($role->id, 'enrol/supervisor:holdkey', CAP_PROHIBIT));
            }
        }
        upgrade_plugin_savepoint(true, 2016052301, 'enrol', 'supervisor');
    }

    // Automatically generated Moodle v3.2.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v3.3.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v3.4.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v3.5.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2020100601) {
        $dbman = $DB->get_manager();
        $table = new xmldb_table('enrol');
        //real student can enter the course start date
        $field = new xmldb_field('customint5_3', XMLDB_TYPE_INTEGER, 10, null, null, null, 0);
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        upgrade_plugin_savepoint(true, 2020100601, 'enrol', 'supervisor');
    }
    
    return true;
}
