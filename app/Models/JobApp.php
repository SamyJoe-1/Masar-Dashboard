<?php

namespace App\Models;

use App\Traits\Job\queryScope;
use App\Traits\Relations\BelongsToOne\BelongsUser;
use App\Traits\Relations\HasMany\HasApplicants;
use Illuminate\Database\Eloquent\Model;

class JobApp extends Model
{
    use BelongsUser, HasApplicants, queryScope;

    protected $fillable = [
        'user_id', 'title', 'description', 'slug', 'public', 'close'
    ];

    protected $casts = [
        'public' => 'boolean',
        'close' => 'boolean',
    ];

    protected $table = 'job_apps';
}
