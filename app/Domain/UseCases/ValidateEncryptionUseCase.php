<?php

namespace App\Domain\UseCases;

use App\Domain\Repositories\FileRepositoryInterface;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Exception;

class ValidateEncryptionUseCase
{
    protected $fileRepository;

    public function __construct(FileRepositoryInterface $fileRepository)
    {
        $this->fileRepository = $fileRepository;
    }

    public function execute($originalFilePath)
    {
        $this->validate($originalFilePath);
        
        $decryptedFilePath = Session::get('decryptedFilePath');

        if (!$decryptedFilePath || !Storage::exists($decryptedFilePath)) {
            return ['status' => 'failure', 'message' => 'Decrypted file not found.'];
        }

        $originalContent = $this->fileRepository->getFileContent($originalFilePath);
        $decryptedContent = $this->fileRepository->getFileContent($decryptedFilePath);

        if ($originalContent === $decryptedContent) {
            return ['status' => 'success', 'message' => 'The files are identical.'];
        } else {
            return ['status' => 'failure', 'message' => 'The files are not identical.'];
        }
    }

    private function validate($originalFilePath)
    {
        $validator = Validator::make(['originalFilePath' => $originalFilePath], [
            'originalFilePath' => 'required|string',
        ]);

        if ($validator->fails()) {
            throw new Exception($validator->errors()->first());
        }
    }
}
