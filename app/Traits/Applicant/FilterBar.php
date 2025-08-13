<?php

namespace App\Traits\Applicant;

trait FilterBar
{
    public $search;

    public function FilterData(){
        $this->search = $parameters['search'] ?? '';
    }
}
