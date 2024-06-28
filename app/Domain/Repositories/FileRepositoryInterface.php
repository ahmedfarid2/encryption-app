<?php

namespace App\Domain\Repositories;

interface FileRepositoryInterface
{
    public function storeChunk($chunk, $filePath);

    public function appendToFile($filePath, $content);

    public function combineChunks($fileId, $originalFileName);

    public function deleteTemporaryChunks($fileId);

    public function storeContent($content, $filePath);

    public function getFileContent($filePath);

    public function delete($filePath);

    public function exists($filePath);

    public function getSize($filePath);

    function makeDirectory($directory);
}
