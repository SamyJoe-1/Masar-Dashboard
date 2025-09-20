<?php

namespace App\Models;

use App\Traits\Applicant\queryScope;
use App\Traits\Relations\BelongsToOne\BelongsFile;
use App\Traits\Relations\BelongsToOne\BelongsJob;
use App\Traits\Relations\BelongsToOne\BelongsUser;
use App\Traits\Relations\HasOne\HasForm;
use Illuminate\Database\Eloquent\Model;

class Applicant extends Model
{
    use BelongsJob, BelongsFile, BelongsUser, HasForm, queryScope;

    protected $fillable = [
        'job_id', 'user_id', 'file_id', 'information', 'email', 'feedback',
        'processing', 'answering', 'omani', 'emails', 'status'
    ];

    protected $casts = [
        'information' => 'array',
        'emails' => 'array',
        'processing' => 'boolean',
        'answering' => 'boolean',
        'omani' => 'boolean',
    ];

    CONST STATUSES = ['pending', 'rejected', 'interview requested', 'waiting for answering', 'approved'];

    CONST APPROVAL_KEYS = [
        0 => '❌ مرفوض',
        1 => '✅ مقبول'
    ];

    protected $table = 'applicants';

    public function getFileName(){
        if (empty($this->information['Name'])){
            return @$this->file->name;
        }else{
            return $this->information['Name'] . '.' . @$this->file->getType();
        }
    }

    public function getIcon()
    {
        switch ($this->status){
            case "pending":
                return "bx bx-loader-alt bx-spin";
            case "rejected":
                return "bx bx-x";
            case "waiting for answering":
                return "bx bx-question-mark";
            case "interview requested":
                return "bx bx-edit-alt";
            case "approved":
                return "bx bx-check";
            default:
                return '';
        }
    }
}
