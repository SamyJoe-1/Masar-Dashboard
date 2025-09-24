<?php

use App\Http\Controllers\Guest\ApplicantFormController;
use App\Http\Controllers\Guest\SessionController;

// routes/api.php

//Interview Session Handling
Route::group(['controller' => SessionController::class, 'prefix' => 'session'], function () {
    Route::post('upload-screenshot', 'storeScreenshot');
    Route::get('start/{id}', 'start');
    Route::post('finish/{id}', 'finish');
    Route::get('close/{id}', 'close');
    Route::get('check/{id}', 'check');
});

// Add this route for camera error logging
Route::post('/api/log-camera-error', function (Request $request) {
    $errorData = $request->all();

    Log::channel('camera')->error('Camera Error on Client', [
        'error' => $errorData['error'] ?? 'Unknown error',
        'context' => $errorData['context'] ?? 'no-context',
        'user_agent' => $errorData['userAgent'] ?? 'unknown',
        'is_mobile' => $errorData['isMobile'] ?? false,
        'camera_enabled' => $errorData['cameraEnabled'] ?? false,
        'video_stream' => $errorData['videoStream'] ?? false,
        'skip_camera' => $errorData['skipCamera'] ?? false,
        'timestamp' => $errorData['timestamp'] ?? now(),
        'ip' => request()->ip(),
        'url' => request()->header('referer')
    ]);

    return response()->json(['logged' => true]);
});
