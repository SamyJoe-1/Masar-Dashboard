<?php

namespace App\Models;

use App\Traits\Relations\BelongsToOne\BelongsFile;
use App\Traits\Relations\BelongsToOne\BelongsUser;
use Illuminate\Database\Eloquent\Model;

class ResumeATS extends Model
{
    use BelongsFile, BelongsUser;

    protected $fillable = [
        'user_id', 'file_id', 'raw_text', 'ats_score', 'content_score', 'skills_score',
        'formatting_score', 'feedback', 'slug', 'is_public'
    ];

    protected $casts = [
        'raw_text' => 'array',
        'feedback' => 'array'
    ];

    protected $table = 'resume_ats';
}
