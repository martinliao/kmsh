<?php
/**
 * Version details.
 *
 * @package    blocks
 * @subpackage smartmenu
 * 
 * @author     Mary Chen (mary@click-ap.com)
 * @author     Jack (jack@click-ap.com)
 * @copyright  Click-AP {@line https://www.click-ap.com}
 * @license    http://www.click-ap.com/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die;
if ($ADMIN->fulltree) {
    
    /*
    $settings->add(new admin_setting_configcolourpicker(
            'block_smartmenu/color_t', get_string('color_teacher', 'block_smartmenu'), '', '#333333', null));
    $settings->add(new admin_setting_configcolourpicker(
            'block_smartmenu/color_s', get_string('color_student', 'block_smartmenu'), '', '#1e7257', null));
    */
    $availabletypes = get_module_types_names();
    $defaultresourcetypes = array();
    
    $modnames = get_module_types_names();
    foreach($modnames as $key => $value){
        $allowtypes[$key] = get_string('pluginname', $key);
    }

    $display = get_config('block_smartmenu', 'display');

    if($display == 2){
        $settings->add(new admin_setting_configmultiselect('block_smartmenu/activitytypes_resources',
                new lang_string('course_resources', 'block_smartmenu'), new lang_string('configresourcetypes', 'block_smartmenu'),
                array('resource','folder'), $allowtypes));
        $settings->add(new admin_setting_configmultiselect('block_smartmenu/activitytypes_quizzes',
                new lang_string('course_quizzes', 'block_smartmenu'), new lang_string('configresourcetypes', 'block_smartmenu'),
                array('assign','quiz'), $allowtypes));
        $settings->add(new admin_setting_configmultiselect('block_smartmenu/activitytypes_videos',
                new lang_string('course_videos', 'block_smartmenu'), new lang_string('configresourcetypes', 'block_smartmenu'),
                $defaultresourcetypes, $allowtypes));
        $settings->add(new admin_setting_configmultiselect('block_smartmenu/activitytypes_forums',
                new lang_string('course_forums', 'block_smartmenu'), new lang_string('configresourcetypes', 'block_smartmenu'),
                array('forum'), $allowtypes));
        $settings->add(new admin_setting_configmultiselect('block_smartmenu/activitytypes_surveys',
                new lang_string('course_surveys', 'block_smartmenu'), new lang_string('configresourcetypes', 'block_smartmenu'),
                array('survey','feedback'), $allowtypes));
    }else{
        $plugins = $allowtypes;

        if(file_exists($CFG->dirroot.'/blocks/smartmenu/format/resources/view.php')){
            $allowtypes = array();
            if (array_key_exists("resource", $plugins)){
                $allowtypes["resource"] = $plugins['resource'];
            }
            if (array_key_exists("folder", $plugins)){
                $allowtypes["folder"] = $plugins['folder'];
            }
            if (array_key_exists("page", $plugins)){
                $allowtypes["page"] = $plugins['page'];
            }
            if (array_key_exists("url", $plugins)){
                $allowtypes["url"] = $plugins['url'];
            }
            if (array_key_exists("label", $plugins)){
                $allowtypes["label"] = $plugins['label'];
            }
            $settings->add(new admin_setting_configmultiselect('block_smartmenu/activitytypes_resources',
                new lang_string('course_resources', 'block_smartmenu'), new lang_string('configresourcetypes', 'block_smartmenu'),
                array('resource','folder'), $allowtypes));
        }

        if(file_exists($CFG->dirroot.'/blocks/smartmenu/format/quizzes/view.php')){
            $allowtypes = array();
            if (array_key_exists("assign", $plugins)){
                $allowtypes["assign"] = $plugins['assign'];
            }
            if (array_key_exists("folder", $plugins)){
                $allowtypes["folder"] = $plugins['folder'];
            }
            if (array_key_exists("quiz", $plugins)){
                $allowtypes["quiz"] = $plugins['quiz'];
            }
            if (array_key_exists("workshop", $plugins)){
                $allowtypes["workshop"] = $plugins['workshop'];
            }
            if (array_key_exists("certificate", $plugins)){
                $allowtypes["certificate"] = $plugins['certificate'];
            }
            $settings->add(new admin_setting_configmultiselect('block_smartmenu/activitytypes_quizzes',
                new lang_string('course_quizzes', 'block_smartmenu'), new lang_string('configresourcetypes', 'block_smartmenu'),
                array('assign','quiz'), $allowtypes));
        }

        if(file_exists($CFG->dirroot.'/blocks/smartmenu/format/videos/view.php')){
            $allowtypes = array();
            if (array_key_exists("videos", $plugins)){
                $allowtypes["videos"] = $plugins['videos'];
            }
            if (array_key_exists("ewantvideo", $plugins)){
                $allowtypes["ewantvideo"] = $plugins['ewantvideo'];
            }
            if (array_key_exists("videofile", $plugins)){
                $allowtypes["videofile"] = $plugins['videofile'];
            }
            if (array_key_exists("scorm", $plugins)){
                $allowtypes["scorm"] = $plugins['scorm'];
            }
            if (array_key_exists("videoquiz", $plugins)){
                $allowtypes["videoquiz"] = $plugins['videoquiz'];
            }
            if (array_key_exists("videotube", $plugins)){
                $allowtypes["videotube"] = $plugins['videotube'];
            }
            if (array_key_exists("qiv", $plugins)){
                $allowtypes["qiv"] = $plugins['qiv'];
            }
            $settings->add(new admin_setting_configmultiselect('block_smartmenu/activitytypes_videos',
                new lang_string('course_videos', 'block_smartmenu'), new lang_string('configresourcetypes', 'block_smartmenu'),
                $defaultresourcetypes, $allowtypes));
        }
        
        $format_forums = $CFG->dirroot.'/blocks/smartmenu/format/forums/view.php';
        if(file_exists($format_forums)){
            $allowtypes = array();
            if (array_key_exists("forum", $plugins)){
                $allowtypes["forum"] = $plugins['forum'];
            }
            $settings->add(new admin_setting_configmultiselect('block_smartmenu/activitytypes_forums',
                new lang_string('course_forums', 'block_smartmenu'), new lang_string('configresourcetypes', 'block_smartmenu'),
                array('forum'), $allowtypes));
        }    

        if(file_exists($CFG->dirroot.'/blocks/smartmenu/format/surveys/view.php')){
            $allowtypes = array();
            if (array_key_exists("survey", $plugins)){
                $allowtypes["survey"] = $plugins['survey'];
            }
            if (array_key_exists("feedback", $plugins)){
                $allowtypes["feedback"] = $plugins['feedback'];
            }
            if (array_key_exists("choice", $plugins)){
                $allowtypes["choice"] = $plugins['choice'];
            }
            $settings->add(new admin_setting_configmultiselect('block_smartmenu/activitytypes_surveys',
                new lang_string('course_surveys', 'block_smartmenu'), new lang_string('configresourcetypes', 'block_smartmenu'),
                array('survey','feedback'), $allowtypes));
        }
    }
}