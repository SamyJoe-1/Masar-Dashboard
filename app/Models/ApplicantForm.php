<?php
// app/Models/ApplicantForm.php - Fixed version
namespace App\Models;

use App\Traits\Relations\BelongsToOne\BelongsApplicant;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ApplicantForm extends Model
{
    use BelongsApplicant;

    protected $fillable = [
        'applicant_id', 'questions', 'answers', 'slug', 'started_at', 'expire_date', 'status'
    ];

    const STATUSES = [
        'waiting', 'answered', 'not answered'
    ];

    protected $table = 'applicant_forms';

    protected $casts = [
        'started_at' => 'datetime',
        'expire_date' => 'datetime',
        'completed_at' => 'datetime',
        'closed_at' => 'datetime',
        'questions' => 'array',
        'answers' => 'array',
    ];

    /**
     * Check if the form is expired
     */
    public function isExpired()
    {
        if (!$this->expire_date) {
            return false;
        }
        return Carbon::parse($this->expire_date)->isPast();
    }

    /**
     * Check if the form is active (started but not expired)
     */
    public function isActive()
    {
        return ($this->status === 'started' || $this->started_at) && !$this->isExpired();
    }

    /**
     * Check if the form is ready to start
     */
    public function isReady()
    {
        return $this->status === 'waiting' && !$this->isExpired();
    }
}
