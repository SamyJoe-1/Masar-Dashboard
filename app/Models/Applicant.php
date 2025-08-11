<?php

namespace App\Models;

use App\Traits\Relations\BelongsToOne\BelongsFile;
use App\Traits\Relations\BelongsToOne\BelongsJob;
use App\Traits\Relations\HasOne\HasForm;
use Illuminate\Database\Eloquent\Model;

class Applicant extends Model
{
    use BelongsJob, BelongsFile, HasForm;

    protected $fillable = [
        'job_id', 'file_id', 'information', 'processing', 'answering', 'status'
    ];

    protected $casts = [
        'information' => 'array',
        'processing' => 'boolean',
        'answering' => 'boolean',
    ];

    CONST STATUSES = ['pending', 'rejected', 'waiting for answering', 'approved'];

    protected $table = 'applicants';
}
