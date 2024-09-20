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
 * Provides an overview of installed admin tools
 *
 * Displays the list of found admin tools, their version (if found) and
 * a link to uninstall the admin tool.
 *
 * The code is based on admin/localplugins.php by David Mudrak.
 *
 * @package   admin
 * @copyright 2017 Click-AP {@link https://www.click-ap.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->libdir.'/tablelib.php');

$PAGE->set_url('/admin/clickap.php');
$context = context_system::instance();
$PAGE->set_context($context);

require_login();
require_capability('moodle/site:config', $context);
admin_externalpage_setup('manageclickap');
$PAGE->set_pagetype('admin-setting-clickap');
$PAGE->set_pagelayout('admin');
$PAGE->set_title('clickap');
$PAGE->navigation->clear_cache();

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('clickap', 'clickap_base'));

/// Print the table of all installed tool plugins
$strsettings = get_string("settings");
$struninstall = get_string('uninstallplugin', 'core_admin');

$table = new flexible_table('clickapplugins_administration_table');
$table->define_columns(array('name', 'version', 'settings', 'uninstall'));
$table->define_headers(array(get_string('plugin'), get_string('version'), $strsettings, $struninstall));
//$table->define_headers(array(get_string('plugin'), get_string('version'), $strsettings));
$table->define_baseurl($PAGE->url);
$table->set_attribute('id', 'clickapplugins');
$table->set_attribute('class', 'admintable generaltable');
$table->setup();

$plugins = array();
foreach (core_component::get_plugin_list('clickap') as $plugin => $plugindir) {
    if (get_string_manager()->string_exists('pluginname', 'clickap_' . $plugin)) {
        $strpluginname = get_string('pluginname', 'clickap_' . $plugin);
    } else {
        $strpluginname = $plugin;
    }
    $plugins[$plugin] = $strpluginname;
}
core_collator::asort($plugins);

$like = $DB->sql_like('plugin', '?', true, true, false, '|');
$params = array('clickap|_%');
$installed = $DB->get_records_select('config_plugins', "$like AND name = 'version'", $params);
$versions = array();
foreach ($installed as $config) {
    $name = preg_replace('/^clickap_/', '', $config->plugin);
    $versions[$name] = $config->value;
    if (!isset($plugins[$name])) {
        $plugins[$name] = $name;
    }
}

foreach ($plugins as $plugin => $name) {
    $uninstall = '';
    if ($uninstallurl = core_plugin_manager::instance()->get_uninstall_url('clickap_'.$plugin, 'manage')) {
        $uninstall = html_writer::link($uninstallurl, $struninstall);
    }

    if (!isset($versions[$plugin])) {
        if (file_exists("$CFG->dirroot/$CFG->admin/clickap/$plugin/version.php")) {
            // not installed yet
            $version = '?';
        } else {
            // no version info available
            $version = '-';
        }
    } else {
        $version = $versions[$plugin];
        if (file_exists("$CFG->dirroot/$CFG->admin/clickap/$plugin")) {
            $version = $versions[$plugin];
        } else {
            // somebody removed plugin without uninstall
            $name = '<span class="notifyproblem">'.$name.' ('.get_string('missingfromdisk').')</span>';
            $version = $versions[$plugin];
        }
    }

    $clickapsettings = admin_get_root()->locate('clickapsettings' . $plugin);
    $settings = '';
    if($clickapsettings){
        if (file_exists($CFG->dirroot.'/admin/clickap/'.$plugin.'/settings.php')) {
            $settings = '<a href="'.$CFG->wwwroot.'/'.$CFG->admin.'/settings.php?section=clickapsettings'.$plugin.'">'.$strsettings.'</a>';
        }
    }
    $table->add_data(array($name, $version, $settings, $uninstall));

}

$table->print_html();
echo $OUTPUT->footer();