<?php
/**
 * coursehours block settings
 *
 * @package    block_coursehours
 * @copyright  2016 Mary Chen(http://www.click-ap.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

function block_coursehours_enrol_get_my_courses_year($userid){
    global $DB;
    
    $params = array('siteid' => SITEID, 'userid1' => $userid);
    $sql = "SELECT DISTINCT from_unixtime(c.startdate, '%Y')-1911 as year
            FROM {course} c 
            JOIN (SELECT DISTINCT e.courseid FROM {enrol} e
                  JOIN {user_enrolments} ue ON (ue.enrolid = e.id AND ue.userid = :userid1)
                  WHERE ue.status = '0' AND e.status = '0') en ON (en.courseid = c.id)
            LEFT JOIN {context} ctx ON (ctx.instanceid = c.id AND ctx.contextlevel = '50')
            WHERE c.id <> :siteid";

    $blocks_plugins = get_plugin_list('block');
    if(array_key_exists("externalverify", $blocks_plugins)){
        $sql .= " UNION
                  SELECT from_unixtime(c.startdate, '%Y')-1911 as year
                  FROM {course_external} c
                  WHERE c.status = 1 AND c.userid = :userid2";
        $params['userid2'] = $userid;
    }

    $clickap_plugins = get_plugin_list('clickap');
    if(array_key_exists("legacy", $clickap_plugins)){
        $sql .= " UNION
                  SELECT from_unixtime(c.startdate, '%Y')-1911 as year
                  FROM {clickap_legacy} c
                  WHERE c.userid = :userid3";
        $params['userid3'] = $userid;
    }
            
    $years = $DB->get_records_sql($sql, $params);
    
    return $years;
}

function block_coursehours_list_myhours($currentyear, $user = null){
    global $CFG, $DB, $USER, $PAGE;

    if(empty($user)){$user = $USER;}
    
    $render = $PAGE->get_renderer('clickap_hourcategories');

    $hasProfile = false;
    $plugins = get_plugin_list('hourcredit');
    if (array_key_exists("profile", $plugins)){
        if($hprender = $PAGE->get_renderer('hourcredit_profile')){
            $hasProfile = true;
        }
    }

    $strrequire = get_string('requirement_everyone', 'block_coursehours');
    $strprofile = get_string('requirement_profile', 'block_coursehours');
    $strrequirement = get_string('hour_requirement', 'block_coursehours');
    $strcompleted = get_string('hour_completed', 'block_coursehours');
    $strnotcompletion = get_string('hour_not-completed', 'block_coursehours');
    $strrate = get_string('hour_rate', 'block_coursehours');
    
    $table = new html_table();
    $table->attributes = array('class'=>'generaltable', 'style'=>'display: table;');//  display: table;table-layout:fixed;
    $table->wrap = array('nowrap');
    if($hasProfile){
        $table->head  = array('&nbsp;', $strrequire, $strprofile, $strrequirement, $strcompleted, $strnotcompletion, $strrate);
        $table->align  = array('left', 'center', 'center', 'center', 'center', 'center', 'right');
    }else{
        $table->head  = array('&nbsp;', $strrequire, $strcompleted, $strnotcompletion, $strrate);
        $table->align  = array('left', 'center', 'center', 'center', 'right');
    }
    //get enroll courses hour
    $myHours = $render->get_my_hours($currentyear, $user);
    $ExtTotalHour = isset($myHours['ext']) ? array_sum($myHours['ext']) : 0;
    //get require categories
    $rules = $DB->get_records('clickap_hourcategories', array('year'=>$currentyear, 'type'=>0, 'visible'=>1));

    $count = $total_rate = $total_profile = 0;
    foreach($rules as $r){
        $total = $r->requirement;

        if($hasProfile){//get profile categories
            $r->profilerequire = 0;
            if($profiles = $DB->get_records('clickap_hourcredit_profile', array('hcid'=>$r->id, 'year'=>$r->year, 'visible'=>1))){
                foreach($profiles as $p){
                    if($hprender->render_is_rules_user($p->rules, $user->id)){
                        $r->profilerequire += $p->requirement;
                        $total_profile += $p->requirement;
                        $total += $p->requirement;
                    }
                }
            }
        }
        
        //如果 requirement =0 則不顯示
        //if($total == 0){continue;}

        /*
        //user arrival not enough a year
        if(!empty($arrivalY) && $currentyear == $arrivalY){
            $requirement = ceil($r->requirement * ((12-$arrivalM+1)/12));
        }
        */
        ++$count;
        
        $list = array();
        $params = array('userid'=>$user->id, 'selectyear'=>$currentyear, 'category'=>$r->id);
        $url = new moodle_url('/blocks/coursehours/courses.php', $params);
        $list['name'] = '<a href = "'.$url.'">'.$r->name.'</a>';
        if($hasProfile){
            $list['general'] = $r->requirement;
            $list['profile'] = $r->profilerequire;
        }
        $list['require'] = $total;
                    
        if(isset($myHours[$r->id])){
            $completeHours = $myHours[$r->id];
            $list['complete'] = $completeHours."(".$myHours['ext'][$r->id].")";
        }else{
            $completeHours = $list['complete'] = 0;
        }

        $notCompleted = $list['require'] - $completeHours;
        if($notCompleted <= 0){
            $list['notcomplete'] = 0;
        }else{
            $list['notcomplete'] = get_string('notcompletedhour', 'block_coursehours', $notCompleted);
        }

        if($list['require'] > 0){
            $rate = number_format(($completeHours / $list['require']), 2) * 100;
            if($rate > 100) {$rate = '100';} 
        }else{
            $rate = '100';
        }
        $total_rate += $rate;
        
        $list['rate'] = get_string('completedrate', 'block_coursehours', $rate);
        $table->data[] = new html_table_row($list);    
    }

    //display default categories
    $rules = $DB->get_records('clickap_hourcategories', array('year'=>$currentyear, 'type'=>1, 'visible'=>1));
    foreach($rules as $r){
        $completed = $rate = 0;

        if($r->idnumber == "permanent" || $r->idnumber == "contract"){
            $completed = $myHours['total'];
        }else if($r->idnumber == "mode-1"){
            $completed = isset($myHours["mode-1"]) ? $myHours["mode-1"] : 0;
        }else if($r->idnumber == "mode-2"){
            $completed = isset($myHours["mode-2"]) ? $myHours["mode-2"] : 0;
        }
        
        $totalRequire = $r->requirement;
        if($hasProfile){
            $totalRequire += $total_profile;
        }
        $not_completed = $totalRequire - $completed;
        if($not_completed < 0){
            $not_completed = 0;
        }else{
            $not_completed = get_string('notcompletedhour', 'block_coursehours', $not_completed);
        }
        
        if($user->auth == 'kmsh'){
            if($r->idnumber == "newcomer"){
                //no show
            }
            else if($r->idnumber == "permanent"){
                if($count > 0){
                    $rate = round($total_rate / $count);
                }
                $rate = get_string('completedrate', 'block_coursehours', $rate);
                
                $rows = new html_table_row();
                if($hasProfile){
                    $rows->cells = array($r->name, $r->requirement, $total_profile, $totalRequire, $completed, $not_completed, $rate);
                }
                else {
                    $rows->cells = array($r->name, $totalRequire, $completed."(".$ExtTotalHour.")", $not_completed, $rate);
                }
                $rows->id = $r->idnumber;
                $rows->attributes['class'] = 'permanent-row';
                $table->data[] = $rows;
            }    
        }
        else {
            if($r->idnumber == "contract"){
                if($count > 0){
                    $rate = round($total_rate / $count);
                }
                $rate = get_string('completedrate', 'block_coursehours', $rate);
                
                $rows = new html_table_row();
                if($hasProfile){
                    $rows->cells = array($r->name, $r->requirement, $total_profile, $totalRequire, $completed, $not_completed, $rate);
                }
                else {
                    $rows->cells = array($r->name, $totalRequire, $completed."(".$ExtTotalHour.")", $not_completed, $rate);
                }
                $rows->id = $r->idnumber;
                $rows->attributes['class'] = 'contract-row';
                $table->data[] = $rows;
            }
        }
        
        if($r->idnumber == "mode-1" || $r->idnumber == "mode-2"){
            $rate = number_format(($completed / $totalRequire), 2) * 100;
            if($rate > 100) {
                $rate = '100';
            }
            $rate = get_string('completedrate', 'block_coursehours', $rate);
            
            $rows = new html_table_row();
            if($hasProfile){
                $rows->cells = array($r->name, $r->requirement, $total_profile, $totalRequire, $completed, $not_completed, $rate);
            }
            else {
                $rows->cells = array($r->name, $totalRequire, $completed, $not_completed, $rate);
            }
            $rows->id = $r->idnumber;
            $rows->attributes['class'] = 'mode-row';
            $table->data[] = $rows;
        }
        else if (!in_array($r->idnumber, array('permanent', 'contract', 'newcomer'))){
            $rate = get_string('completedrate', 'block_coursehours', $rate);
            if($hasProfile){
                $table->data[] = new html_table_row(array($r->name, $r->requirement, $total_profile, $totalRequire, $completed, $not_completed, $rate));
            }else{
                $table->data[] = new html_table_row(array($r->name, $totalRequire, $completed, $not_completed, $rate));
            }
        }
    }
    
    return html_writer::table($table);
}

function block_coursehours_list_myhours_excel($currentyear, $user = null, &$worksheet){
    global $CFG, $DB, $USER, $PAGE;

    $render = $PAGE->get_renderer('clickap_hourcategories');
    
    $hasProfile = false;
    $plugins = get_plugin_list('hourcredit');
    if (array_key_exists("profile", $plugins)){
        if($hprender = $PAGE->get_renderer('hourcredit_profile')){
            $hasProfile = true;
        }
    }
    
    if(empty($user)){$user = $USER;}

    $col = 0;
    $worksheet->write(0, $col++, get_string('hourcategories', 'block_coursehours'));
    if($hasProfile){
        $worksheet->write(0, $col++, get_string('requirement_everyone', 'block_coursehours'));
        $worksheet->write(0, $col++, get_string('requirement_profile', 'block_coursehours'));
    }
    $worksheet->write(0, $col++, get_string('hour_requirement', 'block_coursehours'));
    $worksheet->write(0, $col++, get_string('hour_completed', 'block_coursehours'));
    $worksheet->write(0, $col++, get_string('hour_not-completed', 'block_coursehours'));
    $worksheet->write(0, $col++, get_string('hour_rate', 'block_coursehours'));
    
    //get enroll courses hour
    $myHours = $render->get_my_hours($currentyear, $user);
    $ExtTotalHour = array_sum($myHours['ext']);
    //get require categories
    $rules = $DB->get_records('clickap_hourcategories', array('year'=>$currentyear, 'type'=>0, 'visible'=>1));

    $count = $total_rate = $total_profile = 0;
    $row = 1;
    foreach($rules as $r){
        $total = $r->requirement;

        if($hasProfile){//get profile categories
            $r->profilerequire = 0;
            if($profiles = $DB->get_records('clickap_hourcredit_profile', array('hcid'=>$r->id, 'year'=>$r->year, 'visible'=>1))){
                foreach($profiles as $p){
                    if($hprender->render_is_rules_user($p->rules, $user->id)){
                        $r->profilerequire += $p->requirement;
                        $total_profile += $p->requirement;
                        $total += $p->requirement;
                    }
                }
            }
        }
        
        //如果 requirement =0 則不顯示
        //if($total == 0){continue;}

        /*
        //user arrival not enough a year
        if(!empty($arrivalY) && $currentyear == $arrivalY){
            $requirement = ceil($r->requirement * ((12-$arrivalM+1)/12));
        }
        */
        ++$count;
        
        $list = array();
        $list['name'] = $r->name;
        if($hasProfile){
            $list['general'] = $r->requirement;
            $list['profile'] = $r->profilerequire;
        }
        $list['require'] = $total;
                    
        if(isset($myHours[$r->id])){
            $completeHours = $myHours[$r->id];
            $list['complete'] = $completeHours."(".$myHours['ext'][$r->id].")";
        }else{
            $completeHours = $list['complete'] = 0;
        }

        $notCompleted = $list['require'] - $completeHours;
        if($notCompleted <= 0){
            $list['notcomplete'] = 0;
        }else{
            $list['notcomplete'] = $notCompleted;
        }
        
        if($list['require'] > 0){
            $rate = number_format(($completeHours / $list['require']), 2) * 100;
            if($rate > 100) {$rate = '100';} 
        }else{
            $rate = '100';
        }
        $total_rate += $rate;
        
        $list['rate'] = get_string('completedrate', 'block_coursehours', $rate);
        
        $col = 0;
        $worksheet->write($row, $col++, $list['name']);
        if($hasProfile){
            $worksheet->write($row, $col++, $list['general']);
            $worksheet->write($row, $col++, $list['profile']);
        }
        $worksheet->write($row, $col++, $list['require']);
        $worksheet->write($row, $col++, $list['complete']);
        $worksheet->write($row, $col++, $list['notcomplete']);
        $worksheet->write($row, $col++, $list['rate']);
        
        $row++;
    }

    //display default categories
    $rules = $DB->get_records('clickap_hourcategories', array('year'=>$currentyear, 'type'=>1, 'visible'=>1));
    foreach($rules as $r){
        $completed = $rate = 0;
        if($r->idnumber == "permanent" || $r->idnumber == "contract" ){
            $completed = $myHours['total'];
        }else if($r->idnumber == "mode-1"){
            $completed = isset($myHours["mode-1"]) ? $myHours["mode-1"] : 0;
        }else if($r->idnumber == "mode-2"){
            $completed = isset($myHours["mode-2"]) ? $myHours["mode-2"] : 0;
        }
        
        $totalRequire = $r->requirement;
        if($hasProfile){
            $totalRequire += $total_profile;
        }
        
        $not_completed = $totalRequire - $completed;
        if($not_completed < 0){
            $not_completed = 0;
        }else{
            $not_completed = $not_completed;
        }
        
        $showrule = false;
        if($user->auth == 'kmsh'){
            if($r->idnumber == "newcomer"){
                $showrule = true;
                //no show
                continue;
            }
            else if($r->idnumber == "permanent"){
                $showrule = true;
                if($count > 0){
                    $rate = round($total_rate / $count);
                }
            }
        }else {
            if($r->idnumber == "contract"){
                $showrule = true;
                if($count > 0){
                    $rate = round($total_rate / $count);
                }
            }
        }
        
        if($r->idnumber == "mode-1" || $r->idnumber == "mode-2"){
            $showrule = true;
            $rate = number_format(($completed / $totalRequire), 2) * 100;
            if($rate > 100) {$rate = '100';}
        }
        
        if($showrule) {
            $rate = get_string('completedrate', 'block_coursehours', $rate);
            
            $col = 0;
            $worksheet->write($row, $col++, $r->name);
            if($hasProfile){
                $worksheet->write($row, $col++, $r->requirement);
                $worksheet->write($row, $col++, $total_profile);
            }
            $worksheet->write($row, $col++, $totalRequire);
            $worksheet->write($row, $col++, $completed."(".$ExtTotalHour.")");
            $worksheet->write($row, $col++, $not_completed);
            $worksheet->write($row, $col++, $rate);
            $row++;
        }
    }
}