<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\StaticPagesController;
use App\Http\Controllers\TestController;

Auth::routes();
Route::get('/authentication', [AuthenticationController::class, 'aio'])->name('authentication');

//HomeController
Route::group(['controller' => HomeController::class], function () {
    Route::get('/', 'index')->name('home');
    Route::get('/home', 'home')->name('home');
});

Route::group(['prefix' => 'dashboard', 'as' => 'dashboard.'], function () {

    Route::get('/', [HomeController::class, 'dashboard'])->name('home');

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


//TestController
Route::group(['controller' => TestController::class, 'prefix' => 'test'], function () {
    Route::get('1', 'test_1');
    Route::get('2', 'test_2');
});
