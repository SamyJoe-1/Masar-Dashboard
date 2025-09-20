<?php

namespace App\Traits\Relations\BelongsToOne;

use App\Models\Oraganization;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsOrganization
{
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Oraganization::class, 'organization_id');
    }
}
