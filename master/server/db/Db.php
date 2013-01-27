<?php

class Db {
    private static $instanse = null;

    private $setting = array(
        'host'  => '127.0.0.1:31101',
        'user'  => 'security',
        'pass'  => 'hto8jDEk',
        'db'    => 'security',
    );

    private $mysql_link = null;
    private $mysql_result = null;

    private function __construct() {
        try{
            $this->mysql_link = mysql_connect($this->setting['host'], $this->setting['user'], $this->setting ['pass']);
        } catch(Exception $e) {
            Log::error('Error: Can not connect: '.$e,__CLASS__);
        }
        if (!$this->mysql_link)
            Log::error('Can not connect: '.mysql_error(),__CLASS__);
        else{
            try{
                $db_selected = mysql_select_db($this->setting['db'], $this->mysql_link);
            }catch(Exception $e) {
                Log::error('Error: Can not select db: '.$e,__CLASS__);
            }
            if (!$db_selected) {
                Log::error('Can not select db: '.mysql_error(),__CLASS__);
            } else {
                $this->execute("SET NAMES 'utf8' COLLATE 'utf8_general_ci'");
            }
        }
    }
    /**
     * @static
     * @return Db
     */
    public static function getInstance() {
        if (self::$instanse === null) {
            self::$instanse = new self;
        }
        return self::$instanse;
    }

    public function __destruct() {
        if($this->mysql_link)
            mysql_close($this->mysql_link);
    }

    private function execute($query) {
        return ($this->mysql_result = mysql_query($query, $this->mysql_link));
    }

    private function select($query){
        $res = array();
        if ($this->mysql_link && false !== $this->execute($query)) {
            while ($row = mysql_fetch_array($this->mysql_result, MYSQL_ASSOC)) {
                $res[] = $row;
            }
            mysql_free_result($this->mysql_result);
        }
        return $res;
    }

    public function saveData($data) {
        if(empty($data)) {
            Log::error('Data to save came empty',__CLASS__);
            return false;
        }
        $data = unserialize($data);
        $keys = $rows = $log = array();
        foreach ($data as $source => $data_rows) {
            foreach ($data_rows as $row) {
                $row['logsource'] = $source;
                if (empty($keys)) {
                    $keys = array_keys($row);
                }
                foreach ($row as $k => $val) {
                    if ($k == 'SDATE') {
                        $row[$k] = '"' . $val . '"';
                    } elseif (is_null($val)) {
                        $row[$k] = 'NULL';
                    } elseif (!is_numeric($val)) {
                        $row[$k] = '"' . mysql_real_escape_string($val) . '"';
                    }
                }
		  $log[] = $row;
                $rows[] = '('.implode(',', $row).')';
            }
        }
        $query = 'INSERT INTO `logs`('.implode(',',$keys).') VALUES '.implode(','."\n",$rows).'
                    ON DUPLICATE KEY UPDATE dublic_found=dublic_found+1, SDATE=VALUES(SDATE)';
        Log::save(var_export($query,true),'debug');
        if (!$this->execute($query)) {
            Log::error('Can not save data to db: '.mysql_error(),__CLASS__);
	        Log::save('INSERT ERROR','debug');
            return false;
        } else {
	        foreach ($log as $v) {
		        Event::Db_AfterAddRow($v);
	        }
	        Log::save('success','debug');
        }
        return true;
    }

    public function getData($params = array()) {
        if (isset($params['limit'])) {
            $limit = 'LIMIT '.$params['limit'];
        } else {
            $limit = '';
        }
        $query = 'SELECT * FROM `logs` WHERE ID_OBJ=123 ORDER BY SDATE DESC '.$limit;
        return $this->select($query);
    }

    public function getCount() {
        $query = 'SELECT count(ID) as num FROM `logs`';
        $res = $this->select($query);
        return empty($res)?0:$res[0]['num'];
    }
}