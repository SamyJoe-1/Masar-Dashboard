<?php

namespace App\Models;

use App\Traits\Relations\BelongsToOne\BelongsApplicant;
use Illuminate\Database\Eloquent\Model;

class ApplicantForm extends Model
{
    use BelongsApplicant;

    protected $fillable = [
        'applicant_id', 'questions', 'answers', 'status'
    ];

    CONST STATUSES = [
        'waiting', 'answered', 'not answered'
    ];

    protected $table = 'applicant_forms';
}
