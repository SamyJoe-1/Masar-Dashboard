<?php

namespace App\Services;

use App\Models\JobApp;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MatchingCVsService
{
    public $jobDescription = null;
    public $applicants = [];

    public function __construct($jobDescription)
    {
        @$this->jobDescription = $jobDescription;
    }

    public function getJobTitle()
    {
        try {
            $http = Http::timeout(40)->acceptJson();
            $response = $http->post(config('app.match_cv_url') . '/ai-job-title', [
                'job_description' => $this->jobDescription,
                "debug" => 'true'
            ]);
            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Files sent successfully',
                    'data' => @$response->json()
                ]);
            } else {
                Log::error('Exception occurred during API (getJobTitle)', [
                    'message' => 'API request failed',
                    'trace' => $response->body()
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'API request failed',
                    'error' => $response->body(),
                    'status' => $response->status()
                ], 400);
            }

        } catch (\Exception $e) {
            Log::error('Exception occurred during API (getJobTitle)', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getData($response)
    {
        return json_decode($response->getContent(), true);
    }
}
