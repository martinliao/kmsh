<?php

/**
 * Session enrol external PHPunit tests
 *
 * @package   enrol_session
 * @copyright 2013 Rajesh Taneja <rajesh@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.6
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/webservice/tests/helpers.php');
require_once($CFG->dirroot . '/enrol/session/externallib.php');

class enrol_session_external_testcase extends externallib_advanced_testcase {

    /**
     * Test get_instance_info
     */
    public function test_get_instance_info() {
        global $DB;

        $this->resetAfterTest(true);

        // Check if self enrolment plugin is enabled.
        $selfplugin = enrol_get_plugin('session');
        $this->assertNotEmpty($selfplugin);

        $studentrole = $DB->get_record('role', array('shortname'=>'student'));
        $this->assertNotEmpty($studentrole);

        $course = self::getDataGenerator()->create_course();

        // Add enrolment methods for course.
        $instanceid1 = $selfplugin->add_instance($course, array('status' => ENROL_INSTANCE_ENABLED,
                                                                'name' => 'Test instance 1',
                                                                'customint6' => 1,
                                                                'roleid' => $studentrole->id));
        $instanceid2 = $selfplugin->add_instance($course, array('status' => ENROL_INSTANCE_DISABLED,
                                                                'customint6' => 1,
                                                                'name' => 'Test instance 2',
                                                                'roleid' => $studentrole->id));

        $instanceid3 = $selfplugin->add_instance($course, array('status' => ENROL_INSTANCE_ENABLED,
                                                                'roleid' => $studentrole->id,
                                                                'customint6' => 1,
                                                                'name' => 'Test instance 3',
                                                                'password' => 'test'));

        $enrolmentmethods = $DB->get_records('enrol', array('courseid' => $course->id, 'status' => ENROL_INSTANCE_ENABLED));
        $this->assertCount(3, $enrolmentmethods);

        $instanceinfo1 = enrol_session_external::get_instance_info($instanceid1);
        $instanceinfo1 = external_api::clean_returnvalue(enrol_session_external::get_instance_info_returns(), $instanceinfo1);

        $this->assertEquals($instanceid1, $instanceinfo1['id']);
        $this->assertEquals($course->id, $instanceinfo1['courseid']);
        $this->assertEquals('self', $instanceinfo1['type']);
        $this->assertEquals('Test instance 1', $instanceinfo1['name']);
        $this->assertTrue($instanceinfo1['status']);
        $this->assertFalse(isset($instanceinfo1['enrolpassword']));

        $instanceinfo2 = enrol_session_external::get_instance_info($instanceid2);
        $instanceinfo2 = external_api::clean_returnvalue(enrol_session_external::get_instance_info_returns(), $instanceinfo2);
        $this->assertEquals($instanceid2, $instanceinfo2['id']);
        $this->assertEquals($course->id, $instanceinfo2['courseid']);
        $this->assertEquals('self', $instanceinfo2['type']);
        $this->assertEquals('Test instance 2', $instanceinfo2['name']);
        $this->assertEquals(get_string('canntenrol', 'enrol_session'), $instanceinfo2['status']);
        $this->assertFalse(isset($instanceinfo2['enrolpassword']));

        $instanceinfo3 = enrol_session_external::get_instance_info($instanceid3);
        $instanceinfo3 = external_api::clean_returnvalue(enrol_session_external::get_instance_info_returns(), $instanceinfo3);
        $this->assertEquals($instanceid3, $instanceinfo3['id']);
        $this->assertEquals($course->id, $instanceinfo3['courseid']);
        $this->assertEquals('self', $instanceinfo3['type']);
        $this->assertEquals('Test instance 3', $instanceinfo3['name']);
        $this->assertTrue($instanceinfo3['status']);
        $this->assertEquals(get_string('password', 'enrol_session'), $instanceinfo3['enrolpassword']);
    }
}
