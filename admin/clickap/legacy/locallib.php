<?php
/**
 * @package    clickap
 * @subpackage legacy
 * @copyright  2024 CLICK-AP {@https://www.click-ap.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

function insert_data($record) {
    global $DB;
    /*
    $sql = "INSERT INTO {$CFG->prefix}legacy 
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
    
    $DB->insert_record('clickap_legacy', $data);
    
    echo 'insert : '.$record->name.'<br>';
}

class legacy_progress_tracker {
    private $_row;
    public $columns = array('status', 'line', 'id', 'course');

    /**
     * Print table header.
     * @return void
     */
    public function start() {
        $ci = 0;
        echo '<table id="uuresults" class="generaltable boxaligncenter flexible-wrap" summary="'.get_string('uploadusersresult', 'clickap_legacy').'">';
        echo '<tr class="heading r0">';
        echo '<th class="header c'.$ci++.'" scope="col">'.get_string('status').'</th>';
        echo '<th class="header c'.$ci++.'" scope="col">'.get_string('uucsvline', 'clickap_legacy').'</th>';
        echo '<th class="header c'.$ci++.'" scope="col">ID</th>';
        echo '<th class="header c'.$ci++.'" scope="col">'.get_string('fullname').'</th>';
        echo '</tr>';
        $this->_row = null;
    }

    /**
     * Flush previous line and start a new one.
     * @return void
     */
    public function flush() {
        if (empty($this->_row) or empty($this->_row['line']['normal'])) {
            // Nothing to print - each line has to have at least number
            $this->_row = array();
            foreach ($this->columns as $col) {
                $this->_row[$col] = array('normal'=>'', 'info'=>'', 'warning'=>'', 'error'=>'');
            }
            return;
        }
        $ci = 0;
        $ri = 1;
        echo '<tr class="r'.$ri.'">';
        foreach ($this->_row as $key=>$field) {
            foreach ($field as $type=>$content) {
                if ($field[$type] !== '') {
                    $field[$type] = '<span class="uu'.$type.'">'.$field[$type].'</span>';
                } else {
                    unset($field[$type]);
                }
            }
            echo '<td class="cell c'.$ci++.'">';
            if (!empty($field)) {
                echo implode('<br />', $field);
            } else {
                echo '&nbsp;';
            }
            echo '</td>';
        }
        echo '</tr>';
        foreach ($this->columns as $col) {
            $this->_row[$col] = array('normal'=>'', 'info'=>'', 'warning'=>'', 'error'=>'');
        }
    }

    /**
     * Add tracking info
     * @param string $col name of column
     * @param string $msg message
     * @param string $level 'normal', 'warning' or 'error'
     * @param bool $merge true means add as new line, false means override all previous text of the same type
     * @return void
     */
    public function track($col, $msg, $level = 'normal', $merge = true) {
        if (empty($this->_row)) {
            $this->flush(); //init arrays
        }
        if (!in_array($col, $this->columns)) {
            debugging('Incorrect column:'.$col);
            return;
        }
        if ($merge) {
            if ($this->_row[$col][$level] != '') {
                $this->_row[$col][$level] .='<br />';
            }
            $this->_row[$col][$level] .= $msg;
        } else {
            $this->_row[$col][$level] = $msg;
        }
    }

    /**
     * Print the table end
     * @return void
     */
    public function close() {
        $this->flush();
        echo '</table>';
    }
}