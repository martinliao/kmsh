<?php
/**
 * Section edit
 * @package    block
 * @subpackage smartmenu
 * 
 * @author     Mary Chen (mary@click-ap.com)
 * @author     Jack (jack@click-ap.com)
 * @copyright  Click-AP {@line https://www.click-ap.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v2 or later
 */
require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->dirroot.'/course/format/lib.php');
global $COURSE, $PAGE, $OUTPUT;

$id = required_param('id', PARAM_NUMBER);

$course = $DB->get_record('course', array('id' => $id));
$context = context_course::instance($course->id, MUST_EXIST);
$course = course_get_format($course)->get_course();
$numsections = course_get_format($course)->get_last_section_number();
if (!$course) {
    print_error('invalidcourseid', 'error');
}
$returnurl = $CFG->wwwroot."/blocks/smartmenu/edit_section.php?id=$id";
$PAGE->set_url($returnurl);
$PAGE->set_course($course);

require_login($course);
require_capability('moodle/course:update', $context);
$selectedsectionids = array();
$params = (array) data_submitted();
foreach ($params as $key => $value) {
    if (preg_match('!^s([0-9]+)$!', $key, $matches)) {
        $selectedsectionids[] = $matches[1];
    }
}

if (optional_param('returntocourse', null, PARAM_TEXT)) {
    redirect(new moodle_url('/blocks/smartmenu/course_section.php?id='.$course->id));
}

if (optional_param('addnewsectionafterselected', null, PARAM_CLEAN) && confirm_sesskey()) { 
    if(empty($selectedsectionids)){
        if(($numsections + 1) > 52){
            echo notice('<p>The section max value is 52.</p>', new moodle_url($returnurl));
        }else{
            $counter = 1;
            $newsection = new stdClass();
            $newsection->course = $course->id;
            $newsection->section = $numsections + 1; 
            $newsection->id = $DB->insert_record('course_sections', $newsection, true);
        }
    }else{
        $counter = 0;
        if(($numsections + count($selectedsectionids)) > 52){
            echo notice('<p>The section max value is 52.</p>', new moodle_url($returnurl));
        }else{
            foreach ($selectedsectionids as $sectionid) {
                $thissection = $DB->get_record('course_sections', array('id'=>$sectionid));
                
                $newsection = new stdClass();
                $newsection->course = $course->id;
                $newsection->section = $thissection->section + 1;
                
                $aftersections = $DB->get_records_select('course_sections', 'course = :course AND section > :thissection', array('course'=>$course->id,'thissection'=>$thissection->section),'section desc');
                foreach($aftersections as $data){
                    $DB->set_field('course_sections', 'section', $data->section + 1, array('id' => $data->id));
                }
                
                $newsection->id = $DB->insert_record('course_sections', $newsection, true);
                $counter++;
            }
        }
    }
}
//delete
if (optional_param('sectiondeleteselected', false, PARAM_BOOL) &&
        !empty($selectedsectionids) && confirm_sesskey()) {
    $return = '';

    if($numsections > count($selectedsectionids))  {
        $zerosection = $DB->get_record('course_sections', array('section'=>0, 'course' => $course->id));
        foreach ($selectedsectionids as $sectionid) {
            $sequence = $DB->get_field('course_sections', 'sequence', array('id' =>$sectionid));
            if (!empty($sequence)) {
                $zerosection->sequence .= ',' . $sequence;
                $return .= $DB->update_record('course_sections', $zerosection);
                $update = "UPDATE {course_modules} set section = :newsection WHERE course = :courseid AND section = :section";
                $return .= $DB->execute($update, array('newsection'=>$zerosection->id, 'courseid'=>$course->id, 'section'=>$sectionid));
            }
            $return .= $DB->delete_records('course_sections', array('id' =>$sectionid));
        }
        $sections = $DB->get_records_select('course_sections', 'course = :courseid AND section <= 52', array('courseid'=>$course->id), 'section asc');
        $cnt = 0;
        foreach ($sections as $section) {
            $section->section = $cnt;
            $return .= $DB->update_record('course_sections', $section);
            $cnt++;
        }
    }else{
        echo notice('<p>You can not delete all section.</p>', new moodle_url($returnurl));
    }
    redirect($returnurl);
}
//rename
if (optional_param('savechanges', false, PARAM_BOOL) && confirm_sesskey()) {
    $sections = array(); // For sections in the new order.
    $sectionnames = array(); // For sections in the new order.
    $rawdata = (array) data_submitted();

    foreach ($rawdata as $key => $value) {
        if (preg_match('!^o(pg)?([0-9]+)$!', $key, $matches)) {
            // Parse input for ordering info.
            $sectionid = $matches[2];
            // Make sure two sections don't overwrite each other. If we get a second
            // section with the same position, shift the second one along to the next gap.
            $value = clean_param($value, PARAM_INTEGER);
            $sections[$value] = $sectionid;
        } else if (preg_match('!^n(pg)?([0-9]+)$!', $key, $namematches)) {
            // Parse input for ordering info.
            $section_id = $namematches[2];
            // Make sure two sections don't overwrite each other. If we get a second
            // section with the same position, shift the second one along to the next gap.
            $value = clean_param($value, PARAM_TEXT);
            $sectionnames[$section_id] = $value;
        }
    }
    
    // If ordering info was given, reorder the sections.
    if ($sections) {
        ksort($sections);
        $counter = 0;
        foreach ($sections as $rank => $sectionid) {     
            //if(){   
                $counter++;
                $DB->set_field('course_sections', 'section', $counter * 100, array('course' => $course->id, 'id' => $sectionid));
            //}
        }
           $sql = "UPDATE {course_sections} set section = section / 100
                   WHERE course = '$course->id'
                   AND section <> 0";
           $DB->execute($sql);
    }
    // If ordering info was given, reorder the sections.
    if ($sectionnames) {
        foreach ($sectionnames as $sectionid => $sectionname) {
            if ($sectionname !== "Untitled") {
                $DB->set_field('course_sections', 'name', $sectionname, array('course' => $course->id, 'id' => $sectionid));
            }
        }
    }
    rebuild_course_cache($course->id, true);
    redirect(new moodle_url('/blocks/smartmenu/course_section.php?id='.$course->id));
}
// End of process commands =====================================================
rebuild_course_cache($course->id, true);
$numsections = course_get_format($course)->get_last_section_number();

$PAGE->set_pagelayout('course');
$PAGE->set_pagetype('course-view-sections');
$PAGE->set_title(get_string('editallsection', 'block_smartmenu'));
$PAGE->set_heading($course->fullname);
$PAGE->navbar->add(get_string('section'));
echo $OUTPUT->header();

$sections = $DB->get_records('course_sections', array('course' =>$course->id),'section asc');
echo $OUTPUT->box(get_string('editsections_help', 'block_smartmenu'), array('class' => 'generalbox'),'intro');
section_print_section_list($sections, $returnurl, $course->id, $numsections);

echo $OUTPUT->footer();

/**
 * Prints a list of sections for the edit.php main view for edit
 *
 * @param moodle_url $pageurl The url of the current page with the parameters required
 *     for links returning to the current page, as a moodle_url object
 */
function section_print_section_list($sections, $thispageurl, $courseid, $numsections) {
    global $CFG, $DB, $OUTPUT;
    
    //$strreturn = get_string('returntocourse');
    $str_remove = get_string('section-delete', 'block_smartmenu');
    $str_reordersections = get_string('section');
    $str_submitsections = get_string('section-save', 'block_smartmenu');       
    $str_addnewsectionafterselected = get_string('section-add', 'block_smartmenu');
    $str_areyousureremoveselected = get_string('section-deleteconfirm','block_smartmenu');
    $str_cancel = get_string('section-cancel', 'block_smartmenu');

    foreach ($sections as $section) {
        unset($sections[$section->id]);
        $order[] = $section->section;
        $sections[$section->section] = $section;
    }

    $lastindex = count($order) - 1;

    $reordercontrolssetdefaultsubmit = '<span class="nodisplay">' .
            '<input type="submit" name="savechanges" value="' . 
            $str_reordersections . '" /></span>';

    $reordercontrols1 = '<span class="sectiondeleteselected">' .
            '<input type="submit" name="sectiondeleteselected" ' .
            'onclick="return confirm(\'' .
            $str_areyousureremoveselected . '\');" style="background-color: #ffb2b2" value="' . $str_remove . '" /></span>';

    $reordercontrols1 .= '<span class="addnewsectionafterselected">' .
            '<input type="submit" name="addnewsectionafterselected" value="' .
            $str_addnewsectionafterselected . '" /></span>';

     $reordercontrols3 = '<span class="nameheader"></span>';               

             $reordercontrolstop = '<div class="reordercontrols">' .
                    $reordercontrols1 . $reordercontrols3 . "</div>";
            
    $reordercontrols4 = '<span class="returntocourse">' .
            '<input type="submit" name="returntocourse" value="' .
            $str_cancel . '" /></span>';

    $reordercontrols2bottom = '<span class="moveselectedonpage">' .
            '<input type="submit" name="savechanges" value="' .
            $str_submitsections . '" /></span>';            
                      
    $reordercontrolsbottom = '<br /><br /><div class="reordercontrols">' .
            $reordercontrols2bottom .$reordercontrols4 .  "</div>";
            
    echo '<div class="editsections">';
    echo '<form method="post" action="edit_section.php" id="sections"><div>';

    echo '<input type="hidden" name="sesskey" value="' . sesskey() . '" />';
    echo '<input type="hidden" name="id" value="' . $courseid . '" />';
    echo '<input type="hidden" name="pageurl" value="' . $thispageurl . '" />';

    echo $reordercontrolstop;
    $sectiontotalcount = count($order);

    // The current section ordinal (no descriptions).
    foreach ($order as $count => $sectnum) {
        $reordercheckbox = '';
        $reordercheckboxlabel = '';
        $reordercheckboxlabelclose = '';
        if ($sectnum != 0) {
            $section = $sections[$sectnum];
            if($section->section <= $numsections){
                // This is an actual section.
                ?>
                <div class="section">
                    <span class="sectioncontainer">
                        <span class="sectnum">
                            <?php
                            $reordercheckbox = '<input type="checkbox" name="s' . $section->id .
                                '" id="s' . $section->id . '" />';
                            $reordercheckboxlabel = '<label for="s' . $section->id . '" style="display: inline">';
                            $reordercheckboxlabelclose = '</label>';
                            $sectionnum = get_string('sectionnumber','block_smartmenu',$sectnum);
                            echo $reordercheckboxlabel . $reordercheckbox . $sectionnum .' '. section_tostring($section, $lastindex, $sectnum) . $reordercheckboxlabelclose;

                            ?>
                        </span>
                        <span class="content">
                            <span class="sectioncontentcontainer">
                                <?php
                                   // print_section_reordertool($section, $lastindex, $sno);
                                ?>
                            </span>
                        </span>
                </span>           
            </div>
            <?php
            }
        }
    }
    echo $reordercontrolsbottom;
    echo '</div></form></div>';
}

/**
 * Print a given single section in quiz for the reordertool tab of edit.php.
 * Meant to be used from quiz_print_section_list()
 *
 * @param object $section A section object from the database sections table
 * @param object $sectionurl The url of the section editing page as a moodle_url object
 * @param object $quiz The quiz in the context of which the section is being displayed
 */
function print_section_reordertool($section, $lastindex, $sno) {
    echo '<span class="singlesection ">';
    echo '<label for="n' . $section->id . '">';
    echo ' ' . section_tostring($section, $lastindex, $sno);
    echo '</label>';
    echo "</span>\n";
}

/**
 * Creates a textual representation of a section for display.
 *
 * @param object $section A section object from the database sections table
 * @param bool $showicon If true, show the section's icon with the section. False by default.
 * @param bool $showsectiontext If true (default), show section text after section name.
 *       If false, show only section name.
 * @param bool $return If true (default), return the output. If false, print it.
 */
function section_tostring($section, $lastindex, $sno, $showicon = false,
        $showsectiontext = true, $return = true) {
    global $COURSE;

    $result = '<span class="">';
    if ($section->name == '') {
        $result .= '<input type="text" name="n' . $section->id .
                                '" value="" tabindex="' . ($lastindex + $sno) . '" /></span>';
    } else {
        $result .= '<input type="text" name="n' . $section->id .
                                '" value="' . strip_tags($section->name) .
                                '" tabindex="' . ($lastindex + $sno) . '" /></span>';
    }
    if ($return) {
        return $result;
    } else {
        echo $result;
    }
}