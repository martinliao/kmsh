<?php
// This file is part of the MRBS block for Moodle
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

defined('MOODLE_INTERNAL') || die();
global $CFG;

// The following couple of lines stop a warning message when setting up PHPUnit.
if (!isset($CFG->supportname)) {
    $CFG->supportname = '';
}
if (!isset($CFG->supportemail)) {
    $CFG->supportemail = '';
}

$cfg_mrbs = get_config('block_mrbs');

$options = array(0 => get_string('pagewindow', 'block_mrbs'), 1 => get_string('newwindow', 'block_mrbs'));
$settings->add(new admin_setting_configselect('block_mrbs/newwindow', get_string('config_new_window', 'block_mrbs'), get_string('config_new_window2', 'block_mrbs'), 0, $options));

//$settings->add(new admin_setting_configtext('block_mrbs/serverpath', get_string('serverpath', 'block_mrbs'),
//                                            get_string('adminview', 'block_mrbs'), $CFG->wwwroot.'/blocks/mrbs/web', PARAM_URL));
set_config('serverpath', $CFG->wwwroot.'/blocks/mrbs/web', 'block_mrbs');

$settings->add(new admin_setting_configtext('block_mrbs/admin', get_string('config_admin', 'block_mrbs'), get_string('config_admin2', 'block_mrbs'), $CFG->supportname, PARAM_TEXT));
$settings->add(new admin_setting_configtext('block_mrbs/admin_email', get_string('config_admin_email', 'block_mrbs'), get_string('config_admin_email2', 'block_mrbs'), $CFG->supportemail, PARAM_TEXT));

$options = array(0 => get_string('no'), 1 => get_string('yes'));
$settings->add(new admin_setting_configselect('block_mrbs/enable_periods', get_string('config_enable_periods', 'block_mrbs'), get_string('config_enable_periods2', 'block_mrbs'), 1, $options));
if (isset($cfg_mrbs->enable_periods)) {
    if ($cfg_mrbs->enable_periods == 0) {

        // Resolution

        unset($options);
        $strunits = get_string('resolution_units', 'block_mrbs');
        $options = array(
            '900' => '15'.$strunits, '1800' => '30'.$strunits, '2700' => '45'.$strunits, '3600' => '60'.$strunits,
            '4500' => '75'.$strunits, '5400' => '90'.$strunits, '6300' => '105'.$strunits, '7200' => '120'.$strunits
        );
        $settings->add(new admin_setting_configselect('block_mrbs/resolution', get_string('config_resolution', 'block_mrbs'), get_string('config_resolution2', 'block_mrbs'), '3600', $options));

        // Start Time (Hours)
        unset($options);
        $options = array(
            1 => '01', 2 => '02', 3 => '03', 4 => '04', 5 => '05', 6 => '06', 7 => '07', 8 => '08', 9 => '09', 10 => '10',
            11 => '11', 12 => '12', 13 => '13', 14 => '14', 15 => '15', 16 => '16', 17 => '17', 18 => '18', 19 => '19', 20 => '20',
            21 => '21', 22 => '22', 23 => '23'
        );
        $settings->add(new admin_setting_configselect('block_mrbs/morningstarts', get_string('config_morningstarts', 'block_mrbs'), get_string('config_morningstarts2', 'block_mrbs'), 8, $options));

        // Start Time (Min)
        unset($options);
        $options = array(
            0 => '00', 5 => '05', 10 => '10', 15 => '15', 20 => '20', 25 => '25', 30 => '30', 35 => '35', 40 => '40', 45 => '45',
            50 => '50', 55 => '55'
        );
        $settings->add(new admin_setting_configselect('block_mrbs/morningstarts_min', get_string('config_morningstarts_min', 'block_mrbs'), get_string('config_morningstarts_min2', 'block_mrbs'), 0, $options));

        // End Time (Hours)
        unset($options);
        $options = array(
            1 => '01', 2 => '02', 3 => '03', 4 => '04', 5 => '05', 6 => '06', 7 => '07', 8 => '08', 9 => '09', 10 => '10',
            11 => '11', 12 => '12', 13 => '13', 14 => '14', 15 => '15', 16 => '16', 17 => '17', 18 => '18', 19 => '19', 20 => '20',
            21 => '21', 22 => '22', 23 => '23'
        );
        $settings->add(new admin_setting_configselect('block_mrbs/eveningends', get_string('config_eveningends', 'block_mrbs'), get_string('config_eveningends2', 'block_mrbs'), 19, $options));

        // End Time Time (Min)
        unset($options);
        $options = array(
            0 => '00', 5 => '05', 10 => '10', 15 => '15', 20 => '20', 25 => '25', 30 => '30', 35 => '35', 40 => '40', 45 => '45',
            50 => '50', 55 => '55'
        );
        $settings->add(new admin_setting_configselect('block_mrbs/eveningends_min', get_string('config_eveningends_min', 'block_mrbs'), get_string('config_eveningends_min2', 'block_mrbs'), 0, $options));

    } else {  //Use Custom Periods
        $default = "08:00\n09:00\n10:00\n11:00\n12:00\n13:00\n14:00\n15:00\n16:00\n17:00\n18:00\n19:00";
        $settings->add(new admin_setting_configtextarea('block_mrbs/periods', get_string('config_periods', 'block_mrbs'), get_string('config_periods2', 'block_mrbs'), $default));
    }
}

// Date Information

//Start of Week
unset($options);
$options = array(
    0 => get_string('sunday', 'calendar'), 1 => get_string('monday', 'calendar'), 2 => get_string('tuesday', 'calendar'),
    3 => get_string('wednesday', 'calendar'), 4 => get_string('thursday', 'calendar'), 5 => get_string('friday', 'calendar'),
    6 => get_string('saturday', 'calendar')
);
$settings->add(new admin_setting_configselect('block_mrbs/weekstarts', get_string('config_weekstarts', 'block_mrbs'), get_string('config_weekstarts2', 'block_mrbs'), 0, $options));

//Length of week
$settings->add(new admin_setting_configtext('block_mrbs/weeklength', get_string('config_weeklength', 'block_mrbs'), get_string('config_weeklength2', 'block_mrbs'), 7, PARAM_INT));

//Date Format
unset($options);
$options = array(0 => get_string('config_date_mmddyy', 'block_mrbs'), 1 => get_string('config_date_ddmmyy', 'block_mrbs'));
$settings->add(new admin_setting_configselect('block_mrbs/dateformat', get_string('config_dateformat', 'block_mrbs'), get_string('config_dateformat2', 'block_mrbs'), 0, $options));

//Time format
unset($options);
$options = array(0 => get_string('timeformat_12', 'calendar'), 1 => get_string('timeformat_24', 'calendar'));
$settings->add(new admin_setting_configselect('block_mrbs/timeformat', get_string('config_timeformat', 'block_mrbs'), get_string('config_timeformat2', 'block_mrbs'), 1, $options));

// $settings = new admin_settingpage('block_mrbs_misc', get_string('block_mrbs_misc','block_mrbs')); // it would be good to be able to break this page up somehow
// Misc Settings
$settings->add(new admin_setting_configtext('block_mrbs/max_rep_entrys', get_string('config_max_rep_entrys', 'block_mrbs'), get_string('config_max_rep_entrys2', 'block_mrbs'), 365, PARAM_INT));

$settings->add(new admin_setting_configtext('block_mrbs/max_advance_days', get_string('config_max_advance_days', 'block_mrbs'), get_string('config_max_advance_days2', 'block_mrbs'), -1, PARAM_INT));

$settings->add(new admin_setting_configtext('block_mrbs/default_report_days', get_string('config_default_report_days', 'block_mrbs'), get_string('config_default_report_days2', 'block_mrbs'), 60, PARAM_INT));

$settings->add(new admin_setting_configtext('block_mrbs/search_count', get_string('config_search_count', 'block_mrbs'), get_string('config_search_count2', 'block_mrbs'), 20, PARAM_INT));

/*
$settings->add(new admin_setting_configtext('block_mrbs/refresh_rate', get_string('config_refresh_rate', 'block_mrbs'), get_string('config_refresh_rate2', 'block_mrbs'), 0, PARAM_INT));
*/

$options = array('list' => get_string('list'), 'select' => get_string('select'));
$settings->add(new admin_setting_configselect('block_mrbs/area_list_format', get_string('config_area_list_format', 'block_mrbs'), get_string('config_area_list_format2', 'block_mrbs'), 'list', $options));

$options = array(
    'both' => get_string('both', 'block_mrbs'), 'description' => get_string('description'),
    'slot' => get_string('slot', 'block_mrbs')
);
$settings->add(new admin_setting_configselect('block_mrbs/monthly_view_entries_details', get_string('config_monthly_view_entries_details', 'block_mrbs'), get_string('config_monthly_view_entries_details2', 'block_mrbs'), 'both', $options));

$options = array(0 => get_string('no'), 1 => get_string('yes'));
$settings->add(new admin_setting_configselect('block_mrbs/view_week_number', get_string('config_view_week_number', 'block_mrbs'), get_string('config_view_week_number2', 'block_mrbs'), 0, $options));

$options = array(0 => get_string('no'), 1 => get_string('yes'));
$settings->add(new admin_setting_configselect('block_mrbs/times_right_side', get_string('config_times_right_side', 'block_mrbs'), get_string('config_times_right_side2', 'block_mrbs'), 0, $options));

$options = array(0 => get_string('no'), 1 => get_string('yes'));
$settings->add(new admin_setting_configselect('block_mrbs/javascript_cursor', get_string('config_javascript_cursor', 'block_mrbs'), get_string('config_javascript_cursor2', 'block_mrbs'), 1, $options));

$options = array(0 => get_string('no'), 1 => get_string('yes'));
$settings->add(new admin_setting_configselect('block_mrbs/show_plus_link', get_string('config_show_plus_link', 'block_mrbs'), get_string('config_show_plus_link2', 'block_mrbs'), 1, $options));

$options = array(
    'bgcolor' => get_string('bgcolor', 'block_mrbs'), 'class' => get_string('class', 'block_mrbs'),
    'hybrid' => get_string('hybrid', 'block_mrbs')
);
$settings->add(new admin_setting_configselect('block_mrbs/highlight_method', get_string('config_highlight_method', 'block_mrbs'), get_string('config_highlight_method2', 'block_mrbs'), 'hybrid', $options));

$options = array('day' => get_string('day'), 'month' => get_string('month', 'block_mrbs'), 'week' => get_string('week'));
$settings->add(new admin_setting_configselect('block_mrbs/default_view', get_string('config_default_view', 'block_mrbs'), get_string('config_default_view2', 'block_mrbs'), 'week', $options));

$settings->add(new admin_setting_configtext('block_mrbs/default_room', get_string('config_default_room', 'block_mrbs'), get_string('config_default_room2', 'block_mrbs'), 0, PARAM_INT));

// should this be the same as the Moodle Site cookie path?
// $settings->add(new admin_setting_configtext('cookie_path_override', get_string('config_cookie_path_override', 'block_mrbs'), get_string('config_cookie_path_override2', 'block_mrbs'), '', PARAM_LOCALURL));
// $settings->settings->cookie_path_override->plugin='block/mrbs';

/*

//select
$options = array('' => get_string('', 'block_mrbs'), '' => get_string('', 'block_mrbs'));
$settings->add(new admin_setting_configselect('', get_string('config_', 'block_mrbs'), get_string('config_2', 'block_mrbs'), '', $options));
$settings->settings->->plugin='block/mrbs';

//text or int
$settings->add(new admin_setting_configtext('', get_string('config_', 'block_mrbs'), get_string('config_2', 'block_mrbs'), 0, PARAM_INT));
$settings->settings->->plugin='block/mrbs';
*/

$settings->add(new admin_setting_configtext('block_mrbs/entry_type_a', get_string('config_entry_type', 'block_mrbs', 'A'), get_string('config_entry_type2', 'block_mrbs', 'A'), null, PARAM_TEXT));
$settings->add(new admin_setting_configtext('block_mrbs/entry_type_b', get_string('config_entry_type', 'block_mrbs', 'B'), get_string('config_entry_type2', 'block_mrbs', 'B'), null, PARAM_TEXT));
$settings->add(new admin_setting_configtext('block_mrbs/entry_type_c', get_string('config_entry_type', 'block_mrbs', 'C'), get_string('config_entry_type2', 'block_mrbs', 'C'), null, PARAM_TEXT));
$settings->add(new admin_setting_configtext('block_mrbs/entry_type_d', get_string('config_entry_type', 'block_mrbs', 'D'), get_string('config_entry_type2', 'block_mrbs', 'D'), null, PARAM_TEXT));
$settings->add(new admin_setting_configtext('block_mrbs/entry_type_e', get_string('config_entry_type', 'block_mrbs', 'E'), get_string('config_entry_type2', 'block_mrbs', 'E'), get_string('external', 'block_mrbs'), PARAM_TEXT));
$settings->add(new admin_setting_configtext('block_mrbs/entry_type_f', get_string('config_entry_type', 'block_mrbs', 'F'), get_string('config_entry_type2', 'block_mrbs', 'F'), null, PARAM_TEXT));
$settings->add(new admin_setting_configtext('block_mrbs/entry_type_g', get_string('config_entry_type', 'block_mrbs', 'G'), get_string('config_entry_type2', 'block_mrbs', 'G'), null, PARAM_TEXT));
$settings->add(new admin_setting_configtext('block_mrbs/entry_type_h', get_string('config_entry_type', 'block_mrbs', 'H'), get_string('config_entry_type2', 'block_mrbs', 'H'), null, PARAM_TEXT));
$settings->add(new admin_setting_configtext('block_mrbs/entry_type_i', get_string('config_entry_type', 'block_mrbs', 'I'), get_string('config_entry_type2', 'block_mrbs', 'I'), get_string('internal', 'block_mrbs'), PARAM_TEXT));
$settings->add(new admin_setting_configtext('block_mrbs/entry_type_j', get_string('config_entry_type', 'block_mrbs', 'J'), get_string('config_entry_type2', 'block_mrbs', 'J'), null, PARAM_TEXT));
set_config('entry_type_c', '', 'block_mrbs');
set_config('entry_type_d', '', 'block_mrbs');
set_config('entry_type_f', '', 'block_mrbs');
set_config('entry_type_g', '', 'block_mrbs');
set_config('entry_type_h', '', 'block_mrbs');
set_config('entry_type_j', '', 'block_mrbs');

$options = array('0' => get_string('no'), '1' => get_string('yes'));
$settings->add(new admin_setting_configselect('block_mrbs/mail_admin_on_bookings', get_string('config_mail_admin_on_bookings', 'block_mrbs'), get_string('config_mail_admin_on_bookings2', 'block_mrbs'), '0', $options));

$options = array('0' => get_string('no'), '1' => get_string('yes'));
$settings->add(new admin_setting_configselect('block_mrbs/mail_area_admin_on_bookings', get_string('config_mail_area_admin_on_bookings', 'block_mrbs'), get_string('config_mail_area_admin_on_bookings2', 'block_mrbs'), 0, $options));

$options = array('0' => get_string('no'), '1' => get_string('yes'));
$settings->add(new admin_setting_configselect('block_mrbs/mail_room_admin_on_bookings', get_string('config_mail_room_admin_on_bookings', 'block_mrbs'), get_string('config_mail_room_admin_on_bookings2', 'block_mrbs'), 0, $options));

$options = array('0' => get_string('no'), '1' => get_string('yes'));
$settings->add(new admin_setting_configselect('block_mrbs/mail_admin_on_delete', get_string('config_mail_admin_on_delete', 'block_mrbs'), get_string('config_mail_admin_on_delete2', 'block_mrbs'), 0, $options));

$options = array('0' => get_string('no'), '1' => get_string('yes'));
$settings->add(new admin_setting_configselect('block_mrbs/mail_admin_all', get_string('config_mail_admin_all', 'block_mrbs'), get_string('config_mail_admin_all2', 'block_mrbs'), 0, $options));

$options = array('0' => get_string('no'), '1' => get_string('yes'));
$settings->add(new admin_setting_configselect('block_mrbs/mail_details', get_string('config_mail_details', 'block_mrbs'), get_string('config_mail_details2', 'block_mrbs'), 0, $options));

$options = array('0' => get_string('no'), '1' => get_string('yes'));
$settings->add(new admin_setting_configselect('block_mrbs/mail_booker', get_string('config_mail_booker', 'block_mrbs'), get_string('config_mail_booker2', 'block_mrbs'), 0, $options));

//$settings->add(new admin_setting_configtext('block_mrbs/mail_from', get_string('config_mail_from', 'block_mrbs'), get_string('config_mail_from2', 'block_mrbs'), $CFG->supportemail, PARAM_TEXT));
set_config('mail_from', $CFG->supportemail, 'block_mrbs');

$settings->add(new admin_setting_configtext('block_mrbs/mail_recipients', get_string('config_mail_recipients', 'block_mrbs'), get_string('config_mail_recipients2', 'block_mrbs'), $CFG->supportemail, PARAM_TEXT));

$settings->add(new admin_setting_configtext('block_mrbs/mail_cc', get_string('config_mail_cc', 'block_mrbs'), get_string('config_mail_cc2', 'block_mrbs'), null, PARAM_TEXT));

//$settings->add(new admin_setting_configtext('block_mrbs/cronfile', get_string('cronfile', 'block_mrbs'), get_string('cronfiledesc', 'block_mrbs'), null, PARAM_TEXT));
set_config('cronfile', '', 'block_mrbs');