<?php

namespace App\Http\Controllers;

use App\Log\LogHelper;
use App\Models\Applicant;
use App\Models\JobApp;
use App\Models\Profile;
use App\Services\MatchingCVsService;
use Illuminate\Http\Request;
use Illuminate\Http\Client\Response;
use Illuminate\Process\Exceptions\ProcessFailedException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Util\PHP\Job;
use Symfony\Component\Process\Process;

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

    public function test_6(){
        $applicant = Applicant::find(23);
        $applicant->status = 'rejected';
        $applicant->save();
        return 'Done';
    }

    public function test_7()
    {
        $title = "Full Stack Developer";
        $applicants = Applicant::select(DB::raw("JSON_UNQUOTE(JSON_EXTRACT(information, '$.Email')) as email_from_info"), 'email', 'information', 'id')->where(   'information->Suggested Roles', 'LIKE', "%$title%")->distinct()->get();
        $applicants = $applicants->unique('email_from_info');

//        $applicants = Applicant::select(DB::raw("JSON_UNQUOTE(JSON_EXTRACT(information, '$.Email')) as email_from_info"), 'email')->where(   'information->Suggested Roles', 'LIKE', "%$title%")->get();

        return $applicants;
    }

    public function test_8()
    {
        $profiles = [];
        $jobApp = JobApp::find(87);
        $profileUsers = Profile::with('user')->whereNotNull('suggested_roles')->get();
        $key = 0;
        foreach ($profileUsers as $profileUser) {
            $suggestedRoles = json_decode($profileUser->suggested_roles ?? []);

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
        return $profiles;
    }

    public function test_9()
    {
        return view('test.2');
    }

    public function test_9_2(Request $request)
    {
        $command = $request->input('command');

        if (empty($command)) {
            return response()->json([
                'output' => '',
                'error' => false
            ]);
        }

        try {
            $process = Process::fromShellCommandline($command);
            $process->setWorkingDirectory(base_path());

            // Set environment variables to fix permission issues
            $process->setEnv([
                'HOME' => '/tmp',  // Use /tmp as home directory for commands
                'PATH' => getenv('PATH') ?: '/usr/local/bin:/usr/bin:/bin',
                'PM2_HOME' => '/tmp/.pm2'  // Specifically for PM2
            ]);

            $process->setTimeout(300);
            $process->run();

            $output = $process->getOutput();
            $errorOutput = $process->getErrorOutput();

            return response()->json([
                'output' => $output ?: ($errorOutput ?: 'Command executed'),
                'error' => !$process->isSuccessful()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'output' => $e->getMessage(),
                'error' => true
            ]);
        }
    }
    private $allowedCommands = [
        'ls', 'pwd', 'whoami', 'date', 'php', 'composer', 'npm', 'git',
        'cat', 'tail', 'head', 'grep', 'find', 'which', 'echo'
    ];

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
}
