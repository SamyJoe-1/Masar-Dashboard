<?php

namespace App\Livewire\Jobs;

use App\Models\JobApp;
use App\Models\Applicant;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MyOffers extends Component
{
    use WithPagination;

    public $expandedCards = [];
    public $search = '';
    public $statusFilter = 'all'; // all, pending, rejected, waiting for answering, approved
    public $applicantStatusFilter = 'all'; // New filter for applicant status

    protected $paginationTheme = 'bootstrap';

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => 'all'],
        'applicantStatusFilter' => ['except' => 'all'],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingApplicantStatusFilter()
    {
        $this->resetPage();
    }

    public function toggleCard($jobId)
    {
        if (in_array($jobId, $this->expandedCards)) {
            $this->expandedCards = array_filter($this->expandedCards, fn($id) => $id !== $jobId);
        } else {
            $this->expandedCards[] = $jobId;
        }
    }

    public function downloadCV($jobId)
    {
        $applicant = Applicant::where('job_id', $jobId)
            ->where('user_id', Auth::id())
            ->with('file')
            ->first();

        if ($applicant && $applicant->file) {
            $filePath = 'public/' . $applicant->file->path;

            if (Storage::exists($filePath)) {
                return Storage::download($filePath, $applicant->file->name);
            }
        }

        $this->dispatch('swal', [
            'icon' => 'error',
            'title' => __('words.File Not Found'),
            'text' => __('words.The CV file could not be found.'),
        ]);
    }

    public function continueApplication($jobId)
    {
        return redirect()->route('dashboard.applicant.jobs.show', $jobId);
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->statusFilter = 'all';
        $this->applicantStatusFilter = 'all';
        $this->resetPage();
    }

    public function render()
    {
        // Only get jobs that the current user has applied to
        $query = JobApp::with(['user', 'applicants' => function($query) {
            $query->where('user_id', Auth::id())->with('file');
        }])
            ->withCount('applicants')
            ->whereHas('applicants', function($query) {
                $query->where('user_id', Auth::id());
            });

        // Apply search filter
        if (!empty($this->search)) {
            $query->where(function($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        // Apply job status filter
        if ($this->statusFilter === 'open') {
            $query->where('close', false);
        } elseif ($this->statusFilter === 'closed') {
            $query->where('close', true);
        }

        // Apply applicant status filter
        if ($this->applicantStatusFilter !== 'all') {
            $query->whereHas('applicants', function($q) {
                $q->where('user_id', Auth::id())
                    ->where('status', $this->applicantStatusFilter);
            });
        }

        $jobs = $query->latest()->paginate(3);

        // Get applicant statuses for the filter dropdown
        $applicantStatuses = Applicant::STATUSES;

        return view('livewire.jobs.my-offers', compact('jobs', 'applicantStatuses'));
    }
}
