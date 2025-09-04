<?php

namespace Core;

class Logger {
    private static function getLogFile($level) {
        return __DIR__ . "/../logs/{$level}.log";
    }

    public static function log($message, $level = 'info') {
        $logFile = self::getLogFile(strtolower($level));
        $date = date('Y-m-d H:i:s');
        $logMessage = "[$date] [$level]: $message" . PHP_EOL;

        if (!file_exists(dirname($logFile))) {
            mkdir(dirname($logFile), 0777, true);
        }

        file_put_contents($logFile, $logMessage, FILE_APPEND);
    }
}
