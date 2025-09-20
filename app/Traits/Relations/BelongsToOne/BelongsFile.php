<?php

namespace App\Traits\Relations\BelongsToOne;

use App\Models\File;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsFile
{
    public function file(): BelongsTo
    {
        return $this->belongsTo(File::class, 'file_id');
    }
}
