<?php

namespace App\Livewire\Jobs;

use App\Models\JobApp;
use App\Traits\Job\SelectByBulk;
use Livewire\Component;
use Livewire\WithPagination;
use App\Traits\Job\FilterBar;

class Index extends Component
{
    use WithPagination, FilterBar, SelectByBulk;

    protected $paginationTheme = 'bootstrap';

    public $search;
    public $sortField = 'id', $sortDirection = 'desc';

    public $perPage, $perPageOptions = [];
    public $columns, $selectedColumns = [], $tableColumns;

    protected $listeners = [
        'updateCountry' => 'setCountry',
    ];

    public function mount(){
        $this->perPageOptions = [10, 20, 50, 100, 200, 500];
        $this->perPage = $this->perPageOptions[0];
        $this->columns = ['id', 'title', 'organization', 'target', 'description', 'applicants_count', 'approved_applicants_count', 'rejected_applicants_count', 'close', 'public', 'created_at', 'updated_at', 'action'];
        $this->selectedColumns = $this->columns;
        $this->tableColumns = [
            'ID' => ['sorting' => true, 'column' => 'id'],
            'Job Title' => ['sorting' => true, 'column' => 'title'],
            'Organization' => ['sorting' => true, 'column' => 'organization'],
            'Target' => ['sorting' => true, 'column' => 'target'],
            'Description' => ['sorting' => true, 'column' => 'description'],
            'Applicants Count' => ['sorting' => true, 'column' => 'applicants_count'],
            'Approved Applicants' => ['sorting' => true, 'column' => 'approved_applicants_count'],
            'Rejected Applicants' => ['sorting' => true, 'column' => 'rejected_applicants_count'],
            'Closed' => ['sorting' => true, 'column' => 'close'],
            'Public' => ['sorting' => true, 'column' => 'public'],
            'Created' => ['sorting' => true, 'column' => 'created_at'],
            'Updated' => ['sorting' => true, 'column' => 'updated_at'],
            'Action' => ['sorting' => false, 'column' => 'action'],
        ];
        $this->FilterData();
    }

    public function render()
    {
        $jobs = $this->getJobs();
        return view('livewire.jobs.index', compact('jobs'));
    }

    public function getJobs(){
        $filters = [
            'search' => $this->search,
            'organizations' => $this->selectedOrganization,
            'target' => $this->selectedOman,
        ];
        return JobApp::Filter($filters)
            ->WithCounts()
//            ->where('user_id', auth()->id())
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);
    }

    public function update(){}

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'desc';
        }

        $this->sortField = $field;
    }

    public function Delete($id)
    {
        $job = JobApp::find($id);
        if ($job) {
            $job->delete();
            $this->dispatch('swal:error', [
                'title' => __("words.Job ad has been successfully deleted."),
                'text' => '',
                'icon' => 'success',
            ]);
        }
    }

    public function Close($id)
    {
        $job = JobApp::find($id);
        if ($job) {
            $job->close = !$job->close;
            $job->save();
            $this->dispatch('swal:error', [
                'title' => __("words.The operation was successful."),
                'text' => '',
                'icon' => 'success',
            ]);
        }
    }

    public function Public($id)
    {
        $job = JobApp::find($id);
        if ($job) {
            $job->public = !$job->public;
            $job->save();
            $this->dispatch('swal:error', [
                'title' => __("words.The operation was successful."),
                'text' => '',
                'icon' => 'success',
            ]);
        }
    }
}
