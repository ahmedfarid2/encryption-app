<?php

namespace App\Data\Models;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    protected $fillable = [
        'path',
        'name',
        'size',
        'extension',
    ];
}