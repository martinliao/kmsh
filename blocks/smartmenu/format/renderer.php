<?php
/**
 * @package    block
 * @subpackage smartmenu
 * 
 * @author     Mary Chen (mary@click-ap.com)
 * @author     Jack (jack@click-ap.com)
 * @copyright  Click-AP {@line https://www.click-ap.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v2 or later
 */
defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot.'/course/format/renderer.php');
//require_once($CFG->dirroot.'/blocks/smartmenu/format/locallib.php');

class block_smartmenu_renderer extends format_section_renderer_base {
    
    /** @var core_course_renderer Stores instances of core_course_renderer */
    protected $courserenderer = null;

    /**
     * Constructor method, calls the parent constructor
     *
     * @param moodle_page $page
     * @param string $target one of rendering target constants
     */
    public function __construct(moodle_page $page, $target) {
        parent::__construct($page, $target);
        $page->set_other_editing_capability('moodle/course:setcurrentsection');
        $this->courserenderer = $page->get_renderer('core', 'course');
    }

    /**
     * Generate the starting container html for a list of sections
     * @return string HTML to output.
     */
    protected function start_section_list($name = 'topics') {
        return html_writer::start_tag('ul', array('class' => $name));
    }

    /**
     * Generate the closing container html for a list of sections
     * @return string HTML to output.
     */
    protected function end_section_list() {
        return html_writer::end_tag('ul');
    }

    /**
     * Generate the title for this section page
     * @return string the page title
     */
    protected function page_title($name = 'topics') {
        return get_string('course_'.$name, 'block_smartmenu');
    }

    /**
     * Generate the edit controls of a section
     *
     * @param stdClass $course The course entry from DB
     * @param stdClass $section The course_section entry from DB
     * @param bool $onsectionpage true if being printed on a section page
     * @return array of links with edit controls
     */
    protected function section_edit_controls2($course, $section, $onsectionpage = false) {
        global $PAGE;

        if (!$PAGE->user_is_editing()) {
            return array();
        }

        $coursecontext = context_course::instance($course->id);

        if ($onsectionpage) {
            $url = course_get_url($course, $section->section);
        } else {
            $url = course_get_url($course);
        }
        $url->param('sesskey', sesskey());
        $controls = array();

        return array_merge($controls, parent::section_edit_controls($course, $section, $onsectionpage));
    }
    
    // Reference from course/format/rednderer.php : print_multiple_section_page()
    public function print_multiple_section_page($course, $sections, $mods, $modnames, $modnamesused, $activitytypes = null) {
        $modinfo = get_fast_modinfo($course);
        $course = course_get_format($course)->get_course();

        //reset coursedisplay
        $course->coursedisplay = COURSE_DISPLAY_MULTIPAGE;
        
        $context = context_course::instance($course->id);
        // Title with completion help icon.
        $completioninfo = new completion_info($course);
        echo $completioninfo->display_help_icon();

        $allowtypes = array();
        if(!empty($activitytypes)){
            $config = get_config('block_smartmenu');
            $configname = 'activitytypes_'.$activitytypes;
            $allowtypes = explode(',', $config->$configname);
            //$allowtypes[] = $activitytypes;
        }
        else{
            $activitytypes = $course->format;
        }        

        echo $this->output->heading($this->page_title($activitytypes), 2, '');

        // Copy activity clipboard..
        echo $this->course_activity_clipboard($course, 0);

        // Now the list of sections..
        echo $this->start_section_list($activitytypes);
        $numsections = course_get_format($course)->get_last_section_number();

        foreach ($modinfo->get_section_info_all() as $section => $thissection) {
            /*
            if ($section == 0) {
                continue;
            }
            */

            if ($section > $numsections) {
                // activities inside this section are 'orphaned', this section will be printed as 'stealth' below
                continue;
            }

            // Show the section if the user is permitted to access it, OR if it's not available
            // but there is some available info text which explains the reason & should display,
            // OR it is hidden but the course has a setting to display hidden sections as unavilable.
            //$showsection = $thissection->uservisible ||
            //        ($thissection->visible && !$thissection->available && $thissection->showavailability
            //        && !empty($thissection->availableinfo));
            //if (!$showsection) {
            //    if (!$course->hiddensections && $thissection->available) {
            //        echo $this->section_hidden($section);
            //    }
            //    continue;
            //}
            $showsection = $thissection->uservisible ||
                    ($thissection->visible && !$thissection->available && !empty($thissection->availableinfo)) ||
                    (!$thissection->visible && !$course->hiddensections);
            if (!$showsection) {
                continue;
            }
            //2018112201 , not support course format - layout(Show one section per page)
            /*
            if (!$PAGE->user_is_editing() && isset($course->coursedisplay) && $course->coursedisplay == COURSE_DISPLAY_MULTIPAGE) {
                echo $this->section_summary($thissection, $course, null);
            } else {
                echo $this->section_header($thissection, $course, false, 0);
                if ($thissection->uservisible) {
                    echo $this->local_section_cm_list($course, $thissection, $allowtypes, 0);
                    echo $this->local_section_add_cm_control($course, $section, $allowtypes, 0, null);
                }
                echo $this->section_footer();
            }
            */
            //only show have modules section(s)           
            $modulehtml = '';
            if ($thissection->uservisible) {
                $modulehtml .= $this->local_section_cm_list($course, $thissection, $allowtypes, 0);
            }
            //if (!$this->page->user_is_editing() && $course->coursedisplay == COURSE_DISPLAY_MULTIPAGE) {
            if (!$this->page->user_is_editing() && $course->coursedisplay == COURSE_DISPLAY_MULTIPAGE) {
                // Display section summary only.
                //echo $this->section_summary($thissection, $course, null);
            } 

            if (!empty(strip_tags($modulehtml)) OR $this->page->user_is_editing()) {    
                echo $this->section_header($thissection, $course, false, 0);
                echo $modulehtml;
                echo $this->local_section_add_cm_control($course, $section, $allowtypes, 0, null);
                echo $this->section_footer();
            }
        }
        echo $this->end_section_list();
    }
    
    protected function section_header($section, $course, $onsectionpage, $sectionreturn=null) {

        $o = $currenttext = $sectionstyle = '';

        if ($section->section != 0) {
            if (!$section->visible) {
                $sectionstyle = ' hidden';
            } else if (course_get_format($course)->is_section_current($section)) {
                $sectionstyle = ' current';
            }
        }

        $o.= html_writer::start_tag('li', array('id' => 'section-'.$section->section,
            'class' => 'section main clearfix'.$sectionstyle, 'role'=>'region',
            'aria-label'=> get_section_name($course, $section)));

        $o.= html_writer::start_tag('div', array('class' => 'content'));

        $hasnamenotsecpg = (!$onsectionpage && ($section->section != 0 || !is_null($section->name)));

        $hasnamesecpg = ($onsectionpage && ($section->section == 0 && !is_null($section->name)));

        if ($hasnamenotsecpg || $hasnamesecpg) {
            $o.= $this->output->heading($this->section_title($section, $course), 3, 'sectionname');
        }

        $o.= html_writer::start_tag('div', array('class' => 'summary'));
        $o.= $this->format_summary_text($section);
        $context = context_course::instance($course->id);
        
        $o.= html_writer::end_tag('div');
        
        $o .= $this->section_availability_message($section,
                has_capability('moodle/course:viewhiddensections', $context));

        return $o;
    }
    
    //ToDo: add restrict mod type\
    // Reference from : course/renderer.php : course_section_cm_list()
    private function local_section_cm_list($course, $section, $allowtypes = array(), $sectionreturn = null, $displayoptions = array()) {
        global $USER;

        $output = '';
        $modinfo = get_fast_modinfo($course);
        if (is_object($section)) {
            $section = $modinfo->get_section_info($section->section);
        } else {
            $section = $modinfo->get_section_info($section);
        }
        $completioninfo = new completion_info($course);

        // check if we are currently in the process of moving a module with JavaScript disabled
        $ismoving = $this->page->user_is_editing() && ismoving($course->id);
        if ($ismoving) {
            $movingpix = new pix_icon('movehere', get_string('movehere'), 'moodle', array('class' => 'movetarget'));
            $strmovefull = strip_tags(get_string("movefull", "", "'$USER->activitycopyname'"));
        }

        // Get the list of modules visible to user (excluding the module being moved if there is one)
        $moduleshtml = array();
        if (!empty($modinfo->sections[$section->section])) {
            foreach ($modinfo->sections[$section->section] as $modnumber) {
                $mod = $modinfo->cms[$modnumber];

                if ($ismoving and $mod->id == $USER->activitycopy) {
                    // do not display moving mod
                    continue;
                }

                $modulehtml = $this->courserenderer->course_section_cm_list_item($course, $completioninfo, $mod, $sectionreturn, $displayoptions);
                if(!empty($allowtypes)) {
                    if(in_array($mod->modname, $allowtypes)) {
                        if ($modulehtml) {
                            $moduleshtml[$modnumber] = $modulehtml;
                        }
                    }
                }
                else {
                    if ($modulehtml) {
                        $moduleshtml[$modnumber] = $modulehtml;
                    }
                }
            }
        }

        $sectionoutput = '';
        if (!empty($moduleshtml) || $ismoving) {
            foreach ($moduleshtml as $modnumber => $modulehtml) {
                if ($ismoving) {
                    $movingurl = new moodle_url('/course/mod.php', array('moveto' => $modnumber, 'sesskey' => sesskey()));
                    $sectionoutput .= html_writer::tag('li',
                            html_writer::link($movingurl, $this->output->render($movingpix), array('title' => $strmovefull)),
                            array('class' => 'movehere'));
                }

                $sectionoutput .= $modulehtml;
            }

            if ($ismoving) {
                $movingurl = new moodle_url('/course/mod.php', array('movetosection' => $section->id, 'sesskey' => sesskey()));
                $sectionoutput .= html_writer::tag('li',
                        html_writer::link($movingurl, $this->output->render($movingpix), array('title' => $strmovefull)),
                        array('class' => 'movehere'));
            }
        }

        // Always output the section module list.
        $output .= html_writer::tag('ul', $sectionoutput, array('class' => 'section img-text'));

        return $output;
    }
    
    /**
     * Build the HTML for the module chooser javascript popup.
     *
     * @param int $courseid The course id to fetch modules for.
     * @return string
     */
    private function course_activitychooser($courseid, $allowtypes = array()) {

        if (!$this->page->requires->should_create_one_time_item_now('core_course_modchooser')) {
            return '';
        }

        // Build an object of config settings that we can then hook into in the Activity Chooser.
        $chooserconfig = (object) [
            'tabmode' => get_config('core', 'activitychoosertabmode'),
            'allowtypes' => $allowtypes
        ];
        //$this->page->requires->js_call_amd('core_course/activitychooser', 'init', [$courseid, $chooserconfig]);
        $this->page->requires->js_call_amd('block_smartmenu/activitychooser', 'init', [$courseid, $chooserconfig]);

        return '';
    }
    
    // From course_section_add_cm_control()
    private function local_section_add_cm_control($course, $section, $allowtypes = array(), $sectionreturn = null, $displayoptions = array()) {
        global $CFG, $USER;

        // The returned control HTML can be one of the following:
        // - Only the non-ajax control (select menus of activities and resources) with a noscript fallback for non js clients.
        // - Only the ajax control (the link which when clicked produces the activity chooser modal). No noscript fallback.
        // - [Behat only]: The non-ajax control and optionally the ajax control (depending on site settings). If included, the link
        // takes priority and the non-ajax control is wrapped in a <noscript>.
        // Behat requires the third case because some features run with JS, some do not. We must include the noscript fallback.
        $behatsite = defined('BEHAT_SITE_RUNNING');
        $nonajaxcontrol = '';
        $ajaxcontrol = '';
        $courseajaxenabled = course_ajax_enabled($course);
        $userchooserenabled = get_user_preferences('usemodchooser', $CFG->modchooserdefault);

        // Decide what combination of controls to output:
        // During behat runs, both controls can be used in conjunction to provide non-js fallback.
        // During normal use only one control or the other will be output. No non-js fallback is needed.
        $rendernonajaxcontrol = $behatsite || !$courseajaxenabled || !$userchooserenabled || $course->id != $this->page->course->id;
        $renderajaxcontrol = $courseajaxenabled && $userchooserenabled && $course->id == $this->page->course->id;

        // The non-ajax control, which includes an entirely non-js (<noscript>) fallback too.
        //$rendernonajaxcontrol = true;
        if ($rendernonajaxcontrol) {
            $vertical = !empty($displayoptions['inblock']);

            // Check to see if user can add menus.
            if (!has_capability('moodle/course:manageactivities', context_course::instance($course->id))
                || !$this->page->user_is_editing()) {
                return '';
            }

            // Retrieve all modules with associated metadata.
            $contentitemservice = \core_course\local\factory\content_item_service_factory::get_content_item_service();
            $urlparams = ['section' => $section];
            if (!is_null($sectionreturn)) {
                $urlparams['sr'] = $sectionreturn;
            }
            $modules = $contentitemservice->get_content_items_for_user_in_course($USER, $course, $urlparams);

            // Return if there are no content items to add.
            if (empty($modules)) {
                return '';
            }

            // We'll sort resources and activities into two lists.
            $activities = array(MOD_CLASS_ACTIVITY => array(), MOD_CLASS_RESOURCE => array());
            foreach ($modules as $module) {
                if(!empty($allowtypes)) {
                    if (in_array($module->name, $allowtypes)) {
                        $activityclass = MOD_CLASS_ACTIVITY;
                        if ($module->archetype == MOD_ARCHETYPE_RESOURCE) {
                            $activityclass = MOD_CLASS_RESOURCE;
                        } else if ($module->archetype === MOD_ARCHETYPE_SYSTEM) {
                            // System modules cannot be added by user, do not add to dropdown.
                            continue;
                        }
                        $link = $module->link;
                        $activities[$activityclass][$link] = $module->title;
                    }
                }
            }

            $straddactivity = get_string('addactivity');
            $straddresource = get_string('addresource');
            $sectionname = get_section_name($course, $section);
            $strresourcelabel = get_string('addresourcetosection', null, $sectionname);
            $stractivitylabel = get_string('addactivitytosection', null, $sectionname);

            $nonajaxcontrol = html_writer::start_tag('div', array('class' => 'section_add_menus', 'id' => 'add_menus-section-'
                . $section));

            if (!$vertical) {
                $nonajaxcontrol .= html_writer::start_tag('div', array('class' => 'horizontal'));
            }

            if (!empty($activities[MOD_CLASS_RESOURCE])) {
                $select = new url_select($activities[MOD_CLASS_RESOURCE], '', array('' => $straddresource), "ressection$section");
                $select->set_help_icon('resources');
                $select->set_label($strresourcelabel, array('class' => 'accesshide'));
                $nonajaxcontrol .= $this->output->render($select);
            }

            if (!empty($activities[MOD_CLASS_ACTIVITY])) {
                $select = new url_select($activities[MOD_CLASS_ACTIVITY], '', array('' => $straddactivity), "section$section");
                $select->set_help_icon('activities');
                $select->set_label($stractivitylabel, array('class' => 'accesshide'));
                $nonajaxcontrol .= $this->output->render($select);
            }

            if (!$vertical) {
                $nonajaxcontrol .= html_writer::end_tag('div');
            }

            $nonajaxcontrol .= html_writer::end_tag('div');
        }

        // The ajax control - the 'Add an activity or resource' link.
        if ($renderajaxcontrol) {
            // The module chooser link.
            $straddeither = get_string('addresourceoractivity');
            $ajaxcontrol = html_writer::start_tag('div', array('class' => 'mdl-right'));
            $ajaxcontrol .= html_writer::start_tag('div', array('class' => 'section-modchooser'));
            $icon = $this->output->pix_icon('t/add', '');
            $span = html_writer::tag('span', $straddeither, array('class' => 'section-modchooser-text'));
            $ajaxcontrol .= html_writer::tag('button', $icon . $span, [
                    'class' => 'section-modchooser-link btn btn-link',
                    'data-action' => 'open-chooser',
                    'data-sectionid' => $section,
                    'data-sectionreturnid' => $sectionreturn,
                ]
            );
            $ajaxcontrol .= html_writer::end_tag('div');
            $ajaxcontrol .= html_writer::end_tag('div');
            // Load the JS for the modal.
            $this->course_activitychooser($course->id, $allowtypes);
        }

        // Behat only: If both controls are being included in the HTML,
        // show the link by default and only fall back to the selects if js is disabled.
        if ($behatsite && $renderajaxcontrol) {
            $nonajaxcontrol = html_writer::tag('div', $nonajaxcontrol, array('class' => 'hiddenifjs addresourcedropdown'));
            $ajaxcontrol = html_writer::tag('div', $ajaxcontrol, array('class' => 'visibleifjs addresourcemodchooser'));
        }

        // If behat is running, we should have the non-ajax control + the ajax control.
        // Otherwise, we'll have one or the other.
        return $ajaxcontrol . $nonajaxcontrol;
    }
}