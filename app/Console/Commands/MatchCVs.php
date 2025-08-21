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
            $applicants = Applicant::where('processing', true)->where('status', 'pending')->get();

            if ($applicants->count()) {
                $urls = [];
                $jobDescription = $applicants[0]->job_app->description ?? '-';

                foreach ($applicants as $applicant) {
                    $urls[] = asset($applicant->file->fullpath);
                }

                $this->info($applicants->count() . ' Applicant(s) caught');

                // Use the service to make the API request
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

                    foreach ($applicants as $applicant) {
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
                    $this->info('Process completed successfully');
                    return Command::SUCCESS;
                } else {
                    $this->error('API request failed: ' . ($responseData['error'] ?? 'Unknown error'));
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
