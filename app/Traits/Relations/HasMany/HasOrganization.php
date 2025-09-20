<?php

namespace App\Traits\Relations\HasMany;

use App\Models\Applicant;
use App\Models\Oraganization;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasOrganization
{
    public function organizations(): HasMany
    {
        return $this->hasMany(Oraganization::class, 'organization_id');
    }
}
