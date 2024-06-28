<?php

namespace App\Domain\UseCases;

use Illuminate\Support\Facades\App;

class FileUseCases
{
    public function getUploadFileChunkUseCase() 
    {
        return App::make(UploadFileChunkUseCase::class);
    }

    public function getFinalizeUploadUseCase()
    {
        return App::make(FinalizeUploadUseCase::class);
    }
    public function getEncryptFileUseCase()
    {
        return App::make(EncryptFileUseCase::class);
    }

    public function getDecryptFileUseCase()
    {
        return App::make(DecryptFileUseCase::class);
    }
}
