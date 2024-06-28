<?php

namespace App\Helpers;

use Exception;

class FileTypeHelper
{
    public static function getFileType($fileContent)
    {
        $header = substr($fileContent, 0, 32);

        foreach (FileSignatures::cases() as $signature) {
            if (strpos($header, $signature->value) === 0) {
                switch ($signature) {
                    case FileSignatures::MKV_WEBM:
                        if (strpos($header, 'webm') !== false) {
                            return 'webm';
                        } elseif (strpos($header, 'matroska') !== false) {
                            return 'mkv';
                        }
                        break;
                    case FileSignatures::AVI_WAV:
                        if (strpos($header, 'WAVE') !== false) {
                            return 'wav';
                        } elseif (strpos($header, 'AVI ') !== false) {
                            return 'avi';
                        }
                        break;
                    case FileSignatures::DOC_XLS_PPT:
                        if (strpos($header, 'WordDocument') !== false) {
                            return 'doc';
                        } elseif (strpos($header, 'Workbook') !== false) {
                            return 'xls';
                        } elseif (strpos($header, 'PowerPoint Document') !== false) {
                            return 'ppt';
                        }
                        break;
                    case FileSignatures::ZIP_DOCX_XLSX_PPTX:
                        if (strpos($header, '[Content_Types].xml') !== false) {
                            if (strpos($header, 'word/') !== false) {
                                return 'docx';
                            } elseif (strpos($header, 'xl/') !== false) {
                                return 'xlsx';
                            } elseif (strpos($header, 'ppt/') !== false) {
                                return 'pptx';
                            }
                        }
                        return 'zip';
                    default:
                        return strtolower($signature->name);
                }
            }
        }
        return 'unknown';
    }
}
