<?php

namespace App\Traits\Relations\BelongsToOne;

use App\Models\Applicant;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsApplicant
{
    public function applicant(): BelongsTo
    {
        return $this->belongsTo(Applicant::class, 'applicant_id');
    }
}
