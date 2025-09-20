<?php

namespace App\Traits\Relations\HasMany;

use App\Models\JobApp;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasJobApps
{
    public function job_apps(): HasMany
    {
        return $this->hasOne(JobApp::class);
    }
}
