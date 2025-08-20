<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\ApplicantForm;
use Illuminate\Http\Request;

class ApplicantFormController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show($interview)
    {
        $interview = ApplicantForm::where('slug', $interview)->where('status', 'waiting')->first();
        if (!$interview){
            abort(404);
        }
        return view('dashboard.guest.interviewForm', compact('interview'));
//        return view('dashboard.guest.interviewForm3', compact('interview'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ApplicantForm $applicantForm)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ApplicantForm $applicantForm)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ApplicantForm $applicantForm)
    {
        //
    }
}
