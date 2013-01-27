<?php

class DeployFailException extends Exception{}

class Deploy {
    const VERSION = 14;

    public static $server = MAIN_URL;

    private $curl = null;
    private $curl_opts = null;

    public function __construct(){

         $this->curl_opts = array(
            CURLOPT_URL => self::$server,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => false,
            CURLOPT_FRESH_CONNECT => true,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_TIMEOUT => 120,
            CURLOPT_POST => true,
        );
    }

    public function toServer($data, $times){

        $this->curl = curl_init();
        curl_setopt_array($this->curl, $this->curl_opts);

        curl_setopt($this->curl, CURLOPT_POSTFIELDS, http_build_query(array(
            'deploy'    => 1,
            'times'     => serialize($times),
            'data'      => serialize($data),
            'pid'       => getmypid(),
        )));

        $res = curl_exec($this->curl);

        $err_no = curl_errno($this->curl);

        curl_close( $this->curl );

        if ($err_no > 0 || $res != 'ok_deploy') {
            throw new DeployFailException('error deploy to server '.self::$server.'. Reasons: '.var_export($err_no,true).'. '.var_export($res,true));
        }

        Logger::msg('send success.');
        return true;
    }
}
