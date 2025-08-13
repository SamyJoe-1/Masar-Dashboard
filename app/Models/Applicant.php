<?php

namespace App\Models;

use App\Traits\Applicant\queryScope;
use App\Traits\Relations\BelongsToOne\BelongsFile;
use App\Traits\Relations\BelongsToOne\BelongsJob;
use App\Traits\Relations\HasOne\HasForm;
use Illuminate\Database\Eloquent\Model;

class Applicant extends Model
{
    use BelongsJob, BelongsFile, HasForm, queryScope;

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

    public function getIcon()
    {
        switch ($this->status){
            case "pending":
                return "bx bx-loader-alt bx-spin";
                break;
            case "rejected":
                return "bx bx-x";
                break;
            case "waiting for answering":
                return "bx bx-question-mark";
                break;
            case "approved":
                return "bx bx-check";
                break;
            default:
                return '';
        }
    }
}
