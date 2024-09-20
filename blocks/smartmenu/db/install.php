<?php
/**
 * smartmenu
 *
 * @package    block
 * @subpackage block_smartmenu
 * @copyright  2015
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v2 or later
 */

defined('MOODLE_INTERNAL') || die();

function xmldb_block_smartmenu_install() {
    global $CFG, $DB;  
    
    /**
    * Display Mothod
    * 1 = customization student page, and limit the activity items of the settings.
    * 2 = teacher and student page is same, and settings page can choose all enable activity items.
    */
    set_config('display', 2, 'block_smartmenu');
    set_config('course', false, 'block_smartmenu');
    $config = get_config('block_smartmenu');
    $display = $config->display;
    $forcourse = $config->course;
    
    //block default
    if(!$forcourse){
        $context = context_system::instance();
        $blockname = 'smartmenu';
        $region = 'side-pre';//right:side-post; left:side-pre
        $weight = '-10';
        $pagetypepattern = '*';
        $subpagepattern = '';
        
        $page = new moodle_page();
        $page->set_context($context);
        $page->set_pagetype('page-type');
        $page->set_subpage('');
        $page->set_url(new moodle_url('/'));
        $blockmanager = new block_manager($page);
        $regions = ['side-pre'];
        $blockmanager->add_regions($regions, false);
        $blockmanager->set_default_region($regions[0]);
        $blockmanager->add_block($blockname, $region, $weight, $context, $pagetypepattern, $subpagepattern);
    }
    
    //Teacher menu
    $color1 = '#333333';
    $color2 = '#FA820F';
    $color2_1 = '#2B6DA3';
    $color3 = '#FA6A6A';
    
    $i = 1;
    while($i <= 12){
        $menu = array();
        $name = 'tmenu'.$i;
        switch($i){
            case 1:
                $menu['shortname'] = 'info';
                $menu['strname'] = 'tmenu_information';
                $menu['parent'] = '0';
                $menu['url'] = $CFG->wwwroot.'/blocks/smartmenu/information.php';
                $menu['params'] = array('id'=>'-1');
                $menu['icon'] = 'fa-info-circle';
                $menu['color'] = $color1;
                break;          
            case 2:
                $menu['shortname'] = 'section';
                $menu['strname'] = 'tmenu_section';
                $menu['parent'] = '0';
                $menu['url'] = $CFG->wwwroot.'/blocks/smartmenu/course_section.php';
                $menu['params'] = array('id'=>'-1');
                $menu['icon'] = 'fa-list-alt';
                $menu['color'] = $color1;
                //$menu['section'] = true;
                break;
            case 3:
                $menu['shortname'] = 'enrollment';
                $menu['strname'] = 'tmenu_enrollment';
                $menu['parent'] = '0';
                $menu['url'] = $CFG->wwwroot.'/user/index.php';
                $menu['params'] = array('id'=>'-1');
                $menu['icon'] = 'fa-users';
                $menu['color'] = $color1;
                break;
            case 4:
                $menu['shortname'] = 'bulletin';
                $menu['strname'] = 'tmenu_bulletin';
                $menu['parent'] = '0';
                $menu['url'] = $CFG->wwwroot.'/blocks/smartmenu/course_news.php';
                $menu['params'] = array('id'=>'-1');
                $menu['icon'] = 'fa-bullhorn';
                $menu['color'] = $color1;
                $menu['matchuri'] = array('/mod/forum/view.php');
                break;
            case 5:
                $menu['shortname'] = 'home';
                $menu['strname'] = 'tmenu_home';
                $menu['parent'] = '0';
                $menu['url'] = $CFG->wwwroot.'/course/view.php';
                $menu['params'] = array('id'=>'-1');
                $menu['icon'] = 'fa-home';
                $menu['color'] = $color1;
                break;
            case 6:
                $menu['shortname'] = 'content';
                $menu['strname'] = 'tmenu_content';
                $menu['parent'] = '0';
                $menu['url'] = '';
                $menu['params'] = '';
                $menu['icon'] = 'fa-desktop';
                $menu['color'] = $color2;
                break;
            case 7:
                $menu['shortname'] = 'file';
                $menu['strname'] = 'tmenu_file';
                $menu['parent'] = '6';
                $menu['url'] = $CFG->wwwroot.'/blocks/smartmenu/format/resources/view.php';
                $menu['params'] = array('id'=>'-1');
                $menu['icon'] = 'fa-file';
                $menu['color'] = $color3;
                break;
            case 8:
                $menu['shortname'] = 'video';
                $menu['strname'] = 'tmenu_video';
                $menu['parent'] = '6';
                $menu['url'] = $CFG->wwwroot.'/blocks/smartmenu/format/videos/view.php';
                $menu['params'] = array('id'=>'-1');
                $menu['icon'] = 'fa-video-camera';
                $menu['color'] = $color3;
                break;
            case 9:
                $menu['shortname'] = 'quiz';
                $menu['strname'] = 'tmenu_quiz';
                $menu['parent'] = '6';
                $menu['url'] = $CFG->wwwroot.'/blocks/smartmenu/format/quizzes/view.php';
                $menu['params'] = array('id'=>'-1');
                $menu['icon'] = 'fa-check-square';
                $menu['color'] = $color3;
                break;
            case 10:
                $menu['shortname'] = 'forum';
                $menu['strname'] = 'tmenu_forum';
                $menu['parent'] = '0';
                $menu['url'] = $CFG->wwwroot.'/blocks/smartmenu/format/forums/view.php';
                $menu['params'] = array('id'=>'-1');
                $menu['icon'] = 'fa-comments';
                $menu['color'] = $color1;
                break;
            case 11:
                $menu['shortname'] = 'survey';
                $menu['strname'] = 'tmenu_survey';
                $menu['parent'] = '0';
                $menu['url'] = $CFG->wwwroot.'/blocks/smartmenu/format/surveys/view.php';
                $menu['params'] = array('id'=>'-1');
                $menu['icon'] = 'fa-bar-chart';
                $menu['color'] = $color1;
                break;
            case 12:
                $menu['shortname'] = 'grade';
                $menu['strname'] = 'tmenu_grade';
                $menu['parent'] = '0';
                $menu['url'] = $CFG->wwwroot.'/grade/report/grader/index.php';
                $menu['params'] = array('id'=>'-1');
                $menu['icon'] = 'fa-table';
                $menu['color'] = $color1;
                break;
            default :
                break;
        }
        $menu['id'] = $i;
        $menu['sortorder'] = $i;
        $menu['visible'] = 1;        
        
        $serialize = serialize($menu);
        set_config($name, $serialize, 'block_smartmenu');
        $i++;
    }

    
    //Student menu
    $color1 = '#1D335D';
    $color2 = '#FA820F';
    $color3 = '#FA6A6A';
    
    $i = 1;
    while($i <= 10){
        $menu = array();
        $name = 'smenu'.$i;
        switch($i){
            case 1:
                $menu['shortname'] = 'info';
                $menu['strname'] = 'smenu_information';
                $menu['parent'] = '0';
                $menu['url'] = $CFG->wwwroot.'/blocks/smartmenu/information.php';
                $menu['params'] = array('id'=>'-1');
                $menu['icon'] = 'fa-info-circle';
                $menu['color'] = $color1;
                break;          
           case 2:
                $menu['shortname'] = 'bulletin';
                $menu['strname'] = 'tmenu_bulletin';
                $menu['parent'] = '0';
                $menu['url'] = $CFG->wwwroot.'/blocks/smartmenu/course_news.php';
                $menu['params'] = array('id'=>'-1');
                $menu['icon'] = 'fa-bullhorn';
                $menu['color'] = $color1;
                $menu['matchuri'] = array('/mod/forum/view.php');
                break;
            case 3:
                $menu['shortname'] = 'home';
                $menu['strname'] = 'tmenu_home';
                $menu['parent'] = '0';
                $menu['url'] = $CFG->wwwroot.'/course/view.php';
                $menu['params'] = array('id'=>'-1');
                $menu['icon'] = 'fa-home';
                $menu['color'] = $color1;
                break;
            case 4:
                $menu['shortname'] = 'content';
                $menu['strname'] = 'tmenu_content';
                $menu['parent'] = '0';
                $menu['url'] = '';
                $menu['params'] = '';
                $menu['icon'] = 'fa-desktop';
                $menu['color'] = $color2;
                break;
            case 5:
                $menu['shortname'] = 'file';
                $menu['strname'] = 'tmenu_file';
                $menu['parent'] = '4';
                $menu['url'] = $CFG->wwwroot.'/blocks/smartmenu/classes/list_files.php';
                if($display == 2){
                    $menu['url'] = $CFG->wwwroot.'/blocks/smartmenu/format/resources/view.php';
                }
                $menu['params'] = array('id'=>'-1');
                $menu['icon'] = 'fa-file';
                $menu['color'] = $color3;
                break;
            case 6:
                $menu['shortname'] = 'video';
                $menu['strname'] = 'tmenu_video';
                $menu['parent'] = '4';
                $menu['url'] = $CFG->wwwroot.'/blocks/smartmenu/classes/list_videos.php';
                if($display == 2){
                    $menu['url'] = $CFG->wwwroot.'/blocks/smartmenu/format/videos/view.php';
                }
                $menu['params'] = array('id'=>'-1');
                $menu['icon'] = 'fa-video-camera';
                $menu['color'] = $color3;
                break;
            case 7:
                $menu['shortname'] = 'quiz';
                $menu['strname'] = 'tmenu_quiz';
                $menu['parent'] = '4';
                $menu['url'] = $CFG->wwwroot.'/blocks/smartmenu/classes/list_quizzes.php';
                if($display == 2){
                    $menu['url'] = $CFG->wwwroot.'/blocks/smartmenu/format/quizzes/view.php';
                }
                $menu['params'] = array('id'=>'-1');
                $menu['icon'] = 'fa-check-square';
                $menu['color'] = $color3;
                break;
            case 8:
                $menu['shortname'] = 'forum';
                $menu['strname'] = 'tmenu_forum';
                $menu['parent'] = '0';
                $menu['url'] = $CFG->wwwroot.'/blocks/smartmenu/classes/list_forums.php';
                if($display == 2){
                    $menu['url'] = $CFG->wwwroot.'/blocks/smartmenu/format/forums/view.php';
                }
                $menu['params'] = array('id'=>'-1');
                $menu['icon'] = 'fa-comments';
                $menu['color'] = $color1;
                break;
            case 9:
                $menu['shortname'] = 'survey';
                $menu['strname'] = 'tmenu_survey';
                $menu['parent'] = '0';
                $menu['url'] = $CFG->wwwroot.'/blocks/smartmenu/classes/list_surveys.php';
                if($display == 2){
                    $menu['url'] = $CFG->wwwroot.'/blocks/smartmenu/format/surveys/view.php';
                }
                $menu['params'] = array('id'=>'-1');
                $menu['icon'] = 'fa-bar-chart';
                $menu['color'] = $color1;
                break;
            case 10:
                $menu['shortname'] = 'grade';
                $menu['strname'] = 'smenu_grade';
                $menu['parent'] = '0';
                $menu['url'] = $CFG->wwwroot.'/grade/report/user/index.php';
                $menu['params'] = array('id'=>'-1');
                $menu['icon'] = 'fa-table';
                $menu['color'] = $color1;
                break;
            default :
                break;
        }
        $menu['id'] = $i;
        $menu['sortorder'] = $i;
        $menu['visible'] = 1;        
        
        $serialize = serialize($menu);
        set_config($name, $serialize, 'block_smartmenu');
        $i++;
    }

    return true;   
}