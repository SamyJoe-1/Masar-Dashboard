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
            $this->dispatchBrowserEvent('swal', [
                'type' => 'error',
                'title' => __('words.Application Failed'),
                'text' => __('words.This job is no longer accepting applications.'),
            ]);
            return;
        }

        // Check if user already applied
        if ($this->hasApplied) {
            $this->dispatchBrowserEvent('swal', [
                'type' => 'warning',
                'title' => __('words.Already Applied'),
                'text' => __('words.You have already applied to this job.'),
            ]);
            return;
        }

        $this->validate();

        try {
            $this->isUploading = true;

            // Store the resume file
            $resumePath = $this->resume->store('resumes', 'public');

            // Create the application
            Applicant::create([
                'job_id' => $this->jobId,
                'user_id' => Auth::id(),
                'resume_path' => $resumePath,
                'applied_at' => now(),
            ]);

            $this->isUploading = false;

            // Success alert and redirect
            $this->dispatchBrowserEvent('swal', [
                'type' => 'success',
                'title' => __('words.Application Submitted'),
                'text' => __('words.Your application has been submitted successfully!'),
                'confirmButtonText' => __('words.OK'),
            ]);

            // Redirect after 2 seconds
            $this->dispatchBrowserEvent('redirect-after-success');

        } catch (\Exception $e) {
            $this->isUploading = false;

            $this->dispatchBrowserEvent('swal', [
                'type' => 'error',
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
