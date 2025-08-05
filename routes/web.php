<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\FileUploadController;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::get('/test', [TestController::class, 'test'])->name('test');

Route::get('/upload', [FileUploadController::class, 'showUploadForm'])->name('upload.form');
Route::post('/upload-files', [FileUploadController::class, 'uploadFiles'])->name('upload.files');
Route::get('/uploaded-files', [FileUploadController::class, 'showUploadedFiles'])->name('uploaded.files');
Route::delete('/delete-file/{filename}', [FileUploadController::class, 'deleteFile'])->name('delete.file');
