<?php
/**
 * plugin infomation
 * 
 * @package    block
 * @subpackage externalverify
 * @author     Elaine Chen(CLICK-AP)
 * @copyright  CLICK-AP {@link https://www.click-ap.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
 
defined('MOODLE_INTERNAL') || die();
require_once($CFG->libdir.'/formslib.php');

class block_externalverify_renderer extends plugin_renderer_base {
    public function manage_page($table, $manageurl, $manageui, $filterform = NULL, $stage = null) {
        echo $this->header();
        if($manageui){
            if(!empty($stage)){
                echo $this->heading(get_string('confirmusers_manager', 'block_externalverify'));
            }else {
                echo $this->heading(get_string('confirmusers', 'block_externalverify'));
            }
            
            echo get_string('confirmusers_desc', 'block_externalverify');
        }else{
            echo $this->heading(get_string('applylist', 'block_externalverify'));
            echo get_string('applylist_desc', 'block_externalverify');
        }
        echo '<div id="tablecontainer">';
        $this->manage_form($table, $manageurl, $manageui, $filterform, $stage);
        echo '</div>';
        //echo $this->footer();
    }

    public function edit_page($mform) {
        echo $this->header();
        echo $this->heading(get_string('pluginname', 'block_externalverify'));
        $mform->display();
        echo $this->footer();
    }

    public function manage_form($table, $manageurl, $manageui, $filterform, $stage) {
        global $PAGE;

        if($manageui){
            echo $filterform;
        }
        echo html_writer::start_tag('form', array(
            'id' => 'external_verify_manage_form',
            'method' => 'post',
            'action' => $manageurl->out()));
        
        $this->manage_table($table, $manageui, $stage);
        echo '<a id="checkall" href="#">'.get_string('selectall').'</a> / ';
        echo '<a id="checknone" href="#">'.get_string('deselectall').'</a>';

        $PAGE->requires->js_amd_inline("
            require(['jquery'], function($) {
            
            $('#checkall').click(function(e) {
            $('#external_verify_manage_form').find('input:checkbox').prop('checked', true);
                        e.preventDefault();
            });
            
            $('#checknone').click(function(e) {
            $('#external_verify_manage_form').find('input:checkbox').prop('checked', false);
                        e.preventDefault();
            });
        });");
        
        echo html_writer::start_tag('p', array('align' => 'left'));
        if($manageui){
            echo html_writer::empty_tag('input', array(
                'type' => 'submit',
                'name' => 'confirm',
                'value' => get_string('btnagree', 'block_externalverify')));
            echo html_writer::empty_tag('input', array(
                'type' => 'submit',
                'name' => 'reject',
                'value' => get_string('btnreject', 'block_externalverify')));
        }else{
            echo html_writer::empty_tag('input', array(
                'type' => 'submit',
                'name' => 'cancel',
                'value' => get_string('btncancel', 'block_externalverify')));
        }

        echo html_writer::end_tag('p');
        echo html_writer::end_tag('form');
    }

    public function manage_table($table, $manageui, $stage) {
        if($manageui){
            if($stage == 2){
                $columns = array(
                    'checkboxcolumn',
                    'applyuser',
                    'firstname',
                    'course',
                    'org',
                    'expense',
                    'startdate',
                    'enddate',
                    'applydate',
                    'validator',
                    'timeverify1',
                    'detail');
                $headers = array(
                    '',
                    get_string('applyuser', 'block_externalverify'),
                    get_string('firstname'),
                    get_string('course'),
                    get_string('org', 'block_externalverify'),
                    get_string('expense', 'block_externalverify'),
                    get_string('startdate', 'block_externalverify'),
                    get_string('enddate', 'block_externalverify'),
                    get_string('applydate', 'block_externalverify'),
                    get_string('validator', 'block_externalverify'),
                    get_string('timeverify1', 'block_externalverify'),
                    ''
                    );
            }
            else {
                $columns = array(
                    'checkboxcolumn',
                    'applyuser',
                    'firstname',
                    'course',
                    'org',
                    'expense',
                    'startdate',
                    'enddate',
                    'applydate',
                    'detail');
                $headers = array(
                    '',
                    get_string('applyuser', 'block_externalverify'),
                    get_string('firstname'),
                    get_string('course'),
                    get_string('org', 'block_externalverify'),
                    get_string('expense', 'block_externalverify'),
                    get_string('startdate', 'block_externalverify'),
                    get_string('enddate', 'block_externalverify'),
                    get_string('applydate', 'block_externalverify'),
                    ''
                    );
            }
        }else{
            $columns = array(
                'checkboxcolumn',
                'org',
                'expense',
                'course',
                'hours',
                'startdate',
                'enddate',
                'applydate',
                'supervisor',
                'validator',
                'timeverify1',
                'status',
                'detail');
            $headers = array(
                '',
                get_string('org', 'block_externalverify'),
                get_string('expense', 'block_externalverify'),
                get_string('course'),
                get_string('course_hours', 'block_externalverify'),
                get_string('startdate', 'block_externalverify'),
                get_string('enddate', 'block_externalverify'),
                get_string('applydate', 'block_externalverify'),
                get_string('superviorname', 'block_externalverify'),
                get_string('validator', 'block_externalverify'),
                get_string('timeverify1', 'block_externalverify'),
                get_string('status', 'block_externalverify'),
                ''
                );
        }
        $table->define_columns($columns);
        $table->define_headers($headers);
        $table->sortable(true, 'applydate', SORT_DESC);
        $table->out(20, true);
    }
//apply history
    public function apply_history_page($table, $manageurl, $filterform) {
        $this->apply_history_form($table, $manageurl, $filterform);
    }

    public function apply_history_form($table, $manageurl, $filterform) {
        $this->apply_history_table($table);
    }

    public function apply_history_table($table) {
        $columns = array(
            'org',
            'expense',
            'course',
            'startdate',
            'enddate',
            'applydate',
            'supervisor',
            'verifydate',
            'status',
            'detail');
        $headers = array(
            get_string('org', 'block_externalverify'),
            get_string('expense', 'block_externalverify'),
            get_string('course'),
            get_string('startdate', 'block_externalverify'),
            get_string('enddate', 'block_externalverify'),
            get_string('applydate', 'block_externalverify'),
            get_string('superviorname', 'block_externalverify'),
            get_string('verifydate', 'block_externalverify'),
            get_string('status'),
            ''
            );
        $table->define_columns($columns);
        $table->define_headers($headers);
        $table->sortable(true, 'applydate', SORT_DESC);           
        $table->out(20, true);
    }
//verify history
    public function verify_history_page($table, $manageurl, $filterform) {
        echo $this->header();
        echo $this->heading(get_string('verifyhistory', 'block_externalverify'));
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
            'org',
            'expense',
            'startdate',
            'enddate',
            'applydate',
            'verifydate',
            'status',
            'detail');
        $headers = array(
            get_string('applyuser', 'block_externalverify'),
            get_string('firstname'),
            get_string('course'),
            get_string('org', 'block_externalverify'),
            get_string('expense', 'block_externalverify'),
            get_string('startdate', 'block_externalverify'),
            get_string('enddate', 'block_externalverify'),
            get_string('applydate', 'block_externalverify'),
            get_string('verifydate', 'block_externalverify'),
            get_string('status'),
            ''
            );

        $table->define_columns($columns);
        $table->define_headers($headers);
        $table->sortable(true, 'verifydate', SORT_DESC);           
        $table->out(20, true);
    }
}
