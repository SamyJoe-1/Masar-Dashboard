<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\FileUploadController;

Auth::routes();

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::get('/test', [TestController::class, 'test'])->name('test');

// Upload form
Route::get('/upload', [FileUploadController::class, 'showUploadForm'])->name('upload.form');

// File upload handling
Route::post('/upload-files', [FileUploadController::class, 'uploadFiles'])->name('upload.files');

// Generate directory
Route::post('/generate-directory', [FileUploadController::class, 'generateDirectory'])->name('generate.directory');

// Show completion page with UUID
Route::get('/done/{directoryUuid}', [FileUploadController::class, 'showDone'])->name('upload.done');

// View directory contents (optional - for direct file access)
Route::get('/directory/{directoryUuid}', [FileUploadController::class, 'viewDirectory'])->name('view.directory');

// Delete directory
Route::delete('/directory/{directoryUuid}', [FileUploadController::class, 'deleteDirectory'])->name('delete.directory');

// Optional: Debug session route (if you want to keep sessions alive)
Route::get('/debug-session', function () {
    return response()->json([
        'session_id' => session()->getId(),
        'session_data' => session()->all()
    ]);
});
