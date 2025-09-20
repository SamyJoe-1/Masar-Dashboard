<?php

namespace App\Console\Commands;

use App\Models\Applicant;
use App\Models\ApplicantForm;
use App\Services\MatchingCVsService;
use Illuminate\Console\Command;

class EvaluateAnswers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'evaluate-answers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Evaluate Applicants Answers';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $applicants = Applicant::where('status', 'waiting for answering')->get();

        if ($applicants->count()) {
            $hasForm = $applicants->filter(function ($applicant) {
                return !empty($applicant->form);
            });
            $doesntHaveForm = $applicants->filter(function ($applicant) {
                return empty($applicant->form);
            });
            $notAnswered = $hasForm->filter(function ($applicant) {
                return $applicant->form->status == 'not answered';
            });
            $answered = $hasForm->filter(function ($applicant) {
                return $applicant->form->status == 'answered';
            });
            $passed = $answered;
            $rejected = $notAnswered->merge($doesntHaveForm);

            $this->info($rejected->count() . ' Rejected Applicant(s) caught');
            foreach ($rejected as $applicant) {
                $applicant->update([
                    'status' => 'rejected'
                ]);
                $this->info("Applicant ($applicant->id) rejected");
            }

            $this->info($passed->count() . ' Applicant(s) caught');
            foreach ($passed as $applicant) {
                $jobDescription = $applicant->job_app->description ?? '-';
                $lang = $applicant->job_app->lang ?? '-';
                $answers = json_encode($applicant->form->answers ?? "-");

                // Debug the data being sent
//                $this->info("Debug - Applicant ID: {$applicant->id}");
//                $this->info("Debug - Job Description: " . (is_string($jobDescription) ? substr($jobDescription, 0, 100) . '...' : json_encode($jobDescription)));
//                $this->info("Debug - Answers: " . (is_array($answers) ? json_encode($answers) : $answers));

                // Validate answers before sending
                if (empty($answers) || $answers === "-") {
                    $this->warn("Applicant {$applicant->id} has no valid answers, rejecting...");
                    $applicant->update(['status' => 'rejected']);
                    continue;
                }

                // Use the service to make the API request
                $matchingService = new MatchingCVsService($jobDescription);
                $response = $matchingService->evaluateAnswers($answers, $lang);

                $responseData = $matchingService->getData($response);

//                $this->info(json_encode($responseData));

                if ($responseData['success'] && !empty($responseData['data'])) {
                    $this->info('Response received. Evaluating applicant answers...');

                    $data = $responseData['data'];

                    // Prepare feedback (excluding overall_score and fit_for_role)
                    $feedback = [
                        'strengths' => $data['strengths'] ?? '',
                        'weaknesses' => $data['weaknesses'] ?? '',
                        'overall_fit_justification' => $data['overall_fit_justification'] ?? ''
                    ];

                    // Determine status based on fit_for_role
                    $fitForRole = $data['fit_for_role'] ?? false;
                    $newStatus = $fitForRole ? 'approved' : 'rejected';

                    // Update applicant
                    $applicant->update([
                        'feedback' => $feedback,
                        'status' => $newStatus
                    ]);

                    $this->info("Applicant ($applicant->id) " . ($fitForRole ? 'approved' : 'rejected') . " - Process completed successfully");
                } else {
                    // API request failed - reject the applicant
                    $applicant->update([
                        'status' => 'rejected'
                    ]);
                    $this->error('API request failed for applicant ' . $applicant->id . ': ' . ($responseData['error'] ?? 'Unknown error'));
                    $this->info("Applicant ($applicant->id) rejected due to API failure");
                }
            }

            $this->info("Evaluation Process of applicants completed successfully");
            return Command::SUCCESS;
        } else {
            $this->info('0 Applicants found');
            return Command::SUCCESS;
        }
    }
}
