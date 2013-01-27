<?php

class LogGrabber {
    const VERSION = 11;

    private $logs = null;
    private $last_times_file = 'lastids';
    private $last_times = null;
    private $old_last_times = null;

    private $Flower1 = null;

    public function __construct() {
        if(is_null($this->last_times) && file_exists($this->last_times_file)) {
            include $this->last_times_file;
            if(!isset($last_times)) {
                $last_times = array(
                    FLOWER1 => array(),
                    FLOWER2 => array(),
                );
            }
            $this->old_last_times = $this->last_times = $last_times;
        }
        $this->cleanLogs();

        $this->Flower1 = new Flower1();
    }

    public function saveLastTimes() {
        if((bool)file_put_contents($this->last_times_file,"<?php \n \$last_times = ".var_export($this->last_times,true).";")) {
            $this->old_last_times = $this->last_times;
        }
    }

    public function revertLastTimes() {
        $this->last_times = $this->old_last_times;
    }

    public function getLastTimes($type=null) {
        if(is_null($type)) {
            return $this->last_times;
        } else {
            return $this->last_times[$type];
        }
    }

    public function setLastTimes($type, $val){
        $this->last_times[$type] = $val;
    }

    public function isEmtyLogs(){
        return (empty($this->logs[FLOWER1]) && empty($this->logs[FLOWER2]));
    }

    public function getLogs() {
        return $this->logs;
    }

    public function cleanLogs() {
        $this->logs = array(
            FLOWER1 => array(),
            FLOWER2 => array(),
        );
    }

    public function parseLogs() {
        $this->parseFlower1Log();
        $this->parseFlower2Log();
    }

    private function parseFlower1Log() {
        //connetc to db
        //get data by companies code
        $data = $this->Flower1->getData($this);
        if(!empty($data)) {
            //save to variable
            $this->logs[FLOWER1] = $data;
        }
    }

    private function parseFlower2Log() {
        //connetc to db
        //get data by company code
        //save to variable
    }
}
?>