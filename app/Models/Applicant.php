<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Applicant extends Model
{
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
