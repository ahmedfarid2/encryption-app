<?php

namespace App\Helpers;

use Exception;

trait FileHelper
{
    public static function readInitialPart($filePath, $length = 32)
    {
        $fileHandle = fopen($filePath, 'rb');
        if (!$fileHandle) {
            throw new Exception('Unable to open file for reading');
        }
        $initialPart = fread($fileHandle, $length);
        fclose($fileHandle);
        return $initialPart;
    }
}
