<?php

class Log {
    public static $eol = "\n";

    public static function error($msg,$file=null) {
        return self::save($msg,'error'.($file?('_'.$file):''));
    }

    public static function msg($msg,$file=null){
        return self::save($msg,'msg'.($file?('_'.$file):''));
    }

    public static function save($msg,$file,$append=true) {
        return (bool) file_put_contents(
            self::getPath($file),
            '['.date('d-m-Y H:i:s').'] '.$msg . self::$eol,
            $append?FILE_APPEND:null);
    }

    private static function getPath($file) {
        $path = 'logs/'.date('Y-m-d').'/'.$file.'.log';
        if (!file_exists(dirname($path))) {
            mkdir(dirname($path));
        }
        return $path;
    }
}
