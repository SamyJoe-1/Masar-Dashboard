<?php

namespace App\Traits\Relations\HasMany;

use App\Models\Applicant;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasApplicants
{
    public function applicants(): HasMany
    {
        return $this->hasMany(Applicant::class, 'job_id');
    }

    public function approved_applicants(): HasMany
    {
        return $this->hasMany(Applicant::class, 'job_id')->where('status', '=', 'approved');
    }

    public function rejected_applicants(): HasMany
    {
        return $this->hasMany(Applicant::class, 'job_id')->where('status', '=', 'rejected');
    }

    public function waiting_applicants(): HasMany
    {
        return $this->hasMany(Applicant::class, 'job_id')->where('status', '=', 'waiting for answering');
    }
}
