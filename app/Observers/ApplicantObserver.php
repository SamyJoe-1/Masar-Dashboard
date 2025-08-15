<?php

namespace App\Observers;

use App\Log\LogHelper;
use App\Models\Applicant;
use Illuminate\Console\Command;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Http;

class ApplicantObserver implements ShouldQueue
{
    use InteractsWithQueue;

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
        if ($applicant->isDirty('status') && $applicant->status == 'waiting for answering' && !$applicant->answering) {
            try {
                $applicants = Applicant::where('processing', true)->where('status', 'pending')->get();

                if ($applicants->count()) {
                    $urls = [];
                    $jobDescription = $applicants[0]->job_app->description ?? '-';
                    foreach ($applicants as $applicant) {
                        $urls[] = asset($applicant->file->fullpath);
                        $http = Http::timeout(6000)->acceptJson();
                    }

                    $response = $http->post(config('app.evaluate_url') . '/match-cvs-from-urls', [
                        'job_description' => $jobDescription,
                        'urls' => $urls,
                        'output_format' => 'json',
                        "debug" => 'true'
                    ]);

                    if (!empty($response->json()['results'])){
                        foreach ($applicants as $key => $applicant) {
                            $result = $response->json()['results'][$key] ?? [];
                            $status = @$result['Score'] >= 50;
                            $applicant->update([
                                'information' => $result,
                                'status' => $status ? 'waiting for answering' : 'rejected',
                                'processing' => false,
                            ]);
                        }
                    }
                }
            } catch (\Exception $e) {
                LogHelper::logError($e);
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
