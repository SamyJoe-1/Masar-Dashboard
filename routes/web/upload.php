<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FileUploadController;

Route::group(['controller' => FileUploadController::class, 'middleware' => ['auth', 'lang']], function () {
    // Upload form
    Route::get('/upload', 'showUploadForm')->name('upload.form');

    // File upload handling
    Route::post('/upload-files', 'uploadFiles')->name('upload.files');

    // Generate directory
    Route::post('/generate-directory', 'generateDirectory')->name('generate.directory');

    // Show completion page with UUID
    Route::post('/result/preview/{directoryUuid}', 'showDone')->name('upload.done');

    // View directory contents
    Route::get('/directory/{directoryUuid}', 'viewDirectory')->name('view.directory');

    // Delete directory
    Route::delete('/directory/{directoryUuid}', 'deleteDirectory')->name('delete.directory');
});

// Optional: Debug session route (if you want to keep sessions alive)
Route::get('/debug-session', function () {
    return response()->json([
        'session_id' => session()->getId(),
        'session_data' => session()->all()
    ]);
});
