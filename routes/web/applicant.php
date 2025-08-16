<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\applicant\JobAppController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth', 'lang'], 'prefix' => 'dashboard/applicant', 'as' => 'dashboard.applicant.'], function () {

    //    HomeController
    Route::get('/', [HomeController::class, 'dashboard_applicant'])->name('home');

    //    Job Applications
    Route::resource('jobs', JobAppController::class);
});
