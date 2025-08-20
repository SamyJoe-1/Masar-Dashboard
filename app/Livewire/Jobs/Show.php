<?php

namespace App\Livewire\Jobs;

use App\Models\Applicant;
use App\Models\JobApp;
use Livewire\Component;

class Show extends Component
{
    public $jobApp;
    public $applicants;
    public $expandedApplicant = null;

    // Pagination properties
    public $currentPage = 1;
    public $perPage = 10;
    public $totalApplicants = 0;

    public function mount(JobApp $jobApp)
    {
        $this->jobApp = $jobApp;
        $this->loadApplicants();
    }

    public function loadApplicants()
    {
        $query = Applicant::where('job_id', @$this->jobApp->id)->with('file');

        $this->totalApplicants = $query->count();

        $offset = ($this->currentPage - 1) * $this->perPage;
        $this->applicants = $query->skip($offset)->take($this->perPage)->get();
    }

    public function nextPage()
    {
        if ($this->hasMorePages()) {
            $this->currentPage++;
            $this->loadApplicants();
        }
    }

    public function previousPage()
    {
        if ($this->currentPage > 1) {
            $this->currentPage--;
            $this->loadApplicants();
        }
    }

    public function goToPage($page)
    {
        if ($page >= 1 && $page <= $this->getTotalPages()) {
            $this->currentPage = $page;
            $this->loadApplicants();
        }
    }

    public function hasMorePages()
    {
        return $this->currentPage < $this->getTotalPages();
    }

    public function getTotalPages()
    {
        return ceil($this->totalApplicants / $this->perPage);
    }

    public function toggleExpand($applicantId)
    {
        $this->expandedApplicant = $this->expandedApplicant === $applicantId ? null : $applicantId;
    }

    public function updateStatus($applicantId, $status)
    {
        $applicant = Applicant::find($applicantId);
        if ($applicant && in_array($status, Applicant::STATUSES)) {
            $applicant->update(['status' => $status]);
            $this->loadApplicants();

            $this->dispatch('status-updated', [
                'message' => __('words.Status updated successfully'),
                'type' => 'success'
            ]);
        }
    }

    public function getStatsProperty()
    {
        // Get stats from all applicants, not just current page
        $allApplicants = Applicant::where('job_id', @$this->jobApp->id)->get();

        return [
            'total' => $allApplicants->count(),
            'approved' => $allApplicants->where('status', 'approved')->count(),
            'rejected' => $allApplicants->where('status', 'rejected')->count(),
            'pending' => $allApplicants->where('status', 'pending')->count(),
            'under_review' => $allApplicants->where('status', 'interview requested')->count(),
            'waiting' => $allApplicants->where('status', 'waiting for answering')->count(),
        ];
    }

    public function render()
    {
        return view('livewire.jobs.show');
    }
}
