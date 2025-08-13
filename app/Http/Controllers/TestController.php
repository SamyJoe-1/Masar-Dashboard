<?php

namespace App\Http\Controllers;

use App\Log\LogHelper;
use App\Models\Applicant;
use App\Models\JobApp;
use App\Services\MatchingCVsService;
use Illuminate\Http\Request;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class TestController extends Controller
{
    public function test_1()
    {
        return view('test.1');
    }

    public function testApiCall()
    {
        // Test receiving endpoint (add this as another route)
    }

    public function receiveTestData(Request $request)
    {
        return response()->json([
            'received_job_description' => $request->input('job_description'),
            'received_files_count' => count($request->allFiles()),
            'files_info' => collect($request->allFiles())->map(function($file, $key) {
                return [
                    'key' => $key,
                    'name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'mime' => $file->getMimeType()
                ];
            })
        ]);
    }

    public function test_2(Request $request)
    {
        try {
            $jobDescription = "I need a full-stack";
            $http = Http::timeout(1200)->acceptJson();
            $response = $http->post(config('app.match_cv_url') . '/match-cvs-from-urls', [
                'job_description' => $jobDescription,
                'urls' => [
                    "https://dashboard.massar.biz/storage/uploads/6f0fd62b-9d57-795c-7d29-47cbeae22346/mariam-ahmed_1754489229_L10uiE.pdf"
                ],
                'output_format' => 'json',
                "debug" => 'true'
            ]);
            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Files sent successfully',
                    'data' => $response->json()['results']
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'API request failed',
                    'error' => $response->body(),
                    'status' => $response->status()
                ], 400);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function test_3()
    {
        $jobs = JobApp::with('user')->get();
        $job = $jobs->first();
        return $job->user;
    }

    public function test_4()
    {
        $obj = new MatchingCVsService('fullstack');
        $fetchJobTitle = $obj->getJobTitle()->getData(true); // This actually exists on JsonResponse
        if ($fetchJobTitle['success']){
            $description = $fetchJobTitle['data']['job_title'];
        }else{
            $description = '';
        }
        return [
            $description,

        ];
    }

    public function test_5()
    {
        try {
            @$id = $_GET['id'];
            $applicant = Applicant::find($id);
            $jobDescription = $applicant->job_app->description ?? '-';
            $urls = [
                asset($applicant->file->fullpath)
            ];
            $http = Http::timeout(1200)->acceptJson();
            $response = $http->post(config('app.match_cv_url') . '/match-cvs-from-urls', [
                'job_description' => $jobDescription,
                'urls' => $urls,
                'output_format' => 'json',
                "debug" => 'true'
            ]);
            if ($response->successful() && !count($response->json()['errors'])) {
                $result = $response->json()['results'][0] ?? [];
                $status = @$result['status'] == Applicant::APPROVAL_KEYS[1];
                $applicant->update([
                    'information' => $result,
                    'status' => $status ? 'waiting for answering':'rejected',
                ]);
                return response()->json([
                    'success' => true,
                    'message' => 'Files sent successfully',
                    'data' => @$response->json()['results']
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'API request failed',
                    'error' => $response->body(),
                    'status' => $response->status()
                ], 400);
            }

        } catch (\Exception $e) {
            LogHelper::logError($e);
            return response()->json([
                'success' => false,
                'message' => 'Error occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
