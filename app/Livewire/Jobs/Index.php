<?php

namespace App\Livewire\Jobs;

use App\Models\JobApp;
use Livewire\Component;
use Livewire\WithPagination;
use App\Traits\Job\FilterBar;

class Index extends Component
{
    use WithPagination, FilterBar;

    protected $paginationTheme = 'bootstrap';

    public $search;
    public $sortField = 'id';
    public $sortDirection = 'desc';

    public $perPage, $perPageOptions = [];
    public $columns, $selectedColumns = [], $tableColumns;
    public $selectAll = false, $selected = [], $bulkAction = '';

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

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selected = $this->getJobs()->pluck('id')->map(fn($id) => (string) $id)->toArray();
        } else {
            $this->selected = [];
        }
    }

    public function updatedSelected()
    {
        if (count($this->selected) == $this->perPage){
            $this->selectAll = true;
        }else{
            $this->selectAll = false;
        }
    }

    public function applyBulkAction()
    {
        if (empty($this->selected) || empty($this->bulkAction)) {
            $this->dispatch('swal:error', [
                'title' => 'No items selected or no action chosen',
                'text' => 'Please select items and an action',
                'icon' => 'warning',
            ]);
            return;
        }

        switch ($this->bulkAction) {
            case 'delete':
                $this->bulkDelete();
                break;
        }

        // Reset selection after action
        $this->selectAll = false;
        $this->selected = [];
        $this->bulkAction = '';
    }

    public function bulkDelete()
    {
        $count = JobApp::whereIn('id', $this->selected)->delete();

        $this->dispatch('swal:success', [
            'title' => "($count) Jobs deleted successfully!",
            'text' => '',
            'icon' => 'success',
        ]);
    }
}
