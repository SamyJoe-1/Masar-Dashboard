<?php

use App\Http\Controllers\applicant\ApplicantController;
use App\Http\Controllers\applicant\JobAppController;
use App\Http\Controllers\applicant\SmartHiringController;
use App\Http\Controllers\applicant\CVAnalyzerController;
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
//        Route::get('analyzer', 'analyzer')->name('analyzer');
        Route::get('matcher', 'matcher')->name('matcher');
    });

    Route::group(['controller' => CVAnalyzerController::class, 'prefix' => 'cv-analyzer', 'as' => 'cv-analyzer.'], function () {
        Route::get('/', 'index')->name('index');
        Route::post('/analyze', 'analyze')->name('analyze');
        Route::get('/results/{slug}', 'show')->name('show')->withoutMiddleware(['auth']);
        Route::post('/results/{slug}/toggle-public','togglePublic')->name('toggle-public');
        Route::get('/results/{slug}/download', 'download')->name('download');
        Route::delete('/results/{slug}', 'destroy')->name('destroy');
        Route::get('/history', 'history')->name('history');
        Route::get('/history', 'history')->name('history');
    });

});
Route::get('career/matcher', [CVAnalyzerController::class, 'index'])->name('career.matcher');
