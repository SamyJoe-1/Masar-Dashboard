<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\CV;
use App\Models\File;
use App\Models\Profile;
use App\Models\Template;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CVController extends Controller
{
    /**
     * Get all templates with their files
     */
    public function getTemplates()
    {
        $templates = Template::with('file')->get()->map(function($template) {
            return [
                'id' => $template->id,
                'name' => $template->name,
                'image' => asset($template->file->fullpath ?? null),
                'data' => $template->data,
                'created_at' => $template->created_at,
            ];
        });

        return response()->json($templates);
    }

    /**
     * Get single template
     */
    public function getTemplate($id)
    {
        $template = Template::with('file')->findOrFail($id);

        return response()->json([
            'id' => $template->id,
            'name' => $template->name,
            'file' => $template->file ? [
                'id' => $template->file->id,
                'fullpath' => $template->file->fullpath,
                'path' => $template->file->path,
                'name' => $template->file->name
            ] : null,
            'data' => $template->data,
        ]);
    }

    /**
     * Get user's draft CVs
     */
    public function getDrafts(Request $request)
    {
        $userId = auth()->id();

        $drafts = CV::where('user_id', $userId)
            ->where('ready', false)
            ->with('template.file')
            ->orderBy('updated_at', 'desc')
            ->get();

        return response()->json($drafts);
    }

    /**
     * Get specific draft by template
     */
    public function getDraftByTemplate(Request $request, $templateId)
    {
        $userId = auth()->id();

        $draft = CV::where('user_id', $userId)
            ->where('template_id', $templateId)
            ->where('ready', false)
            ->with('template.file')
            ->first();

        return response()->json($draft);
    }

    /**
     * Save or update draft
     */
    public function saveDraft(Request $request)
    {
        $validated = $request->validate([
            'template_id' => 'required|exists:templates,id',
            'slug' => 'nullable|string',
            'personal_details' => 'nullable|array',
            'employment_history' => 'nullable|array',
            'education' => 'nullable|array',
            'skills' => 'nullable|array',
            'summary' => 'nullable|string',
            'additional_sections' => 'nullable|array',
            'customize' => 'nullable|array',
        ]);

        $userId = auth()->id();

        // Find existing draft or create new
        $cv = CV::where('user_id', $userId)
            ->where('template_id', $validated['template_id'])
            ->where('ready', false)
            ->first();

        if (!$cv) {
            $cv = new CV();
            $cv->user_id = $userId;
            $cv->template_id = $validated['template_id'];
            $cv->slug = $validated['slug'] ?? Str::uuid();
            $cv->ready = false;
        }

        // Update fields
        $cv->personal_details = $validated['personal_details'] ?? [];
        $cv->employment_history = $validated['employment_history'] ?? [];
        $cv->education = $validated['education'] ?? [];
        $cv->skills = $validated['skills'] ?? [];
        $cv->summary = $validated['summary'] ?? '';
        $cv->additional_sections = $validated['additional_sections'] ?? [];
        $cv->customize = $validated['customize'] ?? [];

        $cv->save();

        return response()->json([
            'success' => true,
            'message' => 'Draft saved successfully',
            'cv' => $cv->load('template.file')
        ]);
    }

    /**
     * Finalize and save CV
     */
    public function finalize(Request $request)
    {
        $validated = $request->validate([
            'template_id' => 'required|exists:templates,id',
            'slug' => 'nullable|string',
            'personal_details' => 'required|array',
            'personal_details.first_name' => 'required|string',
            'personal_details.last_name' => 'required|string',
            'employment_history' => 'nullable|array',
            'education' => 'nullable|array',
            'skills' => 'nullable|array',
            'summary' => 'nullable|string',
            'additional_sections' => 'nullable|array',
            'customize' => 'nullable|array',
        ]);

        $userId = auth()->id();

        // Check if updating existing draft
        $cv = CV::where('user_id', $userId)
            ->where('template_id', $validated['template_id'])
            ->where('ready', false)
            ->first();

        if (!$cv) {
            $cv = new CV();
            $cv->user_id = $userId;
            $cv->template_id = $validated['template_id'];
        }

        // Generate unique slug if not provided
        if (empty($validated['slug'])) {
            $baseSlug = Str::slug($validated['personal_details']['first_name'] . '-' . $validated['personal_details']['last_name']);
            $slug = $baseSlug;
            $counter = 1;

            while (CV::where('slug', $slug)->where('id', '!=', $cv->id ?? 0)->exists()) {
                $slug = $baseSlug . '-' . $counter;
                $counter++;
            }

            $cv->slug = $slug;
        } else {
            $cv->slug = $validated['slug'];
        }

        // Update all fields
        $cv->personal_details = $validated['personal_details'];
        $cv->employment_history = $validated['employment_history'] ?? [];
        $cv->education = $validated['education'] ?? [];
        $cv->skills = $validated['skills'] ?? [];
        $cv->summary = $validated['summary'] ?? '';
        $cv->additional_sections = $validated['additional_sections'] ?? [];
        $cv->customize = $validated['customize'] ?? [];
        $cv->ready = true;

        $cv->save();

        return response()->json([
            'success' => true,
            'message' => 'CV finalized successfully',
            'cv' => $cv->load('template.file'),
            'view_url' => route('home', $cv->slug)
        ]);
    }

    /**
     * Delete draft
     */
    public function deleteDraft(Request $request, $id)
    {
        $userId = auth()->id();

        $cv = CV::where('id', $id)
            ->where('user_id', $userId)
            ->where('ready', false)
            ->firstOrFail();

        $cv->delete();

        return response()->json([
            'success' => true,
            'message' => 'Draft deleted successfully'
        ]);
    }

    /**
     * Get user's completed CVs
     */
    public function getCompletedCVs(Request $request)
    {
        $userId = auth()->id();

        $cvs = CV::where('user_id', $userId)
            ->where('ready', true)
            ->with('template.file')
            ->orderBy('updated_at', 'desc')
            ->get();

        return response()->json($cvs);
    }

    public function storePDF(Request $request)
    {
        try {
            $user = auth()->user();

            // Validate the request
            $validated = $request->validate([
                'cv_id' => 'required|exists:cvs,id',
                'pdf_base64' => 'required|string',
                'filename' => 'required|string'
            ]);

            // Find the CV
            $cv = CV::where('id', $validated['cv_id'])
                ->where('user_id', $user->id)
                ->firstOrFail();

            // Decode base64 PDF
            $pdfData = base64_decode(preg_replace('#^data:application/pdf;base64,#i', '', $validated['pdf_base64']));

            // Create filename
            $filename = $validated['filename'];
            $path = 'cvs/' . $user->id . '/' . time() . '_' . $filename;

            // Store the PDF
            Storage::disk('public')->put($path, $pdfData);

            $fullpath = 'storage/' . $path;

            // Create File record
            $file = File::create([
                'name' => $filename,
                'path' => $path,
                'fullpath' => $fullpath,
                'type' => 'application/pdf',
                'size' => strlen($pdfData)
            ]);

            // Update CV with file_id
            $cv->update(['file_id' => $file->id]);

            return response()->json([
                'success' => true,
                'message' => 'PDF stored successfully',
                'cv' => $cv->fresh(),
                'file' => $file
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to store PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateProfile(Request $request)
    {
        try {
            $user = auth()->user();

            $validated = $request->validate([
                'cv_id' => 'required|exists:cvs,id'
            ]);

            // Find the CV
            $cv = CV::where('id', $validated['cv_id'])
                ->where('user_id', $user->id)
                ->firstOrFail();

            if (!$cv->file_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'CV PDF not found'
                ], 400);
            }

            // Update user profile with the CV file_id
            $profile = Profile::firstOrCreate(['user_id' => $user->id]);
            $profile->update(['cv' => $cv->file_id]);

            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully',
                'redirect' => '/profile'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update profile: ' . $e->getMessage()
            ], 500);
        }
    }
}
