<?php
/**
 * Strings for component 'enrol_signin', language 'en'.
 * 
 * @package    enrol_signin
 * @copyright  2018 CLICK-AP {@link https://www.click-ap.com/}
 * @author     Wei Chi & Maria Tan
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['canntenrol'] = 'Enrolment is disabled or inactive';
$string['cohortnonmemberinfo'] = 'Only members of cohort \'{$a}\' can signin-enrol.';
$string['cohortonly'] = 'Only cohort members';
$string['cohortonly_help'] = 'signin enrolment may be restricted to members of a specified cohort only. Note that changing this setting has no effect on existing enrolments.';
$string['customwelcomemessage'] = 'Custom welcome message';
$string['customwelcomemessage_help'] = 'A custom welcome message may be added as plain text or Moodle-auto format, including HTML tags and multi-lang tags.

The following placeholders may be included in the message:

* Course name {$a->coursename}
* Link to user\'s profile page {$a->profileurl}';
$string['defaultrole'] = 'Default role assignment';
$string['defaultrole_desc'] = 'Select role which should be assigned to users during signin enrolment';
$string['enrolenddate'] = 'End date';
$string['enrolenddate_help'] = 'If enabled, users can enrol themselves until this date only.';
$string['enrolenddaterror'] = 'Enrolment end date cannot be earlier than start date';
$string['enrolme'] = 'Enrol me';
$string['enrolperiod'] = 'Enrolment duration';
$string['enrolperiod_desc'] = 'Default length of time that the enrolment is valid. If set to zero, the enrolment duration will be unlimited by default.';
$string['enrolperiod_help'] = 'Length of time that the enrolment is valid, starting with the moment the user enrols themselves. If disabled, the enrolment duration will be unlimited.';
$string['enrolstartdate'] = 'Start date';
$string['enrolstartdate_help'] = 'If enabled, users can enrol themselves from this date onward only.';
$string['expiredaction'] = 'Enrolment expiration action';
$string['expiredaction_help'] = 'Select action to carry out when user enrolment expires. Please note that some user data and settings are purged from course during course unenrolment.';
$string['expirymessageenrollersubject'] = 'signin enrolment expiry notification';
$string['expirymessageenrollerbody'] = 'signin enrolment in the course \'{$a->course}\' will expire within the next {$a->threshold} for the following users:

{$a->users}

To extend their enrolment, go to {$a->extendurl}';
$string['expirymessageenrolledsubject'] = 'signin enrolment expiry notification';
$string['expirymessageenrolledbody'] = 'Dear {$a->user},

This is a notification that your enrolment in the course \'{$a->course}\' is due to expire on {$a->timeend}.

If you need help, please contact {$a->enroller}.';
$string['groupkey'] = 'Use group enrolment keys';
$string['groupkey_desc'] = 'Use group enrolment keys by default.';
$string['groupkey_help'] = 'In addition to restricting access to the course to only those who know the key, use of group enrolment keys means users are automatically added to groups when they enrol in the course.

Note: An enrolment key for the course must be specified in the signin enrolment settings as well as group enrolment keys in the group settings.';
$string['longtimenosee'] = 'Unenrol inactive after';
$string['longtimenosee_help'] = 'If users haven\'t accessed a course for a long time, then they are automatically unenrolled. This parameter specifies that time limit.';
$string['maxenrolled'] = 'Max enrolled users';
$string['maxenrolled_help'] = 'Specifies the maximum number of users that can signin enrol. 0 means no limit.';
$string['maxenrolledreached'] = 'Maximum number of users allowed to signin-enrol was already reached.';
$string['messageprovider:expiry_notification'] = 'signin enrolment expiry notifications';
$string['newenrols'] = 'Allow new enrolments';
$string['newenrols_desc'] = 'Allow users to signin enrol into new courses by default.';
$string['newenrols_help'] = 'This setting determines whether a user can enrol into this course.';
$string['nopassword'] = 'No enrolment key required.';
$string['password'] = 'Enrolment key';
$string['password_help'] = 'An enrolment key enables access to the course to be restricted to only those who know the key.

If the field is left blank, any user may enrol in the course.

If an enrolment key is specified, any user attempting to enrol in the course will be required to supply the key. Note that a user only needs to supply the enrolment key ONCE, when they enrol in the course.';
$string['passwordinvalid'] = 'Incorrect enrolment key, please try again';
$string['passwordinvalidhint'] = 'That enrolment key was incorrect, please try again<br />
(Here\'s a hint - it starts with \'{$a}\')';
$string['pluginname'] = 'signin enrolment';
$string['pluginname_desc'] = 'The signin enrolment plugin allows users to choose which courses they want to participate in. The courses may be protected by an enrolment key. Internally the enrolment is done via the manual enrolment plugin which has to be enabled in the same course.';
$string['requirepassword'] = 'Require enrolment key';
$string['requirepassword_desc'] = 'Require enrolment key in new courses and prevent removing of enrolment key from existing courses.';
$string['role'] = 'Default assigned role';
$string['signin:config'] = 'Configure signin enrol instances';
$string['signin:manage'] = 'Manage enrolled users';
$string['signin:unenrol'] = 'Unenrol users from course';
$string['signin:unenrolself'] = 'Unenrol signin from the course';
$string['sendcoursewelcomemessage'] = 'Send course welcome message';
$string['sendcoursewelcomemessage_help'] = 'If enabled, users receive a welcome message via email when they signin-enrol in a course.';
$string['showhint'] = 'Show hint';
$string['showhint_desc'] = 'Show first letter of the guest access key.';
$string['status'] = 'Enable existing enrolments';
$string['status_desc'] = 'Enable signin enrolment method in new courses.';
$string['status_help'] = 'If disabled all existing signin enrolments are suspended and new users can not enrol.';
$string['unenrol'] = 'Unenrol user';
$string['unenrolselfconfirm'] = 'Do you really want to unenrol yoursignin from course "{$a}"?';
$string['unenroluser'] = 'Do you really want to unenrol "{$a->user}" from course "{$a->course}"?';
$string['usepasswordpolicy'] = 'Use password policy';
$string['usepasswordpolicy_desc'] = 'Use standard password policy for enrolment keys.';
$string['welcometocourse'] = 'Welcome to {$a}';
$string['welcometocoursetext'] = 'Welcome to {$a->coursename}!

If you have not done so already, you should edit your profile page so that we can learn more about you:

  {$a->profileurl}';
