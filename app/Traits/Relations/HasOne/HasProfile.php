<?php

namespace App\Traits\Relations\HasOne;

use App\Models\ApplicantForm;
use App\Models\Profile;
use Illuminate\Database\Eloquent\Relations\HasOne;

trait HasProfile
{
    public function profile(): HasOne
    {
        return $this->hasOne(Profile::class);
    }
}
