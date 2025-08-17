<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\applicant\JobAppController;
use App\Http\Controllers\applicant\ApplicantController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth', 'lang'], 'prefix' => 'dashboard/applicant', 'as' => 'dashboard.applicant.'], function () {

    //    HomeController
    Route::get('/', [HomeController::class, 'dashboard_applicant'])->name('home');

    //    Job Applications
    Route::resource('jobs', JobAppController::class);

    //    Orders
    Route::resource('orders', ApplicantController::class);
});
