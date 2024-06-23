<?php

namespace App\Http\Controllers;

use App\Domain\UseCases\FileUseCases;
use Illuminate\Http\Request;
use App\Helpers\ErrorHelper;
use App\Helpers\ErrorLevels;
use Exception;

class FileController extends Controller
{
    protected $fileUseCases;

    public function __construct(FileUseCases $fileUseCases)
    {
        $this->fileUseCases = $fileUseCases;
    }

    public function index()
    {
        return view('file');
    }

    public function upload(Request $request)
    {
        try {
            $filePath = $this->fileUseCases->getUploadFileUseCase()->execute(
                $request->file('file')
            );

            return response()->json([
                'filePath' => $filePath,
                'fileName' => $request->file('file')->getClientOriginalName(),
                'fileSize' => $request->file('file')->getSize(),
                'fileExtension' => $request->file('file')->getClientOriginalExtension(),
            ]);
        } catch (Exception $e) {
            ErrorHelper::logError(
                message: 'Error uploading file',
                level: ErrorLevels::ERROR,
                name: 'FileController.upload',
                error: $e,
                stackTrace: $e->getTraceAsString()
            );

            return response()->json(['error' => 'File upload failed.'], 500);
        }
    }


    public function encrypt(Request $request)
    {
        try {
            $result = $this->fileUseCases->getEncryptFileUseCase()->execute(
                $request->input('filePath'),
                $request->input('outputFileName')
            );

            return response()->json($result);
        } catch (Exception $e) {
            ErrorHelper::logError(
                message: 'Error encrypting file',
                level: ErrorLevels::ERROR,
                name: 'FileController.encrypt',
                error: $e,
                stackTrace: $e->getTraceAsString()
            );

            return response()->json(['error' => 'File encryption failed.'], 500);
        }
    }

    public function decrypt(Request $request)
    {
        try {
            $result = $this->fileUseCases->getDecryptFileUseCase()->execute(
                $request->input('filePath'),
                $request->input('outputFileName')
            );

            return response()->json($result);
        } catch (Exception $e) {
            ErrorHelper::logError(
                message: 'Error decrypting file',
                level: ErrorLevels::ERROR,
                name: 'FileController.decrypt',
                error: $e,
                stackTrace: $e->getTraceAsString()
            );

            return response()->json(['error' => 'File decryption failed.'], 500);
        }
    }

    public function validateEncryption(Request $request)
    {
        try {
            $result = $this->fileUseCases->getValidateEncryptionUseCase()->execute(
                $request->input('originalFilePath')
            );

            return response()->json($result);
        } catch (Exception $e) {
            ErrorHelper::logError(
                message: 'Error validating encryption',
                level: ErrorLevels::ERROR,
                name: 'FileController.validateEncryption',
                error: $e,
                stackTrace: $e->getTraceAsString()
            );

            return response()->json(['error' => 'Validation failed.'], 500);
        }
    }
}
