<?php

namespace App\Livewire\Jobs;

use App\Models\JobApp;
use Livewire\Component;
use Livewire\WithPagination;

class Offers extends Component
{
    use WithPagination;

    public $expandedCards = [];
    public $search = '';
    public $statusFilter = 'all'; // all, open, closed

    protected $paginationTheme = 'bootstrap';

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => 'all'],
    ];

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

    public function clearFilters()
    {
        $this->search = '';
        $this->statusFilter = 'all';
        $this->resetPage();
    }

    public function render()
    {
        $query = JobApp::with('user')
            ->withCount('applicants')
            ->where('public', true);

        // Apply search filter
        if (!empty($this->search)) {
            $query->where(function($q) {
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

        $jobs = $query->latest()->paginate(3);

        return view('livewire.jobs.offers', compact('jobs'));
    }
}
