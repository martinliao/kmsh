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
require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->dirroot . '/course/lib.php');
require_once($CFG->dirroot . '/mod/forum/lib.php');

$id = optional_param('id', 0, PARAM_INT);

$url = new moodle_url('/blocks/smartmenu/courses_news.php', array('id'=>$id));
$PAGE->set_url($url);

if ($id) {
    if (! $course = $DB->get_record('course', array('id' => $id))) {
        print_error('invalidcourseid');
    }
} else {
    $course = get_site();
}

require_course_login($course);
$PAGE->set_pagelayout('course');

$str_forums       = get_string('forums', 'forum');
$str_forum        = get_string('forum', 'forum');
$str_description  = get_string('description');
$str_discussions  = get_string('discussions', 'forum');

$searchform = forum_search_form($course);
$digestoptions = forum_get_user_digest_options();

$generaltable = new html_table();
$generaltable->head  = array ($str_forum, $str_description, $str_discussions);
$generaltable->align = array ('left', 'left', 'center');
$generaltable->width = '100%';
$generaltable->attributes = array('style'=>'display: table;', 'class'=>'');

$usesections = course_format_uses_sections($course->format);
$table = new html_table();
/*
$forums = $DB->get_records_sql("SELECT f.*, d.maildigest
                                FROM {forum} f
                                LEFT JOIN {forum_digests} d ON d.forum = f.id AND d.userid = ?
                                WHERE f.course = ? ", array($USER->id, $course->id));
*/
$forums = $DB->get_records('forum', array('type'=>'news','course'=>$course->id));
//auto create news forum

if (!$forums) {
    $fourm = forum_get_course_forum($course->id, 'news');
    $forums = $DB->get_records('forum', array('id'=>$fourm->id));
}
                                
$generalforums  = array();
$modinfo = get_fast_modinfo($course);
foreach ($modinfo->get_instances_of('forum') as $forumid=>$cm) {
    if (!$cm->uservisible or !isset($forums[$forumid])) {
        continue;
    }

    $forum = $forums[$forumid];
    
    if (!$context = context_module::instance($cm->id, IGNORE_MISSING)) {
        continue;   // Shouldn't happen
    }
    
    if (!has_capability('mod/forum:viewdiscussion', $context)) {
        continue;
    }

    // fill two type array - order in modinfo is the same as in course
    if ($forum->type == 'news') {
        $generalforums[$forum->id] = $forum;
    }
}

if ($generalforums) {
    foreach ($generalforums as $forum) {
        if(sizeof($generalforums) > 1){
            $cm      = $modinfo->instances['forum'][$forum->id];
            $count = forum_count_discussions($forum, $cm, $course);
            
            $forum->intro = shorten_text(format_module_intro('forum', $forum, $cm->id), $CFG->forum_shortpost);
            if ($cm->visible) {
                $style = '';
            } else {
                $style = 'class="dimmed"';
            }
            $forumlink = "<a href=\"$CFG->wwwroot/mod/forum/view.php?f=$forum->id\" $style>".format_string($forum->name,true)."</a>";
            $discussionlink = "<a href=\"$CFG->wwwroot/mod/forum/view.php?f=$forum->id\" $style>".$count."</a>";

            $row = array ($forumlink, $forum->intro, $discussionlink);
            $generaltable->data[] = $row;
        }else{
            $url = $CFG->wwwroot.'/mod/forum/view.php?f='.$forum->id;
            redirect($url);
        }
    }
}

/// Output the page
$PAGE->navbar->add($str_forums);
$PAGE->set_title("$course->shortname: $str_forums");
$PAGE->set_heading($course->fullname);
$PAGE->set_button($searchform);
echo $OUTPUT->header();

echo $OUTPUT->heading(get_string('course_news', 'block_smartmenu'), 2);
echo html_writer::table($generaltable);

echo $OUTPUT->footer();