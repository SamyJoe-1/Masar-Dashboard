<?php

namespace App\Livewire\Applicants;

use App\Models\Applicant;
use App\Traits\Applicant\SelectByBulk;
use Livewire\Component;
use Livewire\WithPagination;
use App\Traits\Applicant\FilterBar;

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

    public function mount($status='all'){
        $this->perPageOptions = [10, 20, 50, 100, 200, 500];
        $this->perPage = $this->perPageOptions[0];
        $this->columns = ['id', 'job_title', 'name', 'email', 'phone', 'status', 'created_at', 'updated_at', 'action'];
        $this->selectedColumns = $this->columns;
        $this->tableColumns = [
            'ID' => ['sorting' => true, 'column' => 'id'],
            'Job' => ['sorting' => true, 'column' => 'job_title'],
            'Name' => ['sorting' => true, 'column' => 'name'],
            'Email' => ['sorting' => true, 'column' => 'email'],
            'Phone' => ['sorting' => true, 'column' => 'phone'],
//            'Processing' => ['sorting' => true, 'column' => 'processing'],
//            'Answering' => ['sorting' => true, 'column' => 'answering'],
            'Status' => ['sorting' => true, 'column' => 'status'],
            'Created' => ['sorting' => true, 'column' => 'created_at'],
            'Updated' => ['sorting' => true, 'column' => 'updated_at'],
            'Action' => ['sorting' => false, 'column' => 'action'],
        ];
        $this->FilterData();
        if ($status != 'all') {
            $this->selectedStatus = [$status];
        }
    }

    public function render()
    {
        $applicants = $this->getApplicants();
        return view('livewire.applicants.index', compact('applicants'));
    }

    public function getApplicants(){
        $filters = [
            'search' => $this->search,
            'status' => $this->selectedStatus,
//            'user' => auth()->id() ?? 0,
        ];
        return Applicant::Filter($filters)
            ->select('applicants.*', 'job_apps.title as job_title', 'applicants.information->Name as name', 'applicants.information->Email as email', 'applicants.information->Phone as phone')
            ->leftJoin('job_apps', 'job_apps.id', '=', 'applicants.job_id')
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
                'title' => __("words.The applicant has been successfully deleted."),
                'text' => '',
                'icon' => 'success',
            ]);
        }
    }
}
