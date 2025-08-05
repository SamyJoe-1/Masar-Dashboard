<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileUploadController extends Controller
{
    public function showUploadForm()
    {
        return view('test.upload'); // Create upload.blade.php view
    }

    public function uploadFiles(Request $request)
    {
        // Validate the request
        $request->validate([
            'files' => 'required|array',
            'files.*' => 'file|max:10240', // Max 10MB per file
        ]);

        $uploadedFiles = [];
        $uploadPath = 'uploads/' . date('Y/m/d'); // Organize by date

        try {
            foreach ($request->file('files') as $file) {
                // Generate unique filename
                $originalName = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $filename = Str::slug(pathinfo($originalName, PATHINFO_FILENAME)) . '_' . time() . '_' . Str::random(6) . '.' . $extension;

                // Store the file
                $filePath = $file->storeAs($uploadPath, $filename, 'public');

                // Collect file information
                $uploadedFiles[] = [
                    'original_name' => $originalName,
                    'stored_name' => $filename,
                    'path' => $filePath,
                    'size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                    'url' => Storage::url($filePath)
                ];
            }

            // Save to database if needed
            // $this->saveFileRecords($uploadedFiles);

            // Return success response
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => count($uploadedFiles) . ' files uploaded successfully!',
                    'files' => $uploadedFiles
                ]);
            }

            return redirect()->back()->with([
                'success' => 'Files uploaded successfully!',
                'uploaded_files' => $uploadedFiles
            ]);

        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Upload failed: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->withErrors(['error' => 'Upload failed: ' . $e->getMessage()]);
        }
    }

    public function showUploadedFiles(Request $request)
    {
        // Get uploaded files from session or database
        $uploadedFiles = session('uploaded_files', []);

        return view('uploaded-files', compact('uploadedFiles'));
    }

    // Optional: Save file records to database
    private function saveFileRecords($files)
    {
        // Example if you have a File model
        /*
        foreach ($files as $file) {
            \App\Models\File::create([
                'original_name' => $file['original_name'],
                'stored_name' => $file['stored_name'],
                'path' => $file['path'],
                'size' => $file['size'],
                'mime_type' => $file['mime_type'],
                'uploaded_by' => auth()->id() ?? null,
            ]);
        }
        */
    }

    public function deleteFile(Request $request, $filename)
    {
        try {
            // Find and delete the file
            $filePath = 'uploads/' . date('Y/m/d') . '/' . $filename;

            if (Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);

                if ($request->ajax()) {
                    return response()->json(['success' => true, 'message' => 'File deleted successfully']);
                }

                return redirect()->back()->with('success', 'File deleted successfully');
            }

            throw new \Exception('File not found');

        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
            }

            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
