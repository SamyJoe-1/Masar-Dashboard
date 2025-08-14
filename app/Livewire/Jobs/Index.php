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
    public $sortField = 'id';
    public $sortDirection = 'desc';

    public $perPage, $perPageOptions = [];
    public $columns, $selectedColumns = [], $tableColumns;

    protected $listeners = [
        'updateCountry' => 'setCountry',
    ];

    public function mount(){
        $this->perPageOptions = [10, 20, 50, 100, 200, 500];
        $this->perPage = $this->perPageOptions[0];
        $this->columns = ['id', 'title', 'description', 'applicants_count', 'approved_applicants_count', 'rejected_applicants_count', 'close', 'public', 'created_at', 'updated_at', 'action'];
        $this->selectedColumns = $this->columns;
        $this->tableColumns = [
            'ID' => ['sorting' => true, 'column' => 'id'],
            'Job Title' => ['sorting' => true, 'column' => 'title'],
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
        ];
        return JobApp::Filter($filters)
            ->WithCounts()
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
        $user = JobApp::find($id);
        if ($user) {
            $user->delete();
            $this->dispatch('swal:error', [
                'title' => 'تم حذف إعلان الوظيفة بنجاح',
                'text' => '',
                'icon' => 'success',
            ]);
        }
    }
}
