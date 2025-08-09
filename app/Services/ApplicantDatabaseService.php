<?php

namespace App\Services;

use App\Models\File;
use App\Models\JobApp;
use App\Models\Applicant;
use Illuminate\Support\Facades\Storage;

class ApplicantDatabaseService
{
    /**
     * Store files and create applicant records based on directory UUID
     */
    public function storeApplicantData($directoryUuid, $jobId = null, $additionalInfo = [])
    {
        $directoryPath = 'uploads/' . $directoryUuid;

        if (!Storage::disk('public')->exists($directoryPath)) {
            throw new \Exception('Directory not found: ' . $directoryUuid);
        }

        // Get all files in the directory
        $files = Storage::disk('public')->files($directoryPath);
        $createdApplicants = [];

        foreach ($files as $filePath) {
            // Store file record
            $file = File::create([
                'name' => basename($filePath),
                'path' => $filePath,
                'fullpath' => Storage::disk('public')->path($filePath),
                'type' => Storage::disk('public')->mimeType($filePath),
                'size' => Storage::disk('public')->size($filePath)
            ]);

            // Create applicant record
            $applicant = Applicant::create([
                'job_id' => $jobId,
                'file_id' => $file->id,
                'information' => array_merge([
                    'directory_uuid' => $directoryUuid,
                    'uploaded_at' => now()->toISOString(),
                    'file_original_name' => basename($filePath)
                ], $additionalInfo),
                'processing' => false,
                'answering' => false,
                'status' => 'pending'
            ]);

            $createdApplicants[] = $applicant;
        }

        return $createdApplicants;
    }

    /**
     * Get applicants by directory UUID
     */
    public function getApplicantsByDirectory($directoryUuid)
    {
        return Applicant::whereJsonContains('information->directory_uuid', $directoryUuid)
            ->with('file')
            ->get();
    }

    /**
     * Update applicant status
     */
    public function updateApplicantStatus($applicantId, $status)
    {
        if (!in_array($status, Applicant::STATUSES)) {
            throw new \Exception('Invalid status: ' . $status);
        }

        return Applicant::find($applicantId)->update(['status' => $status]);
    }

    /**
     * Bulk process applicants from directory
     */
    public function bulkProcessApplicants($directoryUuid, $processing = true)
    {
        return Applicant::whereJsonContains('information->directory_uuid', $directoryUuid)
            ->update(['processing' => $processing]);
    }

    /**
     * Get applicant statistics by directory
     */
    public function getDirectoryStats($directoryUuid)
    {
        $applicants = $this->getApplicantsByDirectory($directoryUuid);

        return [
            'total' => $applicants->count(),
            'pending' => $applicants->where('status', 'pending')->count(),
            'rejected' => $applicants->where('status', 'rejected')->count(),
            'waiting_for_answering' => $applicants->where('status', 'waiting for answering')->count(),
            'approved' => $applicants->where('status', 'approved')->count(),
            'processing' => $applicants->where('processing', true)->count(),
        ];
    }
}
