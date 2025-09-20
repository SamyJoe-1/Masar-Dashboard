<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Traits\Relations\HasMany\HasApplicants;
use App\Traits\Relations\HasMany\HasJobApps;
use App\Traits\Relations\HasOne\HasProfile;
use App\Traits\User\Memberships;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasJobApps, HasProfile, HasApplicants, Memberships;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $table = 'users';

    CONST ROLES = ['hr', 'applicant'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function sendPasswordResetNotification($token)
    {
        \Log::info('Sending reset notification to: ' . $this->email);

        $this->notify(new \Illuminate\Auth\Notifications\ResetPassword($token));

        \Log::info('Reset notification sent');
    }
}
