<?php

namespace App\Livewire\Jobs;

use App\Models\JobApp;
use App\Models\Applicant;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class Apply extends Component
{
    use WithFileUploads;

    public $jobId;
    public $job;
    public $resume;
    public $uploadProgress = 0;
    public $isUploading = false;
    public $uploadedFileName;
    public $hasApplied = false;

    protected $rules = [
        'resume' => 'required|file|mimes:pdf,doc,docx|max:5120', // 5MB max
    ];

    protected $messages = [
        'resume.required' => 'Resume file is required.',
        'resume.mimes' => 'Resume must be a PDF, DOC, or DOCX file.',
        'resume.max' => 'Resume file size must not exceed 5MB.',
    ];

    public function mount($jobId)
    {
        $this->jobId = $jobId;
        $this->job = JobApp::with('user')->withCount('applicants')->findOrFail($jobId);

        // Check if user already applied
        $this->hasApplied = Applicant::where('job_id', $jobId)
            ->where('user_id', Auth::id())
            ->exists();
    }

    public function updatedResume()
    {
        $this->validate();
        $this->uploadedFileName = $this->resume->getClientOriginalName();
    }

    public function removeFile()
    {
        $this->resume = null;
        $this->uploadedFileName = null;
        $this->uploadProgress = 0;
    }

    public function downloadFile()
    {
        if ($this->resume) {
            return response()->download($this->resume->getRealPath(), $this->uploadedFileName);
        }
    }

    public function submitApplication()
    {
        // Check if job is still open
        if ($this->job->close) {
            $this->dispatch('swal', [
                'icon' => 'error',
                'title' => __('words.Application Failed'),
                'text' => __('words.This job is no longer accepting applications.'),
            ]);
            return;
        }

        // Check if user already applied
        if ($this->hasApplied) {
            $this->dispatch('swal', [
                'icon' => 'warning',
                'title' => __('words.Already Applied'),
                'text' => __('words.You have already applied to this job.'),
            ]);
            return;
        }

        $this->validate();

        try {
            $this->isUploading = true;

            // Generate UUID for the folder
            $uuid = \Str::uuid();

            // Get original filename
            $originalName = $this->resume->getClientOriginalName();

            // Store the resume file in uploads/<uuid>/<filename>
            $resumePath = $this->resume->storeAs("uploads/{$uuid}", $originalName, 'public');

            // Create File record
            $file = \App\Models\File::create([
                'name' => $originalName,
                'path' => $resumePath,
                'fullpath' => "/storage/$resumePath",
                'type' => $this->resume->getMimeType(),
                'size' => $this->resume->getSize(),
            ]);

            // Create the application
            Applicant::create([
                'job_id' => $this->jobId,
                'user_id' => Auth::id(),
                'file_id' => @$file->id,
                'information' => null,
                'processing' => true,
                'answering' => false,
                'omani' => auth()->user()->profile->nationality ?? 0,
            ]);

            $this->isUploading = false;

            // Success alert and redirect
            $this->dispatch('swal', [
                'icon' => 'success',
                'title' => __('words.Application Submitted'),
                'text' => __('words.Your application has been submitted successfully!'),
                'confirmButtonText' => __('words.OK'),
            ]);

            // Redirect after 2 seconds
            $this->dispatch('redirect-after-success');

        } catch (\Exception $e) {
            $this->isUploading = false;

            $this->dispatch('swal', [
                'icon' => 'error',
                'title' => __('words.Application Failed'),
                'text' => __('words.Something went wrong. Please try again.'),
            ]);
        }
    }

    public function render()
    {
        return view('livewire.jobs.apply');
    }
}
