<?php

namespace App\Observers;

use App\Log\LogHelper;
use App\Mail\SuggestedRoleNotificationMail;
use App\Models\Applicant;
use App\Models\JobApp;
use App\Models\Profile;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class JobAppObserver implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the JobApp "created" event.
     */
    public function created(JobApp $jobApp): void
    {
        if (strlen($jobApp->title) >= 3){
            $applicants = Applicant::select(DB::raw("JSON_UNQUOTE(JSON_EXTRACT(information, '$.Email')) as email_from_info"), 'email', 'information', 'id')->where(   'information->Suggested Roles', 'LIKE', "%$jobApp->title%")->distinct()->get();
            $applicants = $applicants->unique('email_from_info');
            $profiles = [];
            $key = 0;
            foreach ($applicants as $applicant) {
                @$profiles[$key]['email'] = $applicant->information['Email'] ?? @$applicant->email;
                @$profiles[$key]['id'] = $applicant->id;
                @$profiles[$key]['name'] = $applicant->information['Name'] ?? '-';
                @$profiles[$key]['phone'] = $applicant->information['Phone'] ?? '-';
                $key++;
            }

            $profileUsers = Profile::with('user')->whereNotNull('suggested_roles')->get();
            foreach ($profileUsers as $profileUser) {
                $suggestedRoles = $profileUser->suggested_roles ?? [];

                $matched = false;
                foreach ($suggestedRoles as $role) {
                    if ($this->fuzzyMatch($role, $jobApp->title)) {
                        $matched = true;
                        break;
                    }
                }

                if ($matched && $profileUser->user) {
                    @$profiles[$key]['email'] = @$profileUser->user->email;
                    @$profiles[$key]['id'] = @$profileUser->user->id;
                    @$profiles[$key]['name'] = $profileUser->user->name ?? '-';
                    @$profiles[$key]['phone'] = '-';
                    $key++;
                }
            }

            $profiles = array_values(array_column(array_column($profiles, null, 'email'), null));
            foreach ($profiles as $profile) {
                if (!empty($profile['email'])) {
                    $email = $profile['email'];
                    $id = $profile['id'];
                    $name = $profile['name'];
                    $phone = $profile['phone'];
                    try {
                        // Send rejection email if applicant has email
                        if (!empty($email)) {
                            Mail::send(new SuggestedRoleNotificationMail($jobApp, $email, $name, $phone, $id));

                            Log::channel('info_log')->info("Suggested role sent to applicant ID: $id, Email: $email");
                        } else {
                            Log::channel('info_log')->info("Cannot send suggested role email to applicant ID: $id - No email address available");
                        }

                    } catch (\Exception $e) {
                        Log::channel('info_log')->info("Failed to send suggested role email to applicant ID: $id - " . $e->getMessage());
                    }
                }
            }
        }
    }

    /**
     * Fuzzy match function for role matching
     */
    private function fuzzyMatch(string $role, string $searchTerm, float $threshold = 0.7): bool
    {
        $role = strtolower(trim($role));
        $searchTerm = strtolower(trim($searchTerm));

        // Exact match
        if ($role === $searchTerm) {
            return true;
        }

        // Contains match (either direction)
        if (str_contains($role, $searchTerm) || str_contains($searchTerm, $role)) {
            return true;
        }

        // Levenshtein distance for typo tolerance
        $maxLength = max(strlen($role), strlen($searchTerm));
        if ($maxLength > 0) {
            $distance = levenshtein($role, $searchTerm);
            $similarity = 1 - ($distance / $maxLength);

            if ($similarity >= $threshold) {
                return true;
            }
        }

        // SOUNDEX for phonetic matching
        if (soundex($role) === soundex($searchTerm)) {
            return true;
        }

        return false;
    }

    /**
     * Handle the JobApp "updated" event.
     */
    public function updated(JobApp $jobApp): void
    {
        //
    }

    /**
     * Handle the JobApp "deleted" event.
     */
    public function deleted(JobApp $jobApp): void
    {
        //
    }

    /**
     * Handle the JobApp "restored" event.
     */
    public function restored(JobApp $jobApp): void
    {
        //
    }

    /**
     * Handle the JobApp "force deleted" event.
     */
    public function forceDeleted(JobApp $jobApp): void
    {
        //
    }
}
