<?php

namespace App\Traits\Relations\HasOne;

use App\Models\ApplicantForm;
use Illuminate\Database\Eloquent\Relations\HasOne;

trait HasForm
{
    public function forms(): HasOne
    {
        return $this->hasMany(ApplicantForm::class);
    }

    public function form(): HasOne
    {
        return $this->hasOne(ApplicantForm::class);
    }
}
