<?php

/**
 * Session enrol plugin external functions and service definitions.
 *
 * @package   enrol_session
 * @copyright 2013 Rajesh Taneja <rajesh@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.6
 */

$functions = array(
    'enrol_session_get_instance_info' => array(
        'classname'   => 'enrol_session_external',
        'methodname'  => 'get_instance_info',
        'classpath'   => 'enrol/session/externallib.php',
        'description' => 'self enrolment instance information.',
        'type'        => 'read'
    )
);
