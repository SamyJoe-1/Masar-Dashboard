<?php

namespace App\Observers;

use App\Models\ApplicantForm;

class ApplicantFormObserver
{
    /**
     * Handle the ApplicantForm "created" event.
     */
    public function created(ApplicantForm $applicantForm): void
    {
        $applicantForm->expire_date = now()->addDays(5);
        $applicantForm->saveQuietly(); // avoids firing update events
    }


    /**
     * Handle the ApplicantForm "updated" event.
     */
    public function updated(ApplicantForm $applicantForm): void
    {
        //
    }

    /**
     * Handle the ApplicantForm "deleted" event.
     */
    public function deleted(ApplicantForm $applicantForm): void
    {
        //
    }

    /**
     * Handle the ApplicantForm "restored" event.
     */
    public function restored(ApplicantForm $applicantForm): void
    {
        //
    }

    /**
     * Handle the ApplicantForm "force deleted" event.
     */
    public function forceDeleted(ApplicantForm $applicantForm): void
    {
        //
    }
}
