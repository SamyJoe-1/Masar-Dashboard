<?php

namespace App\Traits\Job;

use App\Models\Oraganization;

trait FilterBar
{
    public $search;
    public $organizations;
    public $selectedOrganization;
    public $selectedOman = 2;

    public function FilterData(){
        $this->search = $parameters['search'] ?? '';
        $this->organizations = Oraganization::select('id', 'name')->get()->pluck('name', 'id');
    }
}
