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

$string['pluginname'] = 'Profile enrolments';
$string['defaultrole'] = 'Default role';
$string['defaultrole_desc'] = 'Default role used to enrol people with this plugin (each instance can override this).';
$string['attrsyntax'] = 'User profile fields rules';
$string['attrsyntax_help'] = '<p>These rules can only use custom user profile fields.</p>';
$string['profile:config'] = 'Configure plugin instances';
$string['profile:manage'] = 'Manage enrolled users';
$string['profile:unenrol'] = 'Unenrol users from the course';
$string['profile:unenrolself'] = 'Unenrol self from the course';
$string['ajax-error'] = 'An error occured';
$string['ajax-okpurged'] = 'OK, enrolments have been purged';
$string['ajax-okforced'] = 'OK, {$a} users have benn enrolled';
$string['purge'] = 'Purge enrolments';
$string['force'] = 'Force enrolments now';
$string['confirmforce'] = 'This will (re)enrol all users corresponding to this rule.';
$string['confirmpurge'] = 'This will remove all enrolments corresponding to this rule.';
$string['mappings'] = 'Shibboleth mappings';
$string['mappings_desc'] = 'When using Shibboleth authentification, this plugin can automatically update a user\'s profile upon each login.<br><br>For instance, if you want to update the user\'s <code>homeorganizationtype</code> profile field with the Shibboleth attribute <code>Shib-HomeOrganizationType</code> (provided that is the environment variable available to the server during login), you can enter on one line: <code>Shib-HomeOrganizationType:homeorganizationtype</code><br>You may enter as many lines as needed.<br><br>To not use this feature or if you don\'t use Shibboleth authentification, simple leave this empty.';
$string['profilefields'] = 'Profile fields to be used in the selector';
$string['profilefields_desc'] = 'Which user profile fields can be used when configuring an enrolment instance?<br><br><b>If you don\'t select any role here, this makes the plugin moot and hence disables its use in courses.</b><br>The feature below may however still be used in this case.';
$string['removewhenexpired'] = 'Unenrol after profile expiration';
$string['removewhenexpired_help'] = 'Unenrol users upon login if they don\'t match the attribute rule anymore.';

$string['sendcoursewelcomemessage'] = 'Send course welcome message';
$string['sendcoursewelcomemessage_help'] = 'If enabled, users receive a welcome message via email when they profile-enrol in a course.';
$string['welcometocourse'] = 'Welcome to {$a}';
$string['welcometocoursetext'] = 'Welcome to  {$a->coursename}

You can enter course to learning:

  {$a->courseurl}';
$string['customwelcomemessage'] = 'Custom welcome message';
$string['customwelcomemessage_help'] = 'A custom welcome message may be added as plain text or Moodle-auto format, including HTML tags and multi-lang tags.

The following placeholders may be included in the message:

* Course name {$a->coursename}
* Link to user\'s profile page {$a->profileurl}';
$string['addcondition'] = "Add condition";
$string['addgroup'] = "Add group";
$string['deletecondition'] = "Delete condition";
$string['anyrule'] = "All user";
$string['notice'] = "Notice";
$string['notice_explain'] = "<p>After adding or changing the user profile fields rules, please press the 'Save changes' button to save the settings.</p>
<p>And return to this page, press the 'Force enrolments now' to add the users to the course participation list.</p>
<p>If you want to remove the unqualified users from the course, please tick 'Unenrol after profile expiration' , or press the 'Clear Electives'; after finishing press the 'Force enrolments now' button to re-engage add the users.</p>";