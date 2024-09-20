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
 * Self enrol plugin installation script
 *
 * @package    enrol_creator
 * @copyright  2019 CLICK-AP  {@link https://www.click-ap.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

function xmldb_enrol_creator_install() {
    global $CFG, $DB;
    $dbman = $DB->get_manager();
    $table = new xmldb_table('enrol');
    
    set_config('requirepassword', 0, 'enrol_creator');
    set_config('usepasswordpolicy', 0, 'enrol_creator');
    set_config('showhint', 0, 'enrol_creator');
    set_config('groupkey', 0, 'enrol_creator');
    set_config('maxenrolled', 0, 'enrol_creator');
    
    //filter department
    $field = new xmldb_field('customint5_1', XMLDB_TYPE_TEXT, null, null, null, null, null);
    if (!$dbman->field_exists($table, $field)) {
        $dbman->add_field($table, $field);
    }
    //filter institution
    $field = new xmldb_field('customint5_2', XMLDB_TYPE_TEXT, null, null, null, null, null);
    if (!$dbman->field_exists($table, $field)) {
        $dbman->add_field($table, $field);
    }
    
    $field = new xmldb_field('unenrolenddate', XMLDB_TYPE_INTEGER, 10, null, null, null, 0);
    if (!$dbman->field_exists($table, $field)) {
        $dbman->add_field($table, $field);
    }
    
    //real student can enter the course start date
    $field = new xmldb_field('customint5_3', XMLDB_TYPE_INTEGER, 10, null, null, null, 0);
    if (!$dbman->field_exists($table, $field)) {
        $dbman->add_field($table, $field);
    }
}
