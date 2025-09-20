<?php

namespace App\Traits\Relations\BelongsToOne;

use App\Models\JobApp;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsJob
{
    public function job_app(): BelongsTo
    {
        return $this->belongsTo(JobApp::class, 'job_id');
    }
}
