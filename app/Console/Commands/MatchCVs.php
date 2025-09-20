<?php

namespace App\Console\Commands;

use App\Log\LogHelper;
use App\Models\Applicant;
use App\Models\JobApp;
use App\Services\MatchingCVsService;
use Illuminate\Console\Command;

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
                ->with('job_app', 'file')
                ->get()->take(60);

            if ($applicants->count()) {
                // Group applicants by job_app_id
                $groupedApplicants = $applicants->groupBy('job_id');

                $this->info($applicants->count() . ' Total Applicant(s) found in ' . $groupedApplicants->count() . ' job(s)');

                foreach ($groupedApplicants as $jobAppId => $jobApplicants) {
                    $jobDescription = $jobApplicants->first()->job_app->description ?? '-';
                    $target = $jobApplicants->first()->job_app->target ?? 2;
                    $urls = [];

                    foreach ($jobApplicants as $applicant) {
                        $nationality = $applicant->omani ?? 0;
                        if (!in_array($target, [2, $nationality])){
                            // Handle case where no matching result found
                            $applicant->update([
                                'information' => null,
                                'status' => 'rejected',
                                'processing' => false,
                            ]);
                            $this->warn("Applicant ({$applicant->id}) skipped: nationality not eligible.");
                            continue;
                        }
                        $urls[] = asset($applicant->file->fullpath);
                    }

                    $this->info("Processing {$jobApplicants->count()} applicant(s) for Job ID: {$jobAppId}");

                    // Use the service to make the API request for this job
                    $matchingService = new MatchingCVsService($jobDescription);
                    $response = $matchingService->matchCVs($urls);

                    $responseData = $matchingService->getData($response);

                    if ($responseData['success'] && !empty($responseData['data']['results'])) {
                        $this->info('Response received. Updating applicants profiles...');

                        // Create a lookup array for results by filename
                        $resultsByFilename = [];
                        foreach ($responseData['data']['results'] as $result) {
                            $filename = $result['File'] ?? null;
                            if ($filename) {
                                $resultsByFilename[$filename] = $result;
                            }
                        }

                        foreach ($jobApplicants as $applicant) {
                            $fileName = $applicant->file->name;

                            // Find the matching result by filename
                            $result = $resultsByFilename[$fileName] ?? [];

                            if (!empty($result)) {
                                $status = @$result['Score'] >= 50;
                                $applicant->update([
                                    'information' => $result,
                                    'status' => $status ? 'interview requested' : 'rejected',
                                    'processing' => false,
                                ]);
                                $this->info("Applicant ({$applicant->id}) updated successfully with score: " . ($result['Score'] ?? 'N/A'));
                            } else {
                                // Handle case where no matching result found
                                $applicant->update([
                                    'information' => null,
                                    'status' => 'rejected',
                                    'processing' => false,
                                ]);
                                $this->warn("No matching result found for applicant ({$applicant->id}) with file: {$fileName}");
                            }
                        }
                    } else {
                        // Mark all applicants in this job as failed
//                        foreach ($jobApplicants as $applicant) {
//                            $applicant->update([
//                                'information' => null,
//                                'status' => 'rejected',
//                                'processing' => false,
//                            ]);
//                        }
                        $this->error('API request failed for Job ID ' . $jobAppId . ': ' . ($responseData['error'] ?? 'Unknown error'));
                    }
                }

                $this->info('Process completed successfully');
                return Command::SUCCESS;
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
