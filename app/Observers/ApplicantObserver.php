<?php

namespace App\Observers;

use App\Log\LogHelper;
use App\Mail\ApprovalNotificationMail;
use App\Models\Applicant;
use App\Mail\RejectionNotificationMail;
use Illuminate\Support\Facades\Mail;

class ApplicantObserver
{

    /**
     * Handle the Applicant "created" event.
     */
    public function created(Applicant $applicant): void
    {
        //
    }

    /**
     * Handle the Applicant "updated" event.
     */
    public function updated(Applicant $applicant): void
    {
        if (empty($applicant->email) && !empty($applicant->information['Email'])) {
            $applicant->update([
                'email' => @$applicant->information['Email'],
            ]);
        }
        if ($applicant->isDirty('status')) {
            \Log::info('Status changed to: ' . $applicant->status);
            @$email = $applicant->information['Email'];
            if ($applicant->status == 'rejected') {
                try {
                    // Send rejection email if applicant has email
                    if (!empty($email)) {
                        Mail::send(new RejectionNotificationMail($applicant));

                        // Log the email sending
                        LogHelper::logInfo("Rejection email sent to applicant ID: {$applicant->id}, Email: $email");
                    } else {
                        // Log if no email available
                        LogHelper::logWarning("Cannot send rejection email to applicant ID: {$applicant->id} - No email address available");
                    }

                } catch (\Exception $e) {
                    LogHelper::logError($e);
                    // Log specific error about email sending failure
                    LogHelper::logError("Failed to send rejection email to applicant ID: {$applicant->id} - " . $e->getMessage());
                }
            } elseif ($applicant->status == 'approved') {
                try {
                    // Send approval email if applicant has email
                    if (!empty($email)) {
                        Mail::send(new ApprovalNotificationMail($applicant));

                        // Log the email sending
                        LogHelper::logInfo("Approval email sent to applicant ID: {$applicant->id}, Email: $email");
                    } else {
                        // Log if no email available
                        LogHelper::logWarning("Cannot send approval email to applicant ID: {$applicant->id} - No email address available");
                    }
                } catch (\Exception $e) {
                    LogHelper::logError($e);
                    // Log specific error about email sending failure
                    LogHelper::logError("Failed to send approval email to applicant ID: {$applicant->id} - " . $e->getMessage());
                }
            }
        }
    }

    /**
     * Handle the Applicant "deleted" event.
     */
    public function deleted(Applicant $applicant): void
    {
        //
    }

    /**
     * Handle the Applicant "restored" event.
     */
    public function restored(Applicant $applicant): void
    {
        //
    }

    /**
     * Handle the Applicant "force deleted" event.
     */
    public function forceDeleted(Applicant $applicant): void
    {
        //
    }
}
