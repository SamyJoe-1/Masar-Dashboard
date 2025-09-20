<?php

use App\Http\Controllers\Guest\ApplicantFormController;
use Illuminate\Support\Facades\Route;

//    Interview Form
Route::resource('interview', ApplicantFormController::class);
