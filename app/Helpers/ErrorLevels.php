<?php

namespace App\Helpers;

enum ErrorLevels: int
{
    case DEBUG = 0;
    case INFO = 1;
    case ERROR = 2;
    case CRITICAL = 3;

    public function getLevelName(): string
    {
        return match ($this) {
            self::DEBUG => 'DEBUG',
            self::INFO => 'INFO',
            self::ERROR => 'ERROR',
            self::CRITICAL => 'CRITICAL',
        };
    }
}
