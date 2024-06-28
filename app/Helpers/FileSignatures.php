<?php

namespace App\Helpers;

enum FileSignatures: string
{
    case JPG = "\xFF\xD8\xFF";
    case PNG = "\x89\x50\x4E\x47\x0D\x0A\x1A\x0A";
    case GIF87A = "\x47\x49\x46\x38\x37\x61";
    case GIF89A = "\x47\x49\x46\x38\x39\x61";
    case PDF = "\x25\x50\x44\x46";
    case ZIP_DOCX_XLSX_PPTX = "\x50\x4B\x03\x04"; // Combined ZIP and DOCX, XLSX, PPTX
    case RAR = "\x52\x61\x72\x21\x1A\x07\x00";
    case SEVEN_Z = "\x37\x7A\xBC\xAF\x27\x1C";
    case TAR = "\x75\x73\x74\x61\x72\x00\x30\x30";
    case GZ = "\x1F\x8B\x08";
    case MP4 = "\x00\x00\x00\x18\x66\x74\x79\x70";
    case MKV_WEBM = "\x1A\x45\xDF\xA3"; // Combined MKV and WEBM
    case AVI_WAV = "\x52\x49\x46\x46"; // Combined AVI and WAV
    case MP3 = "\x49\x44\x33";
    case FLAC = "\x66\x4C\x61\x43";
    case DOC_XLS_PPT = "\xD0\xCF\x11\xE0\xA1\xB1\x1A\xE1"; // Combined DOC, XLS, PPT
    case ENC = "\x45\x4E\x43\x21";
}
