<?php

define('LOCKPROCESS_VERSION', 3);

$lockFileHandler = fopen(LOCK_FILE, "a+");
// снятие блокировки по окончанию работы
// если этот callback, не будет выполнен, то блокировка
// все равно будет снята ядром, но файл останется
register_shutdown_function(function() use ($lockFileHandler) {
    flock($lockFileHandler, LOCK_UN);
    fclose($lockFileHandler);
//    unlink(LOCK_FILE);
    Logger::remote("CLOSE !!! " . getmypid(), 'lock');
});
// check for multiple processes
while (true) {
    if (flock($lockFileHandler, LOCK_EX)) { // выполняем эксклюзивную блокировку
        rewind($lockFileHandler);
        ftruncate($lockFileHandler, 0); // очищаем файл
        fwrite($lockFileHandler, getmypid());
        Logger::remote("WORK !!! " . getmypid(), 'lock');
        $_SESSION['proc_lock']['wait'][getmypid()] = null;
        $_SESSION['proc_lock']['work'][getmypid()] = time();
        break;
    } elseif (fgets($lockFileHandler) == getmypid()) {
        break;
    } else {
        $_SESSION['proc_lock']['wait'][getmypid()] = time();
        Logger::remote("Other process already OPEN. I am " . getmypid() . ' other ' . var_export($_SESSION['proc_lock']['wait'], true), 'lock');
        sleep(SLEEP_TIME_ON_LOCK_WAIT);
    }
}
// fclose($lockFileHandler);  специально нет, т.к. потеряется блокировка