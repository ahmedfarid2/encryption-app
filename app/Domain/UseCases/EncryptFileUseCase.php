<?php

namespace App\Domain\UseCases;

use App\Domain\Repositories\FileRepositoryInterface;
use Illuminate\Support\Facades\Validator;
use Exception;
use App\Helpers\ErrorHelper;
use App\Helpers\ErrorLevels;
use App\Helpers\FileSignatures;

class EncryptFileUseCase
{
    protected $fileRepository;
    private $chunkSize;
    private $key;

    public function __construct(FileRepositoryInterface $fileRepository, string $key, int $chunkSize)
    {
        $this->fileRepository = $fileRepository;
        $this->chunkSize = $chunkSize;
        $this->key = $key;
    }

    public function execute($filePath)
    {
        try {
            $this->validate($filePath);
            $iv = random_bytes(16);

            $orignalFilePath = storage_path('app/' . $filePath);
            $encryptedFileName = pathinfo($filePath, PATHINFO_FILENAME) . '.enc';

            $encryptedFilePath = 'encrypted_files/' . $encryptedFileName;
            $encryptedFileFullPath = storage_path('app/' . $encryptedFilePath);
            $this->fileRepository->makeDirectory(dirname($encryptedFilePath));

            $inputHandle = fopen($orignalFilePath, 'rb');
            $outputHandle = fopen($encryptedFileFullPath, 'wb');

            fwrite($outputHandle, FileSignatures::ENC->value);
            fwrite($outputHandle, $iv);

            while (!feof($inputHandle)) {
                $chunk = fread($inputHandle, $this->chunkSize);
                if ($chunk === false) break;
                $encryptedChunk = openssl_encrypt($chunk, 'aes-256-cbc', $this->key, OPENSSL_RAW_DATA, $iv);
                fwrite($outputHandle, $encryptedChunk);
            }

            fclose($inputHandle);
            fclose($outputHandle);

            return [
                'filePath' => $encryptedFilePath,
                'fileName' => $encryptedFileName
            ];
        } catch (Exception $e) {
            ErrorHelper::logError(
                message: 'Error encrypting file',
                level: ErrorLevels::ERROR,
                name: 'EncryptFileUseCase.execute',
                error: $e,
                stackTrace: $e->getTraceAsString()
            );
        }
    }

    private function validate($filePath)
    {
        $validator = Validator::make(['filePath' => $filePath], [
            'filePath' => 'required|string',
        ]);

        if ($validator->fails()) {
            throw new Exception($validator->errors()->first());
        }
    }
}
