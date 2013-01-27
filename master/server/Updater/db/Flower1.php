<?php

class Flower1 {
    const VERSION = 27;

    private $tables = array(
        'arc',
//        'sgn',
    );

    private $object_ids = array(
        '123',
    );

    private $setting = array(
        'host'  => 'localhost',
        'user'  => 'root',
        'pass'  => 'masterkey',
        'db'    => 'contact',
    );

    private $mysql_link = null;

    public function __construct() {
        $this->connect();
    }

    public function __destruct() {
        if($this->mysql_link)
            mysql_close($this->mysql_link);
    }

    public function connect() {
        try{
            $this->mysql_link = mysql_connect($this->setting['host'], $this->setting['user'], $this->setting['pass']);
        } catch(Exception $e) {
            Logger::error('Error: Can not connect: '.$e,true);
            return false;
        }
        if (!$this->mysql_link) {
            Logger::error('Can not connect: '.mysql_error());
            return false;
        } else {
            try{
                $db_selected = mysql_select_db($this->setting['db'], $this->mysql_link);
            }catch(Exception $e) {
                Logger::error('Error: Can not select db: '.$e,true);
                return false;
            }
            if (!$db_selected) {
                Logger::error('Can not select db: '.mysql_error());
                return false;
            } else {
                mysql_query("SET NAMES 'utf8' COLLATE 'utf8_general_ci'", $this->mysql_link);
            }
        }
        return true;
    }

    public function getData(LogGrabber $LogGrabber) {
        $res = array();
        if (!$this->mysql_link && !$this->connect()) {
            return $res;
        }
        try{
            $from_times = $LogGrabber->getLastTimes(FLOWER1);

            // ppl таблица - контактные лица
            // arc - некие логи
            // mes - сообщения по логам
            // mesgrp - группа логов

            foreach ($this->tables as $table) {
                foreach($this->object_ids as $obj_id) {
                    if (!isset($from_times[$table.'_'.$obj_id])) {
                        $from_times[$table.'_'.$obj_id] = 0;
                    }
                    $query = 'SELECT
                        a.COMN, a.ID, a.ID_MES, a.ID_OBJ, a.ID_POD, a.ID_PPL, a.ID_PR, a.ID_SGN, a.ID_ZON,
                        a.SDATE, a.ZONA, a.id_calltype, a.id_atype, a.id_alarm02, a.id_alarmres, a.id_sended,
                        m.MES, m.ID_MESGRP, mg.TEXT as mes_type
                    FROM '.$table.' as a
                    LEFT JOIN mes as m ON m.ID = a.ID_MES
                    LEFT JOIN mesgrp as mg ON mg.ID = m.ID_MESGRP
                    WHERE
                        a.ID > '.$from_times[$table.'_'.$obj_id].'
                        and a.ID_OBJ = '.$obj_id.'
                    ORDER BY a.SDATE ASC
                    LIMIT 70';

                    if ($this->mysql_link && false !== ($result = mysql_query($query,$this->mysql_link))) {
                        $new_data = false;
                        while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
                            $new_data = $row['ID'];
                            $res[] = $row;
                        }
                        mysql_free_result($result);
                        if ($new_data !== false) {
                            $from_times[$table.'_'.$obj_id] = $new_data;
                            $LogGrabber->setLastTimes(FLOWER1,$from_times);
                        }
                    }
                }
            }
        } catch (Exception $e){
            Logger::error($e);
        }
        return $res;
    }
}
