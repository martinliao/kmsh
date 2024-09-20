<?php
/**
 * Version details.
 *
 * @package    clickap
 * @subpackage program
 * @copyright  2018 Click-AP <elaine@click-ap.com>
 * @license    http://www.click-ap.com/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->libdir . '/tablelib.php');

/**
 * Standard HTML output renderer for programs
 */
class clickap_program_renderer extends plugin_renderer_base {

    // Outputs programs list.
    public function print_programs_list($programs, $userid, $profile = false, $external = false) {
        global $USER, $CFG;
        foreach ($programs as $program) {
            if (!$external) {
                $context = ($program->type == PROGRAM_TYPE_SITE) ? context_system::instance() : context_course::instance($program->courseid);
                $bname = $program->name;
                $imageurl = moodle_url::make_pluginfile_url($context->id, 'programs', 'medal', $program->id, '/', 'f1', false);
            } else {
                $bname = s($program->assertion->program->name);
                $imageurl = $program->imageUrl;
            }

            $name = html_writer::tag('span', $bname, array('class' => 'program-name'));

            $image = html_writer::empty_tag('img', array('src' => $imageurl, 'class' => 'program-image'));
            if (!empty($program->dateexpire) && $program->dateexpire < time()) {
                $image .= $this->output->pix_icon('i/expired',
                        get_string('expireddate', 'programs', userdate($program->dateexpire)),
                        'moodle',
                        array('class' => 'expireimage'));
                $name .= '(' . get_string('expired', 'programs') . ')';
            }

            $download = $status = $push = '';
            if (($userid == $USER->id) && !$profile) {
                $url = new moodle_url('myprograms.php', array('download' => $program->id, 'hash' => $program->uniquehash, 'sesskey' => sesskey()));
                $notexpiredprogram = (empty($program->dateexpire) || $program->dateexpire > time());
                $backpackexists = programs_user_has_backpack($USER->id);
                if (!empty($CFG->programs_allowexternalbackpack) && $notexpiredprogram && $backpackexists) {
                    $assertion = new moodle_url('/admin/clickap/programs/assertion.php', array('b' => $program->uniquehash));
                    $action = new component_action('click', 'addtobackpack', array('assertion' => $assertion->out(false)));
                    $push = $this->output->action_icon(new moodle_url('#'), new pix_icon('t/backpack', get_string('addtobackpack', 'clickap_program')), $action);
                }

                $download = $this->output->action_icon($url, new pix_icon('t/download', get_string('download')));
                if ($program->visible) {
                    $url = new moodle_url('myprograms.php', array('hide' => $program->issuedid, 'sesskey' => sesskey()));
                    $status = $this->output->action_icon($url, new pix_icon('t/hide', get_string('makeprivate', 'clickap_program')));
                } else {
                    $url = new moodle_url('myprograms.php', array('show' => $program->issuedid, 'sesskey' => sesskey()));
                    $status = $this->output->action_icon($url, new pix_icon('t/show', get_string('makepublic', 'clickap_program')));
                }
            }

            if (!$profile) {
                $url = new moodle_url('/local/program/program.php', array('hash' => $program->uniquehash));
            } else {
                if (!$external) {
                    $url = new moodle_url('/admin/clickap/program/program.php', array('hash' => $program->uniquehash));
                } else {
                    $hash = hash('md5', $program->hostedUrl);
                    $url = new moodle_url('/admin/clickap/program/external.php', array('hash' => $hash, 'user' => $userid));
                }
            }
            $actions = html_writer::tag('div', $push . $download . $status, array('class' => 'program-actions'));
            $items[] = html_writer::link($url, $image . $actions . $name, array('title' => $bname));
        }

        return html_writer::alist($items, array('class' => 'programs'));
    }

    public function print_programs_table_list($programs, $userid, $completioncos) {
        global $USER, $CFG;
        
        $table = new html_table();
        $table->attributes = array('class'=>'admintable generaltable','style'=>'display: table;table-layout:fixed;');
        $table->head  = array('&nbsp;', get_string('programname', 'clickap_program'), get_string('degreeofcompletion', 'clickap_program'), get_string('awarddate', 'clickap_program'), '');
        $table->size  = array('5%', '40%', '10%', '20%', '20%');
        $table->align  = array('left', 'left', 'center', 'left', 'right');
        
        $cnt = 0;
        foreach ($programs as $program) {
            $pcos = programs_get_program_courses($program->id);
            $mcos = array_intersect($pcos, $completioncos);
            $completion = sizeof($mcos).'/'.sizeof($pcos);
            
            $list = array();
            $list[] = ++$cnt;
            if($program->status == PROGRAM_STATUS_ARCHIVED){
                $list[] = $program->name;
                $list[] = $completion;
            }else{
                $list[] = '<a href = "'.$CFG->wwwroot.'/local/program/courses.php?id='.$program->id.'" target="_blank">'.$program->name.'</a>';
                $list[] = '<a href = "'.$CFG->wwwroot.'/local/program/courses.php?id='.$program->id.'" target="_blank">'.$completion.'</a>';;
            }
            
            if(!empty($program->dateissued)){
                $list[] = date('Y/m/d', $program->dateissued);
            }else{
                $list[] = get_string('unfinished', 'clickap_program');
            }

            if(!empty($program->dateissued) && $userid == $USER->id){
                if(!empty($program->dateexpire) && $program->dateexpire < time()){
                    $list[] = get_string('expiredate', 'clickap_program' ,date('Y/m/d', $program->dateexpire));
                }
                else {
                    $list[] = '<a href = "'.$CFG->wwwroot.'/admin/clickap/program/award_download.php?id='.$program->id.'">'.get_string('download', 'clickap_program').'</a>';
                }
            }else{
                $list[] = '';
            }
            $table->data[] = new html_table_row($list);
        }

        return html_writer::table($table);;
    }

    // Recipients selection form.
    public function recipients_selection_form(user_selector_base $existinguc, user_selector_base $potentialuc) {
        $output = '';
        $formattributes = array();
        $formattributes['id'] = 'recipientform';
        $formattributes['action'] = $this->page->url;
        $formattributes['method'] = 'post';
        $output .= html_writer::start_tag('form', $formattributes);
        $output .= html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'sesskey', 'value' => sesskey()));

        $existingcell = new html_table_cell();
        $existingcell->text = $existinguc->display(true);
        $existingcell->attributes['class'] = 'existing';
        $actioncell = new html_table_cell();
        $actioncell->text  = html_writer::start_tag('div', array());
        $actioncell->text .= html_writer::empty_tag('input', array(
                    'type' => 'submit',
                    'name' => 'award',
                    'value' => $this->output->larrow() . ' ' . get_string('award', 'clickap_program'),
                    'class' => 'actionbutton')
                );
        $actioncell->text .= html_writer::end_tag('div', array());
        $actioncell->attributes['class'] = 'actions';
        $potentialcell = new html_table_cell();
        $potentialcell->text = $potentialuc->display(true);
        $potentialcell->attributes['class'] = 'potential';

        $table = new html_table();
        $table->attributes['class'] = 'recipienttable boxaligncenter';
        $table->data = array(new html_table_row(array($existingcell, $actioncell, $potentialcell)));
        $output .= html_writer::table($table);

        $output .= html_writer::end_tag('form');
        return $output;
    }

    // Prints a program overview infomation.
    public function print_program_overview($program, $context) {
        $display = "";

        // Program details.

        $display .= $this->heading(get_string('programdetails', 'clickap_program'), 3);
        $dl = array();
        $dl[get_string('name')] = $program->name;
        $dl[get_string('description', 'clickap_program')] = $program->description;
        $dl[get_string('createdon', 'search')] = userdate($program->timecreated);
        $dl[get_string('programimage', 'clickap_program')] = print_program_image($program, $context, 'large');
        $display .= $this->definition_list($dl);

        // Issuer details.
        $display .= $this->heading(get_string('issuerdetails', 'clickap_program'), 3);
        $dl = array();
        //$dl[get_string('issuername', 'clickap_program')] = $program->issuername;
        //$dl[get_string('contact', 'clickap_program')] = html_writer::tag('a', $program->issuercontact, array('href' => 'mailto:' . $program->issuercontact));
        $display .= $this->definition_list($dl);

        // Issuance details if any.
        $display .= $this->heading(get_string('issuancedetails', 'clickap_program'), 3);
        if ($program->can_expire()) {
            if ($program->expiredate) {
                $display .= get_string('expiredate', 'clickap_program', userdate($program->expiredate));
            } else if ($program->expireperiod) {
                if ($program->expireperiod < 60) {
                    $display .= get_string('expireperiods', 'clickap_program', round($program->expireperiod, 2));
                } else if ($program->expireperiod < 60 * 60) {
                    $display .= get_string('expireperiodm', 'clickap_program', round($program->expireperiod / 60, 2));
                } else if ($program->expireperiod < 60 * 60 * 24) {
                    $display .= get_string('expireperiodh', 'clickap_program', round($program->expireperiod / 60 / 60, 2));
                } else {
                    $display .= get_string('expireperiod', 'clickap_program', round($program->expireperiod / 60 / 60 / 24, 2));
                }
            }
        } else {
            $display .= get_string('noexpiry', 'clickap_program');
        }

        // Criteria details if any.
        $display .= $this->heading(get_string('bcriteria', 'clickap_program'), 3);
        if ($program->has_criteria()) {
            $display .= self::print_program_criteria($program);
        } else {
            $display .= get_string('nocriteria', 'clickap_program');
            if (has_capability('clickap/programs:configurecriteria', $context)) {
                $display .= $this->output->single_button(
                    new moodle_url('/admin/clickap/program/criteria.php', array('id' => $program->id)),
                    get_string('addcriteria', 'clickap_program'), 'POST', array('class' => 'activateprogram'));
            }
        }

        // Awards details if any.
        if (has_capability('clickap/programs:viewawarded', $context)) {
            $display .= $this->heading(get_string('awards', 'clickap_program'), 3);
            if ($program->has_awards()) {
                $url = new moodle_url('/admin/clickap/program/recipients.php', array('id' => $program->id));
                $a = new stdClass();
                $a->link = $url->out();
                $a->count = count($program->get_awards());
                $display .= get_string('numawards', 'clickap_program', $a);
            } else {
                $display .= get_string('noawards', 'clickap_program');
            }

            if (has_capability('clickap/programs:awardprogram', $context) &&
                $program->has_manual_award_criteria() &&
                $program->is_active()) {
                $display .= $this->output->single_button(
                        new moodle_url('/admin/clickap/program/award.php', array('id' => $program->id)),
                        get_string('award', 'dlickap_program'), 'POST', array('class' => 'activateprogram'));
            }
        }

        return html_writer::div($display, null, array('id' => 'program-overview'));
    }

    // Prints action icons for the program.
    public function print_program_table_actions($program, $context) {
        $actions = "";

        if (has_capability('clickap/programs:configuredetails', $context) && $program->has_criteria()) {
            // Activate/deactivate program.
            if ($program->status == PROGRAM_STATUS_INACTIVE || $program->status == PROGRAM_STATUS_INACTIVE_LOCKED) {
                // "Activate" will go to another page and ask for confirmation.
                $url = new moodle_url('/admin/clickap/program/action.php');
                $url->param('id', $program->id);
                $url->param('activate', true);
                $url->param('sesskey', sesskey());
                $return = new moodle_url(qualified_me());
                $url->param('return', $return->out_as_local_url(false));
                $actions .= $this->output->action_icon($url, new pix_icon('t/show', get_string('activate', 'clickap_program'))) . " ";
            } else {
                $url = new moodle_url(qualified_me());
                $url->param('lock', $program->id);
                $url->param('sesskey', sesskey());
                $actions .= $this->output->action_icon($url, new pix_icon('t/hide', get_string('deactivate', 'clickap_program'))) . " ";
            }
        }

        // Award program manually.
        if ($program->has_manual_award_criteria() &&
                has_capability('clickap/programs:awardprogram', $context) &&
                $program->is_active()) {
            $url = new moodle_url('/admin/clickap/program/award.php', array('id' => $program->id));
            $actions .= $this->output->action_icon($url, new pix_icon('t/award', get_string('award', 'clickap_program'))) . " ";
        }

        // Edit program.
        if (has_capability('clickap/programs:configuredetails', $context)) {
            $url = new moodle_url('/admin/clickap/program/edit.php', array('id' => $program->id, 'action' => 'details'));
            $actions .= $this->output->action_icon($url, new pix_icon('t/edit', get_string('edit'))) . " ";
        }

        // Duplicate program.
        if (has_capability('clickap/programs:createprogram', $context)) {
            $url = new moodle_url('/admin/clickap/program/action.php', array('copy' => '1', 'id' => $program->id, 'sesskey' => sesskey()));
            $actions .= $this->output->action_icon($url, new pix_icon('t/copy', get_string('copy'))) . " ";
        }

        // Delete program.
        if (has_capability('clickap/programs:deleteprogram', $context)) {
            $url = new moodle_url(qualified_me());
            $url->param('delete', $program->id);
            $actions .= $this->output->action_icon($url, new pix_icon('t/delete', get_string('delete'))) . " ";
        }

        return $actions;
    }

    // Outputs issued program with actions available.
    protected function render_issued_program(issued_program $iprogram) {
        global $USER, $CFG, $DB, $SITE;
        $issued = $iprogram->issued;
        $userinfo = $iprogram->recipient;
        $programclass = $iprogram->programclass;
        $program = new program($iprogram->programid);
        $now = time();
        $expiration = isset($issued['expires']) ? $issued['expires'] : $now + 86400;

        $output = '';
        $output .= html_writer::start_tag('div', array('id' => 'program'));
        $output .= html_writer::start_tag('div', array('id' => 'program-image'));
        $output .= html_writer::empty_tag('img', array('src' => $programclass['image'], 'alt' => $program->name));
        if ($expiration < $now) {
            $output .= $this->output->pix_icon('i/expired',
            get_string('expireddate', 'clickap_program', userdate($issued['expires'])),
                'moodle',
                array('class' => 'expireimage'));
        }

        if ($USER->id == $userinfo->id) {
            $output .= $this->output->single_button(
                        new moodle_url('/admin/clickap/program/program.php', array('hash' => $issued['uid'], 'bake' => true)),
                        get_string('download'),
                        'POST');
            if (!empty($CFG->programs_allowexternalbackpack) && ($expiration > $now) && programs_user_has_backpack($USER->id)) {
                $assertion = new moodle_url('/admin/clickap/program/assertion.php', array('b' => $issued['uid']));
                $action = new component_action('click', 'addtobackpack', array('assertion' => $assertion->out(false)));
                $attributes = array(
                        'type'  => 'button',
                        'id'    => 'addbutton',
                        'value' => get_string('addtobackpack', 'clickap_program'));
                $tobackpack = html_writer::tag('input', '', $attributes);
                $this->output->add_action_handler($action, 'addbutton');
                $output .= $tobackpack;
            }
        }
        $output .= html_writer::end_tag('div');

        $output .= html_writer::start_tag('div', array('id' => 'program-details'));
        // Recipient information.
        $output .= $this->output->heading(get_string('recipientdetails', 'clickap_program'), 3);
        $dl = array();
        if ($userinfo->deleted) {
            $strdata = new stdClass();
            $strdata->user = fullname($userinfo);
            $strdata->site = format_string($SITE->fullname, true, array('context' => context_system::instance()));

            $dl[get_string('name')] = get_string('error:userdeleted', 'clickap_program', $strdata);
        } else {
            $dl[get_string('name')] = fullname($userinfo);
        }
        $output .= $this->definition_list($dl);

        $output .= $this->output->heading(get_string('issuerdetails', 'clickap_program'), 3);
        $dl = array();
        $dl[get_string('issuername', 'clickap_program')] = $program->issuername;
        if (isset($program->issuercontact) && !empty($program->issuercontact)) {
            $dl[get_string('contact', 'clickap_program')] = obfuscate_mailto($program->issuercontact);
        }
        $output .= $this->definition_list($dl);

        $output .= $this->output->heading(get_string('programdetails', 'clickap_program'), 3);
        $dl = array();
        $dl[get_string('name')] = $program->name;
        $dl[get_string('description', 'clickap_program')] = $program->description;

        if ($program->type == PROGRAM_TYPE_COURSE && isset($program->courseid)) {
            $coursename = $DB->get_field('course', 'fullname', array('id' => $program->courseid));
            $dl[get_string('course')] = $coursename;
        }
        $dl[get_string('bcriteria', 'clickap_program')] = self::print_program_criteria($program);
        $output .= $this->definition_list($dl);

        $output .= $this->output->heading(get_string('issuancedetails', 'clickap_program'), 3);
        $dl = array();
        $dl[get_string('dateawarded', 'clickap_program')] = userdate($issued['issuedOn']);
        if (isset($issued['expires'])) {
            if ($issued['expires'] < $now) {
                $dl[get_string('expirydate', 'clickap_program')] = userdate($issued['expires']) . get_string('warnexpired', 'clickap_program');

            } else {
                $dl[get_string('expirydate', 'clickap_program')] = userdate($issued['expires']);
            }
        }

        // Print evidence.
        $agg = $program->get_aggregation_methods();
        $evidence = $program->get_criteria_completions($userinfo->id);
        //$eids = array_map(create_function('$o', 'return $o->critid;'), $evidence);
        $eids = array_map(function($o) {
            return $o->critid;
        }, $evidence);
        unset($program->criteria[PROGRAM_CRITERIA_TYPE_OVERALL]);

        $items = array();
        foreach ($program->criteria as $type => $c) {
            if (in_array($c->id, $eids)) {
                if (count($c->params) == 1) {
                    $items[] = get_string('criteria_descr_single_' . $type , 'clickap_program') . $c->get_details();
                } else {
                    $items[] = get_string('criteria_descr_' . $type , 'clickap_program',
                            core_text::strtoupper($agg[$program->get_aggregation_method($type)])) . $c->get_details();
                }
            }
        }

        $dl[get_string('evidence', 'clickap_program')] = get_string('completioninfo', 'clickap_program') . html_writer::alist($items, array(), 'ul');
        $output .= $this->definition_list($dl);
        $output .= html_writer::end_tag('div');

        return $output;
    }

    // Outputs external program.
    protected function render_external_program(external_program $iprogram) {
        $issued = $iprogram->issued;
        $assertion = $issued->assertion;
        $issuer = $assertion->program->issuer;
        $userinfo = $iprogram->recipient;
        $table = new html_table();
        $today = strtotime(date('Y-m-d'));

        $output = '';
        $output .= html_writer::start_tag('div', array('id' => 'program'));
        $output .= html_writer::start_tag('div', array('id' => 'program-image'));
        $output .= html_writer::empty_tag('img', array('src' => $issued->imageUrl));
        if (isset($assertion->expires)) {
            $expiration = !strtotime($assertion->expires) ? s($assertion->expires) : strtotime($assertion->expires);
            if ($expiration < $today) {
                $output .= $this->output->pix_icon('i/expired',
                        get_string('expireddate', 'clickap_program', userdate($expiration)),
                        'moodle',
                        array('class' => 'expireimage'));
            }
        }
        $output .= html_writer::end_tag('div');

        $output .= html_writer::start_tag('div', array('id' => 'program-details'));

        // Recipient information.
        $output .= $this->output->heading(get_string('recipientdetails', 'clickap_program'), 3);
        $dl = array();
        // Technically, we should alway have a user at this point, but added an extra check just in case.
        if ($userinfo) {
            if (!$iprogram->valid) {
                $notify = $this->output->notification(get_string('recipientvalidationproblem', 'clickap_program'), 'notifynotice');
                $dl[get_string('name')] = fullname($userinfo) . $notify;
            } else {
                $dl[get_string('name')] = fullname($userinfo);
            }
        } else {
            $notify = $this->output->notification(get_string('recipientidentificationproblem', 'clickap_program'), 'notifynotice');
            $dl[get_string('name')] = $notify;
        }
        $output .= $this->definition_list($dl);

        $output .= $this->output->heading(get_string('issuerdetails', 'clickap_program'), 3);
        $dl = array();
        $dl[get_string('issuername', 'clickap_program')] = s($issuer->name);
        $dl[get_string('issuerurl', 'clickap_program')] = html_writer::tag('a', $issuer->origin, array('href' => $issuer->origin));

        if (isset($issuer->contact)) {
            $dl[get_string('contact', 'clickap_program')] = obfuscate_mailto($issuer->contact);
        }
        $output .= $this->definition_list($dl);

        $output .= $this->output->heading(get_string('programdetails', 'clickap_program'), 3);
        $dl = array();
        $dl[get_string('name')] = s($assertion->program->name);
        $dl[get_string('description', 'clickap_program')] = s($assertion->program->description);
        $dl[get_string('bcriteria', 'clickap_program')] = html_writer::tag('a', s($assertion->program->criteria), array('href' => $assertion->program->criteria));
        $output .= $this->definition_list($dl);

        $output .= $this->output->heading(get_string('issuancedetails', 'clickap_program'), 3);
        $dl = array();
        if (isset($assertion->issued_on)) {
            $issuedate = !strtotime($assertion->issued_on) ? s($assertion->issued_on) : strtotime($assertion->issued_on);
            $dl[get_string('dateawarded', 'clickap_program')] = userdate($issuedate);
        }
        if (isset($assertion->expires)) {
            if ($expiration < $today) {
                $dl[get_string('expirydate', 'clickap_program')] = userdate($expiration) . get_string('warnexpired', 'clickap_program');
            } else {
                $dl[get_string('expirydate', 'clickap_program')] = userdate($expiration);
            }
        }
        if (isset($assertion->evidence)) {
            $dl[get_string('evidence', 'clickap_program')] = html_writer::tag('a', s($assertion->evidence), array('href' => $assertion->evidence));
        }
        $output .= $this->definition_list($dl);
        $output .= html_writer::end_tag('div');

        return $output;
    }

    // Displays the user programs.
    protected function render_program_user_collection(program_user_collection $programs) {
        global $CFG, $USER, $SITE;
        $backpack = $programs->backpack;
        $mybackpack = new moodle_url('/admin/clickap/program/mybackpack.php');

        $paging = new paging_bar($programs->totalcount, $programs->page, $programs->perpage, $this->page->url, 'page');
        $htmlpagingbar = $this->render($paging);

        // Set backpack connection string.
        $backpackconnect = '';
        if (!empty($CFG->programs_allowexternalbackpack) && is_null($backpack)) {
            $backpackconnect = $this->output->box(get_string('localconnectto', 'clickap_program', $mybackpack->out()), 'noticebox');
        }
        // Search box.
        $searchform = $this->output->box($this->helper_search_form($programs->search), 'boxwidthwide boxaligncenter');

        // Download all button.
        $downloadall = $this->output->single_button(
                    new moodle_url('/admin/clickap/program/myprograms.php', array('downloadall' => true, 'sesskey' => sesskey())),
                    get_string('downloadall'), 'POST', array('class' => 'activateprogram'));

        // Local programs.
        $localhtml = html_writer::start_tag('div', array('id' => 'issued-program-table', 'class' => 'generalbox'));
        $heading = get_string('localprograms', 'clickap_program', format_string($SITE->fullname, true, array('context' => context_system::instance())));
        $localhtml .= $this->output->heading_with_help($heading, 'localprogramsh', 'clickap_program');
        if ($programs->program) {
            $downloadbutton = $this->output->heading(get_string('programsearned', 'clickap_program', $programs->totalcount), 4, 'activateprogram');
            $downloadbutton .= $downloadall;

            $htmllist = $this->print_programs_list($progrsms->programs, $USER->id);
            $localhtml .= $backpackconnect . $downloadbutton . $searchform . $htmlpagingbar . $htmllist . $htmlpagingbar;
        } else {
            $localhtml .= $searchform . $this->output->notification(get_string('noprograms', 'clickap_program'));
        }
        $localhtml .= html_writer::end_tag('div');

        // External programs.
        $externalhtml = "";
        if (!empty($CFG->programs_allowexternalbackpack)) {
            $externalhtml .= html_writer::start_tag('div', array('class' => 'generalbox'));
            $externalhtml .= $this->output->heading_with_help(get_string('externalprograms', 'clickap_program'), 'externalprograms', 'clickap_program');
            if (!is_null($backpack)) {
                if ($backpack->totalcollections == 0) {
                    $externalhtml .= get_string('nobackpackcollections', 'clickap_program', $backpack);
                } else {
                    if ($backpack->totalprograms == 0) {
                        $externalhtml .= get_string('nobackpackprograms', 'clickap_program', $backpack);
                    } else {
                        $externalhtml .= get_string('backpackprograms', 'clickap_program', $backpack);
                        $externalhtml .= '<br/><br/>' . $this->print_programs_list($backpack->programs, $USER->id, true, true);
                    }
                }
            } else {
                $externalhtml .= get_string('externalconnectto', 'clickap_program', $mybackpack->out());
            }

            $externalhtml .= html_writer::end_tag('div');
        }

        return $localhtml . $externalhtml;
    }

    // Displays the available programs.
    protected function render_program_collection(program_collection $programs) {
        $paging = new paging_bar($programs->totalcount, $programs->page, $programs->perpage, $this->page->url, 'page');
        $htmlpagingbar = $this->render($paging);
        $table = new html_table();
        $table->attributes['class'] = 'collection';

        $sortbyname = $this->helper_sortable_heading(get_string('name'),
                'name', $programs->sort, $programs->dir);
        $sortbyawarded = $this->helper_sortable_heading(get_string('awardedtoyou', 'clickap_program'),
                'dateissued', $programs->sort, $programs->dir);
        $table->head = array(
                    get_string('image', 'clickap_program'),
                    $sortbyname,
                    get_string('description', 'clickap_program'),
                    get_string('bcriteria', 'clickap_program'),
                    $sortbyawarded
                );
        $table->colclasses = array('image', 'name', 'description', 'criteria', 'awards');

        foreach ($programs->programs as $program) {
            $programimage = print_program_image($program, $this->page->context, 'large');
            $name = $program->name;
            $description = $program->description;
            $criteria = self::print_program_criteria($program);
            if ($program->dateissued) {
                $icon = new pix_icon('i/valid',
                            get_string('dateearned', 'clickap_program',
                                userdate($program->dateissued, get_string('strftimedatefullshort', 'core_langconfig'))));
                $programurl = new moodle_url('/admin/clickap/program/program.php', array('hash' => $program->uniquehash));
                $awarded = $this->output->action_icon($programurl, $icon, null, null, true);
            } else {
                $awarded = "";
            }
            $row = array($programimage, $name, $description, $criteria, $awarded);
            $table->data[] = $row;
        }

        $htmltable = html_writer::table($table);

        return $htmlpagingbar . $htmltable . $htmlpagingbar;
    }

    // Outputs table of programs with actions available.
    protected function render_program_management(program_management $programs) {
        $paging = new paging_bar($programs->totalcount, $programs->page, $programs->perpage, $this->page->url, 'page');

        // New program button.
        $htmlnew = '';
        if (has_capability('clickap/programs:createprogram', $this->page->context)) {
            $n['type'] = $this->page->url->get_param('type');
            $n['id'] = $this->page->url->get_param('id');
            $htmlnew = $this->output->single_button(new moodle_url('newprogram.php', $n), get_string('newprogram', 'clickap_program'));
        }

        $htmlpagingbar = $this->render($paging);
        $table = new html_table();
        $table->attributes['class'] = 'collection';

        $sortbyname = $this->helper_sortable_heading(get_string('name'),
                'name', $programs->sort, $programs->dir);
        $sortbystatus = $this->helper_sortable_heading(get_string('status', 'clickap_program'),
                'status', $programs->sort, $programs->dir);
        $table->head = array(
                $sortbyname,
                $sortbystatus,
                get_string('bcriteria', 'clickap_program'),
                get_string('awards', 'clickap_program'),
                get_string('actions')
            );
        $table->colclasses = array('name', 'status', 'criteria', 'awards', 'actions');

        foreach ($programs->programs as $b) {
            $style = !$b->is_active() ? array('class' => 'dimmed') : array();
            $forlink =  print_program_image($b, $this->page->context) . ' ' .
                        html_writer::start_tag('span') . $b->name . html_writer::end_tag('span');
            $name = html_writer::link(new moodle_url('/admin/clickap/program/edit.php', array('id' => $b->id)), $forlink, $style);
            $status = $b->statstring;
            $criteria = self::print_program_criteria($b, 'short');

            if (has_capability('clickap/programs:viewawarded', $this->page->context)) {
                $awards = html_writer::link(new moodle_url('/admin/clickap/program/recipients.php', array('id' => $b->id)), $b->awards);
            } else {
                $awards = $b->awards;
            }

            $actions = self::print_program_table_actions($b, $this->page->context);

            $row = array($name, $status, $criteria, $awards, $actions);
            $table->data[] = $row;
        }
        $htmltable = html_writer::table($table);

        return $htmlnew . $htmlpagingbar . $htmltable . $htmlpagingbar;
    }

    // Prints tabs for program editing.
    public function print_program_tabs($programid, $context, $current = 'overview') {
        global $DB;

        $row = array();
        /*
        $row[] = new tabobject('overview',
                    new moodle_url('/admin/clcikap/program/overview.php', array('id' => $programid)),
                    get_string('boverview', 'clickap_program')
                );
        */
        if (has_capability('clickap/programs:configuredetails', $context)) {
            $row[] = new tabobject('details',
                        new moodle_url('/admin/clickap/program/edit.php', array('id' => $programid, 'action' => 'details')),
                        get_string('bdetails', 'clickap_program')
                    );
        }

        if (has_capability('clickap/programs:configurecriteria', $context)) {
            $row[] = new tabobject('criteria',
                        new moodle_url('/admin/clickap/program/criteria.php', array('id' => $programid)),
                        get_string('bcriteria', 'clickap_program')
                    );
            $row[] = new tabobject('pcategory',
                        new moodle_url('/admin/clickap/program/category/management.php', array('programid' => $programid)),
                        get_string('program_category', 'clickap_program')
                    );
        }

        if (has_capability('clickap/programs:configuremessages', $context)) {
            $row[] = new tabobject('message',
                        new moodle_url('/admin/clickap/program/edit.php', array('id' => $programid, 'action' => 'message')),
                        get_string('bmessage', 'clickap_program')
                    );
        }

        if (has_capability('clickap/programs:viewawarded', $context)) {
            $awarded = $DB->count_records_sql('SELECT COUNT(b.userid)
                                               FROM {program_issued} b INNER JOIN {user} u ON b.userid = u.id
                                               WHERE b.programid = :programid AND u.deleted = 0', array('programid' => $programid));
            $row[] = new tabobject('awards',
                        new moodle_url('/admin/clickap/program/recipients.php', array('id' => $programid)),
                        get_string('bawards', 'clickap_program', $awarded)
                    );
        }

        echo $this->tabtree($row, $current);
    }

    /**
     * Prints program status box.
     * @return Either the status box html as a string or null
     */
    public function print_program_status_box(program $program) {
        if (has_capability('clickap/programs:configurecriteria', $program->get_context())) {

            if (!$program->has_criteria()) {
                $criteriaurl = new moodle_url('/admin/clickap/program/criteria.php', array('id' => $program->id));
                $status = get_string('nocriteria', 'clickap_program');
                if ($this->page->url != $criteriaurl) {
                    $action = $this->output->single_button(
                        $criteriaurl,
                        get_string('addcriteria', 'clickap_program'), 'POST', array('class' => 'activateprogram'));
                } else {
                    $action = '';
                }

                $message = $status . $action;
            } else {
                $status = get_string('statusmessage_' . $program->status, 'clickap_program');
                if ($program->is_active()) {
                    $action = $this->output->single_button(new moodle_url('/admin/clickap/program/action.php',
                                array('id' => $program->id, 'lock' => 1, 'sesskey' => sesskey(),
                                      'return' => $this->page->url->out_as_local_url(false))),
                            get_string('deactivate', 'clickap_program'), 'POST', array('class' => 'activateprogram'));
                } else {
                    $action = $this->output->single_button(new moodle_url('/admin/clickap/program/action.php',
                                array('id' => $program->id, 'activate' => 1, 'sesskey' => sesskey(),
                                      'return' => $this->page->url->out_as_local_url(false))),
                            get_string('activate', 'clickap_program'), 'POST', array('class' => 'activateprogram'));
                }

                $message = $status . $this->output->help_icon('status', 'clickap_program') . $action;

            }

            $style = $program->is_active() ? 'generalbox statusbox active' : 'generalbox statusbox inactive';
            return $this->output->box($message, $style);
        }

        return null;
    }

    /**
     * Returns information about program criteria in a list form.
     *
     * @param program $program Program objects
     * @param string $short Indicates whether to print full info about this program
     * @return string $output HTML string to output
     */
    public function print_program_criteria(program $program, $short = '') {
        $agg = $program->get_aggregation_methods();
        if (empty($program->criteria)) {
            return get_string('nocriteria', 'clickap_program');
        }

        $overalldescr = '';
        $overall = $program->criteria[PROGRAM_CRITERIA_TYPE_OVERALL];
        if (!$short && !empty($overall->description)) {
            $overalldescr = $this->output->box(
                format_text($overall->description, $overall->descriptionformat, array('context' => $program->get_context())),
                'criteria-description'
                );
        }

        // Get the condition string.
        if (count($program->criteria) == 2) {
            $condition = '';
            if (!$short) {
                $condition = get_string('criteria_descr', 'clickap_program');
            }
        } else {
            $condition = get_string('criteria_descr_' . $short . PROGRAM_CRITERIA_TYPE_OVERALL, 'clickap_program',
                                      core_text::strtoupper($agg[$program->get_aggregation_method()]));
        }

        unset($program->criteria[PROGRAM_CRITERIA_TYPE_OVERALL]);

        $items = array();
        // If only one criterion left, make sure its description goe to the top.
        if (count($program->criteria) == 1) {
            $c = reset($program->criteria);
            if (!$short && !empty($c->description)) {
                $overalldescr = $this->output->box(
                    format_text($c->description, $c->descriptionformat, array('context' => $program->get_context())),
                    'criteria-description'
                    );
            }
            if (count($c->params) == 1) {
                $items[] = get_string('criteria_descr_single_' . $short . $c->criteriatype , 'clickap_program') .
                           $c->get_details($short);
            } else {
                $items[] = get_string('criteria_descr_' . $short . $c->criteriatype, 'clickap_program',
                        core_text::strtoupper($agg[$program->get_aggregation_method($c->criteriatype)])) .
                        $c->get_details($short);
            }
        } else {
            foreach ($program->criteria as $type => $c) {
                $criteriadescr = '';
                if (!$short && !empty($c->description)) {
                    $criteriadescr = $this->output->box(
                        format_text($c->description, $c->descriptionformat, array('context' => $program->get_context())),
                        'criteria-description'
                        );
                }
                if (count($c->params) == 1) {
                    $items[] = get_string('criteria_descr_single_' . $short . $type , 'clickap_program') .
                               $c->get_details($short) . $criteriadescr;
                } else {
                    $items[] = get_string('criteria_descr_' . $short . $type , 'clickap_program',
                            core_text::strtoupper($agg[$program->get_aggregation_method($type)])) .
                            $c->get_details($short) .
                            $criteriadescr;
                }
            }
        }

        return $overalldescr . $condition . html_writer::alist($items, array(), 'ul');;
    }

    // Prints criteria actions for program editing.
    public function print_criteria_actions(program $program) {
        $output = '';
        if (!$program->is_active() && !$program->is_locked()) {
            $accepted = $program->get_accepted_criteria();
            $potential = array_diff($accepted, array_keys($program->criteria));

            if (!empty($potential)) {
                foreach ($potential as $p) {
                    if ($p != 0) {
                        $select[$p] = get_string('criteria_' . $p, 'clickap_program');
                    }
                }
                $output .= $this->output->single_select(
                    new moodle_url('/admin/clickap/program/criteria_settings.php', array('programid' => $program->id, 'add' => true)),
                    'type',
                    $select,
                    '',
                    array('' => 'choosedots'),
                    null,
                    array('label' => get_string('addprogramcriteria', 'clickap_program'))
                );
            } else {
                $output .= $this->output->box(get_string('nothingtoadd', 'clickap_program'), 'clearfix');
            }
        }

        return $output;
    }

    // Renders a table with users who have earned the program.
    // Based on stamps collection plugin.
    protected function render_program_recipients(program_recipients $recipients) {
        $paging = new paging_bar($recipients->totalcount, $recipients->page, $recipients->perpage, $this->page->url, 'page');
        $htmlpagingbar = $this->render($paging);
        $table = new html_table();
        $table->attributes['class'] = 'generaltable boxaligncenter boxwidthwide';

        $sortbyfirstname = $this->helper_sortable_heading(get_string('firstname'),
                'firstname', $recipients->sort, $recipients->dir);
        $sortbylastname = $this->helper_sortable_heading(get_string('lastname'),
                'lastname', $recipients->sort, $recipients->dir);
        if ($this->helper_fullname_format() == 'lf') {
            $sortbyname = $sortbylastname . ' / ' . $sortbyfirstname;
        } else {
            $sortbyname = $sortbyfirstname . ' / ' . $sortbylastname;
        }

        $sortbydate = $this->helper_sortable_heading(get_string('dateawarded', 'clickap_program'),
                'dateissued', $recipients->sort, $recipients->dir);

        $table->head = array($sortbyname, $sortbydate, '');

        foreach ($recipients->userids as $holder) {
            $fullname = fullname($holder);
            $fullname = html_writer::link(
                            new moodle_url('/user/profile.php', array('id' => $holder->userid)),
                            $fullname
                        );
            $awarded  = userdate($holder->dateissued);
            $programurl = html_writer::link(
                            new moodle_url('/admin/clickap/program/program.php', array('hash' => $holder->uniquehash)),
                            get_string('viewprogram', 'clickap_program')
                        );

            $row = array($fullname, $awarded, $programurl);
            $table->data[] = $row;
        }

        $htmltable = html_writer::table($table);

        return $htmlpagingbar . $htmltable . $htmlpagingbar;
    }

    ////////////////////////////////////////////////////////////////////////////
    // Helper methods
    // Reused from stamps collection plugin
    ////////////////////////////////////////////////////////////////////////////

    /**
     * Renders a text with icons to sort by the given column
     *
     * This is intended for table headings.
     *
     * @param string $text    The heading text
     * @param string $sortid  The column id used for sorting
     * @param string $sortby  Currently sorted by (column id)
     * @param string $sorthow Currently sorted how (ASC|DESC)
     *
     * @return string
     */
    protected function helper_sortable_heading($text, $sortid = null, $sortby = null, $sorthow = null) {
        $out = html_writer::tag('span', $text, array('class' => 'text'));

        if (!is_null($sortid)) {
            if ($sortby !== $sortid || $sorthow !== 'ASC') {
                $url = new moodle_url($this->page->url);
                $url->params(array('sort' => $sortid, 'dir' => 'ASC'));
                $out .= $this->output->action_icon($url,
                        new pix_icon('t/sort_asc', get_string('sortbyx', 'core', s($text)), null, array('class' => 'iconsort')));
            }
            if ($sortby !== $sortid || $sorthow !== 'DESC') {
                $url = new moodle_url($this->page->url);
                $url->params(array('sort' => $sortid, 'dir' => 'DESC'));
                $out .= $this->output->action_icon($url,
                        new pix_icon('t/sort_desc', get_string('sortbyxreverse', 'core', s($text)), null, array('class' => 'iconsort')));
            }
        }
        return $out;
    }
    /**
     * Tries to guess the fullname format set at the site
     *
     * @return string fl|lf
     */
    protected function helper_fullname_format() {
        $fake = new stdClass();
        $fake->lastname = 'LLLL';
        $fake->firstname = 'FFFF';
        $fullname = get_string('fullnamedisplay', '', $fake);
        if (strpos($fullname, 'LLLL') < strpos($fullname, 'FFFF')) {
            return 'lf';
        } else {
            return 'fl';
        }
    }
    /**
     * Renders a search form
     *
     * @param string $search Search string
     * @return string HTML
     */
    protected function helper_search_form($search) {
        global $CFG;
        require_once($CFG->libdir . '/formslib.php');

        $mform = new MoodleQuickForm('searchform', 'POST', $this->page->url);

        $mform->addElement('hidden', 'sesskey', sesskey());

        $el[] = $mform->createElement('text', 'search', get_string('search'), array('size' => 20));
        $mform->setDefault('search', $search);
        $el[] = $mform->createElement('submit', 'submitsearch', get_string('search'));
        $el[] = $mform->createElement('submit', 'clearsearch', get_string('clear'));
        $mform->addGroup($el, 'searchgroup', get_string('searchname', 'clickap_program'), ' ', false);

        ob_start();
        $mform->display();
        $out = ob_get_clean();

        return $out;
    }

    /**
     * Renders a definition list
     *
     * @param array $items the list of items to define
     * @param array
     */
    protected function definition_list(array $items, array $attributes = array()) {
        $output = html_writer::start_tag('dl', $attributes);
        foreach ($items as $label => $value) {
            $output .= html_writer::tag('dt', $label);
            $output .= html_writer::tag('dd', $value);
        }
        $output .= html_writer::end_tag('dl');
        return $output;
    }

    /**
     * Opens a grid column
     *
     * @param int $size The number of segments this column should span.
     * @param string $id An id to give the column.
     * @param string $class A class to give the column.
     * @return string
     */
    public function grid_column_start($size, $id = null, $class = null) {

        // Calculate Bootstrap grid sizing.
        $bootstrapclass = 'span'.$size;

        // Calculate YUI grid sizing.
        if ($size === 12) {
            $maxsize = 1;
            $size = 1;
        } else {
            $maxsize = 12;
            $divisors = array(8, 6, 5, 4, 3, 2);
            foreach ($divisors as $divisor) {
                if (($maxsize % $divisor === 0) && ($size % $divisor === 0)) {
                    $maxsize = $maxsize / $divisor;
                    $size = $size / $divisor;
                    break;
                }
            }
        }
        if ($maxsize > 1) {
            $yuigridclass =  "col-sm d-flex flex-wrap px-3 mb-3 grid_column_start grid-col-{$size}-{$maxsize} grid-col";
        } else {
            $yuigridclass =  "col-sm d-flex flex-wrap px-3 mb-3 grid_column_start grid-col-1 grid-col";
        }

        if (is_null($class)) {
            $class = $yuigridclass . ' ' . $bootstrapclass;
        } else {
            $class .= ' ' . $yuigridclass . ' ' . $bootstrapclass;
        }
        $attributes = array();
        if (!is_null($id)) {
            $attributes['id'] = $id;
        }
        return html_writer::start_div($class, $attributes);
    }
    /**
     * Renderers the actions that are possible for the course category listing.
     *
     * These are not the actions associated with an individual category listing.
     * That happens through category_listitem_actions.
     *
     * @param coursecat $category
     * @return string
     */
    public function category_listing_actions($programid) {
        $actions = array();
        $url = new moodle_url('/admin/clickap/program/category/editcategory.php', array('programid' => $programid));
        $actions[] = html_writer::link($url, get_string('createnewcategory'));

        return html_writer::div(join(' | ', $actions), 'listing-actions category-listing-actions');
    }

    /**
     * Presents a course category listing.
     *
     * @param coursecat $category The currently selected category. Also the category to highlight in the listing.
     * @return string
     */
    public function category_listing($programid, $category) {
        global $DB;

        $attributes = array(
            'class' => 'ml',
            'role' => 'tree',
            'aria-labelledby' => 'category-listing-title'
        );

        $sql = "SELECT p.*, count(pc.id) as coursecount
                FROM {program_category} p
                LEFT JOIN {program_category_courses} pc ON pc.categoryid= p.id
                WHERE p.programid = :programid
                GROUP BY pc.categoryid, p.id
                ORDER BY p.sortorder";
        $listing = $DB->get_records_sql($sql, array('programid'=>$programid));
        
        $html  = html_writer::start_div('category-listing card w-100');
        $html .= html_writer::tag('h3', get_string('categories'), array('id' => 'category-listing-title'));
        $html .= $this->category_listing_actions($programid);
        $html .= html_writer::start_tag('ul', $attributes);
        foreach ($listing as $listitem) {
            $html .= $this->category_listitem(
                $programid, $listitem, $category->id
            );
        }
        $html .= html_writer::end_tag('ul');
        //$html .= $this->category_bulk_actions($category);
        $html .= html_writer::end_div();
        return $html;
    }

    /**
     * Returns an array of actions that can be performed upon a category being shown in a list.
     *
     * @param \coursecat $category
     * @return array
     */
    public static function get_category_listitem_actions($category) {
        $baseurl = new \moodle_url('/admin/clickap/program/category/management.php', array('programid'=>$category->programid, 'categoryid' => $category->id, 'sesskey' => \sesskey()));
        $actions = array();

        if(!empty($category->name)){
            // Edit.
            $actions['edit'] = array(
                'url' => new \moodle_url('/admin/clickap/program/category/editcategory.php', array('id' => $category->id)),
                'icon' => new \pix_icon('t/edit', new \lang_string('edit')),
                'string' => new \lang_string('edit')
            );
            // Delete.
            $actions['delete'] = array(
                'url' => new \moodle_url($baseurl, array('action' => 'deletecategory')),
                'icon' => new \pix_icon('t/delete', new \lang_string('delete')),
                'string' => new \lang_string('delete')
            );
        }
        // Move up/down.
        $actions['moveup'] = array(
            'url' => new \moodle_url($baseurl, array('action' => 'movecategoryup')),
            'icon' => new \pix_icon('t/up', new \lang_string('up')),
            'string' => new \lang_string('up')
        );
        $actions['movedown'] = array(
            'url' => new \moodle_url($baseurl, array('action' => 'movecategorydown')),
            'icon' => new \pix_icon('t/down', new \lang_string('down')),
            'string' => new \lang_string('down')
        );
        return $actions;
    }

    /**
     * Renderers the actions for individual category list items.
     *
     * @param coursecat $category
     * @param array $actions
     * @return string
     */
    public function category_listitem_actions($category, array $actions = null) {
        $menu = new action_menu();
        $menu->attributes['class'] .= ' category-item-actions item-actions';

        foreach ($actions as $key => $action) {
            $menu->add(new action_menu_link(
                $action['url'],
                $action['icon'],
                $action['string'],
                in_array($key, array('moveup', 'movedown')),
                array('data-action' => $key, 'class' => 'action-'.$key)
            ));
        }

        return $this->render($menu);
    }
    
    /**
     * Renders a category list item.
     *
     * This function gets called recursively to render sub categories.
     *
     * @param coursecat $category The category to render as listitem.
     * @param coursecat[] $subcategories The subcategories belonging to the category being rented.
     * @param int $totalsubcategories The total number of sub categories.
     * @param int $selectedcategory The currently selected category
     * @param int[] $selectedcategories The path to the selected category and its ID.
     * @return string
     */
    public function category_listitem($programid, $category, $selectedcategory) {

        $isexpandable = 0;
        $isexpanded = 0;
        $activecategory = ($selectedcategory === $category->id);
        $attributes = array(
            'class' => 'listitem listitem-category',
            'data-id' => $category->id,
            'data-programid' => $programid,
            'data-expandable' => 0,
            'data-expanded' => 0,
            'data-selected' => $activecategory ? '1' : '0',
            'data-visible' => 1,
            'role' => 'treeitem',
            'aria-expanded' => $isexpanded ? 'true' : 'false'
        );
        if(empty($category->name)){
            $text = format_string(get_string('notcategorised', 'clickap_program'));
        }else{
            $text = format_string($category->name);
        }
        
        $viewcaturl = new moodle_url('/admin/clickap/program/category/management.php', array('programid'=>$programid,'categoryid' => $category->id));
        $icon = $this->output->pix_icon('i/navigationitem', '', 'moodle',
            array('class' => 'tree-icon', 'style' => 'margin-left:20px;' , 'title' => get_string('showcategory', 'moodle', $text))
        );
        $icon = html_writer::span($icon, 'float-left d-flex');

        $html = html_writer::start_tag('li', $attributes);
        $html .= html_writer::start_div('clearfix');
        $html .= $icon;

        $textattributes = array('class' => 'float-left  d-flex categoryname');
        $html .= html_writer::link($viewcaturl, $text, $textattributes);
        $html .= html_writer::start_div('float-right d-flex');
        $actions = $this->get_category_listitem_actions($category);
        $html .= $this->category_listitem_actions($category, $actions);

        $countid = 'course-count-'.$category->id;
        $courseicon = $this->output->pix_icon('i/course', get_string('courses'));
        $html .= html_writer::span(
            html_writer::span($category->coursecount) .
            html_writer::span(get_string('courses'), 'accesshide', array('id' => $countid)) .
            $courseicon,
            'course-count dimmed',
            array('aria-labelledby' => $countid)
        );
        $html .= html_writer::end_div();
        $html .= html_writer::end_div();
        $html .= html_writer::end_tag('li');
        return $html;
    }
    
    /**
     * Renders a course listing.
     *
     * @param coursecat $category The currently selected category. This is what the listing is focused on.
     * @param course_in_list $course The currently selected course.
     * @param int $page The page being displayed.
     * @param int $perpage The number of courses to display per page.
     * @return string
     */
    public function course_listing($category = null, $page = 0, $perpage = 20) {
        global $DB;
        
        $sql = "SELECT c.id, c.fullname, c.visible, c.category
                FROM {program_category_courses} p
                LEFT JOIN {course} c ON p.courseid = c.id
                WHERE p.categoryid = :categoryid
                ORDER BY p.sortorder";
        $courses = $DB->get_records_sql($sql, array('categoryid'=>$category->id),$page,$perpage);
        $page = max($page, 0);
        $perpage = max($perpage, 2);
        $totalcourses = $category->coursecount;
        $totalpages = ceil($totalcourses / $perpage);
        if ($page > $totalpages - 1) {
            $page = $totalpages - 1;
        }
        $options = array(
            'offset' => $page * $perpage,
            'limit' => $perpage
        );

        $class = '';
        if ($page === 0) {
            $class .= ' firstpage';
        }
        if ($page + 1 === (int)$totalpages) {
            $class .= ' lastpage';
        }

        $html  = html_writer::start_div('card course-listing w-100 '.$class, array(
            'data-category' => $category->id,
            'data-page' => $page,
            'data-totalpages' => $totalpages,
            'data-totalcourses' => $totalcourses,
            'data-canmoveoutof' => 1
        ));
        
        if(empty($category->name)){
            $category->name = get_string('notcategorised', 'clickap_program');
        }
        $text = format_string($category->name);
        $html .= html_writer::tag('h3', $text, array('id' => 'course-listing-title', 'tabindex' => '0'));
        //$html .= $this->course_listing_actions($category, $course, $perpage);
        $html .= $this->listing_pagination($category, $page, $perpage);
        $html .= html_writer::start_tag('ul', array('class' => 'ml', 'role' => 'group'));
        foreach ($courses as $listitem) {
            $html .= $this->course_listitem($category, $listitem);
        }
        $html .= html_writer::end_tag('ul');
        $html .= $this->listing_pagination($category, $page, $perpage, true);
        $html .= $this->course_bulk_actions($category);
        $html .= html_writer::end_div();
        return $html;
    }

    /**
     * Renderers bulk actions that can be performed on courses.
     *
     * @param coursecat $category The currently selected category and the category in which courses that
     *      are selectable belong.
     * @return string
     */
    public function course_bulk_actions($category) {
        global $DB;
        
        $html  = html_writer::start_div('course-bulk-actions bulk-actions');
        $html .= html_writer::div(get_string('coursebulkaction'), 'accesshide', array('tabindex' => '0'));
        $str = get_string('notcategorised', 'clickap_program');
        $sql = "SELECT id, CASE  name WHEN '' THEN '$str' ELSE name END as name FROM {program_category} 
                WHERE programid = :programid ORDER BY sortorder";
        $options = $DB->get_records_sql_menu($sql, array('programid'=>$category->programid));
        $select = html_writer::select(
            $options,
            'movecoursesto',
            '',
            array(),
            array('aria-labelledby' => 'moveselectedcoursesto')
        );
        $submit = array('type' => 'submit', 'name' => 'bulkmovecourses', 'value' => get_string('move'));
        $programid = array('type' => 'hidden', 'name' => 'programid', 'value' => $category->programid);
        $html .= $this->detail_pair(
            html_writer::span(get_string('moveselectedcoursesto'), '', array('id' => 'moveselectedcoursesto')),
            $select . html_writer::empty_tag('input', $submit) . html_writer::empty_tag('input', $programid)
        );
        $html .= html_writer::end_div();
        return $html;
    }

    /**
     * Renderers a key value pair of information for display.
     *
     * @param string $key
     * @param string $value
     * @param string $class
     * @return string
     */
    protected function detail_pair($key, $value, $class ='') {
        $html = html_writer::start_div('detail-pair row yui3-g '.preg_replace('#[^a-zA-Z0-9_\-]#', '-', $class));
        $html .= html_writer::div(html_writer::span($key), 'pair-key col-md-4 yui3-u-1-4 font-weight-bold');
        $html .= html_writer::div(html_writer::span($value), 'pair-value col-md-7 yui3-u-3-4');
        $html .= html_writer::end_div();
        return $html;
    }
    
    /**
     * Renderers a course list item.
     *
     * This function will be called for every course being displayed by course_listing.
     *
     * @param coursecat $category The currently selected category and the category the course belongs to.
     * @param course_in_list $course The course to produce HTML for.
     * @param int $selectedcourse The id of the currently selected course.
     * @return string
     */
    public function course_listitem($category, $course) {

        $text = format_string($course->fullname);
        //$courseicon = $this->output->pix_icon('i/course', get_string('courses'));
        $attributes = array(
            'class' => 'listitem listitem-course',
            'data-id' => $course->id,
            'data-programid' => $category->programid,
            'data-categoryid' => $category->id,
            'data-selected' => '0',
            'data-visible' => $course->visible ? '1' : '0'
        );
        $bulkcourseinput = array(
            'type' => 'checkbox',
            'name' => 'bc[]',
            'value' => $course->id,
            'class' => 'bulk-action-checkbox',
            'aria-label' => get_string('bulkactionselect', 'moodle', $text),
            'data-action' => 'select'
        );
        
        $viewcourseurl = new moodle_url('/course/view.php', array('id' => $course->id));

        $html  = html_writer::start_tag('li', $attributes);
        $html .= html_writer::start_div('clearfix');
        $html .= html_writer::div($this->output->pix_icon('i/move_2d', get_string('dndcourse')), 'float-left drag-handle');
        $html .= html_writer::start_div('ba-checkbox float-left');
        $html .= html_writer::empty_tag('input', $bulkcourseinput).'&nbsp;';
        $html .= html_writer::end_div();
        $html .= html_writer::link($viewcourseurl, $text, array('class' => 'float-left coursename'));
        $html .= html_writer::start_div('float-right');
        $html .= $this->course_listitem_actions($category, $course);
        $html .= html_writer::end_div();
        $html .= html_writer::end_div();
        $html .= html_writer::end_tag('li');
        return $html;
    }

    /**
     * Renderers actions for individual course actions.
     *
     * @param coursecat $category The currently selected category.
     * @param course_in_list $course The course to renderer actions for.
     * @return string
     */
    public function course_listitem_actions($category, $course) {
        $baseurl = new \moodle_url(
            '/admin/clickap/program/category/management.php',
            array('courseid' => $course->id, 'categoryid' => $course->category, 'sesskey' => \sesskey())
        );
        $actions = array();
        // Move up/down.
        $actions[] = array(
            'url' => new \moodle_url($baseurl, array('action' => 'movecourseup')),
            'icon' => new \pix_icon('t/up', \get_string('up')),
            'attributes' => array('data-action' => 'moveup', 'class' => 'action-moveup')
        );
        $actions[] = array(
            'url' => new \moodle_url($baseurl, array('action' => 'movecoursedown')),
            'icon' => new \pix_icon('t/down', \get_string('down')),
            'attributes' => array('data-action' => 'movedown', 'class' => 'action-movedown')
        );

        $actionshtml = array();
        foreach ($actions as $action) {
            $action['attributes']['role'] = 'button';
            $actionshtml[] = $this->output->action_icon($action['url'], $action['icon'], null, $action['attributes']);
        }
        return html_writer::span(join('', $actionshtml), 'course-item-actions item-actions');
    }
    
    /**
     * Renders pagination for a course listing.
     *
     * @param coursecat $category The category to produce pagination for.
     * @param int $page The current page.
     * @param int $perpage The number of courses to display per page.
     * @param bool $showtotals Set to true to show the total number of courses and what is being displayed.
     * @return string
     */
    protected function listing_pagination($category, $page, $perpage, $showtotals = false) {
        $html = '';
        $totalcourses = $category->coursecount;
        $totalpages = ceil($totalcourses / $perpage);
        if ($showtotals) {
            if ($totalpages == 0) {
                $str = get_string('nocoursesyet');
            } else if ($totalpages == 1) {
                $str = get_string('showingacourses', 'moodle', $totalcourses);
            } else {
                $a = new stdClass;
                $a->start = ($page * $perpage) + 1;
                $a->end = min((($page + 1) * $perpage), $totalcourses);
                $a->total = $totalcourses;
                $str = get_string('showingxofycourses', 'moodle', $a);
            }
            $html .= html_writer::div($str, 'listing-pagination-totals dimmed');
        }

        if ($totalcourses <= $perpage) {
            return $html;
        }
        $aside = 2;
        $span = $aside * 2 + 1;
        $start = max($page - $aside, 0);
        $end = min($page + $aside, $totalpages - 1);
        if (($end - $start) < $span) {
            if ($start == 0) {
                $end = min($totalpages - 1, $span - 1);
            } else if ($end == ($totalpages - 1)) {
                $start = max(0, $end - $span + 1);
            }
        }
        $items = array();
        $baseurl = new moodle_url('/admin/clickap/program/category/management.php', array('programid' => $category->programid, 'categoryid' => $category->id));
        if ($page > 0) {
            $items[] = $this->action_button(new moodle_url($baseurl, array('page' => 0)), get_string('first'));
            $items[] = $this->action_button(new moodle_url($baseurl, array('page' => $page - 1)), get_string('prev'));
            $items[] = '...';
        }
        for ($i = $start; $i <= $end; $i++) {
            $class = '';
            if ($page == $i) {
                $class = 'active-page';
            }
            $pageurl = new moodle_url($baseurl, array('page' => $i));
            $items[] = $this->action_button($pageurl, $i + 1, null, $class, get_string('pagea', 'moodle', $i+1));
        }
        if ($page < ($totalpages - 1)) {
            $items[] = '...';
            $items[] = $this->action_button(new moodle_url($baseurl, array('page' => $page + 1)), get_string('next'));
            $items[] = $this->action_button(new moodle_url($baseurl, array('page' => $totalpages - 1)), get_string('last'));
        }

        $html .= html_writer::div(join('', $items), 'listing-pagination');
        return $html;
    }
    
    /**
     * Creates an action button (styled link)
     *
     * @param moodle_url $url The URL to go to when clicked.
     * @param string $text The text for the button.
     * @param string $id An id to give the button.
     * @param string $class A class to give the button.
     * @param array $attributes Any additional attributes
     * @return string
     */
    protected function action_button(moodle_url $url, $text, $id = null, $class = null, $title = null, array $attributes = array()) {
        if (isset($attributes['class'])) {
            $attributes['class'] .= ' yui3-button';
        } else {
            $attributes['class'] = 'yui3-button';
        }
        if (!is_null($id)) {
            $attributes['id'] = $id;
        }
        if (!is_null($class)) {
            $attributes['class'] .= ' '.$class;
        }
        if (is_null($title)) {
            $title = $text;
        }
        $attributes['title'] = $title;
        if (!isset($attributes['role'])) {
            $attributes['role'] = 'button';
        }
        return html_writer::link($url, $text, $attributes);
    }
}

/**
 * An issued programs for program.php program
 */
class issued_program implements renderable {
    /** @var issued program */
    public $issued;

    /** @var program recipient */
    public $recipient;

    /** @var program class */
    public $programclass;

    /** @var program visibility to others */
    public $visible = 0;

    /** @var program class */
    public $programid = 0;

    /**
     * Initializes the program to display
     *
     * @param string $hash Issued program hash
     */
    public function __construct($hash) {
        global $DB;

        $assertion = new core_programs_assertion($hash);
        $this->issued = $assertion->get_program_assertion();
        $this->programclass = $assertion->get_program_class();

        $rec = $DB->get_record_sql('SELECT userid, visible, programid
                FROM {program_issued}
                WHERE ' . $DB->sql_compare_text('uniquehash', 40) . ' = ' . $DB->sql_compare_text(':hash', 40),
                array('hash' => $hash), IGNORE_MISSING);
        if ($rec) {
            // Get a recipient from database.
            $namefields = get_all_user_name_fields(true, 'u');
            $user = $DB->get_record_sql("SELECT u.id, $namefields, u.deleted, u.email
                        FROM {user} u WHERE u.id = :userid", array('userid' => $rec->userid));
            $this->recipient = $user;
            $this->visible = $rec->visible;
            $this->programid = $rec->programid;
        }
    }
}

/**
 * Program recipients rendering class
 */
class program_recipients implements renderable {
    /** @var string how are the data sorted */
    public $sort = 'lastname';

    /** @var string how are the data sorted */
    public $dir = 'ASC';

    /** @var int page number to display */
    public $page = 0;

    /** @var int number of program recipients to display per page */
    public $perpage = PROGRAM_PERPAGE;

    /** @var int the total number or program recipients to display */
    public $totalcount = null;

    /** @var array internal list of  program recipients ids */
    public $userids = array();
    /**
     * Initializes the list of users to display
     *
     * @param array $holders List of program holders
     */
    public function __construct($holders) {
        $this->userids = $holders;
    }
}

/**
 * Collection of all programs for view.php page
 */
class program_collection implements renderable {

    /** @var string how are the data sorted */
    public $sort = 'name';

    /** @var string how are the data sorted */
    public $dir = 'ASC';

    /** @var int page number to display */
    public $page = 0;

    /** @var int number of programs to display per page */
    public $perpage = 25;

    /** @var int the total number of programs to display */
    public $totalcount = null;

    /** @var array list of programs */
    public $programs = array();

    /**
     * Initializes the list of programs to display
     *
     * @param array $programs Programs to render
     */
    public function __construct($programs) {
        $this->programs = $programs;
    }
}

/**
 * Collection of programs used at the index.php page
 */
class program_management extends program_collection implements renderable {
}

/**
 * Collection of user programs used at the myprograms.php page
 */
class program_user_collection extends program_collection implements renderable {
    /** @var array backpack settings */
    public $backpack = null;

    /** @var string search */
    public $search = '';

    /**
     * Initializes user program collection.
     *
     * @param array $programs Programs to render
     * @param int $userid Programs owner
     */
    public function __construct($programs, $userid) {
        global $CFG;
        parent::__construct($programs);

        if (!empty($CFG->programs_allowexternalbackpack)) {
            $this->backpack = get_backpack_settings($userid, true);
        }
    }
}
