<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobApp extends Model
{
    protected $fillable = [
        'user_id', 'title', 'description'
    ];

    protected $table = 'job_apps';
}
