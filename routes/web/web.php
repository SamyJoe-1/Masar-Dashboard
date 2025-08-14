<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\StaticPagesController;
use App\Http\Controllers\JobAppController;
use App\Http\Controllers\ApplicantController;
use App\Http\Controllers\TestController;

Auth::routes();
Route::group(['controller' => AuthenticationController::class, 'middleware' => ['lang']], function () {
    Route::get('authentication', 'aio')->name('authentication');
    Route::get('logout', 'logout')->name('logout');
    Route::get('profile', 'profile')->name('profile');
});


//HomeController
Route::group(['middleware' => ['lang']], function () {

    //HomeController
    Route::group(['controller' => HomeController::class], function () {
        Route::get('/', 'index')->name('home');
        Route::get('/home', 'home')->name('home');
    });

    //StaticPagesController
    Route::group(['controller' => StaticPagesController::class], function () {
        Route::get('about', 'about')->name('about');
        Route::get('contact', 'contact')->name('contact');
        Route::get('terms', 'terms')->name('terms');
        Route::get('privacy', 'privacy')->name('privacy');
        Route::get('faq', 'faq')->name('faq');
        Route::get('services', 'services')->name('services');
        Route::get('sitemap', 'sitemap')->name('sitemap');
    });

});

Route::group(['middleware' => ['auth', 'lang'], 'prefix' => 'dashboard/hr', 'as' => 'dashboard.hr.'], function () {

    //    HomeController
    Route::get('/', [HomeController::class, 'dashboard'])->name('home');

    //    Job Applications
    Route::resource('jobs', JobAppController::class);

    //    Applicants
    Route::resource('applicants', ApplicantController::class);

});


//TestController
Route::group(['controller' => TestController::class, 'prefix' => 'test'], function () {
    Route::get('1', 'test_1');
    Route::get('2', 'test_2');
    Route::get('3', 'test_3');
    Route::get('4', 'test_4');
    Route::get('5', 'test_5');
});
