<?php

namespace App\Models;

use App\Traits\Relations\BelongsToOne\BelongsUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Profile extends Model
{
    use BelongsUser;

    protected $fillable = [
        'user_id', 'cv', 'education', 'college', 'position', 'nationality',
        'suggested_roles', 'last_job', 'bio', 'avatar',
    ];

    protected $casts = [
        'suggested_roles' => 'array',
    ];

    protected $table = 'profiles';

    public function r_avatar() :BelongsTo
    {
        return $this->belongsTo('App\Models\File', 'avatar');
    }

    public function getAvatar()
    {
        return asset(@$this->r_avatar->fullpath);
    }

    public function r_cv() :BelongsTo
    {
        return $this->belongsTo('App\Models\File', 'cv');
    }

    public function getCV()
    {
        return asset(@$this->r_cv->fullpath);
    }
}
