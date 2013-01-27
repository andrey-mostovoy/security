<?php

class Environment {

    public static function Index_Request() {
        if (!empty($_POST['pid'])) {
            Log::save($_POST['pid'],'pid_test');
        }

        if (isset($_GET['ping'])) {
            self::ping();
            die();
        }

        if (isset($_GET['update_check'])) {
            self::updateCheck();
            die();
        }

        if (isset($_GET['update'])) {
            self::update();
            die();
        }

        if (isset($_POST['log'])) {
            self::log();
            die();
        }

        if (isset($_POST['deploy'])) {
            self::deploy();
            die();
        }

        if (isset($_GET['clear_log'])) {
            self::clearLog();
            die();
        }
    }

    private static function ping() {
        // ping server. If that ping return != 1 than graber proccess will restart themself
        echo 1;
        file_put_contents(
            'runtimeInfo/last_ping.php',
            '<?php'."\n".'$last_ping = '.var_export(date('Y-m-d H:i:s'),true).";\n?>"
        );
        Log::save('last ping','ping', false);
    }

    private static function updateCheck() {
        //get file versions
        Log::msg('UPDATE CHECK','update');
        chdir('Updater');
        include 'Updater.php';
        echo serialize(Updater::getLocalCurrentVersion());
    }

    private static function update() {
        //do update. return file contents
        chdir('Updater');
        echo file_get_contents($_GET['update']);
    }

    private static function log() {
        //save log
        if (Log::save(var_export($_POST['msg'], true), $_POST['log'])) {
            echo 'ok_log';
        } else {
            echo 'error_log';
        }
    }

    private static function deploy() {
        //do data deploy
        if (Db::getInstance()->saveData($_POST['data'])) {
            Log::save('last deploy data time','deploy', false);
            file_put_contents(
                'runtimeInfo/last_ids.php',
                '<?php'."\n".'$last_ids = '.var_export(unserialize($_POST['times']),true).";\n?>"
            );
            echo 'ok_deploy';
        } else {
            echo 'error_deploy';
        }
    }

    private static function clearLog() {
        $from = $to = null;
        if (!empty($_GET['from'])) {
            $from = strtotime($_GET['from']);
        }
        if (!empty($_GET['to'])) {
            $to = strtotime($_GET['to']);
        }

        $directories = scandir('logs/');

        foreach ($directories as $directory) {
            if ($directory == '..' || $directory == '.' || !is_dir('logs/' . $directory)) {
                continue;
            }
            $toDelete = false;
            $dirTime = strtotime($directory);
            if ($from && $to) {
                $toDelete = ($from <= $dirTime && $to >= $dirTime);
            } else {
                if ($from) {
                    $toDelete = ($from <= $dirTime);
                } else if ($to) {
                    $toDelete = ($to >= $dirTime);
                }
            }

            if ($toDelete) {
                if (self::removeDir('logs/' . $directory)) {
                    echo 'Delete ' . $directory . PHP_EOL;
                } else {
                    echo 'error delete ' . $directory . PHP_EOL;
                }
            }
        }
    }

    private static function removeDir($dir) {
        $objects = scandir($dir);
        foreach ($objects as $object) {
            if ($object != "." && $object != "..") {
                if (is_dir($dir.'/'.$object)) {
                    self::removeDir($dir."/".$object);
                } else {
                    unlink($dir."/".$object);
                }
            }
        }
        return rmdir($dir);
    }
}

?>