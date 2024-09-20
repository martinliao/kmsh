<?php
/**
 * Version details.
 *
 * @package    blocks
 * @subpackage smartmenu
 * @author     Mary Chen (mary@click-ap.com)
 * @author     Jack (jack@click-ap.com)
 * @copyright  Click-AP {@line https://www.click-ap.com}
 * @license    http://www.click-ap.com/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

class block_smartmenu extends block_base {
    
    public $blockname = null;
    
    function init() {
        $this->blockname = get_class($this);
        $this->title = get_string('pluginname', 'block_smartmenu');
        $this->config = get_config('block_smartmenu');
    }

    function applicable_formats() {
        return array('all' => false, 'course' => true);
    }

    function hide_header() {
        return true;
    }
    function instance_allow_multiple() {   //only one
        if($this->config->course){
            return true;
        }
        return false;
    }
    /**
     * This block cannot be hidden by default as it is integral to the mooc-course of Moodle.
     *
     * @return false
     */
    function instance_can_be_hidden() {
        return false;
    }
    
    function has_config() {
        return true;
    }
    
    function user_can_edit() {
        if($this->config->course){
            return true;
        }
        return false;
    }
    
    function get_content() {
        global $CFG, $DB, $USER, $PAGE; 
        require_once($CFG->dirroot .'/blocks/smartmenu/locallib.php');
        
        if($this->content !== NULL) {
            return $this->content;
        }
        
        $this->content = new stdClass;
        $this->content->text = '';
        $this->content->footer = '';

        $course = $this->page->course;
        $context = context_course::instance($course->id, MUST_EXIST);
        if($course->id == SITEID){
            $this->content->text = '';
            return $this->content;
        }
        
        $switchrole = '';
        if(!empty($USER->access['rsw'][$context->path])){
            $roleid = $USER->access['rsw'][$context->path];
            $switchrole = $DB->get_field('role', 'archetype', array('id'=>$roleid));
        }
        
        $content = '';
        $content .= html_writer::start_tag('div', array('id' => 'smartmenudiv', 'class' => 'menulist'));
        
        if((has_capability('moodle/course:update', $context) && $switchrole == '') 
            || has_capability('moodle/course:bulkmessaging', $context)){//editteacher or teacher
            list($mainmenu, $submenu) = block_smartmenu_list($course, 'tmenu', 12);
            
        }else if(!has_capability('moodle/course:update', $context) 
            || $switchrole == 'student' || $switchrole == 'auditor'){
            list($mainmenu, $submenu) = block_smartmenu_list($course, 'smenu', 10);
        }
        
        //$PAGE->requires->css('/blocks/smartmenu/css/font-awesome.min.css');
        //$PAGE->requires->css('/blocks/smartmenu/css/styles.css');
        $PAGE->requires->js('/blocks/smartmenu/module.js');
        
        $config = get_config('block_smartmenu');
        if(isset($mainmenu)){
            $urlpath = $_SERVER['SCRIPT_NAME'];
            $menudata = $this->showmenu($mainmenu, $submenu, $urlpath, $config);
            $content .= $menudata[0];
        }
        $content .= html_writer::end_tag('div');
        
        $this->content->text = $content;
        return $this->content;
    }
    
    function showmenu($mainmenu, $submenu, $urlpath, $config){
        $content = '';
        
        foreach($mainmenu as $name => $htmlstr){
            $displaystyle = "display: block;";
            $content .= $htmlstr;
            if(isset($submenu[$name])){
                $subcontent = '';
                foreach($submenu[$name] as $name2 => $htmlstr2){
                    $pos = strpos($htmlstr2, 'SwitchCourseMenu');
                    if(!$display = strpos($htmlstr2, $urlpath)){
                        if(isset($config->$name2)){
                            $setting = unserialize($config->$name2);
                            if (isset($setting['matchuri'])){
                                $display = block_smartmenu_matchuri($setting['matchuri'], htmlspecialchars_decode($this->page->url->out()));
                            }
                        }
                    }
                    if($displaystyle != 'display: block;' && $display){
                        $displaystyle = "display: block;";
                    }
                    if ($pos === false) {
                        $subcontent .= $htmlstr2;
                    }else{
                        $menudata = $this->showmenu(array($name2 =>$htmlstr2), $submenu, $urlpath, $config);
                        $subcontent .= $menudata[0];
                        if($menudata[1] == 'display: block;'){
                            $displaystyle = "display: block;";
                        }
                    }
                }
                $content .= html_writer::start_tag('span', array('id'=>$name, 'class' => 'submenu', 'style' => $displaystyle));
                $content .= $subcontent;
                $content .= html_writer::end_tag('span');
            }
            
        }
        return array($content, $displaystyle);
    }
}