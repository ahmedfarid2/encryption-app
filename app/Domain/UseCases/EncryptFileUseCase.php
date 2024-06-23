<?php

namespace App\Domain\UseCases;

use App\Domain\Repositories\FileRepositoryInterface;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Exception;

class EncryptFileUseCase
{
    protected $fileRepository;

    public function __construct(FileRepositoryInterface $fileRepository)
    {
        $this->fileRepository = $fileRepository;
    }

    public function execute($filePath, $outputFileName)
    {
        $this->validate($filePath, $outputFileName);

        $fileContent = $this->fileRepository->getFileContent($filePath);

        $key = hex2bin(env('AES_SECRET_KEY'));
        $iv = random_bytes(16);
        $encrypted = openssl_encrypt($fileContent, 'aes-256-cbc', $key, 0, $iv);

        $encryptedFileName = $outputFileName . '.enc';
        $encryptedFilePath = 'encrypted_files/' . $encryptedFileName;
        $this->fileRepository->storeContent($iv . $encrypted, $encryptedFilePath);
        
        Session::put([
            'encryptedFilePath' => $encryptedFilePath,
            'originalFileExtension' => pathinfo($filePath, PATHINFO_EXTENSION)
        ]);

        return [
            'filePath' => $encryptedFilePath,
            'fileName' => $encryptedFileName
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
