<?php

namespace App\Models;

use App\Traits\Job\queryScope;
use App\Traits\Relations\BelongsToOne\BelongsOrganization;
use App\Traits\Relations\BelongsToOne\BelongsUser;
use App\Traits\Relations\HasMany\HasApplicants;
use Illuminate\Database\Eloquent\Model;

class JobApp extends Model
{
    use BelongsUser, BelongsOrganization, HasApplicants, queryScope;

    protected $fillable = [
        'user_id', 'organization_id', 'title', 'description',
        'target', 'lang', 'slug', 'public', 'close'
    ];

    CONST TARGETS = [
        0 => "Non-Omani",
        1 => "Omani",
        2 => "All",
    ];

    protected $casts = [
        'public' => 'boolean',
        'close' => 'boolean',
    ];

    protected $table = 'job_apps';

    public function isApplied()
    {
        return Applicant::where('job_id', @$this->id)->where('user_id', @auth()->id())->count();
    }
}
