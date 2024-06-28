<?php

namespace App\Domain\UseCases;

use App\Domain\Repositories\FileRepositoryInterface;
use App\Helpers\ErrorHelper;
use App\Helpers\ErrorLevels;
use Exception;
use App\Helpers\FileTypeHelper;
use App\Helpers\FileSignatures;
use App\Helpers\FileHelper;

class FinalizeUploadUseCase
{
    protected $fileRepository;

    public function __construct(FileRepositoryInterface $fileRepository)
    {
        $this->fileRepository = $fileRepository;
    }

    public function execute($fileId, $originalFileName)
    {
        try {
            $this->fileRepository->combineChunks($fileId, $originalFileName);
            $this->fileRepository->deleteTemporaryChunks($fileId);

            $relativeFilePath = "uploads/{$originalFileName}";
            $filePath = storage_path("app/{$relativeFilePath}");

            $fileSize = $this->fileRepository->getSize($relativeFilePath);
            $fileContent = FileHelper::readInitialPart($filePath);            

            $fileType = $this->detectFileType($fileContent);

            return [
                'filePath' =>  $relativeFilePath,
                'fileName' => $originalFileName,
                'fileSize' => $fileSize,
                'fileExtension' => pathinfo($originalFileName, PATHINFO_EXTENSION),
                'fileType' => $fileType
            ];
        } catch (Exception $e) {
            ErrorHelper::logError(
                message: "Error finalizing file upload: " . $e->getMessage(),
                level: ErrorLevels::ERROR,
                name: "FinalizeUploadUseCase.execute",
                error: $e,
                stackTrace: $e->getTraceAsString()
            );
        }
    }

    private function detectFileType($fileContent)
    {
        $signature = substr($fileContent, 0, 8);

        if ($signature === FileSignatures::ENC->value) {
            return 'enc';
        }

        return FileTypeHelper::getFileType($fileContent);
    }
}
