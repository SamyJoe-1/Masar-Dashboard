<?php

namespace App\Http\Controllers\applicant;

use App\Models\ResumeATS;
use App\Models\File;
use App\Services\TextExtractionHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;

class CVAnalyzerController extends Controller
{
    /**
     * Show the CV analyzer page
     */
    public function index()
    {
        return view('dashboard.applicant.smart.cv_analyzer');
    }

    /**
     * Show analysis results by slug
     */
    public function show($slug)
    {
        $analysis = ResumeATS::where('slug', $slug)->firstOrFail();

        // Check if user has permission to view this analysis
        if ($analysis->user_id !== auth()->id() && !$analysis->is_public) {
            abort(403, 'Unauthorized access to this analysis');
        }

        return view('cv_analyzer_results', compact('analysis'));
    }

    /**
     * Analyze CV - Main endpoint
     */
    public function analyze(Request $request)
    {
        $request->validate([
            'cv_source' => 'required|in:upload,existing',
            'cv_file' => 'required_if:cv_source,upload|file|mimes:pdf,doc,docx|max:5120',
            'job_description' => 'nullable|string|max:10000'
        ]);

        try {
            $fileId = null;
            $rawText = null;

            // Step 1: Handle file upload or existing CV
            if ($request->cv_source === 'upload') {
                $fileId = $this->handleFileUpload($request->file('cv_file'));
            } else {
                $fileId = auth()->user()->profile->cv_id ?? null;

                if (!$fileId) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No CV found in your profile'
                    ], 404);
                }
            }

            // Step 2: Extract text content
            $rawText = $this->extractTextFromFile($fileId);

            // Step 3: Call AI APIs for analysis (placeholder for now)
            $analysisResults = $this->performAIAnalysis($rawText, $request->job_description);

            // Step 4: Save results to database
            $resumeATS = $this->saveAnalysisResults(
                $fileId,
                $rawText,
                $analysisResults
            );

            return response()->json([
                'success' => true,
                'data' => [
                    'slug' => $resumeATS->slug,
                    'url' => route('cv.analyzer.show', $resumeATS->slug),
                    'ats_score' => $resumeATS->ats_score,
                    'content_score' => $resumeATS->content_score,
                    'skills_score' => $resumeATS->skills_score,
                    'formatting_score' => $resumeATS->formatting_score,
                    'feedback' => $resumeATS->feedback,
                    'suggested_roles' => $analysisResults['suggested_roles'] ?? []
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Analysis failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle file upload
     */
    private function handleFileUpload($uploadedFile)
    {
        $path = $uploadedFile->store('cvs', 'public');

        $file = File::create([
            'name' => $uploadedFile->getClientOriginalName(),
            'path' => $path,
            'type' => $uploadedFile->getMimeType(),
            'size' => $uploadedFile->getSize(),
            'user_id' => auth()->id()
        ]);

        return $file->id;
    }

    /**
     * Extract text from file
     */
    private function extractTextFromFile($fileId)
    {
        $file = File::findOrFail($fileId);

//         Use TextExtractionHelper for actual extraction
//         When ready to use real extraction, uncomment:
//         $extractedData = TextExtractionHelper::extractFromFile(
//             $file->path,
//             $file->type
//         );

        // For now, return dummy data
        return [
            'full_text' => 'Sample extracted text from CV...',
            'sections' => [
                'experience' => 'Work experience content...',
                'education' => 'Education content...',
                'skills' => 'Skills content...',
                'summary' => 'Professional summary...'
            ],
            'contact' => [
                'email' => 'sample@email.com',
                'phone' => '+1234567890',
                'linkedin' => null
            ]
        ];
    }

    /**
     * Perform AI analysis
     * This will call multiple AI APIs for different analyses
     */
    private function performAIAnalysis($rawText, $jobDescription = null)
    {
        // TODO: Replace with actual AI API calls
        // Call different APIs for:
        // 1. ATS Score calculation
        // 2. Content quality analysis
        // 3. Formatting check
        // 4. Skills matching
        // 5. Keyword extraction
        // 6. Grammar checking

        // For now, return dummy data
        return [
            'ats_score' => rand(60, 85),
            'content_score' => rand(55, 90),
            'formatting_score' => rand(70, 95),
            'skills_score' => rand(65, 88),
            'feedback' => $this->generateDummyFeedback(),
            'suggested_roles' => [
                'Full Stack Developer',
                'Laravel Developer',
                'Backend Engineer',
                'Software Developer'
            ]
        ];
    }

    /**
     * Generate dummy feedback structure
     */
    private function generateDummyFeedback()
    {
        return [
            'content' => [
                'title' => 'Content Quality Analysis',
                'icon' => 'fas fa-align-left',
                'type' => 'points',
                'items' => [
                    [
                        'title' => 'Quantifiable Achievements',
                        'description' => 'Add specific metrics and numbers to demonstrate impact.',
                        'passed' => false
                    ],
                    [
                        'title' => 'Action Verbs Usage',
                        'description' => 'Good use of strong action verbs.',
                        'passed' => true
                    ]
                ]
            ],
            'formatting' => [
                'title' => 'Formatting & ATS Compatibility',
                'icon' => 'fas fa-paint-brush',
                'type' => 'points',
                'items' => [
                    [
                        'title' => 'File Format',
                        'description' => 'PDF format is correct and compatible.',
                        'passed' => true
                    ],
                    [
                        'title' => 'Layout Structure',
                        'description' => 'Consider single-column layout for better ATS parsing.',
                        'passed' => false
                    ]
                ]
            ],
            'skills' => [
                'title' => 'Skills Analysis',
                'icon' => 'fas fa-code',
                'type' => 'badges',
                'items' => [
                    ['name' => 'PHP', 'relevant' => true],
                    ['name' => 'Laravel', 'relevant' => true],
                    ['name' => 'Vue.js', 'relevant' => true],
                    ['name' => 'MS Office', 'relevant' => false]
                ]
            ],
            'overall' => [
                'title' => 'Overall Assessment',
                'icon' => 'fas fa-clipboard-check',
                'type' => 'paragraph',
                'content' => 'Your CV shows solid experience. Focus on adding quantifiable achievements to boost your score.'
            ]
        ];
    }

    /**
     * Save analysis results to database
     */
    private function saveAnalysisResults($fileId, $rawText, $analysisResults)
    {
        $slug = Str::slug(auth()->user()->name . '-cv-analysis-' . now()->timestamp);

        return ResumeATS::create([
            'user_id' => auth()->id(),
            'file_id' => $fileId,
            'slug' => $slug,
            'raw_text' => $rawText,
            'ats_score' => $analysisResults['ats_score'],
            'content_score' => $analysisResults['content_score'],
            'skills_score' => $analysisResults['skills_score'],
            'formatting_score' => $analysisResults['formatting_score'],
            'feedback' => $analysisResults['feedback'],
            'is_public' => false
        ]);
    }

    /**
     * Make analysis public/shareable
     */
    public function togglePublic($slug)
    {
        $analysis = ResumeATS::where('slug', $slug)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $analysis->update([
            'is_public' => !$analysis->is_public
        ]);

        return response()->json([
            'success' => true,
            'is_public' => $analysis->is_public,
            'share_url' => $analysis->is_public ? route('cv.analyzer.show', $slug) : null
        ]);
    }

    /**
     * Download analyzed CV
     */
    public function download($slug)
    {
        $analysis = ResumeATS::where('slug', $slug)->firstOrFail();

        if ($analysis->user_id !== auth()->id() && !$analysis->is_public) {
            abort(403);
        }

        $file = $analysis->file;
        $filePath = Storage::disk('public')->path($file->path);

        if (!file_exists($filePath)) {
            abort(404, 'File not found');
        }

        return response()->download($filePath, $file->name);
    }

    /**
     * Delete analysis
     */
    public function destroy($slug)
    {
        $analysis = ResumeATS::where('slug', $slug)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $analysis->delete();

        return response()->json([
            'success' => true,
            'message' => 'Analysis deleted successfully'
        ]);
    }

    /**
     * Get user's analysis history
     */
    public function history()
    {
        $analyses = ResumeATS::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $analyses
        ]);
    }
}
