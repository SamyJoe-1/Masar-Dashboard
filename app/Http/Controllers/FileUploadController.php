<?php

namespace App\Http\Controllers;

use App\Models\Applicant;
use App\Models\File;
use App\Models\JobApp;
use App\Models\Oraganization;
use App\Services\MatchingCVsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileUploadController extends Controller
{
    public function showUploadForm()
    {
        $orgs = Oraganization::select('name', 'id')->get()->pluck('name', 'id');
        return view('cv_handling.upload', compact('orgs'));
    }

    public function uploadFiles(Request $request)
    {
        // Validate the request
        $request->validate([
            'files' => 'required|array',
            'files.*' => 'file|max:102400', // Max 10MB per file
        ]);

        $uploadedFiles = [];
        $tempUploadPath = 'temp_uploads/' . session()->getId(); // Temporary location

        try {
            // Create temp directory if it doesn't exist
            if (!Storage::disk('public')->exists($tempUploadPath)) {
                Storage::disk('public')->makeDirectory($tempUploadPath);
            }

            foreach ($request->file('files') as $file) {
                // Generate unique filename
                $originalName = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $filename = Str::slug(pathinfo($originalName, PATHINFO_FILENAME)) . '_' . time() . '_' . Str::random(6) . '.' . $extension;

                // Store the file in temp location
                $filePath = $file->storeAs($tempUploadPath, $filename, 'public');

                // Collect file information
                $uploadedFiles[] = [
                    'original_name' => $originalName,
                    'stored_name' => $filename,
                    'temp_path' => $filePath,
                    'size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                ];
            }

            // Store uploaded files info in session for the next step
            session(['temp_uploaded_files' => $uploadedFiles]);

            // IMPORTANT: Force session save to ensure data persists
            session()->save();

            \Log::info('Files uploaded to temp. Session ID: ' . session()->getId());
            \Log::info('Temp uploaded files stored in session:', $uploadedFiles);

            // Return success response
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => count($uploadedFiles) . ' files uploaded successfully to temporary location!',
                    'files' => $uploadedFiles,
                    'session_id' => session()->getId(), // For debugging
                    'temp_path' => $tempUploadPath
                ]);
            }

            return redirect()->back()->with([
                'success' => 'Files uploaded successfully!',
                'uploaded_files' => $uploadedFiles
            ]);

        } catch (\Exception $e) {
            \Log::error('Upload error: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Upload failed: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->withErrors(['error' => 'Upload failed: ' . $e->getMessage()]);
        }
    }

    public function generateDirectory(Request $request)
    {
        try {
            // Debug session info
            \Log::info('Generate Directory - Session ID: ' . session()->getId());
            \Log::info('Generate Directory - All session data:', session()->all());

            // Get uploaded files from session
            $tempUploadedFiles = session('temp_uploaded_files', []);

            if (empty($tempUploadedFiles)) {
                // Try to get files from temp directory directly as fallback
                $tempPath = 'temp_uploads/' . session()->getId();
                \Log::info('No session data found. Checking temp directory: ' . $tempPath);

                if (Storage::disk('public')->exists($tempPath)) {
                    $tempFiles = Storage::disk('public')->files($tempPath);
                    \Log::info('Found temp files:', $tempFiles);

                    if (!empty($tempFiles)) {
                        // Reconstruct file info from temp directory
                        $tempUploadedFiles = [];
                        foreach ($tempFiles as $file) {
                            $filename = basename($file);
                            $tempUploadedFiles[] = [
                                'original_name' => $filename, // We'll use stored name as original for now
                                'stored_name' => $filename,
                                'temp_path' => $file,
                                'size' => Storage::disk('public')->size($file),
                                'mime_type' => Storage::disk('public')->mimeType($file),
                            ];
                        }
                        \Log::info('Reconstructed file info from temp directory:', $tempUploadedFiles);
                    }
                }

                if (empty($tempUploadedFiles)) {
                    throw new \Exception('No files found to process. Session might have expired or files were not uploaded properly.');
                }
            }

            // Generate UUID-like directory name
            $directoryUuid = $this->generateUUID();
            $finalPath = 'uploads/' . $directoryUuid;

            // Create the final directory
            if (!Storage::disk('public')->makeDirectory($finalPath)) {
                throw new \Exception('Failed to create final directory: ' . $finalPath);
            }

            \Log::info('Created final directory: ' . $finalPath);

            // Move files from temp to final location
            $finalFiles = [];
            $tempPath = 'temp_uploads/' . session()->getId();

            \Log::info('Moving files from temp path: ' . $tempPath);
            \Log::info('Moving files to final path: ' . $finalPath);
            \Log::info('Files to move:', $tempUploadedFiles);

            foreach ($tempUploadedFiles as $fileInfo) {
                $tempFilePath = $fileInfo['temp_path'];
                $finalFilePath = $finalPath . '/' . $fileInfo['stored_name'];

                \Log::info('Moving from: ' . $tempFilePath . ' to: ' . $finalFilePath);

                // Check if temp file exists
                if (!Storage::disk('public')->exists($tempFilePath)) {
                    \Log::error('Temp file does not exist: ' . $tempFilePath);
                    // List all files in temp directory for debugging
                    $allTempFiles = Storage::disk('public')->allFiles($tempPath);
                    \Log::error('All files in temp directory:', $allTempFiles);
                    throw new \Exception('Temp file not found: ' . $tempFilePath);
                }

                // Move file to final location
                if (!Storage::disk('public')->move($tempFilePath, $finalFilePath)) {
                    throw new \Exception('Failed to move file: ' . $tempFilePath . ' to ' . $finalFilePath);
                }

                $finalFiles[] = [
                    'original_name' => $fileInfo['original_name'],
                    'stored_name' => $fileInfo['stored_name'],
                    'final_path' => $finalFilePath,
                    'size' => $fileInfo['size'],
                    'mime_type' => $fileInfo['mime_type'],
                    'url' => Storage::url($finalFilePath)
                ];

                \Log::info('Successfully moved file: ' . $finalFilePath);
            }

            // Clean up temp directory
            if (Storage::disk('public')->exists($tempPath)) {
                Storage::disk('public')->deleteDirectory($tempPath);
                \Log::info('Cleaned up temp directory: ' . $tempPath);
            }

            // Save directory info to database if needed
            $this->saveDirectoryRecord($directoryUuid, $finalFiles);

            // Clear temp files session
            session()->forget('temp_uploaded_files');

            \Log::info('Directory generation completed successfully: ' . $directoryUuid);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Directory generated successfully!',
                    'directory_uuid' => $directoryUuid,
                    'directory_path' => $finalPath,
                    'directory_url' => Storage::url($finalPath),
                    'files' => $finalFiles,
                    'redirect_url' => '/result/preview/' . $directoryUuid
                ]);
            }

            return redirect('/result/preview/' . $directoryUuid)->with('success', 'Directory generated successfully!');

        } catch (\Exception $e) {
            \Log::error('Generate directory error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                    'debug' => [
                        'session_id' => session()->getId(),
                        'session_data' => session()->all()
                    ]
                ], 500);
            }

            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function viewDirectory($directoryUuid)
    {
        $directoryPath = 'uploads/' . $directoryUuid;

        if (!Storage::disk('public')->exists($directoryPath)) {
            abort(404, 'Directory not found');
        }

        // Get all files in the directory
        $files = Storage::disk('public')->files($directoryPath);
        $fileDetails = [];

        foreach ($files as $file) {
            $fileDetails[] = [
                'name' => basename($file),
                'type' => Storage::disk('public')->mimeType($file),
                'original_name' => basename($file),
                'size' => Storage::disk('public')->size($file), // Keep as bytes, don't format
                'url' => Storage::url($file),
                'modified' => date('Y-m-d H:i:s', Storage::disk('public')->lastModified($file))
            ];
        }

        return view('directory-view', [
            'directory_uuid' => $directoryUuid,
            'directory_path' => $directoryPath,
            'files' => $fileDetails
        ]);
    }

    private function generateUUID()
    {
        // Generate a UUID-like string: 8-4-4-4-12 format
        return sprintf(
            '%08x-%04x-%04x-%04x-%012x',
            mt_rand(0, 0xffffffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffffffffffff)
        );
    }

    private function saveDirectoryRecord($directoryUuid, $files)
    {
        // Optional: Save directory record to database
        /*
        \App\Models\UploadDirectory::create([
            'uuid' => $directoryUuid,
            'path' => 'uploads/' . $directoryUuid,
            'file_count' => count($files),
            'total_size' => collect($files)->sum('size'),
            'created_by' => auth()->id() ?? null,
        ]);

        // Save individual file records
        foreach ($files as $file) {
            \App\Models\File::create([
                'directory_uuid' => $directoryUuid,
                'original_name' => $file['original_name'],
                'stored_name' => $file['stored_name'],
                'path' => $file['final_path'],
                'size' => $file['size'],
                'mime_type' => $file['mime_type'],
            ]);
        }
        */
    }

    public function showDone($directoryUuid, Request $request)
    {
        $type = @$request->job_type == "without_cv";
        $target = !isset($request->oman) ? 2:(!in_array($request->oman, [0, 1, 2]) ? 2:$request->oman);
        $visibility = $request->visibility ?? false;
        try {
            if (!$type) {
                $directoryPath = 'uploads/' . $directoryUuid;

                // Check if directory exists
                if (!Storage::disk('public')->exists($directoryPath)) {
                    return redirect('/')->withErrors(['error' => 'Directory not found. Please upload files first.']);
                }

                $directoryUrl = Storage::url($directoryPath);

                // Get all files in the directory
                $files = Storage::disk('public')->files($directoryPath);
                $fileDetails = [];

                foreach ($files as $file) {
                    $fileDetails[] = [
                        'name' => basename($file),
                        'type' => Storage::disk('public')->mimeType($file),
                        'original_name' => basename($file), // You might want to store this differently
                        'size' => $this->formatFileSize(Storage::disk('public')->size($file)),
                        'url' => Storage::url($file),
                        'modified' => date('Y-m-d H:i:s', Storage::disk('public')->lastModified($file))
                    ];
                }
            }


            $obj = new MatchingCVsService($request->description ?? "-");
            $fetchJobTitle = $obj->getJobTitle()->getData(true); // This actually exists on JsonResponse
            if ($fetchJobTitle['success']){
                @$title = $fetchJobTitle['data']['job_title'];
                if (stristr($title, 'cannot extract') || stristr($title, 'sorry')){
                    $title = '';
                }
            }else{
                $title = '';
            }

            if (!$type) {
                $files = [];
                foreach ($fileDetails as $fileDetail) {
                    $files[] = File::create([
                        'name' => $fileDetail['name'],
                        'path' => $directoryPath,
                        'fullpath' => $fileDetail['url'],
                        'type' => $fileDetail['type'],
                        'size' => (float) $fileDetail['size'],
                    ]);
                }
            }

            $application = JobApp::create([
                'user_id' => auth()->id(),
                'organization_id' => @$request->org,
                'lang' => $request->lang ?? "ar",
                'target' => $target,
                'public' => $visibility,
                'title' => $title,
                'description' => $request->description,
                'slug' => $directoryUuid,
            ]);
            if (!$type) {
                foreach ($files as $file) {
                    Applicant::create([
                        'job_id' => $application->id,
                        'file_id' => $file->id,
                        'omani' => true,
                    ]);
                }
            }

            return response()->json([
                'application' => $application->id,
                'title' => $title,
                'description' => $request->description
            ]);
        }catch (\Exception $e) {
            abort(500, $e->getMessage());;
        }
    }

    private function formatFileSize($bytes)
    {
        if ($bytes === 0) return '0 Bytes';
        $k = 1024;
        $sizes = ['Bytes', 'KB', 'MB', 'GB'];
        $i = floor(log($bytes) / log($k));
        return round(($bytes / pow($k, $i)), 2) . ' ' . $sizes[$i];
    }

    public function deleteDirectory($directoryUuid)
    {
        try {
            $directoryPath = 'uploads/' . $directoryUuid;

            if (Storage::disk('public')->exists($directoryPath)) {
                Storage::disk('public')->deleteDirectory($directoryPath);
                return response()->json(['success' => true, 'message' => 'Directory deleted successfully']);
            }

            throw new \Exception('Directory not found');

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
        }
    }

    public function checkLimits()
    {
        return [
            'max_file_uploads' => ini_get('max_file_uploads'),
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'post_max_size' => ini_get('post_max_size'),
            'max_input_vars' => ini_get('max_input_vars'),
            'memory_limit' => ini_get('memory_limit'),
            'max_execution_time' => ini_get('max_execution_time')
        ];
    }
}
