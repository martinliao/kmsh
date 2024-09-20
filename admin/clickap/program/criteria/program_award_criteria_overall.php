<?php
/**
 * Version details.
 *
 * @package    clickap
 * @subpackage program
 * @copyright  2018 Click-AP <elaine@click-ap.com>
 * @license    http://www.click-ap.com/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Overall program award criteria
 *
 */
class program_award_criteria_overall extends program_award_criteria {

    /* @var int Criteria [PROGRAM_CRITERIA_TYPE_OVERALL] */
    public $criteriatype = PROGRAM_CRITERIA_TYPE_OVERALL;

    /**
     * Add appropriate form elements to the criteria form
     *
     * @param stdClass $data details of overall criterion
     */
    public function config_form_criteria($data) {
        global $OUTPUT;
        $prefix = 'criteria-' . $this->id;
        if (count($data->criteria) > 2) {
            echo $OUTPUT->box_start();
            if (!empty($this->description)) {
                $program = new program($this->programid);
                echo $OUTPUT->box(
                    format_text($this->description, $this->descriptionformat, array('context' => $program->get_context())),
                    'criteria-description');
            }
            echo $OUTPUT->heading($this->get_title(), 2);

            $agg = $data->get_aggregation_methods();
            if (!$data->is_locked() && !$data->is_active()) {
                $editurl = new moodle_url('/admin/clickap/program/criteria_settings.php',
                               array('programid' => $this->programid,
                                   'edit' => true,
                                   'type' => $this->criteriatype,
                                   'crit' => $this->id
                               )
                        );
                $editaction = $OUTPUT->action_icon($editurl, new pix_icon('t/edit', get_string('edit')), null,
                              array('class' => 'criteria-action'));
                echo $OUTPUT->box($editaction, array('criteria-header'));

                $url = new moodle_url('criteria.php', array('id' => $data->id, 'sesskey' => sesskey()));
                echo $OUTPUT->single_select($url, 'update', $agg, $data->get_aggregation_method($this->criteriatype),
                    null, null, array('aria-describedby' => 'overall'));
                echo html_writer::span(get_string('overallcrit', 'clickap_program'), '', array('id' => 'overall'));
            } else {
                echo $OUTPUT->box(get_string('criteria_descr_' . $this->criteriatype, 'programs',
                        core_text::strtoupper($agg[$data->get_aggregation_method()])), 'clearfix');
            }
            echo $OUTPUT->box_end();
        }
    }

    /**
     * Add appropriate parameter elements to the criteria form
     *
     */
    public function config_options(&$mform, $param) {
    }

    /**
     * Get criteria details for displaying to users
     *
     * @return string
     */
    public function get_details($short = '') {
    }

    /**
     * Review this criteria and decide if it has been completed
     * Overall criteria review should be called only from other criteria handlers.
     *
     * @param int $userid User whose criteria completion needs to be reviewed.
     * @param bool $filtered An additional parameter indicating that user list
     *        has been reduced and some expensive checks can be skipped.
     *
     * @return bool Whether criteria is complete
     */
    public function review($userid, $filtered = false) {
        global $DB;

        $sql = "SELECT bc.*, bcm.critid, bcm.userid, bcm.datemet
                FROM {program_criteria} bc
                LEFT JOIN {program_criteria_met} bcm
                    ON bc.id = bcm.critid AND bcm.userid = :userid
                WHERE bc.programid = :programid
                    AND bc.criteriatype != :criteriatype ";

        $params = array(
                    'userid' => $userid,
                    'programid' => $this->programid,
                    'criteriatype' => PROGRAM_CRITERIA_TYPE_OVERALL
                );

        $criteria = $DB->get_records_sql($sql, $params);
        $overall = false;
        foreach ($criteria as $crit) {
            if ($this->method == PROGRAM_CRITERIA_AGGREGATION_ALL) {
                if ($crit->datemet === null) {
                    return false;
                } else {
                    $overall = true;
                    continue;
                }
            } else {
                if ($crit->datemet === null) {
                    $overall = false;
                    continue;
                } else {
                    return true;
                }
            }
        }

        return $overall;
    }

    /**
     * Returns array with sql code and parameters returning all ids
     * of users who meet this particular criterion.
     *
     * @return array list($join, $where, $params)
     */
    public function get_completed_criteria_sql() {
        return array('', '', array());
    }

    /**
     * Add appropriate criteria elements to the form
     *
     */
    public function get_options(&$mform) {
    }

    /**
     * Return criteria parameters
     *
     * @param int $critid Criterion ID
     * @return array
     */
    public function get_params($cid) {
    }

    /**
     * Saves overall program criteria description.
     *
     * @param array $params Values from the form or any other array.
     */
    public function save($params = array()) {
        global $DB;

        // Sort out criteria description.
        // If it is coming from the form editor, it is an array of (text, format).
        $description = '';
        $descriptionformat = FORMAT_HTML;
        if (isset($params['description']['text'])) {
            $description = $params['description']['text'];
            $descriptionformat = $params['description']['format'];
        } else if (isset($params['description'])) {
            $description = $params['description'];
        }

        $fordb = new stdClass();
        $fordb->criteriatype = $this->criteriatype;
        $fordb->programid = $this->programid;
        $fordb->description = $description;
        $fordb->descriptionformat = $descriptionformat;
        if ($this->id !== 0) {
            $fordb->id = $this->id;
            $DB->update_record('program_criteria', $fordb);
        } else {
            // New record in DB, set aggregation to ALL by default.
            $fordb->method = PROGRAM_CRITERIA_AGGREGATION_ALL;
            $DB->insert_record('program_criteria', $fordb);
        }
    }
}
