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
 * Empty enrol_waiting form.
 *
 * Useful to mimic valid enrol instances UI when the enrolment instance is not available.
 *
 * @package    enrol_waiting
 * @copyright  2020 CLICK-AP  {@link https://www.click-ap.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/formslib.php');

class enrol_waiting_empty_form extends moodleform {

    /**
     * Form definition.
     * @return void
     */
    public function definition() {
        $this->_form->addElement('header', 'selfheader', $this->_customdata->header);
        $this->_form->addElement('static', 'info', '', $this->_customdata->info);
        //$this->_form->addElement('static', 'lab1', '', get_string('enrolme2', 'enrol_waiting', $this->_customdata->header));
        //$this->_form->addElement('static', 'lab2', '', get_string('enrolme2', 'enrol_waiting', $this->_customdata->header).$this->_customdata->info);
        
    }
}
