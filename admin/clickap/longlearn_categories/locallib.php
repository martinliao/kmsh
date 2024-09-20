<?php
/**
 * @package    clickap
 * @subpackage longlearn_categories
 * @copyright  2024 CLICK-AP {@https://www.click-ap.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

function insert_data($record) {
    global $DB;
    /*
    $sql = "INSERT INTO {$CFG->prefix}longlearn_categories 
        (id, idnumber, name, depth, parent, path, timemodified) VALUES 
        ($record->id, $record->idnumber, '$record->name', $record->depth, $record->parent, '$record->path', $record->timemodified)
    ";
    echo 'insert : '.$record->name.'<br>';
    $pdo = new PDO("mysql:dbname=$CFG->dbname;host=$CFG->dbhost", $CFG->dbuser, $CFG->dbpass);
    $pdo->exec("SET CHARACTER SET utf8");
    $pdo->query($sql);
    */
    
    $data = new stdClass();
    $data->id = $record->id;
    $data->idnumber = $record->idnumber;
    $data->name = $record->name;
    $data->depth = $record->depth;
    $data->parent = $record->parent;
    $data->path = $record->path;
    $data->timemodified = time();
    
    $DB->insert_record('longlearn_categories', $data);
    
    echo 'insert : '.$record->name.'<br>';
}
