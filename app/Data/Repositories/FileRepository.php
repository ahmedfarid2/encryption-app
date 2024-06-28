<?php

namespace App\Data\Repositories;

use App\Domain\Repositories\FileRepositoryInterface;
use Illuminate\Support\Facades\Storage;
use App\Helpers\ErrorHelper;
use App\Helpers\ErrorLevels;
use Exception;

class FileRepository implements FileRepositoryInterface
{
    public function storeChunk($chunk, $filePath)
    {
        try {
            $directory = dirname($filePath);

            if (!$this->exists($directory)) {
                $this->makeDirectory($directory);
            }
            $this->put($filePath, file_get_contents($chunk->getRealPath()));

            return true;
        } catch (Exception $e) {
            $this->logError("FileRepository.storeChunk", $e);
            return false;
        }
    }

    public function appendToFile($filePath, $content)
    {
        try {
            return $this->append($filePath, $content);
        } catch (Exception $e) {
            $this->logError("FileRepository.appendToFile", $e);
            return false;
        }
    }

    public function combineChunks($fileId, $originalFileName)
    {
        try {
            ini_set('max_execution_time', 1800);

            $tempDirectory = "uploads/tmp/{$fileId}";
            $files = $this->files($tempDirectory);

            $validFiles = array_filter($files, function ($file) {
                return preg_match('/^part\d+$/', basename($file));
            });

            usort($validFiles, function ($a, $b) {
                return intval(preg_replace('/^part(\d+)$/', '$1', basename($a))) - intval(preg_replace('/^part(\d+)$/', '$1', basename($b)));
            });

            $finalFile = fopen(storage_path('app/uploads/' . $originalFileName), 'wb');

            foreach ($validFiles as $file) {
                fwrite($finalFile, $this->get($file));
                $this->delete($file);
            }

            fclose($finalFile);
            $this->deleteDirectory($tempDirectory);

            return true;
        } catch (Exception $e) {
            $this->logError("FileRepository.combineChunks", $e);
            return false;
        }
    }

    public function deleteTemporaryChunks($fileId)
    {
        try {
            $this->deleteDirectory("uploads/tmp/{$fileId}");
        } catch (Exception $e) {
            $this->logError("FileRepository.deleteTemporaryChunks", $e);
        }
    }

    public function storeContent($content, $filePath)
    {
        try {
            return $this->put($filePath, $content);
        } catch (Exception $e) {
            $this->logError("FileRepository.storeContent", $e);
            return false;
        }
    }

    public function getFileContent($filePath)
    {
        try {
            return $this->get($filePath);
        } catch (Exception $e) {
            $this->logError("FileRepository.getFileContent", $e);
            return false;
        }
    }

    public function delete($filePath)
    {
        try {
            return Storage::delete($filePath);
        } catch (Exception $e) {
            $this->logError("FileRepository.delete", $e);
            return false;
        }
    }

    public function exists($filePath)
    {
        try {
            return Storage::exists($filePath);
        } catch (Exception $e) {
            $this->logError("FileRepository.exists", $e);
            return false;
        }
    }

    public function getSize($filePath)
    {
        try {
            $sizeInBytes = Storage::size($filePath);
            return $this->formatSizeUnits($sizeInBytes);
        } catch (Exception $e) {
            $this->logError("FileRepository.getSize", $e);
            return false;
        }
    }

    private function logError($name, Exception $e)
    {
        ErrorHelper::logError(
            message: $e->getMessage(),
            level: ErrorLevels::ERROR,
            name: $name,
            error: $e,
            stackTrace: $e->getTraceAsString()
        );
    }

    private function formatSizeUnits($bytes)
    {
        if ($bytes >= 1073741824) {
            $size = number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            $size = number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            $size = number_format($bytes / 1024, 2) . ' KB';
        } elseif ($bytes > 1) {
            $size = $bytes . ' bytes';
        } elseif ($bytes == 1) {
            $size = $bytes . ' byte';
        } else {
            $size = '0 bytes';
        }

        return $size;
    }

    function makeDirectory($directory)
    {
        Storage::makeDirectory($directory);
    }

    private function put($filePath, $content)
    {
        Storage::put($filePath, $content);
    }

    private function append($filePath, $content)
    {
        Storage::append($filePath, $content);
    }

    private function get($filePath)
    {
        return Storage::get($filePath);
    }

    private function deleteDirectory($directory)
    {
        Storage::deleteDirectory($directory);
    }

    private function files($directory)
    {
        return Storage::files($directory);
    }
}
