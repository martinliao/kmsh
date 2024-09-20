<?php
/**
 * 
 * @package clickap_hourcategories
 * @author 2018 Mary Tan
 * @copyright CLICK-AP (https://www.click-ap.com)
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die;

function clickap_hourcategory_create($data) {
    global $DB;

    $data->timemodified = time();
    if($maxsort = $DB->get_field('clickap_hourcategories', 'max(sortorder)',  array('year'=>$data->year))){
        $data->sortorder = ++$maxsort;
    } else{
        $data->sortorder = 1;
    }
    
    //return $DB->insert_record('clickap_hourcategories', $data);
    if($id = $DB->insert_record('clickap_hourcategories', $data)){
        if($data->visible && $data->requirement > 0){
            /*
            if($permanent = $DB->get_record('clickap_hourcategories', array('year'=>$data->year, 'idnumber'=>'permanent'))){
                $udata = new stdClass();
                $udata->id = $permanent->id;
                $udata->requirement = $permanent->requirement + $data->requirement;
                $DB->update_record('clickap_hourcategories', $udata);
            }
            */
        }
    }
    return $id;
}

function clickap_hourcategory_update($data) {
    global $DB;
    
    //return $DB->update_record('clickap_hourcategories', $data);
    if($org = $DB->get_record('clickap_hourcategories', array('id'=>$data->id))){
        $data->timemodified = time();
        $DB->update_record('clickap_hourcategories', $data);

        if($data->requirement > 0) {
            /*
            if($permanent = $DB->get_record('clickap_hourcategories', array('year'=>$data->year, 'idnumber'=>'permanent'))){
                if($data->visible){
                    $udata = new stdClass();
                    $udata->id = $permanent->id;
                    if($org->visible == 0){
                        $udata->requirement = $permanent->requirement + $data->requirement;
                    }else {
                        $udata->requirement = $permanent->requirement + ($data->requirement - $org->requirement);
                    }
                    $DB->update_record('clickap_hourcategories', $udata);
                }
                else {
                    $udata = new stdClass();
                    $udata->id = $permanent->id;
                    $udata->requirement = $permanent->requirement - $org->requirement;
                    $DB->update_record('clickap_hourcategories', $udata);
                }
            }
            */
        }
        return true;
    }    
    return false;
}

function clickap_hourcategory_delete($id){
    global $DB;
    
    //return $DB->delete_records('clickap_hourcategories', array('id' => $id));
    if($data = $DB->get_record('clickap_hourcategories', array('id'=>$id))){
        $DB->delete_records('clickap_hourcategories', array('id' => $data->id));
        
        if($data->visible && $data->requirement > 0){
            /*
            if($permanent = $DB->get_record('clickap_hourcategories', array('year'=>$data->year, 'idnumber'=>'permanent'))){
                $udata = new stdClass();
                $udata->id = $permanent->id;
                
                if($total = $DB->get_field('clickap_hourcategories', 'sum(requirement)',  array('year'=>$data->year, 'type'=>0, 'visible'=>1))){
                    $udata->requirement = $total;                    
                }else{
                    $udata->requirement = 0;
                }

                $DB->update_record('clickap_hourcategories', $udata);
            }
            */
        }
        return true;
    }
    return false;
}

function clickap_hourcategories_automatic_create(){
    global $DB;

    $normals = $DB->get_records('clickap_hourcategories', array('year'=>0));
    $maxyear = $DB->get_field('clickap_hourcategories', 'max(year)',array());
    if($maxyear != 0){
        $currentyear = $maxyear + 1;
    }else{
        $currentyear = date('Y') - 1911;
    }

    foreach($normals as $normal){
        $data = new stdClass();
        $data->year         = $currentyear;
        $data->name         = $normal->name;
        $data->idnumber     = $normal->idnumber;
        $data->sortorder    = $normal->sortorder;
        $data->type         = $normal->type;
        $data->visible      = $normal->visible;
        $data->requirement  = $normal->requirement;
        $data->timemodified =  time();
        $DB->insert_record('clickap_hourcategories', $data);
    }
    return true;
}

function clickap_hourcategories_duplicate_categories($data){
    global $DB;

    $origin = $data->origin;
    $dest = $data->dest;

    if(!$data->retain){
        //delete destination year all categories
        $delsql = "DELETE FROM {clickap_hourcategories} WHERE year =:year AND type = 0";
        $DB->execute($delsql, array('year'=>$dest));
    }
    
    $copyprofile = false;
    $dbman = $DB->get_manager();
    $table = new xmldb_table('clickap_hourcredit_profile');
    if ($dbman->table_exists($table) && $data->copyprofile) {
        $copyprofile = true;
    }

    $maxsort = $DB->get_field('clickap_hourcategories', 'max(sortorder)',  array('year'=>$dest));
    if($origindata = $DB->get_records('clickap_hourcategories', array('year'=>$origin, 'type'=>0), 'sortorder')){
        foreach($origindata as $data){
            if($copyprofile){
                //get origin year profile categories
                $profiledata = $DB->get_records('clickap_hourcredit_profile', array('year'=>$origin, 'hcid'=>$data->id));
            }
            
            unset($data->id);
            $data->year = $dest;
            $data->sortorder = ++$maxsort;
            $data->timemodified = time();
            $data->id = $DB->insert_record('clickap_hourcategories', $data);
            if(isset($profiledata) && $profiledata){
                foreach($profiledata as $pdata){
                    unset($pdata->id);
                    $pdata->hcid = $data->id;
                    $pdata->year = $dest;
                    $pdata->timemodified = time();
                    $DB->insert_record('clickap_hourcredit_profile', $pdata, false);
                }
            }
        }
        
        /*
        if($permanent = $DB->get_record('clickap_hourcategories', array('year'=>$dest, 'idnumber'=>'permanent'))){
            $total = $DB->get_field('clickap_hourcategories', 'sum(requirement)',  array('year'=>$dest, 'type'=>0, 'visible'=>1));
            
            $udata = new stdClass();
            $udata->id = $permanent->id;
            $udata->requirement = $total;
            $DB->update_record('clickap_hourcategories', $udata);
        }
        */
    }
    return true;
}