<?php

namespace App\Http\Controllers;

use App\Models\JobApp;
use App\Models\JobApp as Job;
use Illuminate\Http\Request;

class JobAppController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('dashboard.hr.jobs.index');
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
    public function show(Job $job)
    {
        return view('dashboard.hr.jobs.show', ['jobApp' => $job]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(JobApp $jobApp)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, JobApp $jobApp)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(JobApp $jobApp)
    {
        //
    }
}
