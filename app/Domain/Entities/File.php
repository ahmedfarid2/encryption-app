<?php

namespace App\Domain\Entities;

class File
{
    public $path;
    public $name;
    public $size;
    public $extension;

    public function __construct($path, $name, $size, $extension)
    {
        $this->path = $path;
        $this->name = $name;
        $this->size = $size;
        $this->extension = $extension;
    }
}