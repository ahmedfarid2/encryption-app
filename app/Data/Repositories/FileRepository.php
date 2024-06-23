<?php

namespace App\Data\Repositories;

use App\Domain\Repositories\FileRepositoryInterface;
use Illuminate\Support\Facades\Storage;

class FileRepository implements FileRepositoryInterface
{
    public function store($file, $directory = 'files')
    {
        return $file->store($directory);
    }

    public function storeContent($content, $filePath)
    {
        return Storage::put($filePath, $content);
    }

    public function getFileContent($filePath)
    {
        return Storage::get($filePath);
    }

    public function delete($filePath)
    {
        return Storage::delete($filePath);
    }

    public function exists($filePath)
    {
        return Storage::exists($filePath);
    }
}