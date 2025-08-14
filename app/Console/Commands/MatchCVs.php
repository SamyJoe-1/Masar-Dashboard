<?php

namespace App\Console\Commands;

use App\Log\LogHelper;
use App\Models\Applicant;
use App\Models\JobApp;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class MatchCVs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'match-cvs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'match-cvs';

    /**
     * Execute the console command.
     */

    public function handle()
    {
        try {
            $applicants = Applicant::where('processing', true)
                ->where('status', 'pending')
                ->get();

            if ($applicants->count()) {
                $urls = [];
                $jobDescription = $applicants[0]->job_app->description ?? '-';
                foreach ($applicants as $applicant) {
                    $urls[] = asset($applicant->file->fullpath);
                    $http = Http::timeout(6000)->acceptJson();
                }

                $this->info($applicants->count() . ' Applicant(s) caught');

                $response = $http->post(config('app.match_cv_url') . '/match-cvs-from-urls', [
                    'job_description' => $jobDescription,
                    'urls' => $urls,
                    'output_format' => 'json',
                    "debug" => 'true'
                ]);

                if (!empty($response->json()['results'])){
                    $this->info('Response received. Updating applicants profiles...');
                    foreach ($applicants as $key => $applicant) {
                        $result = $response->json()['results'][$key] ?? [];
                        $status = @$result['status'] == Applicant::APPROVAL_KEYS[1];
                        $applicant->update([
                            'information' => $result,
                            'status' => $status ? 'waiting for answering' : 'rejected',
                            'processing' => false,
                        ]);
                        $this->info("Applicant ({$applicant->id}) updated successfully");
                    }
                    $this->info('Process completed successfully');
                    return Command::SUCCESS;
                } else {
                    $this->error('API request failed: ' . $response->body());
                    return Command::FAILURE;
                }
            } else {
                $this->info('0 Applicants found');
                return Command::SUCCESS;
            }
        } catch (\Exception $e) {
            $this->error('Error occurred: ' . $e->getMessage());
            LogHelper::logError($e);
            return Command::FAILURE;
        }
    }
}
