<?php

namespace App\Domain\UseCases;

use App\Domain\Repositories\FileRepositoryInterface;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Exception;

class UploadFileUseCase
{
    protected $fileRepository;

    public function __construct(FileRepositoryInterface $fileRepository)
    {
        $this->fileRepository = $fileRepository;
    }

    public function execute($file)
    {
        $this->validate($file);
        
        $this->clearPreviousSessionData();
        return $this->fileRepository->store($file);
    }

    private function validate($file)
    {
        $validator = Validator::make(['file' => $file], [
            'file' => 'required|file',
        ]);

        if ($validator->fails()) {
            throw new Exception($validator->errors()->first());
        }
    }

    private function clearPreviousSessionData()
    {
        if (Session::has('encryptedFilePath')) {
            $this->fileRepository->delete(Session::get('encryptedFilePath'));
        }
        if (Session::has('decryptedFilePath')) {
            $this->fileRepository->delete(Session::get('decryptedFilePath'));
        }
        Session::forget([
            'originalFileExtension',
            'encryptedFilePath',
            'decryptedFilePath',
        ]);
    }
}
