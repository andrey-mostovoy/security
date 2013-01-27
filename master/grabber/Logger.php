<?php

class Logger {
    const VERSION = 14;

    public static $eol = "\n";
    private static $url = MAIN_URL;

    public static function error($msg,$only_local=false) {
        self::console($msg,true);
        if(!$only_local)
            self::remote($msg,'error');
    }

    public static function msg($msg,$only_local=false){
        self::console($msg);
        if(!$only_local)
            self::remote($msg,'msg');
    }

    public static function console($msg,$important=false) {
        if($important)
            echo self::$eol;
        echo '['.date('d-m-Y H:i:s').'] '.$msg . self::$eol;
        if($important)
            echo self::$eol;
    }

    public static function remote($msg,$log) {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => self::$url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => false,
            CURLOPT_FRESH_CONNECT => true,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_TIMEOUT => 120,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query(array(
                'log'=>$log,
                'msg'=>'Time on remote ['.date('d-m-Y H:i:s').']: '.$msg,
                'pid'       => getmypid(),
            ))
        ));

        $res = curl_exec($curl);

        $err_no = curl_errno($curl);

        curl_close( $curl );

        if ($err_no > 0 || $res != 'ok_log') {
            Logger::error('error deploy LOG to server '.self::$url.'. Err_no: '.$err_no.'. Res: '.var_export($res,true),true);
        }
    }
}
