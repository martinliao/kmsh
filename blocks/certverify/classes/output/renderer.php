<?php
/**
 * 
 * @package    block_certverify
 * @author     Elaine Chen(CLICK-AP)
 * @copyright  CLICK-AP {@link https://www.click-ap.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/formslib.php');

class block_certverify_renderer extends plugin_renderer_base {

    protected function render_report_table($table): string {
        ob_start();

        $output = ob_get_contents();

        ob_end_clean();

        return $output;
    }

    public function manage_page($table, $manageurl, $manageui, $filterform = NULL) {
        echo $this->header();
        if($manageui){
            echo $this->heading(get_string('confirmusers', 'block_certverify'));
            echo get_string('confirmusers_desc', 'block_certverify');
        }else{
            echo $this->heading(get_string('applylist', 'block_certverify'));
            echo get_string('applylist_desc', 'block_certverify');
        }
        echo '<div id="tablecontainer">';
        if($manageui){
            echo $filterform;
        }
        $this->manage_form($table, $manageurl, $manageui);
        echo '</div>';
        //echo $this->footer();
    }

    public function manage_form($table, $manageurl, $manageui) {
        global $PAGE;

        echo html_writer::start_tag('form', array(
            'id' => 'certificate_verify_apply_form',
            'method' => 'post',
            'action' => $manageurl->out()));
        
        $this->manage_table($table, $manageui);
        echo '<a id="checkall" href="#">'.get_string('selectall').'</a> / ';
        echo '<a id="checknone" href="#">'.get_string('deselectall').'</a>';

        $PAGE->requires->js_amd_inline("
            require(['jquery'], function($) {
            
            $('#checkall').click(function(e) {
            $('#certificate_verify_manage_form').find('input:checkbox').prop('checked', true);
                        e.preventDefault();
            });
            
            $('#checknone').click(function(e) {
            $('#certificate_verify_manage_form').find('input:checkbox').prop('checked', false);
                        e.preventDefault();
            });
        });");
        
        echo html_writer::start_tag('p', array('align' => 'left'));
        if($manageui){
            echo html_writer::empty_tag('input', array(
                'type' => 'submit',
                'name' => 'confirm',
                'value' => get_string('btnagree', 'block_certverify')));
            echo html_writer::empty_tag('input', array(
                'type' => 'submit',
                'name' => 'reject',
                'value' => get_string('btnreject', 'block_certverify')));
        }else{
            echo html_writer::empty_tag('input', array(
                'type' => 'submit',
                'name' => 'cancel',
                'value' => get_string('btncancel', 'block_certverify')));
        }

        echo html_writer::end_tag('p');
        echo html_writer::end_tag('form');
    }

    public function manage_table($table, $manageui) {
        if($manageui){
            $columns = array(
                'checkboxcolumn',
                'firstname',
                'certname',
                'certnumber',
                'dateissued',
                'dateexpire',
                'remark',
                'applydate',
                'validators',
                'detail');
            $headers = array(
                '',
                get_string('applyuser', 'block_certverify'),
                get_string('certname', 'block_certverify'),
                get_string('certnumber', 'block_certverify'),
                get_string('dateissued', 'block_certverify'),
                get_string('dateexpire', 'block_certverify'),
                get_string('remark', 'block_certverify'),
                get_string('applydate', 'block_certverify'),
                get_string('validators', 'block_certverify'),
                ''
                );
        }else{
            $columns = array(
                'checkboxcolumn',
                'certname',
                'certnumber',
                'dateissued',
                'dateexpire',
                'remark',
                'applydate',
                'validators',
                'detail');
            $headers = array(
                '',
                get_string('certname', 'block_certverify'),
                get_string('certnumber', 'block_certverify'),
                get_string('dateissued', 'block_certverify'),
                get_string('dateexpire', 'block_certverify'),
                get_string('remark', 'block_certverify'),
                get_string('applydate', 'block_certverify'),
                get_string('validators', 'block_certverify'),
                ''
                );
        }
        $table->define_columns($columns);
        $table->define_headers($headers);
        $table->sortable(true, 'applydate', SORT_DESC);
        $table->out(20, true);
    }

    /**
     * Apply history
     */
    public function apply_history_page($table, $manageurl, $filterform) {
        $this->apply_history_form($table, $manageurl);
    }

    public function apply_history_form($table, $manageurl) {
        $this->apply_history_table($table);
    }

    public function apply_history_table($table) {
        $columns = array(
            'certname',
            'certnumber',
            'dateissued',
            'dateexpire',
            'remark',
            'applydate',
            'validator',
            'timeverify',
            'status',
            'reason',
            'detail');
        $headers = array(
            get_string('certname', 'block_certverify'),
            get_string('certnumber', 'block_certverify'),
            get_string('dateissued', 'block_certverify'),
            get_string('dateexpire', 'block_certverify'),
            get_string('remark', 'block_certverify'),
            get_string('applydate', 'block_certverify'),
            get_string('validator', 'block_certverify'),
            get_string('timeverify', 'block_certverify'),
            get_string('status', 'block_certverify'),
            get_string('reason', 'block_certverify'),
            ''
            );
        $table->define_columns($columns);
        $table->define_headers($headers);
        $table->sortable(true, 'applydate', SORT_DESC);           
        $table->out(20, true);
    }

    /**
     * Verify history
     */
    public function verify_history_page($table, $manageurl, $filterform) {
        echo $this->header();
        echo $this->heading(get_string('verifyhistory', 'block_certverify'));
        echo '<div id="tablecontainer">';
        echo $filterform;
        $this->verify_history_form($table, $manageurl);
        echo '</div>';
        //echo $this->footer();
    }

    public function verify_history_form($table, $manageurl) {
        $this->verify_history_table($table);
    }

    public function verify_history_table($table) {
        $columns = array(
            'firstname',
            'certname',
            'certnumber',
            'dateissued',
            'dateexpire',
            'remark',
            'applydate',
            'validator',
            'timeverify',
            'status',
            'reason',
            'detail');
        $headers = array(
            get_string('applyuser', 'block_certverify'),
            get_string('certname', 'block_certverify'),
            get_string('certnumber', 'block_certverify'),
            get_string('dateissued', 'block_certverify'),
            get_string('dateexpire', 'block_certverify'),
            get_string('remark', 'block_certverify'),
            get_string('applydate', 'block_certverify'),
            get_string('validator', 'block_certverify'),
            get_string('timeverify', 'block_certverify'),
            get_string('status', 'block_certverify'),
			get_string('reason', 'block_certverify'),
            ''
            );

        $table->define_columns($columns);
        $table->define_headers($headers);
        $table->sortable(true, 'timeverify', SORT_DESC);           
        $table->out(20, true);
    }
}
