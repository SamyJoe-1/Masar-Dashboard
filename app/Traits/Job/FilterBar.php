<?php

namespace App\Traits\Job;

trait FilterBar
{
    public $search;

    public function FilterData(){
        $this->search = $parameters['search'] ?? '';
    }
}
