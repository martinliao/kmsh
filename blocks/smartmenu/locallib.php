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
/*
function block_smartmenu_get_course_newsforum($course) {
    global $DB;   
    $forumid = 1;  
    $sql = "SELECT cm.id FROM {forum} f
            JOIN {modules} m  ON m.name= 'forum'
            JOIN {course_modules} cm ON cm.course = f.course AND cm.module = m.id  AND f.id = cm.instance
            WHERE f.course= :courseid  AND f.type = 'news' 
            ORDER BY id
            LIMIT 1";   
    $params['courseid']  = $course->id;
    if($forum = $DB->get_record_sql($sql, $params, 0, '')){
        $forumid = $forum->id;
    }
    return $forumid;
}
*/

function block_smartmenu_get_course_attendance($course) {
    global $DB;   
    $attid=1;  
    $sql = "SELECT cm.id FROM {attendance} a
            JOIN {modules} m  ON m.name= 'attendance'
            JOIN {course_modules} cm ON cm.course = a.course AND cm.module = m.id  AND a.id = cm.instance
            WHERE a.course= :courseid
            ORDER BY id 
            LIMIT 1";   
    $params['courseid']  = $course->id;
    if($attendance = $DB->get_record_sql($sql, $params, 0, '')){
        $attid = $attendance->id;
    }
    return $attid;
}

function block_smartmenu_matchuri($patternUrl, string $currentUrl){
    $isActive = false;

    if( is_array($patternUrl)) { 
        foreach($patternUrl as $pUrl) {
            if(strpos($currentUrl, $pUrl) !== false){
                $isActive = true;
                break;
            }
        }
    }
    else {
        if(strpos($currentUrl, $patternUrl) !== false){
            $isActive = true;
        }
    }

    return $isActive;   
}

function block_smartmenu_get_activity_types($config){
    $has_file = $has_quiz = $has_video = $has_forum = $has_survey = false;
    
    if(!empty($config->activitytypes_resources)){
        $has_file = true;
    }
    if(!empty($config->activitytypes_quizzes)){
        $has_quiz = true;
    }
    if(!empty($config->activitytypes_videos)){
        $has_video = true;
    }
    if(!empty($config->activitytypes_forums)){
        $has_forum = true;
    }
    if(!empty($config->activitytypes_surveys)){
        $has_survey = true;
    }
    
    return array($has_file,$has_quiz,$has_video,$has_forum,$has_survey);
}

function block_smartmenu_list($course, $rname, $count){
    global $DB, $CFG, $PAGE;

    require_once($CFG->libdir .'/completionlib.php');
    
    $config = get_config('block_smartmenu');
    list($has_file,$has_quiz,$has_video,$has_forum,$has_survey) = block_smartmenu_get_activity_types($config);
    $submenu = $mainmenu = array();
    $i = 1;
    while($i <= $count){
        $name = $rname.$i;
        if(isset($config->$name)){
            $setting = unserialize($config->$name);
            if($setting['shortname'] == 'content'){
                if(!$has_file && !$has_quiz && !$has_video){
                    $i++;
                    continue;
                }
            }
            else if(($setting['shortname'] == 'file' && !$has_file)
                 OR ($setting['shortname'] == 'video' && !$has_video)
                 OR ($setting['shortname'] == 'quiz' && !$has_quiz)
                 OR ($setting['shortname'] == 'forum' && !$has_forum)
                 OR ($setting['shortname'] == 'survey' && !$has_survey)){
                $i++;
                continue;
            }
            
            if($setting['visible']){
                if($setting['strname'] == 'tmenu_coursecompletion'){
                    $completion = new completion_info($course);
                    if (!$completion->is_enabled() && !$completion->has_criteria()) {
                        $i++;
                        continue;
                    }
                }
                
                $showname = '';
                if($setting['parent'] > 1){
                    //$showname = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                }

                $activestyle = "";
                if((!empty($setting['url']) && strpos(htmlspecialchars_decode($PAGE->url->out()), $setting['url']) !== false)){
                    $activestyle = "active";
                }
                else if (isset($setting['matchuri'])){
                    if(block_smartmenu_matchuri($setting['matchuri'], htmlspecialchars_decode($PAGE->url->out()))){
                        $activestyle = "active";
                    }
                }
                
                if(!empty($setting['icon'])){
                    $showname .= '<i class="fa '. $setting['icon'] .' fa-fw"></i>&nbsp;'.get_string($setting['strname'], 'block_smartmenu');
                }else{
                    $showname .= get_string($setting['strname'], 'block_smartmenu');
                }
                
                if($setting['parent'] == 0 && empty($setting['params'])){
                    $mainmenu[$name] = html_writer::tag('div', $showname, array('class' => 'mainmenu', 'onclick' => "SwitchCourseMenu('$name')", 'style' => "cursor:pointer;background:".$setting['color'].";"));
                    
                    $numsections = course_get_format($course)->get_last_section_number();
                    if(isset($setting['section']) && $setting['section'] == true){
                        $parentmenu = $rname.$setting['id'];
                        $sql = "SELECT section,name FROM {course_sections} 
                                WHERE course=:course and section != 0 
                                      AND section <= :numsections)
                                ORDER BY section";
                        $sections = $DB->get_records_sql_menu($sql ,array('course'=>$course->id, 'courseid'=>$course->id, 'numsections'=>$numsections));
                        foreach ($sections as $key => $sectionname) {
                            $sectionstyle = '';
                            $sectionbg = "#2B6DA3;";
                            
                            if($sectionname == null){
                                $sec = $DB->get_record('course_sections',array('course'=>$course->id,'section'=>$key));
                                $sectionname = get_section_name($course, $sec);
                            }
                            //$showname = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$sectionname;
                            $showname = $sectionname;
                            $sectionurl = $CFG->wwwroot.'/course/view.php?id='.$course->id.'&section='.$key;

                            if(strpos(htmlspecialchars_decode($PAGE->url->out()), $sectionurl) !== false){
                                $sectionstyle = "active";
                            }
                            
                            $link = html_writer::link($sectionurl,$showname, array('class'=> $sectionstyle));
                            $submenu[$parentmenu][$sectionname] = html_writer::tag('li', $link, array('style' => "cursor:pointer;background:".$sectionbg.""));
                        }
                    }
                }elseif($setting['parent'] == 0 ){
                    $pathparam = '';
                    if(!empty($setting['params'])){
                        $pathparams = '?';
                        foreach($setting['params'] as $id => $value){
                            if($pathparams != '?'){$pathparams .= '&';}
                            if($value == '-1'){$value = $course->id;}
                            $pathparams .= $id.'='.$value;
                        }
                    }
                    $link = html_writer::link($setting['url'].$pathparams, $showname,  array('class'=> $activestyle));
                    $menulink = html_writer::tag('li', $link, array('class'=> $activestyle, 'style' => "cursor:pointer;background:".$setting['color']));
                    $mainmenu[$name] = $menulink;
                }else{
                    $parentmenu = $rname.$setting['parent'];
                    
                    if(!empty($setting['url'])){
                        if(!empty($setting['params'])){
                            $pathparams = '?';
                            foreach($setting['params'] as $id => $value){
                                if($pathparams != '?'){$pathparams .= '&';}
                                if($value == '-1'){$value = $course->id;}
                                $pathparams .= $id.'='.$value;
                            }
                            $link = html_writer::link($setting['url']. $pathparams, $showname,  array('class'=> $activestyle));
                        }else{
                            $link = html_writer::link($setting['url'], $showname, array('class'=> $activestyle));
                        }
                        $submenu[$parentmenu][$name] = html_writer::tag('li', $link, array('class'=> $activestyle, 'style' => "cursor:pointer;background:".$setting['color']));
                    }else{
                        //$submenu[$parentmenu][$name] = html_writer::tag('div', $showname, array('class' => 'mainmenu', 'onclick' => "SwitchCourseMenu('$name')", 'style' => "background:".$setting['color'].";"));
                        $link = html_writer::tag('span', $showname);
                        $submenu[$parentmenu][$name] = html_writer::tag('li', $link, array('class' => 'mainmenu', 'onclick' => "SwitchCourseMenu('$name')", 'style' => "background:".$setting['color'].";"));
                    }
                }
            }                    
        }
        $i++;
    }
    return array($mainmenu, $submenu);
}
?>