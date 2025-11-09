<?php

use App\Http\Controllers\Guest\ApplicantFormController;
use App\Http\Controllers\Guest\SessionController;
use App\Http\Controllers\API\CVController;
use App\Http\Controllers\applicant\JobMatcherController;
use App\Http\Controllers\applicant\CVImproverController;

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


// Add these routes to routes/api.php

// Templates
Route::get('/templates', [CVController::class, 'getTemplates']);
Route::get('/templates/{id}', [CVController::class, 'getTemplate']);

// CV Management (requires authentication)
Route::middleware('auth:sanctum')->group(function () {
    // Drafts
    Route::get('/cv/drafts', [CVController::class, 'getDrafts']);
    Route::get('/cv/drafts/template/{templateId}', [CVController::class, 'getDraftByTemplate']);
    Route::delete('/cv/draft/{id}', [CVController::class, 'deleteDraft']);

    // Finalize CV
    Route::post('/cv/finalize', [CVController::class, 'finalize']);

    // Completed CVs
    Route::get('/cv/completed', [CVController::class, 'getCompletedCVs']);

    Route::post('/cv/draft', [CVController::class, 'saveDraft']);
    Route::post('/cv/finalize', [CVController::class, 'finalize']);

    Route::post('/cv/store-pdf', [CVController::class, 'storePDF']);
    Route::post('/cv/update-profile', [CVController::class, 'updateProfile']);

//    Route::post('/cv/generate-pdf', [CVImproverController::class, 'generatePDF']);
});

Route::post('/cv/generate-pdf', [CVImproverController::class, 'generatePDF']);


Route::middleware(['auth:sanctum'])->prefix('job-matcher')->group(function () {

    // Main matching endpoint - POST /api/job-matcher/match
    Route::post('/match', [JobMatcherController::class, 'match']);

    // Download feedback for specific job - POST /api/job-matcher/download-feedback/{jobId}
    Route::post('/download-feedback/{jobId}', [JobMatcherController::class, 'downloadFeedback']);

    // Download full report - POST /api/job-matcher/download-report
    Route::post('/download-report', [JobMatcherController::class, 'downloadFullReport']);

});
