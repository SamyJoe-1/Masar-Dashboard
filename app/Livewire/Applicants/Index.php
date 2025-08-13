<?php

namespace App\Livewire\Applicants;

use App\Models\Applicant;
use Livewire\Component;
use Livewire\WithPagination;
use App\Traits\Applicant\FilterBar;

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
        $this->columns = ['id', 'job_id', 'processing', 'answering', 'status', 'created_at', 'updated_at', 'action'];
        $this->selectedColumns = $this->columns;
        $this->tableColumns = [
            'ID' => ['sorting' => true, 'column' => 'id'],
            'Job ID' => ['sorting' => true, 'column' => 'job_id'],
            'Processing' => ['sorting' => true, 'column' => 'processing'],
            'Answering' => ['sorting' => true, 'column' => 'answering'],
            'Status' => ['sorting' => true, 'column' => 'status'],
            'Created' => ['sorting' => true, 'column' => 'created_at'],
            'Updated' => ['sorting' => true, 'column' => 'updated_at'],
            'Action' => ['sorting' => false, 'column' => 'action'],
        ];
        $this->FilterData();
    }

    public function render()
    {
        $applicants = $this->getApplicants();
        return view('livewire.applicants.index', compact('applicants'));
    }

    public function getApplicants(){
        $filters = [
            'search' => $this->search,
        ];
        return Applicant::Filter($filters)
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
        $user = Applicant::find($id);
        if ($user) {
            $user->delete();
            $this->dispatch('swal:error', [
                'title' => 'تم حذف المتقدم بنجاح',
                'text' => '',
                'icon' => 'success',
            ]);
        }
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selected = $this->getApplicants()->pluck('id')->map(fn($id) => (string) $id)->toArray();
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
        $count = Applicant::whereIn('id', $this->selected)->delete();

        $this->dispatch('swal:success', [
            'title' => "($count) Applicants deleted successfully!",
            'text' => '',
            'icon' => 'success',
        ]);
    }
}
