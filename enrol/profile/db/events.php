<?php
/**
 *
 * @package    enrol
 * @subpackage profile
 * @author     Maria Tan(CLICK-AP)
 * @author     Martin Freeman(CLICK-AP)
 * @copyright  CLICK-AP {@link https://www.click-ap.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
$observers = array (

    array (
        'eventname' => '\core\event\attrigutes_user_loggedin',
        'callback'  => 'enrol_profile_handler::process_login',
        //'includefile' => '/enrol/profile/locallib.php'
    ),
);