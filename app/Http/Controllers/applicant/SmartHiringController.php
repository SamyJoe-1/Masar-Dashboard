<?php

namespace App\Http\Controllers\applicant;

use App\Http\Controllers\Controller;

class SmartHiringController extends Controller
{
    public function builder()
    {
        return view('dashboard.applicant.smart.cv_builder');
    }

    public function analyzer()
    {
        return view('dashboard.applicant.smart.cv_analyzer');
    }

    public function matcher()
    {
        return view('dashboard.applicant.smart.cv_matcher');
    }

    public function improve()
    {
        return view('dashboard.applicant.smart.cv_improver');
    }
}
