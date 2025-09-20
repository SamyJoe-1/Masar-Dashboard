<?php

use App\Http\Controllers\Guest\HomeController;
use App\Http\Controllers\Guest\StaticPagesController;
use App\Http\Controllers\TestController;
use Illuminate\Support\Facades\Route;

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
        Route::post('contact', 'store')->name('contact.submit');
        Route::get('terms', 'terms')->name('terms');
        Route::get('privacy', 'privacy')->name('privacy');
        Route::get('faq', 'faq')->name('faq');
        Route::get('services', 'services')->name('services');
        Route::get('sitemap', 'sitemap')->name('sitemap');
    });

});

//TestController
Route::group(['controller' => TestController::class, 'prefix' => 'test'], function () {
    Route::get('1', 'test_1');
    Route::get('2', 'test_2');
    Route::get('3', 'test_3');
    Route::get('4', 'test_4');
    Route::get('5', 'test_5');
    Route::get('6', 'test_6');
    Route::get('7', 'test_7');
    Route::get('8', 'test_8');
    Route::get('9', 'test_9');
    Route::post('9', 'test_9_2')->name('exc');
});
