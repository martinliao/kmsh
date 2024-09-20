<?php
/**
 * plugin infomation
 *
 * @package    block_enrolverifysupervisor
 * @copyright  2020 CLICK-AP  {@link https://www.click-ap.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();
require_once($CFG->libdir.'/formslib.php');

class block_enrolverifysupervisor_renderer extends plugin_renderer_base {
    public function manage_page($table, $manageurl, $manageui, $filterform = NULL) {
        echo $this->header();
        if($manageui){
            echo $this->heading(get_string('confirmusers', 'block_enrolverifysupervisor'));
            echo get_string('confirmusers_desc', 'block_enrolverifysupervisor');
        }else{
            echo $this->heading(get_string('applylist', 'block_enrolverifysupervisor'));
            echo get_string('applylist_desc', 'block_enrolverifysupervisor');
        }
        echo '<div id="tablecontainer">';
        $this->manage_form($table, $manageurl, $manageui, $filterform);
        echo '</div>';
        //echo $this->footer();
    }

    public function edit_page($mform) {
        echo $this->header();
        echo $this->heading(get_string('pluginname', 'block_enrolverifysupervisor'));
        $mform->display();
        echo $this->footer();
    }

    public function manage_form($table, $manageurl, $manageui, $filterform) {
        global $PAGE;
        
        echo $filterform;
        echo html_writer::start_tag('form', array(
            'id' => 'enrol_verify_supervisor_manage_form',
            'method' => 'post',
            'action' => $manageurl->out()));

        $this->manage_table($table, $manageui);

        echo '<a id="checkall" href="#">'.get_string('selectall').'</a> / ';
        echo '<a id="checknone" href="#">'.get_string('deselectall').'</a>';
        $PAGE->requires->js_amd_inline("
            require(['jquery'], function($) {
            
            $('#checkall').click(function(e) {
            $('#enrol_verify_supervisor_manage_form').find('input:checkbox').prop('checked', true);
                        e.preventDefault();
            });
            
            $('#checknone').click(function(e) {
            $('#enrol_verify_supervisor_manage_form').find('input:checkbox').prop('checked', false);
                        e.preventDefault();
            });
        });");
        
        echo html_writer::start_tag('div');
        if($manageui){
            echo html_writer::empty_tag('input', array(
                'type' => 'submit',
                'name' => 'confirm',
                'value' => get_string('btnagree', 'block_enrolverifysupervisor')));
            echo html_writer::empty_tag('input', array(
                'type' => 'submit',
                'name' => 'reject',
                'value' => get_string('btnreject', 'block_enrolverifysupervisor')));
        }else{
            echo html_writer::empty_tag('input', array(
                'type' => 'submit',
                'name' => 'cancel',
                'value' => get_string('btncancel', 'block_enrolverifysupervisor')));
        }

        echo html_writer::end_tag('div');
        echo html_writer::end_tag('form');
    }

    public function manage_table($table, $manageui) {
        if($manageui){
            $columns = array(
                'checkboxcolumn',
                'applyuser',
                'firstname',
                'course',
                'category',
                'startdate',
                'applydate',
                'verifyuser');
            $headers = array(
                '',
                get_string('lastname'),
                get_string('firstname'),
                get_string('fullnamecourse'),
                get_string('category'),
                get_string('startdate'),
                get_string('applydate', 'block_enrolverifysupervisor'),
                get_string('verifyuser', 'block_enrolverifysupervisor'));
        }else{
            $columns = array(
                'checkboxcolumn',
                'course',
                'category',
                'startdate',
                'applydate',
                'verifyuser');
            $headers = array(
                '',
                get_string('fullnamecourse'),
                get_string('category'),
                get_string('startdate'),
                get_string('applydate', 'block_enrolverifysupervisor'),
                get_string('verifyuser', 'block_enrolverifysupervisor'));
        }
        $table->define_columns($columns);
        $table->define_headers($headers);
        $table->sortable(true, 'applydate', SORT_DESC);
        $table->out(20, true);
    }
//apply history
    public function apply_history_page($table, $manageurl, $filterform) {
        echo $this->header();
        echo $this->heading(get_string('applyhistory', 'block_enrolverifysupervisor'));
        echo '<div id="tablecontainer">';
        $this->apply_history_form($table, $manageurl, $filterform);
        echo '</div>';
        //echo $this->footer();
    }

    public function apply_history_form($table, $manageurl, $filterform) {
        echo $filterform;
        $this->apply_history_table($table);
    }

    public function apply_history_table($table) {
        $columns = array(
            'course',
            'category',
            'startdate',
            'applydate',
            'verifyuser',
            'verifydate',
            'status',
            'reason');
        $headers = array(
            get_string('course'),
            get_string('category'),
            get_string('startdate'),
            get_string('applydate', 'block_enrolverifysupervisor'),
            get_string('verifyuser', 'block_enrolverifysupervisor'),
            get_string('verifydate', 'block_enrolverifysupervisor'),
            get_string('status'),
            get_string('reason', 'block_enrolverifysupervisor'),
            );
        $table->define_columns($columns);
        $table->define_headers($headers);
        $table->sortable(true, 'applydate', SORT_DESC);           
        $table->out(20, true);
    }
//verify history
    public function verify_history_page($table, $manageurl, $filterform) {
        echo $this->header();
        echo $this->heading(get_string('verifyhistory', 'block_enrolverifysupervisor'));
        echo '<div id="tablecontainer">';
        $this->verify_history_form($table, $manageurl, $filterform);
        echo '</div>';
        //echo $this->footer();
    }

    public function verify_history_form($table, $manageurl, $filterform) {
        echo $filterform;
        $this->verify_history_table($table);
    }

    public function verify_history_table($table) {
        $columns = array(
            'applyuser',
            'firstname',
            'course',
            'category',
            'startdate',
            'applydate',
            'verifydate',
            'verifyuser',
            'status',
            'reason');
        $headers = array(
            get_string('lastname'),
            get_string('firstname'),
            get_string('course'),
            get_string('category'),
            get_string('startdate'),
            get_string('applydate', 'block_enrolverifysupervisor'),
            get_string('verifydate', 'block_enrolverifysupervisor'),
            get_string('verifyuser', 'block_enrolverifysupervisor'),
            get_string('status'),
            get_string('reason', 'block_enrolverifysupervisor'),
            );
        $table->define_columns($columns);
        $table->define_headers($headers);
        $table->sortable(true, 'verifydate', SORT_DESC);           
        $table->out(20, true);
    }
}

class block_enrolverifysupervisor_reject_request_form extends moodleform {
    function definition() {
        $mform =& $this->_form;
        $userenrolments = $this->_customdata['userenrolments'];
        
        $mform->addElement('hidden', 'rejectusers', $userenrolments);
        $mform->setType('rejectusers', PARAM_TEXT);

        $mform->addElement('textarea', 'reason', get_string('reason', 'block_enrolverifysupervisor'), array('rows'=>'1', 'cols'=>'50'));
        $mform->addRule('reason', get_string('missingreqreason'), 'required', null, 'client');
        $mform->setType('reason', PARAM_TEXT);
        $this->add_action_buttons(true, get_string('reject'));
        
    }
}