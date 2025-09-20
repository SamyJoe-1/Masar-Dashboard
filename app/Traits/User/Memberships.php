<?php

namespace App\Traits\User;

trait Memberships
{
    public function isHR()
    {
        return $this->role == 'hr';
    }

    public function isApplicant()
    {
        return $this->role == 'applicant';
    }

    public function getRole()
    {
        switch ($this->role){
            case 'hr':
                return 'HR';
                break;
            case 'applicant':
                return 'Applicant';
                break;
            default:
                return 'N/A';
        }
    }
}
