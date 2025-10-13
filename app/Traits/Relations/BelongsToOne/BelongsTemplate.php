<?php

namespace App\Traits\Relations\BelongsToOne;

use App\Models\File;
use App\Models\Template;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsTemplate
{
    public function template(): BelongsTo
    {
        return $this->belongsTo(Template::class, 'template_id');
    }
}
