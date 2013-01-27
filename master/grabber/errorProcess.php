<?php

define('ERRORPROCESS_VERSION', 1);

function errorHandler($errno, $errstr, $errfile, $errline) {
    switch ($errno) {
        case E_PARSE:
        case E_ERROR:
        case E_STRICT:
        case E_WARNING:
        case E_COMPILE_ERROR:
        case E_CORE_ERROR:
            Logger::error("ERROR!!! " . getmypid() . ': ' . $errstr);
            if (Updater::doUpdateIfNeed()) {
                exit();
            }
            break;
    }
    /* запускаем внутренний обработчик ошибок PHP */
    return false;
}
// переключаемся на пользовательский обработчик
$old_error_handler = set_error_handler("myErrorHandler");