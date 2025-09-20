<?php

use App\Http\Controllers\Auth\AuthenticationController;
use App\Http\Controllers\Auth\GoogleController;
use Illuminate\Support\Facades\Route;


Route::group(['middleware' => ['lang']], function () {

    //    Login / Register / Forgot-Password
    Auth::routes();

    //    GoogleController
    Route::group(['controller' => GoogleController::class], function () {
        Route::get('auth/google', 'redirectToGoogle')->name('auth.google');
        Route::get('auth/google/callback', 'handleGoogleCallback');
    });

    //    AuthenticationController
    Route::group(['controller' => AuthenticationController::class, 'middleware' => ['lang']], function () {
        Route::get('authentication', 'aio')->name('authentication');
        Route::get('logout', 'logout')->name('logout');
        Route::get('profile', 'profile')->name('profile');
    });
});
