<?php

// This file is part of the MRBS block for Moodle
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/config.php');

function mrbsForceMove($room_id, $starttime, $endtime, $name, $id = null) {

    global $CFG, $USER, $DB;

    $cfg_mrbs = get_config('block_mrbs');

    $output = '';
    // Select any meetings which overlap ($starttime,$endtime) for this room:
    $sql = 'SELECT e.id AS entryid,
        e.name as entryname,
        e.description as description,
        e.type as type,
        e.create_by,
        e.room_id,
        e.name,
        e.start_time,
        e.end_time,
        r.room_name,
        r.description,
        r.area_id
              FROM {block_mrbs_entry} e
              JOIN {block_mrbs_room} r
              ON e.room_id = r.id
             WHERE ((e.start_time >= ? AND e.end_time < ?)
             OR (e.start_time < ? AND e.end_time > ?)
             OR (e.start_time < ? AND e.end_time >= ?))';

    //this is so that if a booking is being edited (normally extended) and forcing the booking a confusing email doesn't get sent to the force booker
    if (!empty($id)) {
        $sql .= ' AND e.id!=?';
    }
    $sql .= ' AND e.room_id = ? ORDER BY e.start_time';

    if (!empty($id)) {
        $oldbookings = $DB->get_records_sql($sql, array(
            $starttime, $endtime, $starttime, $starttime, $endtime, $endtime, $id, $room_id
        ));
    } else {
        $oldbookings = $DB->get_records_sql($sql, array(
            $starttime, $endtime, $starttime, $starttime, $endtime, $endtime, $room_id
        ));
    }

    foreach ($oldbookings as $oldbooking) {

        $today = mktime(0, 0, 0, date('n'), date('j'), date('Y'));
        $hrstarttime = to_hr_time($oldbooking->start_time - ($today));

        //Work out how many students so they don't get put in a tiny classroom
        $sizequery = 'SELECT count(*) as count
                        FROM {context} c
                            JOIN {role_assignments} ra
                                ON ra.contextid = c.id AND ra.roleid = ?
                            JOIN {course} co
                                ON c.contextlevel = ? and c.instanceid = co.id
                        WHERE co.shortname  = ?';
        $shortname = clean_param($oldbooking->entryname, PARAM_TEXT);

        if ($result = $DB->get_record_sql($sizequery, array('5', '50', $shortname))) {
            $class_size = $result->count;
        } else {
            $class_size = 0;
        }

        $findroomquery = 'SELECT DISTINCT
                                r.id,
                                r.room_name,
                                a.area_name,
                                IF (r.description = ?, 1, 0) AS sort1,
                                IF (a.id = ?, 1, 0) AS sort2
                             FROM {block_mrbs_room} r
                             JOIN {block_mrbs_area} a
                                ON r.area_id = a.id
                             JOIN {block_mrbs_entry} e
                                ON r.id= e.room_id
                             WHERE ( SELECT COUNT(*) FROM {block_mrbs_entry} e2
                                 WHERE ((e2.start_time >= ? AND e2.end_time < ?)
                                 OR (e2.start_time < ? AND e2.end_time > ?)
                                 OR (e2.start_time < ? AND e2.end_time >= ?))
                                 AND e2.room_id = r.id ) < 1
                             AND r.description like ?
                             AND r.capacity >= ?
                             AND (r.description not like ? OR r.id= ?)
                             ORDER BY sort1 DESC, sort2 DESC';

        //dump them in first room on the list
        //            $findroomresult=get_record_sql($findroomquery,true);
        $params = array(
            $oldbooking->description,
            $oldbooking->area_id,
            $oldbooking->start_time,
            $oldbooking->end_time,
            $oldbooking->start_time,
            $oldbooking->start_time,
            $oldbooking->end_time,
            $oldbooking->end_time,
            '%teaching%',
            $class_size,
            '%special%',
            $oldbooking->area_id
        );

        $findroomresult = $DB->get_record_sql($findroomquery, $params);

        $subject = get_string('bookingmoved', 'block_mrbs');
        $langvars = new stdClass;
        $langvars->name = $oldbooking->entryname;
        $langvars->id = $oldbooking->entryid;

        $langvars->oldroom = $oldbooking->room_name;
        $langvars->newroom = isset($findroomresult->room_name) ? $findroomresult->room_name : $oldbooking->room_name;
        $langvars->area = isset($findroomresult->area_name) ? $findroomresult->area_name : "";
        $langvars->date = date('d/m/Y', $oldbooking->start_time);
        $langvars->starttime = $hrstarttime;
        $langvars->newbookingname = $name;

        if($findroomresult){
            $booking = new stdClass;
            $booking->id = $oldbooking->entryid;
            $booking->room_id = $findroomresult->id;
                //If it is an imported booking, mark it as edited
            if ($oldbooking->type == 'K') {
                $booking->type = 'L';
            } else {
                $booking->type = $oldbooking->type;
            }
        }

        if ($findroomresult 
            and $DB->update_record('block_mrbs_entry', $booking) 
            and $oldbookingowner = $DB->get_record('user', array('username' => $oldbooking->create_by))) {

            $message = get_string('bookingmovedmessage', 'block_mrbs', $langvars);
            $output .= '<br>'.get_string('bookingmovedshort', 'block_mrbs', $langvars);
            //email_to_user($oldbookingowner, $USER, $subject, $message);
            $eventdata = new \core\message\message();
            $eventdata->courseid         = SITEID;
            $eventdata->component        = 'block_mrbs';
            $eventdata->name             = 'notification';
            $eventdata->userfrom         = $USER;
            $eventdata->userto           = $oldbookingowner;
            $eventdata->subject          = $subject;
            $eventdata->fullmessage      = html_to_text($message);;
            $eventdata->fullmessageformat = FORMAT_HTML;
            $eventdata->fullmessagehtml  = $message;
            $eventdata->smallmessage     = '';
            $eventdata->notification     = 1;

            if (!empty($CFG->supportemail)) {
                $eventdata->replyto = $CFG->supportemail;
            }else if (!empty($CFG->noreplyaddress)) {
                $eventdata->replyto = $CFG->noreplyaddress;
            }
            message_send($eventdata);

        } else {
            
            $output .= '<br>'.get_string('bookingmoveerrorshort', 'block_mrbs', $langvars);
            //mail($cfg_mrbs->admin_email, get_string('bookingmoveerror', 'block_mrbs'), get_string('bookingmoveerrormessage', 'block_mrbs', $langvars));
            
            $subject = get_string('bookingmoveerror', 'block_mrbs');

            $year = date('Y', $oldbooking->start_time);
            $month = date('n', $oldbooking->start_time);
            $day = date('j', $oldbooking->start_time);
            $langvars->link = $CFG->wwwroot.'/blocks/mrbs/web/day.php?area='.$oldbooking->area_id.'&year='.$year.'&month='.$month.'&day='.$day;
            
            
            $message = get_string('bookingmoveerrormessage', 'block_mrbs', $langvars);
            $messagetext = html_to_text($message);
            //to block_mrbs setting admin_email
            $fromuser = block_mrbs_generate_email_user($CFG->noreplyaddress, 'noreply');
            $adminName = '';
            if(!empty($cfg_mrbs->admin)){$adminName = $cfg_mrbs->admin;}
            $touser = block_mrbs_generate_email_user($cfg_mrbs->admin_email, $adminName);
            email_to_user($touser, $fromuser, $subject, $messagetext, $message);

            block_mrbs_mail_room_admin($oldbooking->room_id, $fromuser, $adminName, $subject, $message);
        }
    }

    return $output;
}

function block_mrbs_mail_room_admin($roomid, $fromuser, $adminName, $subject, $message){
    global $DB;
    $messagetext = html_to_text($message);
    
    $sql = "SELECT r.id, r.room_admin_email, r.area_id, a.area_admin_email
            FROM {block_mrbs_room} r
            LEFT JOIN {block_mrbs_area} a ON r.area_id=a.id
            WHERE r.id = :roomid ";
    if($results = $DB->get_record_sql($sql, array('roomid'=>$roomid))){
        if(!empty($results->room_admin_email)){
            $touser = block_mrbs_generate_email_user($results->room_admin_email, $adminName);
            email_to_user($touser, $fromuser, $subject, $messagetext, $message);
        }
        if(!empty($results->area_admin_email)){
            $touser = block_mrbs_generate_email_user($results->area_admin_email, $adminName);
            email_to_user($touser, $fromuser, $subject, $messagetext, $message);
        }
    }
}

/**
 * Generate a user info object based on provided parameters.
 *
 */
function block_mrbs_generate_email_user($email, $name = '', $id = -99) {
    $name = format_text($name, FORMAT_HTML, array('trusted' => false, 'noclean' => false));
    
    $emailuser = new stdClass();
    $emailuser->email = $email;
    $emailuser->firstname = $name;
    $emailuser->lastname = '';
    $emailuser->maildisplay = true;
    $emailuser->mailformat = 1; // 0 (zero) text-only emails, 1 (one) for HTML emails.
    $emailuser->id = $id;
    $emailuser->firstnamephonetic = '';
    $emailuser->lastnamephonetic = '';
    $emailuser->middlename = '';
    $emailuser->alternatename = '';

    return $emailuser;
}