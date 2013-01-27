<?php

define('INDEX_VERSION', 42);

include_once 'config.php';

//prevent including all other files in update process
if(isset($_GET['update_check'])) {
    return;
}

include_once 'Logger.php';
include_once 'db/Flower1.php';
include_once 'LogGrabber.php';
include_once 'Deploy.php';
include_once 'Updater.php';

require_once 'errorProcess.php';
require_once 'lockProcess.php';

$LogGrabber = new LogGrabber();
$Deploy = new Deploy();

$update_time = $remind_time = $ping_time = time();
$sleep_time = 1;

while(true) {
    if((time()-$ping_time) > PING_INTERVAL) {
        $res = file_get_contents(MAIN_URL.'?ping=1');
        if($res != 1) {
            Logger::msg('Server required restart. Do it!');
            exit();
        }
        $ping_time = time();
    }
    //do not remove reminder if first iteration in loop or timework more than NOT_DELETE_REMINDER_INTERVAL
    if((time()-$remind_time) > NOT_DELETE_REMINDER_INTERVAL || $sleep_time == 1) {
        Logger::msg("|============================================|",true);
        Logger::msg("|                 DO NOT CLOSE               |",true);
        Logger::msg("|============================================|",true);
        $remind_time = time();
    }

    //update if first iteration in loop or timework more than UPDATE_CHECK_INTERVAL
    if((time()-$update_time) > UPDATE_CHECK_INTERVAL || $sleep_time == 1) {
        //try to update. If success exit, else update start time
        if(Updater::doUpdateIfNeed())
            exit();
        else
            $update_time = time();
    }

    //process
    try {
        $sleep_time = SLEEP_TIME_ON_FAIL;
        $LogGrabber->parseLogs();
        if(!$LogGrabber->isEmtyLogs()
            && $Deploy->toServer($LogGrabber->getLogs(), $LogGrabber->getLastTimes())
        ) {
            $LogGrabber->saveLastTimes();
            $sleep_time = SLEEP_TIME_ON_SUCCESS;
        }
    } catch(Exception $e) {
        $LogGrabber->revertLastTimes();
        Logger::error($e->getMessage());
        $sleep_time = SLEEP_TIME_ON_FAIL;
    }
    $LogGrabber->cleanLogs();
    Logger::msg('tick',true);
    sleep($sleep_time);
}