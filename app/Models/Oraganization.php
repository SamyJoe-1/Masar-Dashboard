<?php

namespace App\Models;

use App\Traits\Relations\HasMany\HasJobApps;
use Illuminate\Database\Eloquent\Model;

class Oraganization extends Model
{
    use HasJobApps;

    protected $fillable = [
        'name'
    ];

    protected $table = 'oraganizations';
}
