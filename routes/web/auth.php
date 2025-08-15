<?php

use App\Http\Controllers\Auth\AuthenticationController;
use App\Http\Controllers\Auth\GoogleController;
use Illuminate\Support\Facades\Route;

Auth::routes();

//Google Authentication
Route::group(['controller' => GoogleController::class, 'middleware' => ['lang']], function () {
    Route::get('auth/google', [GoogleController::class, 'redirectToGoogle'])->name('auth.google');
    Route::get('auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);
});

Route::group(['controller' => AuthenticationController::class, 'middleware' => ['lang']], function () {
    Route::get('authentication', 'aio')->name('authentication');
    Route::get('logout', 'logout')->name('logout');
    Route::get('profile', 'profile')->name('profile');
});
