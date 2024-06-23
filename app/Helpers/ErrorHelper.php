<?php

namespace App\Helpers;

use Throwable;

class ErrorHelper
{
    public static function logError(string $message = '', ErrorLevels $level = ErrorLevels::DEBUG, string $name = '', Throwable $error = null, string $stackTrace = null): void
    {
        $levelName = $level->getLevelName();
        $logMessage = "[$levelName] $name: $message";

        if ($error) {
            $logMessage .= " | Error: " . self::formatError($error);
        }

        if ($stackTrace) {
            $logMessage .= " | StackTrace: " . self::limitStackTrace($stackTrace);
        }

        error_log($logMessage);
    }

    private static function formatError(Throwable $error): string
    {
        $errorString = $error->getMessage();
        $errorString .= " in " . $error->getFile() . " on line " . $error->getLine();
        return $errorString;
    }

    private static function limitStackTrace(string $stackTrace, int $limit = 2): string
    {
        $lines = explode("\n", $stackTrace);
        return implode("\n", array_slice($lines, 0, $limit));
    }
}
