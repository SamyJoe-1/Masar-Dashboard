<?php

namespace App\Traits\Applicant;

use App\Models\Applicant;

trait FilterBar
{
    public $search;
    public $statuses;
    public $selectedStatus;

    public function FilterData(){
        $this->search = $parameters['search'] ?? '';
        $this->statuses = Applicant::STATUSES;
    }
}
