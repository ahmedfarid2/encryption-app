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

    public function uploadChunk(Request $request)
    {
        try {
            $chunk = $request->file('chunk');
            $fileId = $request->input('fileId');
            $chunkIndex = $request->input('chunkIndex');

            $this->fileUseCases->getUploadFileChunkUseCase()->execute(
                $chunk,
                $fileId,
                $chunkIndex
            );
        } catch (Exception $e) {
            ErrorHelper::logError(
                message: 'Error uploading file chunk',
                level: ErrorLevels::ERROR,
                name: 'FileController.uploadChunk',
                error: $e,
                stackTrace: $e->getTraceAsString()
            );

            return response()->json(['error' => 'File upload chunk failed.'], 500);
        }
    }

    public function finalizeUpload(Request $request)
    {
        try {
            $fileId = $request->input('fileId');
            $originalFileName = $request->input('originalFileName');

            $response = $this->fileUseCases->getFinalizeUploadUseCase()->execute(
                $fileId,
                $originalFileName
            );
            return response()->json($response);
        } catch (Exception $e) {
            ErrorHelper::logError(
                message: 'Error finalizing file upload',
                level: ErrorLevels::ERROR,
                name: 'FileController.finalizeUpload',
                error: $e,
                stackTrace: $e->getTraceAsString()
            );

            return response()->json(['error' => 'File upload finalization failed.'], 500);
        }
    }

    public function encrypt(Request $request)
    {
        try {
            $result = $this->fileUseCases->getEncryptFileUseCase()->execute(
                $request->input('filePath'),
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
}
