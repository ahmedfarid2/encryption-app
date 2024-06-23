<?php

namespace App\Domain\Repositories;

interface FileRepositoryInterface
{
    public function store($file);
    public function storeContent($content, $filePath);
    public function getFileContent($filePath);
    public function delete($filePath);
    public function exists($filePath);
}