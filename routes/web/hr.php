<?php

use App\Http\Controllers\Guest\HomeController;
use App\Http\Controllers\hr\ApplicantController;
use App\Http\Controllers\hr\JobAppController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth', 'hr', 'lang', 'profile'], 'prefix' => 'dashboard/hr', 'as' => 'dashboard.hr.'], function () {

    //    HomeController
    Route::get('/', [HomeController::class, 'dashboard_hr'])->name('home');

    //    Job Applications
    Route::resource('jobs', JobAppController::class);

    //    Applicants
    Route::resource('applicants', ApplicantController::class);

});
