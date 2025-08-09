<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApplicantForm extends Model
{
    protected $fillable = [
        'applicant_id', 'questions', 'answers', 'status'
    ];

    CONST STATUSES = [
        'waiting', 'answered', 'not answered'
    ];

    protected $table = 'applicant_forms';
}
