<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FileController;
use Illuminate\Support\Facades\Storage;

Route::controller(FileController::class)->group(function () {
    Route::get('/', 'index');
    Route::post('/upload', 'upload');
    Route::post('/encrypt', 'encrypt');
    Route::post('/decrypt', 'decrypt');
    Route::post('/validate', 'validateEncryption');
});

Route::get('/download', function (Request $request) {
    $filePath = $request->query('filePath');
    if (Storage::exists($filePath)) {
        return Storage::download($filePath);
    }
    return abort(404);
});
