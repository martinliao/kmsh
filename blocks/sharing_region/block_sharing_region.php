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
 * Site main menu block.
 *
 * @package    block_sharing_region
 * @copyright  1999 onwards Martin Dougiamas (http://dougiamas.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class block_sharing_region extends block_list {
    function init() {
        $this->title = get_string('pluginname', 'block_sharing_region');
    }

    function applicable_formats() {
        return array('site' => true);
    }

    function get_content() {
        global $USER, $CFG, $DB, $OUTPUT;

        if ($this->content !== NULL) {
            return $this->content;
        }

        $this->content = new stdClass();
        $this->content->items = array();
        $this->content->icons = array();
        $this->content->footer = '';

        if (empty($this->instance)) {
            return $this->content;
        }

        $course = $this->page->course;
        require_once($CFG->dirroot.'/course/lib.php');
        $context = context_course::instance($course->id);
        $isediting = $this->page->user_is_editing() && has_capability('moodle/course:manageactivities', $context);

/// extra fast view mode
        if (!$isediting) {
            if(isguestuser($USER) or !isloggedin()){
                return $this->content;
            }

            $modinfo = get_fast_modinfo($course);
            if (!empty($modinfo->sections[2])) {
                $options = array('overflowdiv'=>true);
                foreach($modinfo->sections[2] as $cmid) {
                    $cm = $modinfo->cms[$cmid];
                    if (!$cm->uservisible) {
                        continue;
                    }

                    $content = $cm->get_formatted_content(array('overflowdiv' => true, 'noclean' => true));
                    $instancename = $cm->get_formatted_name();

                    if (!($url = $cm->url)) {
                        $this->content->items[] = $content;
                        $this->content->icons[] = '';
                    } else {
                        $linkcss = $cm->visible ? '' : ' class="dimmed" ';
                        //Accessibility: incidental image - should be empty Alt text
                        $icon = '<img src="' . $cm->get_icon_url() . '" class="icon" alt="" />';
                        $this->content->items[] = '<a title="'.$cm->modplural.'" '.$linkcss.' '.$cm->extra.
                                ' href="' . $url . '">' . $icon . $instancename . '</a>';
                    }
                }
            }
            
            $managers = '';
            if($roleid = $DB->get_field('role', 'id', array('shortname'=>'manager'))){
                $context = context_system::instance();
                if($DB->record_exists('role_assignments', array('roleid'=>$roleid, 'contextid'=>$context->id, 'userid'=>$USER->id))){
                    if (!empty($modinfo->sections[3])) {
                        $this->content->items[] = '<hr>';
                        $this->content->items[] = get_string('sharemanager', 'block_sharing_cart');
                        $options = array('overflowdiv'=>true);
                        foreach($modinfo->sections[3] as $cmid) {
                            $cm = $modinfo->cms[$cmid];
                            if (!$cm->uservisible) {
                                continue;
                            }

                            $content = $cm->get_formatted_content(array('overflowdiv' => true, 'noclean' => true));
                            $instancename = $cm->get_formatted_name();

                            if (!($url = $cm->url)) {
                                $this->content->items[] = $content;
                                $this->content->icons[] = '';
                            } else {
                                $linkcss = $cm->visible ? '' : ' class="dimmed" ';
                                //Accessibility: incidental image - should be empty Alt text
                                $icon = '<img src="' . $cm->get_icon_url() . '" class="icon" alt="" />';
                                $this->content->items[] = '<a title="'.$cm->modplural.'" '.$linkcss.' '.$cm->extra.
                                        ' href="' . $url . '">' . $icon . $instancename . '</a>';
                            }
                        }
                    }
                }
            }
            
            return $this->content;
        }

        // Slow & hacky editing mode.
        /** @var core_course_renderer $courserenderer */
        $courserenderer = $this->page->get_renderer('core', 'course');
        $ismoving = ismoving($course->id);
        
        $modinfo = get_fast_modinfo($course);
        $section = $modinfo->get_section_info(2);

        if ($ismoving) {
            $strmovehere = get_string('movehere');
            $strmovefull = strip_tags(get_string('movefull', '', "'$USER->activitycopyname'"));
            $strcancel= get_string('cancel');
            $stractivityclipboard = $USER->activitycopyname;
        } else {
            $strmove = get_string('move');
        }
        $editbuttons = '';

        if ($ismoving) {
            $this->content->icons[] = '<img src="'.$OUTPUT->pix_url('t/move') . '" class="iconsmall" alt="" />';
            $this->content->items[] = $USER->activitycopyname.'&nbsp;(<a href="'.$CFG->wwwroot.'/course/mod.php?cancelcopy=true&amp;sesskey='.sesskey().'">'.$strcancel.'</a>)';
        }

        if (!empty($modinfo->sections[2])) {
            $options = array('overflowdiv'=>true);
            foreach ($modinfo->sections[2] as $modnumber) {
                $mod = $modinfo->cms[$modnumber];
                if (!$mod->uservisible) {
                    continue;
                }
                if (!$ismoving) {
                    $actions = course_get_cm_edit_actions($mod, -1);

                    if(!is_siteadmin($USER)){
                        unset($actions['hide']);
                        unset($actions['show']);
                        unset($actions['delete']);
                    }
                    unset($actions['update']);
                    unset($actions['groupsnone']);
                    unset($actions['assign']);

                    // Prepend list of actions with the 'move' action.
                    $actions = array('move' => new action_menu_link_primary(
                        new moodle_url('/course/mod.php', array('sesskey' => sesskey(), 'copy' => $mod->id)),
                        new pix_icon('t/move', $strmove, 'moodle', array('class' => 'iconsmall', 'title' => '')),
                        $strmove
                    )) + $actions;

                    $editbuttons = html_writer::tag('div',
                        $courserenderer->course_section_cm_edit_actions($actions, $mod, array('donotenhance' => true)),
                        array('class' => 'buttons')
                    );
                } else {
                    $editbuttons = '';
                }
                if ($mod->visible || has_capability('moodle/course:viewhiddenactivities', $mod->context)) {
                    if ($ismoving) {
                        if ($mod->id == $USER->activitycopy) {
                            continue;
                        }
                        $this->content->items[] = '<a title="'.$strmovefull.'" href="'.$CFG->wwwroot.'/course/mod.php?moveto='.$mod->id.'&amp;sesskey='.sesskey().'">'.
                            '<img style="height:16px; width:80px; border:0px" src="'.$OUTPUT->pix_url('movehere') . '" alt="'.$strmovehere.'" /></a>';
                        $this->content->icons[] = '';
                    }
                    $content = $mod->get_formatted_content(array('overflowdiv' => true, 'noclean' => true));
                    $instancename = $mod->get_formatted_name();
                    $linkcss = $mod->visible ? '' : ' class="dimmed" ';

                    if (!($url = $mod->url)) {
                        $this->content->items[] = $content . $editbuttons;
                        $this->content->icons[] = '';
                    } else {
                        //Accessibility: incidental image - should be empty Alt text
                        $icon = '<img src="' . $mod->get_icon_url() . '" class="icon" alt="" />';
                        $this->content->items[] = '<a title="' . $mod->modfullname . '" ' . $linkcss . ' ' . $mod->extra .
                            ' href="' . $url . '">' . $icon . $instancename . '</a>' . $editbuttons;
                    }
                }
            }
        }
        if (!empty($modinfo->sections[3])) {
            $this->content->items[] = '<hr>';
            $this->content->items[] = get_string('sharemanager', 'block_sharing_cart');
            $options = array('overflowdiv'=>true);
            foreach ($modinfo->sections[3] as $modnumber) {
                $mod = $modinfo->cms[$modnumber];
                if (!$mod->uservisible) {
                    continue;
                }
                if (!$ismoving) {
                    $actions = course_get_cm_edit_actions($mod, -1);

                    if(!is_siteadmin($USER)){
                        unset($actions['hide']);
                        unset($actions['show']);
                        unset($actions['delete']);
                    }
                    unset($actions['update']);
                    unset($actions['groupsnone']);
                    unset($actions['assign']);

                    // Prepend list of actions with the 'move' action.
                    $actions = array('move' => new action_menu_link_primary(
                        new moodle_url('/course/mod.php', array('sesskey' => sesskey(), 'copy' => $mod->id)),
                        new pix_icon('t/move', $strmove, 'moodle', array('class' => 'iconsmall', 'title' => '')),
                        $strmove
                    )) + $actions;

                    $editbuttons = html_writer::tag('div',
                        $courserenderer->course_section_cm_edit_actions($actions, $mod, array('donotenhance' => true)),
                        array('class' => 'buttons')
                    );
                } else {
                    $editbuttons = '';
                }
                if ($mod->visible || has_capability('moodle/course:viewhiddenactivities', $mod->context)) {
                    if ($ismoving) {
                        if ($mod->id == $USER->activitycopy) {
                            continue;
                        }
                        $this->content->items[] = '<a title="'.$strmovefull.'" href="'.$CFG->wwwroot.'/course/mod.php?moveto='.$mod->id.'&amp;sesskey='.sesskey().'">'.
                            '<img style="height:16px; width:80px; border:0px" src="'.$OUTPUT->pix_url('movehere') . '" alt="'.$strmovehere.'" /></a>';
                        $this->content->icons[] = '';
                    }
                    $content = $mod->get_formatted_content(array('overflowdiv' => true, 'noclean' => true));
                    $instancename = $mod->get_formatted_name();
                    $linkcss = $mod->visible ? '' : ' class="dimmed" ';

                    if (!($url = $mod->url)) {
                        $this->content->items[] = $content . $editbuttons;
                        $this->content->icons[] = '';
                    } else {
                        //Accessibility: incidental image - should be empty Alt text
                        $icon = '<img src="' . $mod->get_icon_url() . '" class="icon" alt="" />';
                        $this->content->items[] = '<a title="' . $mod->modfullname . '" ' . $linkcss . ' ' . $mod->extra .
                            ' href="' . $url . '">' . $icon . $instancename . '</a>' . $editbuttons;
                    }
                }
            }
        }

        if ($ismoving) {
            $this->content->items[] = '<a title="'.$strmovefull.'" href="'.$CFG->wwwroot.'/course/mod.php?movetosection='.$section->id.'&amp;sesskey='.sesskey().'">'.
                                      '<img style="height:16px; width:80px; border:0px" src="'.$OUTPUT->pix_url('movehere') . '" alt="'.$strmovehere.'" /></a>';
            $this->content->icons[] = '';
        }

        //$this->content->footer = $courserenderer->course_section_add_cm_control($course, 0, null, array('inblock' => true));

        return $this->content;
    }
}


