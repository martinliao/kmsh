<?php
/**
 * Language strings
 *
 * @package    local
 * @subpackage cwbadmin
 * @copyright  2014 Jack Liou <jack@click-ap.com>
 * @copyright  2014 Elaine Chen <elaine@click-ap.com>
 * @license    http://www.click-ap.com/copyleft/gpl.html GNU GPL v3 or later
 */
$string['pluginname'] = 'Program';

$string['programs:manageglobalsettings'] = 'Manage global settings';
$string['programs:viewprogram'] = 'View program';
$string['programs:manageownprogram'] = 'Manage own program';
$string['programs:viewotherprogram'] = 'View other program';
$string['programs:earnprogram'] = 'Earn program';
$string['programs:createprogram'] = 'Create program';
$string['programs:deleteprogram'] = 'Delete program';
$string['programs:configuredetails'] = 'Configure details';
$string['programs:configurecriteria'] = 'Configure criteria';
$string['programs:configuremessages'] = 'Configure messages';
$string['programs:awardprogram'] = 'Award program';
$string['programs:viewawarded'] = 'View awarded';

$string['activate'] = 'Enable access';
$string['activatesuccess'] = 'Access to the programs was successfully enabled.';
$string['addprogram'] = 'Add program';
$string['addcriteria'] = 'Add criteria';
$string['addprogramcriteria'] = 'Add program criteria';
$string['addcriteriatext'] = 'To start adding criteria, please select one of the options from the drop-down menu.';
$string['addcourse'] = 'Add courses';
$string['addcourse_help'] = 'Select all courses that should be added to this program requirement. Hold CTRL key to select multiple items.';
$string['after'] = 'after the date of issue.';
$string['all'] = 'All';
$string['any'] = 'Any';
$string['anymethodcourseset'] = 'Any of the selected courses is complete';
$string['archiveprogram'] = 'Would you like to delete program \'{$a}\', but keep existing issued programs?';
$string['archiveconfirm'] = 'Delete and keep existing issued programs';
$string['archivehelp'] = '<p>This option means that the program will be marked as "retired" and will no longer appear in the list of programs. Users will no longer be able to earn this program, however existing program recipients will still be able to display this program on their profile page and push it to their external backpacks.</p>
<p>If you would like your users to retain access to the earned programs it is important to select this option instead of fully deleting programs.</p>';

$string['allmethodcourseset'] = 'All of the selected courses are complete';
$string['awards'] = 'Awards';



$string['bcriteria'] = 'Criteria';
$string['boverview'] = 'Overview';
$string['bdetails'] = 'Edit details';
$string['bawards'] = 'Recipients ({$a})';
$string['bmessage'] = 'Message';
$string['bydate'] = ' complete by';

$string['copyof'] = 'Copy of {$a}';
$string['create'] = 'New program';
$string['createbutton'] = 'Create program';
$string['crontask'] = 'Program cron task';
$string['criteria_descr_bydate'] = ' by <em>{$a}</em> ';
$string['criteria_descr_grade'] = ' with minimum grade of <em>{$a}</em> ';
$string['criteria_0'] = 'This program is awarded when...';
$string['criteria_1'] = 'Activity completion';
$string['criteria_1_help'] = 'Allows a program to be awarded to users based on the completion of a set of activities within a course.';
$string['criteria_2'] = 'Manual issue by role';
$string['criteria_2_help'] = 'Allows a program to be awarded manually by users who have a particular role within the site or course.';
$string['criteria_3'] = 'Social participation';
$string['criteria_3_help'] = 'Social';
$string['criteria_4'] = 'Course completion';
$string['criteria_4_help'] = 'Allows a program to be awarded to users who have completed the course. This criterion can have additional parameters such as minimum grade and date of course completion.';
$string['criteria_5'] = 'Completing a set of courses';
$string['criteria_5_help'] = 'Allows a program to be awarded to users who have completed a set of courses. Each course can have additional parameters such as minimum grade and date of course completion. ';
$string['criteria_6'] = 'Profile completion';
$string['criteria_6_help'] = 'Allows a program to be awarded to users for completing certain fields in their profile. You can select from default and custom profile fields that are available to users. ';
$string['criteriacreated'] = 'Program criteria successfully created';
$string['criteriadeleted'] = 'Program criteria successfully deleted';
$string['criteriaupdated'] = 'Program criteria successfully updated';
$string['criteria_descr'] = 'Users are awarded this program when they complete the following requirement:';
$string['criteria_descr_0'] = 'Users are awarded this program when they complete <strong>{$a}</strong> of the listed requirements.';
$string['criteria_descr_1'] = '<strong>{$a}</strong> of the following activities are completed:';
$string['criteria_descr_2'] = 'This program has to be awarded by the users with <strong>{$a}</strong> of the following roles:';
$string['criteria_descr_4'] = 'Users must complete the course';
$string['criteria_descr_5'] = '<strong>{$a}</strong> of the following courses have to be completed:';
$string['criteria_descr_6'] = '<strong>{$a}</strong> of the following user profile fields have to be completed:';

$string['criteria_descr_short0'] = 'Complete <strong>{$a}</strong> of: ';
$string['criteria_descr_short1'] = 'Complete <strong>{$a}</strong> of: ';
$string['criteria_descr_short2'] = 'Awarded by <strong>{$a}</strong> of: ';
$string['criteria_descr_short4'] = 'Complete the course ';
$string['criteria_descr_short5'] = 'Complete <strong>{$a}</strong> of: ';
$string['criteria_descr_short6'] = 'Complete <strong>{$a}</strong> of: ';$string['criteria_descr_single_1'] = 'The following activity has to be completed:';
$string['criteria_descr_single_2'] = 'This program has to be awarded by a user with the following role:';
$string['criteria_descr_single_4'] = 'Users must complete the course';
$string['criteria_descr_single_5'] = 'The following course has to be completed:';
$string['criteria_descr_single_6'] = 'The following user profile field has to be completed:';

$string['criteria_descr_single_short1'] = 'Complete: ';
$string['criteria_descr_single_short2'] = 'Awarded by: ';
$string['criteria_descr_single_short4'] = 'Complete the course ';
$string['criteria_descr_single_short5'] = 'Complete: ';
$string['criteria_descr_single_short6'] = 'Complete: ';



$string['deactivate'] = 'Disable access';
$string['deactivatesuccess'] = 'Access to the program was successfully disabled.';
$string['delprogram'] = 'Would you like to delete program \'{$a}\' and remove all existing issued programs?';
$string['delconfirm'] = 'Delete and remove existing issued programs';
$string['deletehelp'] = '<p>Fully deleting a program means that all its information and criteria records will be permanently removed. Users who have earned this program will no longer be able to access it and display it on their profile pages.</p>
<p>Note: Users who have earned this program and have already pushed it to their external backpack, will still have this program in their external backpack. However, they will not be able to access criteria and evidence pages linking back to this web site.</p>';

$string['description'] = 'Description';

$string['error:invalidexpireperiod'] = 'Expiry period cannot be negative or equal 0.';
$string['error:duplicatename'] = 'Program with such name already exists in the system.';
$string['error:invalidexpiredate'] = 'Expiry date has to be in the future.';
$string['error:nosuchcourse'] = 'Warning: This course is no longer available.';
$string['error:nocourses'] = 'Course completion is not enabled for any of the courses in this site, so none can be displayed. Course completion may be enabled in the course settings.';
$string['error:parameter'] = 'Warning: At least one parameter should be selected to ensure correct program issuing workflow.';

$string['expirydate'] = 'Expiry date';
$string['expirydate_help'] = 'Optionally, programs can expire on a specific date, or the date can be calculated based on the date when the program was issued to a user. ';

$string['fixed'] = 'Fixed date';


$string['manageprograms'] = 'Manage program';
$string['method'] = 'This criterion is complete when...';
$string['messagesubject'] = 'Congratulations! You just earned a program!';
$string['messagebody'] = '<p>You have been awarded the program "%programname%"!</p>
<p>More information about this program can be found on the %programlink% program information page.</p>';
$string['mingrade'] = 'Minimum grade required';

$string['newprogram'] = 'Create Program';
$string['never'] = 'Never';
$string['nocourses'] = 'There are no courses available.';
$string['noprograms'] = 'There are no programs available.';
$string['noparamstoadd'] = 'There are no additional parameters available to add to this program requirement.';
$string['nocriteria'] = 'Criteria for this program have not been set up yet.';
$string['nothingtoadd'] = 'There are no available criteria to add.';

$string['issuancedetails'] = 'Program expiry';
$string['program'] = 'Program';
$string['program_help'] = 'Program can only be awarded to users for site-related activities. These include completing a set of courses or parts of user profiles. Program can also be issued manually by one user to another.';
$string['programbanner'] = 'Banner';
$string['programaward'] = 'Award';
$string['programdetails'] = 'Program details';
$string['programsettings'] = 'Program settings';
$string['programstoearn'] = 'Number of programs available: {$a}';
$string['programimage'] = 'Image';
$string['programimage_help'] = 'This is an image that will be used when this program is issued.

To add a new image, browse and select an image (in JPG or PNG format) then click "Save changes". The image will be cropped to a square and resized to match program image requirements. ';
$string['programtitle'] = 'Porgram Name : ';
$string['programstatus_0'] = 'Not available to users';
$string['programstatus_1'] = 'Available to users';
$string['programstatus_2'] = 'Not available to users';
$string['programstatus_3'] = 'Available to users';
$string['programstatus_4'] = 'Archived';

$string['relative'] = 'Relative date';
$string['requiredcourse'] = 'At least one course should be added to the courseset criterion.';
$string['reviewconfirm'] = '<p>This will make your program visible to users and allow them to start earning it.</p>

<p>It is possible that some users already meet this program\'s criteria and will be issued this program immediately after you enable it.</p>

<p>Once a program has been issued it will be <strong>locked</strong> - certain settings including the criteria and expiry settings can no longer be changed.</p>

<p>Are you sure you want to enable access to the program \'{$a}\'?</p>';
$string['reviewprogram'] = 'Changes in program access';

$string['save'] = 'Save';
$string['status'] = 'Program status';
$string['status_help'] = 'Status of a program determines its behaviour in the system:

* **AVAILABLE** – Means that this program can be earned by users. While a program is available to users, its criteria cannot be modified.

* **NOT AVAILABLE** – Means that this program is not available to users and cannot be earned or manually issued. If such program has never been issued before, its criteria can be changed.

Once a program has been issued to at least one user, it automatically becomes **LOCKED**. Locked programs can still be earned by users, but their criteria can no longer be changed. If you need to modify details or criteria of a locked program, you can duplicate this program and make all the required changes.

*Why do we lock programs?*

We want to make sure that all users complete the same requirements to earn a program. Currently, it is not possible to revoke programs. If we allowed programs requirements to be modified all the time, we would most likely end up with users having the same program for meeting completely different requirements.';
$string['statusmessage_0'] = 'This program is currently not available to users. Enable access if you want users to earn this program. ';
$string['statusmessage_1'] = 'This program is currently available to users. Disable access to make any changes. ';
$string['statusmessage_2'] = 'This program is currently not available to users, and its criteria are locked. Enable access if you want users to earn this program. ';
$string['statusmessage_3'] = 'This program is currently available to users, and its criteria are locked. ';
$string['statusmessage_4'] = 'This program is currently archived.';

$string['eventprogramawarded'] = 'Program awarded';
$string['subject'] = 'Message subject';
$string['message'] = 'Message';
$string['notification'] = 'Notify program creator';
$string['notification_help'] = 'This setting manages notifications sent to a program creator to let them know that the program has been issued.

The following options are available:

* **NEVER** – Do not send notifications.

* **EVERY TIME** – Send a notification every time this program is awarded.

* **DAILY** – Send notifications once a day.

* **WEEKLY** – Send notifications once a week.

* **MONTHLY** – Send notifications once a month.';
$string['notifydaily'] = 'Daily';
$string['notifyevery'] = 'Every time';
$string['notifymonthly'] = 'Monthly';
$string['notifyweekly'] = 'Weekly';
$string['configuremessage'] = 'Program message';
$string['variablesubstitution'] = 'Variable substitution in messages.';
$string['variablesubstitution_help'] = 'In a program message, certain variables can be inserted into the subject and/or body of a message so that they will be replaced with real values when the message is sent. The variables should be inserted into the text exactly as they are shown below. The following variables can be used:

%programname%
: This will be replaced by the program\'s full name.

%username%
: This will be replaced by the recipient\'s full name.

%programlink%
: This will be replaced by the public URL with information about the issued program.';

$string['attachment'] = 'Attach program to message';
$string['attachment_help'] = 'If enabled, an issued program file will be attached to the recipient\'s email for download. (Attachments must be enabled in Site administration > Plugins > Message outputs > Email to use this option.)';
$string['noawards'] = 'This program has not been earned yet.';
$string['dateawarded'] = 'Date issued';
$string['viewprogram'] = 'View issued badge';
$string['creatorbody'] = '<p>{$a->user} has completed all badge requirements and has been awarded the badge. View issued badge at {$a->link} </p>';
$string['creatorsubject'] = '\'{$a}\' has been awarded!';

$string['expiredate'] = 'This program expires on {$a}.';
$string['expireperiod'] = 'This program expires {$a} day(s) after being issued.';
$string['expireperiodh'] = 'This program expires {$a} hour(s) after being issued.';
$string['expireperiods'] = 'This program expires {$a} second(s) after being issued.';
$string['expireperiodm'] = 'This program expires {$a} minute(s) after being issued.';
$string['noexpiry'] = 'This program does not have an expiry date.';

$string['strftimedate_0'] = '中&nbsp;華&nbsp;民&nbsp;國';
$string['strftimedate_1'] = '西&nbsp;&nbsp;&nbsp;&nbsp;元';
$string['strftimedate_y'] = '年';
$string['strftimedate_m'] = '月';
$string['strftimedate_d'] = '日';

$string['download'] = 'Download';
$string['programname'] = 'Program Name';
$string['awarddate'] = 'Award Date';
$string['borderstyle'] = 'Border Image';
$string['notcategorised'] = 'Not categorised';
$string['program_category'] = 'Category';
$string['delcritconfirm'] = 'Are you sure that you want to delete this criterion?';
$string['degreeofcompletion'] = 'Degree of completion';
$string['unfinished'] = 'Unfinished';