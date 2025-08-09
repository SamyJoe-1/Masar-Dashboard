<?php

namespace App\Http\Controllers;

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
            // Job description
            $jobDescription = "I need a full-stack";

            $disk = Storage::disk('public');
            $files = $disk->files('uploads/c4e0458d-645b-57e9-ccdb-6c1f3274cf6c');

// Start the HTTP request
            $http = Http::timeout(60)->acceptJson();

// Attach each file (this is exactly how Postman does it)
            foreach ($files as $file) {
                $fileName = basename($file);
                $fileContent = $disk->get($file);

                // This creates multipart/form-data just like Postman
                $http->attach("files[]", $fileContent, $fileName);
            }

// Send the request with form data
            $response = $http->post('http://127.0.0.1:8000/process-resumes', [
                'job_description' => $jobDescription,
//                'files' => array_values($files)
            ]);

//            $response = Http::timeout(60)
//                ->asForm()  // This sets proper form content type
//                ->post('http://127.0.0.1:8000/process-resumes', $data);



            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Files sent successfully',
                    'data' => $response->json()
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
}
