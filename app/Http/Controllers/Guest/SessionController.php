<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\ApplicantForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SessionController extends Controller
{
    public function storeScreenshot(Request $request)
    {
        try {
            // Log the request for debugging
            Log::info('Screenshot upload attempt', [
                'has_file' => $request->hasFile('screenshot'),
                'timestamp' => $request->timestamp
            ]);

            // Simple validation - allow smaller files now
            $request->validate([
                'screenshot' => 'required|image|max:2048', // Max 2MB instead of 10MB
            ]);

            $file = $request->file('screenshot');

            // Check if file is valid
            if (!$file->isValid()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid file upload'
                ], 422);
            }

            // Create directory structure
            $date = now()->format('Y-m-d');
            $directory = "screenshots/{$date}";

            // Generate filename
            $filename = 'screenshot_' . now()->format('His') . '_' . uniqid() . '.jpg';

            // Store the file
            $path = $file->storeAs($directory, $filename, 'public');

            Log::info('Screenshot stored successfully', ['path' => $path]);

            return response()->json([
                'success' => true,
                'message' => 'Screenshot stored successfully',
                'data' => [
                    'filename' => $filename,
                    'path' => $path,
                    'url' => Storage::url($path)
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Screenshot upload failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to store screenshot: ' . $e->getMessage()
            ], 500);
        }
    }

    public function start($id)
    {
        $interview = ApplicantForm::where('id', $id)->where('status', 'waiting')->first();

        if (!$interview) {
            return response()->json([
                'status' => 404,
                'message' => 'Interview not found',
            ]);
        }

        if ($interview->isExpired()){
            return response()->json([
                'status' => 403,
                'message' => 'Interview expired',
            ]);
        }
        if ($interview->isActive()){
            return response()->json([
                'status' => 401,
                'message' => 'Interview is already active',
                'data' => $interview,
            ]);
        }
        $interview->update([
            'started_at' => now(),
        ]);
        return response()->json([
            'status' => 200,
            'message' => 'Interview Ready to Start',
            'data' => $interview,
        ]);
    }

    public function close($id)
    {
        $interview = ApplicantForm::where('id', $id)->first();

        if (!$interview) {
            return response()->json([
                'status' => 404,
                'message' => 'Interview not found',
            ]);
        }
        $interview->update([
            'status' => 'not answered',
        ]);
        return response()->json([
            'status' => 200,
            'message' => 'Interview Has Closed',
            'data' => $interview,
        ]);
    }

    public function check($id)
    {
        $interview = ApplicantForm::where('id', $id)->where('status', 'waiting')->first();
        if (!$interview) {
            return response()->json([
                'status' => 404,
                'message' => 'Interview not found',
            ]);
        }
        if ($interview->isExpired()){
            return response()->json([
                'status' => 403,
                'message' => 'Interview expired',
            ]);
        }
        if ($interview->isActive()){
            return response()->json([
                'status' => 401,
                'message' => 'Interview is already active',
                'data' => $interview,
            ]);
        }
        if ($interview->isReady()){
            return response()->json([
                'status' => 200,
                'message' => 'Interview Ready to Start',
                'data' => $interview,
            ]);
        }
        return response()->json([
            'status' => 501,
            'message' => 'Not found the event, please try again',
        ]);
    }

    public function finish($id, Request $request)
    {
        $interview = ApplicantForm::where('id', $id)->where('status', 'waiting')->first();
        if (!$interview) {
            return response()->json([
                'status' => 404,
                'message' => 'Interview not found',
            ]);
        }
        if (!$interview->isActive()){
            return response()->json([
                'status' => 401,
                'message' => 'Failed to finish the interview',
                'data' => $interview,
            ]);
        }
        $answers = $request->all();
        $interview->update([
            'answers' => $answers,
            'status' => 'answered',
        ]);
        return response()->json([
            'status' => 200,
            'message' => 'Interview is Submitted successfully',
        ]);
    }
}
