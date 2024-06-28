<?php

namespace App\Domain\UseCases;

use App\Domain\Repositories\FileRepositoryInterface;
use Illuminate\Support\Facades\Validator;
use Exception;
use App\Helpers\ErrorHelper;
use App\Helpers\ErrorLevels;
use App\Helpers\FileSignatures;
use App\Helpers\FileTypeHelper;
use App\Helpers\FileHelper;

class DecryptFileUseCase
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

            $encryptedFilePath = storage_path('app/' . $filePath);
            $inputHandle = fopen($encryptedFilePath, 'rb');

            $signature = fread($inputHandle, strlen(FileSignatures::ENC->value));
            if ($signature !== FileSignatures::ENC->value) {
                throw new Exception('Invalid file signature');
            }

            $iv = fread($inputHandle, 16);

            $decryptedFilePath = 'decrypted_files/' . pathinfo($filePath, PATHINFO_FILENAME);
            $decryptedFileFullPath = storage_path('app/' . $decryptedFilePath);

            $this->fileRepository->makeDirectory(dirname($decryptedFilePath));
            $outputHandle = fopen($decryptedFileFullPath, 'wb');

            while (!feof($inputHandle)) {
                $chunk = fread($inputHandle, $this->chunkSize + 16);
                if ($chunk === false) break;
                $decryptedChunk = openssl_decrypt($chunk, 'aes-256-cbc', $this->key, OPENSSL_RAW_DATA, $iv);
                if ($decryptedChunk === false) {
                    throw new Exception('Decryption error');
                }
                fwrite($outputHandle, $decryptedChunk);
            }
            fclose($inputHandle);
            fclose($outputHandle);

            $fileContent = FileHelper::readInitialPart($decryptedFileFullPath);
            $fileType = FileTypeHelper::getFileType($fileContent);
            if ($fileType === 'unknown') {
                throw new Exception('Unknown file type after decryption');
            }

            $decryptedFileName = pathinfo($filePath, PATHINFO_FILENAME) . '.' . $fileType;
            rename($decryptedFileFullPath, storage_path('app/decrypted_files/' . $decryptedFileName));

            return [
                'filePath' => 'decrypted_files/' . $decryptedFileName,
                'fileName' => $decryptedFileName
            ];
        } catch (Exception $e) {
            ErrorHelper::logError(
                message: 'Error decrypting file',
                level: ErrorLevels::ERROR,
                name: 'DecryptFileUseCase.execute',
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
