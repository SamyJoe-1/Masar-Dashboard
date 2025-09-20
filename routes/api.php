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
