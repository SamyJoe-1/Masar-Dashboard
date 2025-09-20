<?php

namespace App\Livewire\Jobs;

use App\Models\JobApp;
use App\Models\Oraganization;
use App\Models\Applicant;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithPagination;

class Offers extends Component
{
    use WithPagination;

    public $expandedCards = [];
    public $search = '';
    public $statusFilter = 'all'; // all, open, closed
    public $organizationFilter = ''; // all, open, closed
    public $organizations;
    protected $paginationTheme = 'bootstrap';

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => 'all'],
        'organizationFilter' => ['except' => ''],
    ];

    public function mount()
    {
        $this->organizations = Oraganization::select('id', 'name')->get()->pluck('name', 'id');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
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

    public function applyToJob($jobId)
    {
        return redirect()->route('jobs.apply', $jobId);
    }

    // NEW FUNCTION: Apply with existing CV
    public function applyWithExistingCV($jobId)
    {
        $user = auth()->user();

        if (!$user->profile || !$user->profile->cv) {
            $this->dispatch('showAlert', [
                'type' => 'error',
                'title' => __('words.Error'),
                'text' => __('words.No CV found')
            ]);
            return;
        }

        // Create applicant with existing CV
        Applicant::create([
            'job_id' => $jobId,
            'user_id' => $user->id,
            'file_id' => $user->profile->cv,
            'status' => 'pending',
            'email' => $user->email,
            'omani' => $user->profile->nationality ?? 0,
        ]);

        $this->dispatch('showAlert', [
            'type' => 'success',
            'title' => __('words.success'),
            'text' => __('words.Application submitted successfully')
        ]);

        // Refresh the component to update the UI
        $this->render();
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->statusFilter = 'all';
        $this->organizationFilter = 'all';
        $this->resetPage();
    }

    public function render()
    {
        $query = JobApp::with('user')->with('organization')
            ->withCount('applicants')
            ->where(function (Builder $query) {
                $omani = auth()->user()->profile->nationality ?? 0;
                $query->where('target', 2)->orWhere('target', $omani);
            })->where('public', true);

        // Apply search filter
        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        // Apply status filter
        if ($this->statusFilter === 'open') {
            $query->where('close', false);
        } elseif ($this->statusFilter === 'closed') {
            $query->where('close', true);
        }

        // Apply status filter
        if (!empty($this->organizationFilter)) {
            $query->where('job_apps.organization_id', $this->organizationFilter);
        }

        $jobs = $query->latest()->paginate(3);

        return view('livewire.jobs.offers', compact('jobs'));
    }
}
