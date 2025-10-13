<?php

namespace App\Models;

use App\Traits\Relations\BelongsToOne\BelongsFile;
use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    use BelongsFile;

    protected $fillable = [
        'name', 'file_id', 'data'
    ];

    protected $casts = [
        'data' => 'array'
    ];

    protected $table = 'templates';
}
