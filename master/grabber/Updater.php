<?php

class Updater {
    const VERSION = 26;

    /**
     * @var array filepath => classname(or empty string if no class)
     */
    public static $map = array(
        'Updater.php' => 'Updater',
        'pid' => 1,
        'Deploy.php' => 'Deploy',
        'index.php' => false,
        'config.php' => false,
        'LogGrabber.php' => 'LogGrabber',
        'LogGrabber.bat' => 1,  // never change it!
        'LogGrabberReserv.bat' => 1,
        'lastids' => 3,
        'Logger.php' => 'Logger',
        'db/Flower1.php' => 'Flower1',
        'errorProcess.php' => false,
        'lockProcess.php' => false,
    );

    private static $url = null;
    private static $download_url = null;

    public static function doUpdateIfNeed(){

        self::$url = MAIN_URL . '?update_check=1';
        self::$download_url = MAIN_URL . '?update=';

        $status = false;
        $res = self::compareVersions(self::getLocalCurrentVersion(),self::getRemoteCurrentVersion());

        if(!empty($res['updated'])) {
            Logger::msg('Update files: '.implode(', ',$res['updated']));
            self::remoteUpdatedFiles($res['updated']);
            $status = true;
        }
        if(!empty($res['new'])) {
            Logger::msg('Add new files: '.implode(', ',$res['new']));
            self::remoteUpdatedFiles($res['new']);
            $status = true;
        }
        if(!empty($res['deleted'])) {
            Logger::msg('Delete files: '.implode(', ',$res['deleted']));
            foreach($res['deleted'] as $deleted) {
                $msg = 'Delete file '.$deleted;
                if(unlink($deleted))
                    $msg .= ' DONE';
                else
                    $msg .= ' FAIL';
                Logger::msg($msg);
            }
            $status = true;
        }
        return $status;
    }

    private static function getRemoteCurrentVersion(){
        try{
            $content = file_get_contents(self::$url);
            return unserialize($content);
        } catch(Exception $e) {
            Logger::error($content);
            Logger::error($e);
            return false;
        }
    }

    private static function compareVersions($local,$remote) {
        $updated = $new = $deleted = array();
        try{
            if(!is_array($remote))
                throw new RuntimeException('compareVersions impossible because remote data is wrong: '.var_export($remote,true));
            foreach($remote as $file => $version) {
                if(isset($local[$file])) {
                    if($local[$file] != $version) {
                        $updated[] = $file;
                    }
                } else {
                    $new[] = $file;
                }
                unset($local[$file]);
            }
            $deleted = array_keys($local);
        } catch(Exception $e) {
            Logger::error($e);
        }

        return compact('updated','new','deleted');
    }

    private static function remoteUpdatedFiles($map){
        foreach($map as $file) {
            if(false !== ($content = file_get_contents(self::$download_url.$file))) {
                $msg = 'Remote file '.$file.' update/add ';
                if('.' != ($dir = dirname($file)) && !file_exists($dir)) {
                    mkdir($dir);
                }
                if(file_put_contents($file,$content)){
                    $msg .= 'DONE';
                } else {
                    $msg .= 'FAIL';
                }
                Logger::msg($msg);
            }
        }
    }

    public static function getLocalCurrentVersion() {
        $result = array();
        foreach(self::$map as $file => $class) {
            ob_start();
            include_once $file;
            ob_end_clean();
            if($class === false) {
                $result[$file] = constant(strtoupper(str_replace('.php','',basename($file))).'_VERSION');
            } elseif(is_int($class)) {
                $result[$file] = $class;
            } else {
                $result[$file] = constant($class.'::VERSION');
            }
        }
        return $result;
    }
}
