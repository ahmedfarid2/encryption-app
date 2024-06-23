<?php

namespace App\Domain\UseCases;

use Illuminate\Support\Facades\App;

class FileUseCases
{
    public function getUploadFileUseCase() 
    {
        return App::make(UploadFileUseCase::class);
    }

    public function getEncryptFileUseCase()
    {
        return App::make(EncryptFileUseCase::class);
    }

    public function getDecryptFileUseCase()
    {
        return App::make(DecryptFileUseCase::class);
    }

    public function getValidateEncryptionUseCase()
    {
        return App::make(ValidateEncryptionUseCase::class);
    }
}
