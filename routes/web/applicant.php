<?php

use App\Http\Controllers\applicant\ApplicantController;
use App\Http\Controllers\applicant\JobAppController;
use App\Http\Controllers\applicant\SmartHiringController;
use App\Http\Controllers\Guest\HomeController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth', 'applicant', 'lang', 'profile'], 'prefix' => 'dashboard/applicant', 'as' => 'dashboard.applicant.'], function () {

    //    HomeController
    Route::get('/', [HomeController::class, 'dashboard_applicant'])->name('home');

    //    Job Applications
    Route::resource('jobs', JobAppController::class);

    //    Orders
    Route::resource('orders', ApplicantController::class);

    //    Smart Hiring Tools
    Route::group(['controller' => SmartHiringController::class, 'prefix' => 'smart/cv', 'as' => 'smart.cv.'], function () {
        Route::get('builder', 'builder')->name('builder');
//        Route::get('builder/{slug}', 'builder')->name('builder');
        Route::get('analyzer', 'analyzer')->name('analyzer');
        Route::get('matcher', 'matcher')->name('matcher');
    });
});
