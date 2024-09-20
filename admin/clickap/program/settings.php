<?php
/**
 * Version details.
 *
 * @package    clickap
 * @subpackage program
 * @copyright  2018 Click-AP <elaine@click-ap.com>
 * @license    http://www.click-ap.com/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;
global $SITE;

if (($hassiteconfig || has_any_capability(array(
            'clickap/programs:viewawarded',
            'clickap/programs:createprogram',
            'clickap/programs:manageglobalsettings',
            'clickap/programs:awardprogram',
            'clickap/programs:configurecriteria',
            'clickap/programs:configuremessages',
            'clickap/programs:configuredetails',
            'clickap/programs:deleteprogram'), context_system::instance()))) {

    $globalsettings = new admin_settingpage('programsettings', new lang_string('programsettings', 'clickap_program'),
            array('clickap/programs:manageglobalsettings'));

    $accepted_types = preg_split('/\s*,\s*/', trim($CFG->courseoverviewfilesext), -1, PREG_SPLIT_NO_EMPTY);
    $globalsettings->add(new admin_setting_configstoredfile('clickap_program/programbanner', new lang_string('programbanner', 'clickap_program'), '', 'programbanner', 0, array('maxfiles' => 1, 'accepted_types' => $accepted_types)));
    
    
    $ADMIN->add('clickapsettings', new admin_category('programs', get_string('pluginname', 'clickap_program')));       
    //$ADMIN->add('programs', $globalsettings);

    $ADMIN->add('programs',
        new admin_externalpage('manageprogram',
            new lang_string('manageprograms', 'clickap_program'),
            new moodle_url('/admin/clickap/program/index.php', array('type' => 1)),
            array(
                'clickap/programs:viewawarded',
                'clickap/programs:createprogram',
                'clickap/programs:awardprogram',
                'clickap/programs:configurecriteria',
                'clickap/programs:configuremessages',
                'clickap/programs:configuredetails',
                'clickap/programs:deleteprogram'
            )
        )
    );

    $ADMIN->add('programs',
        new admin_externalpage('newprogram',
            new lang_string('addprogram', 'clickap_program'),
            new moodle_url('/admin/clickap/program/newprogram.php', array('type' => 1)),
            array('clickap/programs:createprogram'))
    );
}
$settings=null;