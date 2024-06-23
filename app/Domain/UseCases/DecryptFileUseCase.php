<?php

namespace App\Domain\UseCases;

use App\Domain\Repositories\FileRepositoryInterface;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Exception;

class DecryptFileUseCase
{
    protected $fileRepository;

    public function __construct(FileRepositoryInterface $fileRepository)
    {
        $this->fileRepository = $fileRepository;
    }

    public function execute($filePath, $outputFileName)
    {
        $this->validate($filePath, $outputFileName);
        
        $encryptedContent = $this->fileRepository->getFileContent($filePath);

        $key = hex2bin(env('AES_SECRET_KEY'));
        $iv = substr($encryptedContent, 0, 16);
        $encrypted = substr($encryptedContent, 16);

        $decrypted = openssl_decrypt($encrypted, 'aes-256-cbc', $key, 0, $iv);
        $originalFileExtension = Session::get('originalFileExtension');

        $decryptedFileName = $outputFileName . '.' . $originalFileExtension;
        $decryptedFilePath = 'decrypted_files/' . $decryptedFileName;        
        $this->fileRepository->storeContent($decrypted, $decryptedFilePath);

        Session::put('decryptedFilePath', $decryptedFilePath);

        return [
            'filePath' => $decryptedFilePath,
            'fileName' => $decryptedFileName
        ];
    }

    private function validate($filePath, $outputFileName)
    {
        $validator = Validator::make(['filePath' => $filePath, 'outputFileName' => $outputFileName], [
            'filePath' => 'required|string',
            'outputFileName' => 'required|string',
        ]);

        if ($validator->fails()) {
            throw new Exception($validator->errors()->first());
        }
    }
}
