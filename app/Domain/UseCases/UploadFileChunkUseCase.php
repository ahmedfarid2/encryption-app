<?php

namespace App\Domain\UseCases;

use App\Domain\Repositories\FileRepositoryInterface;
use Illuminate\Support\Facades\Validator;
use Exception;
use App\Helpers\ErrorHelper;
use App\Helpers\ErrorLevels;

class UploadFileChunkUseCase
{
    protected $fileRepository;

    public function __construct(FileRepositoryInterface $fileRepository)
    {
        $this->fileRepository = $fileRepository;
    }

    public function execute($chunk, $fileId, $chunkIndex)
    {
        try {
            $this->validate($chunk, $fileId, $chunkIndex);
            $filePath = "uploads/tmp/{$fileId}/part{$chunkIndex}";
            $this->fileRepository->storeChunk($chunk, $filePath);
        } catch (Exception $e) {
            ErrorHelper::logError(
                message: "Error file upload chunk: " . $e->getMessage(),
                level: ErrorLevels::ERROR,
                name: "UploadFileChunkUseCase.execute",
                error: $e,
                stackTrace: $e->getTraceAsString()
            );
        }
    }

    private function validate($chunk, $fileId, $chunkIndex)
    {
        $validator = Validator::make(
            ['chunk' => $chunk, 'fileId' => $fileId, 'chunkIndex' => $chunkIndex],
            ['chunk' => 'required|file', 'fileId' => 'required|string', 'chunkIndex' => 'required|integer']
        );

        if ($validator->fails()) {
            throw new Exception($validator->errors()->first());
        }
    }
}
