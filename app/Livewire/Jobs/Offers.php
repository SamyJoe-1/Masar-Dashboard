<?php

namespace App\Livewire\Jobs;

use App\Models\JobApp;
use Livewire\Component;
use Livewire\WithPagination;

class Offers extends Component
{
    use WithPagination;

    public $expandedCards = [];

    protected $paginationTheme = 'bootstrap';

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

    public function render()
    {
        $jobs = JobApp::with('user')
            ->withCount('applicants')
            ->where('public', true)
            ->latest()
            ->paginate(3);

        return view('livewire.jobs.offers', compact('jobs'));
    }
}
