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

    public function matchCVs($urls)
    {
        try {
            $http = Http::timeout(120)->acceptJson();
            $response = $http->post(config('app.match_cv_url') . '/match-cvs-from-urls', [
                'job_description' => $this->jobDescription,
                'urls' => $urls,
                'output_format' => 'json',
                "debug" => 'true'
            ]);

            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'message' => 'CVs matched successfully',
                    'data' => $response->json()
                ]);
            } else {
                Log::error('Exception occurred during API (matchCVsFromUrls)', [
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
            Log::error('Exception occurred during API (matchCVsFromUrls)', [
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

    public function sendInterview($url, $lang)
    {
        try {
            $lang = $lang == 'en' ? "English":"Arabic";
            $response = Http::asForm()
                ->timeout(120)
                ->post(rtrim(config('app.evaluate_url'), '/') . '/extract-questions-from-cv-url', [
                    'pdf_url' => url($url),
                    'job_description' => $this->jobDescription,
                    'language' => $lang,
                    'output_format' => 'json',
                    'debug' => true,
                ]);



            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Interview Sent successfully',
                    'data' => $response->json()
                ]);
            } else {
                Log::error('Exception occurred during API (sendInterview)', [
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
            Log::error('Exception occurred during API (sendInterview)', [
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

    public function evaluateAnswers($answers, $lang)
    {
        try {
            $lang = $lang == 'en' ? "English":"Arabic";
            $response = Http::asForm()
                ->timeout(120)
                ->post(config('app.evaluate_url') . '/evaluate-user-answer', [
                    'job_description' => $this->jobDescription, // Remove the @
                    'questions_with_answers' => $answers, // Remove the @
                    'language' => $lang,
                ]);

            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Answer Evaluated successfully',
                    'data' => $response->json()
                ]);
            } else {
                Log::error('Exception occurred during API (evaluateAnswers)', [
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
            Log::error('Exception occurred during API (evaluateAnswers)', [
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
