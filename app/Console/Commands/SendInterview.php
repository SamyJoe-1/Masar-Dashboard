<?php

namespace App\Console\Commands;

use App\Models\Applicant;
use App\Models\ApplicantForm;
use App\Services\MatchingCVsService;
use Illuminate\Console\Command;

class SendInterview extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send-interview';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $applicants = Applicant::where('processing', false)->where('answering', false)->where('status', 'interview requested')->get();

        if ($applicants->count()) {
            $jobDescription = $applicants[0]->job_app->description ?? '-';

            foreach ($applicants as $applicant) {
                $this->info($applicants->count() . ' Applicant(s) caught');

                // Use the service to make the API request
                $matchingService = new MatchingCVsService($jobDescription);
                $response = $matchingService->sendInterview(@$applicant->file->fullpath);

                $responseData = $matchingService->getData($response);

                if ($responseData['success'] && !empty($responseData['data']['structured_data'])) {
                    $this->info('Response received. Sending Applicants Questions...');
                    $applicant->update([
                        'status' => 'waiting for answering'
                    ]);
                    ApplicantForm::create([
                        'applicant_id' => $applicant->id,
                        'questions' => @$responseData['data']['structured_data'],
                        'status' => "waiting",
                        'slug' => getRandUUID(),
                    ]);
                    $this->info("Process applicant ($applicant->id) completed successfully");
                } else {
                    $this->error('API request failed: ' . ($responseData['error'] ?? 'Unknown error'));
                    return Command::FAILURE;
                }
            }
            $this->info("Process of applicants completed successfully");
            return Command::SUCCESS;
        } else {
            $this->info('0 Applicants found');
            return Command::SUCCESS;
        }
    }
}
