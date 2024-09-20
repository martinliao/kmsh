<?php
/**
 * Version details.
 *
 * @package    clickap_code
 * @copyright  2021 CLICK-AP {@link https://www.click-ap.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die;
define('CODE_TABLE', 'clickap_code');

function clickap_code_table_exists() {
	global $DB;
	$dbman = $DB->get_manager();
	return $dbman->table_exists(CODE_TABLE);
}

function clickap_code_get_list($type,$canedit) {
    global $DB, $OUTPUT;
    
    $str_type = get_string('type','clickap_code');
    $str_name = get_string('name','clickap_code');
    $str_code = get_string('idnumber','clickap_code');
    $str_status = get_string('status','clickap_code');
    $str_function = get_string('function','clickap_code');
    
    $params = array();
    if(!empty($type)){
        $params['type'] = $type;
    }
       
    $data = $DB->get_records('clickap_code' ,$params ,'sortorder');
    
    $table = new html_table();
    $table->attributes['class'] = 'generaltable';
    $table->width = '100%';
    $table->attributes = array('style'=>'display: table;', 'class'=>'');
    if($canedit){
        $table->head = array('&nbsp;', $str_type, $str_name, $str_code, $str_status, $str_function);
        $table->align = array('center', 'left', 'left', 'center', 'center', 'center');
    }else{
        $table->head = array('&nbsp;', $str_type, $str_name, $str_code, $str_status);
        $table->align = array('center', 'left', 'left', 'center', 'center');
    }
    
    $table->size = array('3%', '20%', '35%', '15%', '10%', '15%');
            
    if(sizeof($data) > 0){
        $analyze_total = array();
        $c=1;
        foreach($data as $value){
            $row = array();
            $row[] = $c;
            $row[] = $value->type;
            $row[] = $value->name;
            $row[] = $value->idnumber;
            if($value->status){
                $row[] = get_string('enable', 'clickap_code');
            }else{
                $row[] = get_string('disable', 'clickap_code');
            }
            if($canedit){
                //Edit
                $options = array('title' => get_string('edit'));
                $image = '<img src="'.$OUTPUT->image_url('t/edit').'" alt="'.$options['title'].'" />';
                $function = html_writer::link(new moodle_url('edit.php', array('id' => $value->id)), $image, $options);
                //Delete
                $options = array('title' => get_string('delete'));
                $image = '<img src="'.$OUTPUT->image_url('t/delete').'" alt="'.$options['title'].'" />';
                $function .= '&nbsp;'.html_writer::link(new moodle_url('delete.php', array('id' => $value->id)), $image, $options);
                if($c > 1){
                    $options = array('title' => get_string('moveup'));
                    $image = '<img src="'.$OUTPUT->image_url('i/up').'" alt="'.$options['title'].'" />';
                    $function .= '&nbsp;'.html_writer::link(new moodle_url('index.php', array('id' => $value->id, 'type'=>$type, 'action'=>'moveup', 'sesskey'=>sesskey())), $image, $options);
                }
                if($c < sizeof($data)){
                    $options = array('title' => get_string('movedown'));
                    $image = '<img src="'.$OUTPUT->image_url('i/down').'" alt="'.$options['title'].'" />';
                    $function .= '&nbsp;'.html_writer::link(new moodle_url('index.php', array('id' => $value->id, 'type'=>$type, 'action'=>'movedown', 'sesskey'=>sesskey())), $image, $options);
                }
            }            
            $row[] = $function;
            
            $table->data[] = new html_table_row($row);
            $c++;
        }
    }
    return $table;

}

function clickap_code_update($data) {
    global $DB, $USER;
    $data->usermodified = $USER->id;
    $data->timemodified = time();
    
    $event = array (
        'context' => context_system::instance(),
        'objectid' => $data->id,
        'relateduserid' => $USER->id,
        'other' => array('code' => $data->type.":".$data->name)
    );
    \clickap_code\event\clickapcode_updated::create($event)->trigger();
    
    return $DB->update_record(CODE_TABLE, $data);
}

function clickap_code_create($data) {
    global $DB, $USER;
    $data->usermodified = $USER->id;
    $data->timecreated = time();
    $data->timemodified = time();
                
    $max_sort = $DB->get_field_sql('SELECT max(sortorder) FROM {clickap_code} WHERE type = ?', array($data->type));
    $data->sortorder = $max_sort + 1;
    
    $id = $DB->insert_record(CODE_TABLE, $data);
    $event = array (
        'context' => context_system::instance(),
        'objectid' => $id,
        'relateduserid' => $USER->id,
        'other' => array('code' => $data->type.":".$data->name)
    );
    \clickap_code\event\clickapcode_created::create($event)->trigger();

    return $id;
}

function clickap_code_delete($data){
    global $DB, $USER;
    
    $event = array (
        'context' => context_system::instance(),
        'objectid' => $data->id,
        'relateduserid' => $USER->id,
        'other' => array('code' => $data->type.":".$data->name)
    );
    \clickap_code\event\clickapcode_deleted::create($event)->trigger();
            
    return $DB->delete_records('clickap_code',array('id'=>$data->id));
    
}

function clickap_code_moveup($id){
    global $DB;
    
    $data = $DB->get_record('clickap_code',array('id'=>$id));
    
    $swapdata = $DB->get_record('clickap_code', array('type'=>$data->type,'sortorder'=>$data->sortorder-1), $fields='*', MUST_EXIST);
    if ($swapdata) {
        $DB->set_field('clickap_code', 'sortorder', $swapdata->sortorder, array('id' => $data->id));
        $DB->set_field('clickap_code', 'sortorder', $data->sortorder, array('id' => $swapdata->id));
    }
}

function clickap_code_movedown($id){
    global $DB;
    
    $data = $DB->get_record('clickap_code',array('id'=>$id));
    $swapdata = $DB->get_record('clickap_code', array('type'=>$data->type,'sortorder'=>$data->sortorder+1), $fields='*', MUST_EXIST);
    if ($swapdata) {
        $DB->set_field('clickap_code', 'sortorder', $swapdata->sortorder, array('id' => $data->id));
        $DB->set_field('clickap_code', 'sortorder', $data->sortorder, array('id' => $swapdata->id));
    }
}

function clickap_code_get_all($type = null) {
    global $DB;
    
    $params = array('status'=>1);
    if(!empty($type)){
        $params['type'] = $type;
    }
    
    $results = array();
    if ($datas = $DB->get_records('clickap_code', $params, 'sortorder')) {
        foreach($datas as $code) {
            $results[$code->type][$code->id] = $code->name;
        }
    }
    return $results;
}
