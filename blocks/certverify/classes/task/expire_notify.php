<?php
/**
 * certificate expire notify
 *
 * @package    block_certverify
 * @author     Elaine Chen(CLICK-AP)
 * @copyright  CLICK-AP {@link https://www.click-ap.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_certverify\task;

use stdClass;

defined('MOODLE_INTERNAL') || die();

class expire_notify extends \core\task\scheduled_task {

    /**
     * Name for this task.
     *
     * @return string
     */
    public function get_name() {
        return get_string('expirenotifytask', 'block_certverify');
    }

    /**
     * Run task for synchronising users.
     */
    public function execute() {
        global $CFG, $DB;
        require_once($CFG->dirroot.'/blocks/certverify/locallib.php');

        @ini_set('max_execution_time', 0);
        raise_memory_limit("256M");
        
        $duenotify = get_config('block_certverify', 'duenotify');

        $today = date('Ymd', time());
        $duedate = strtotime($today) + $duenotify;
        mtrace('Today：'.$today);
        mtrace('Due notify date：'.date('Ymd', $duedate));

        $sql = "SELECT c.*, cc.name as certname, u.id as userid, u.firstname, u.lastname
                     , u.idnumber as useridnumber, uid.data as deptname
                FROM {user_certs} c
                LEFT JOIN {clickap_code} cc ON c.certid = cc.id
                LEFT JOIN {user} u ON u.id = c.userid
                LEFT JOIN {user_info_data} uid ON u.id = uid.userid 
                     AND uid.fieldid = (SELECT id FROM {user_info_field} WHERE shortname = 'DeptName')
                WHERE c.status = 1 AND c.notification = 0 AND c.dateexpire != 0 AND c.dateexpire <= :duenotify";
        if($results = $DB->get_records_sql($sql, array('duenotify' => $duedate))){
            foreach($results as $data){
                $data->dateexpire = date('Y/m/d', $data->dateexpire);
                mtrace('User：'.$data->lastname.'('.$data->firstname.')-'.$data->deptname.',Certificate：'.$data->certname.'('.$data->idnumber.')-'.$data->dateexpire);

                if(block_certverify_due_notifications($data)){
                    $newdate = new stdClass();
                    $newdate->id = $data->id;
                    $newdate->notification = 1;
                    $DB->update_record('user_certs', $newdate);
                }
            }
        }
    }
}