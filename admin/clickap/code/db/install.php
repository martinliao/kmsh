<?php
/**
 * Version details.
 *
 * @package    clickap_code
 * @copyright  2021 CLICK-AP {@link https://www.click-ap.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

function xmldb_clickap_code_install() {
    global $DB;
    
    /**
     * genre:課程分類(cwblms)
     * period:上課時機(audix)
     * cert:證照類別(kmsh)
     */
    set_config('type','model,credit,unit,city,cert','clickap_code');
    
    $dbman = $DB->get_manager();

    $table = new xmldb_table('mooccourse_course_code');
    if($dbman->table_exists($table)){
        $dbman->rename_table($table, 'clickap_code');
        
        $table = new xmldb_table('clickap_code');
        $field = new xmldb_field('code');
        if($dbman->field_exists($table, $field)) {
            $field->set_attributes(XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, null);
            $dbman->rename_field($table, $field, 'idnumber');
        }

        $field = new xmldb_field('usermodified', XMLDB_TYPE_INTEGER, '10');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $index = new xmldb_index('fk_type', XMLDB_INDEX_NOTUNIQUE, array('type'));
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        set_config('type','model,credit,unit,city','clickap_code');
        $sql = "UPDATE {clickap_code} SET type ='model' WHERE type ='mode'";
        $DB->execute($sql);
    }
    else{
        $table = new xmldb_table('clickap_code');
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('type', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('idnumber', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, null);
        $table->add_field('status', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, null);
        $table->add_field('sortorder', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('usermodified', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_index('fk_type', XMLDB_INDEX_NOTUNIQUE, ['type']);
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }
        
        $now = time();
        
        $entries = array();
        //mode:學習性質
        $entries[] = array('type'=>'model','name'=>'數位', 'idnumber'=>1, 'status'=>1, 'sortorder'=>1, 'timecreated'=>$now);
        $entries[] = array('type'=>'model','name'=>'實體', 'idnumber'=>2, 'status'=>1, 'sortorder'=>2, 'timecreated'=>$now);
        $entries[] = array('type'=>'model','name'=>'混成', 'idnumber'=>3, 'status'=>1, 'sortorder'=>3, 'timecreated'=>$now);
        //credit:學位學分
        $entries[] = array('type'=>'credit','name'=>'博士', 'idnumber'=>1, 'status'=>1, 'sortorder'=>1, 'timecreated'=>$now);
        $entries[] = array('type'=>'credit','name'=>'碩士', 'idnumber'=>2, 'status'=>1, 'sortorder'=>2, 'timecreated'=>$now);
        $entries[] = array('type'=>'credit','name'=>'大學', 'idnumber'=>3, 'status'=>1, 'sortorder'=>3, 'timecreated'=>$now);
        $entries[] = array('type'=>'credit','name'=>'專科', 'idnumber'=>4, 'status'=>1, 'sortorder'=>4, 'timecreated'=>$now);
        $entries[] = array('type'=>'credit','name'=>'學分', 'idnumber'=>5, 'status'=>1, 'sortorder'=>5, 'timecreated'=>$now);
        $entries[] = array('type'=>'credit','name'=>'無'  , 'idnumber'=>6, 'status'=>1, 'sortorder'=>6, 'timecreated'=>$now);
        //unti:訓練總數單位
        $entries[] = array('type'=>'unit','name'=>'小時', 'idnumber'=>1, 'status'=>1, 'sortorder'=>1, 'timecreated'=>$now);
        $entries[] = array('type'=>'unit','name'=>'天'  , 'idnumber'=>2, 'status'=>1, 'sortorder'=>2, 'timecreated'=>$now);
        $entries[] = array('type'=>'unit','name'=>'學分', 'idnumber'=>6, 'status'=>1, 'sortorder'=>3, 'timecreated'=>$now);
        //city:上課縣市
        $entries[] = array('type'=>'city','name'=>'海外'  , 'idnumber'=>'01', 'status'=>1, 'sortorder'=>1, 'timecreated'=>$now);
        $entries[] = array('type'=>'city','name'=>'臺北市', 'idnumber'=>'10', 'status'=>1, 'sortorder'=>2, 'timecreated'=>$now);
        $entries[] = array('type'=>'city','name'=>'基隆市', 'idnumber'=>'20', 'status'=>1, 'sortorder'=>3, 'timecreated'=>$now);
        $entries[] = array('type'=>'city','name'=>'福建省連江縣', 'idnumber'=>'21', 'status'=>1, 'sortorder'=>4, 'timecreated'=>$now);
        $entries[] = array('type'=>'city','name'=>'臺北縣', 'idnumber'=>'22', 'status'=>1, 'sortorder'=>5, 'timecreated'=>$now);
        $entries[] = array('type'=>'city','name'=>'新北市', 'idnumber'=>'23', 'status'=>1, 'sortorder'=>6, 'timecreated'=>$now);
        $entries[] = array('type'=>'city','name'=>'宜蘭縣', 'idnumber'=>'26', 'status'=>1, 'sortorder'=>7, 'timecreated'=>$now);
        $entries[] = array('type'=>'city','name'=>'新竹市', 'idnumber'=>'30', 'status'=>1, 'sortorder'=>8, 'timecreated'=>$now);
        $entries[] = array('type'=>'city','name'=>'新竹縣', 'idnumber'=>'31', 'status'=>1, 'sortorder'=>9, 'timecreated'=>$now);
        $entries[] = array('type'=>'city','name'=>'桃園縣', 'idnumber'=>'32', 'status'=>1, 'sortorder'=>10, 'timecreated'=>$now);
        $entries[] = array('type'=>'city','name'=>'桃園市', 'idnumber'=>'33', 'status'=>1, 'sortorder'=>11, 'timecreated'=>$now);
        $entries[] = array('type'=>'city','name'=>'苗栗縣', 'idnumber'=>'35', 'status'=>1, 'sortorder'=>12, 'timecreated'=>$now);
        $entries[] = array('type'=>'city','name'=>'臺中市', 'idnumber'=>'40', 'status'=>1, 'sortorder'=>13, 'timecreated'=>$now);
        $entries[] = array('type'=>'city','name'=>'臺中縣', 'idnumber'=>'41', 'status'=>1, 'sortorder'=>14, 'timecreated'=>$now);
        $entries[] = array('type'=>'city','name'=>'彰化縣', 'idnumber'=>'50', 'status'=>1, 'sortorder'=>15, 'timecreated'=>$now);
        $entries[] = array('type'=>'city','name'=>'南投縣', 'idnumber'=>'54', 'status'=>1, 'sortorder'=>16, 'timecreated'=>$now);
        $entries[] = array('type'=>'city','name'=>'嘉義市', 'idnumber'=>'60', 'status'=>1, 'sortorder'=>17, 'timecreated'=>$now);
        $entries[] = array('type'=>'city','name'=>'嘉義縣', 'idnumber'=>'61', 'status'=>1, 'sortorder'=>18, 'timecreated'=>$now);
        $entries[] = array('type'=>'city','name'=>'雲林縣', 'idnumber'=>'63', 'status'=>1, 'sortorder'=>19, 'timecreated'=>$now);
        $entries[] = array('type'=>'city','name'=>'臺南市', 'idnumber'=>'70', 'status'=>1, 'sortorder'=>20, 'timecreated'=>$now);
        $entries[] = array('type'=>'city','name'=>'臺南縣', 'idnumber'=>'71', 'status'=>1, 'sortorder'=>21, 'timecreated'=>$now);
        $entries[] = array('type'=>'city','name'=>'高雄市', 'idnumber'=>'80', 'status'=>1, 'sortorder'=>22, 'timecreated'=>$now);
        $entries[] = array('type'=>'city','name'=>'高雄縣', 'idnumber'=>'81', 'status'=>1, 'sortorder'=>23, 'timecreated'=>$now);
        $entries[] = array('type'=>'city','name'=>'澎湖縣', 'idnumber'=>'88', 'status'=>1, 'sortorder'=>24, 'timecreated'=>$now);
        $entries[] = array('type'=>'city','name'=>'福建省金門縣', 'idnumber'=>'89', 'status'=>1, 'sortorder'=>25, 'timecreated'=>$now);
        $entries[] = array('type'=>'city','name'=>'屏東縣', 'idnumber'=>'90', 'status'=>1, 'sortorder'=>26, 'timecreated'=>$now);
        $entries[] = array('type'=>'city','name'=>'臺東縣', 'idnumber'=>'95', 'status'=>1, 'sortorder'=>27, 'timecreated'=>$now);
        $entries[] = array('type'=>'city','name'=>'花蓮縣', 'idnumber'=>'97', 'status'=>1, 'sortorder'=>28, 'timecreated'=>$now);

        $DB->insert_records('clickap_code', $entries);
    }

    $entries = array();
    //model:學習性質(混成)
    if(!$DB->record_exists('clickap_code', array('type'=>'model','idnumber'=>3))){    
        $entries[] = array('type'=>'model','name'=>'混成', 'idnumber'=>3, 'status'=>1, 'sortorder'=>3, 'timecreated'=>time());
        $DB->insert_records('clickap_code', $entries);
    }

    return true;
}