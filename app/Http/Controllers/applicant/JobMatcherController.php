<?php

namespace App\Http\Controllers\applicant;

use App\Models\File;
use App\Models\Job;
use App\Services\TextExtractionHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;

class JobMatcherController extends Controller
{
    /**
     * Show the job matcher page
     */
    public function index()
    {
        return view('dashboard.applicant.smart.job_matcher');
    }

    /**
     * Match CV with jobs - Main endpoint
     */
    public function match(Request $request)
    {
        $request->validate([
            'cv_source' => 'required|in:upload,existing',
            'cv_file' => 'required_if:cv_source,upload|file|mimes:pdf,doc,docx|max:5120',
            'job_preferences' => 'nullable|string|max:5000'
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

            // Step 2: Extract text content from CV
            $rawText = $this->extractTextFromFile($fileId);

            // Step 3: Get all active jobs
            $jobs = Job::where('status', 'active')
                ->with(['company', 'category'])
                ->get();

            // Step 4: Match CV with each job and calculate ATS scores
            $jobMatches = $this->matchJobsWithCV(
                $jobs,
                $rawText,
                $request->job_preferences
            );

            // Step 5: Sort by ATS score (highest first)
            $jobMatches = collect($jobMatches)->sortByDesc('ats_score')->values()->all();

            return response()->json([
                'success' => true,
                'data' => [
                    'jobs' => $jobMatches,
                    'total_matches' => count($jobMatches),
                    'cv_summary' => $this->generateCVSummary($rawText)
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Job matching failed: ' . $e->getMessage()
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
     * Extract text from CV file
     */
    private function extractTextFromFile($fileId)
    {
        $file = File::findOrFail($fileId);

        // TODO: Use TextExtractionHelper for actual extraction
        // Uncomment when ready:
        // $extractedData = TextExtractionHelper::extractFromFile(
        //     $file->path,
        //     $file->type
        // );

        // For now, return dummy structured data
        return [
            'full_text' => 'Sample CV content with 5+ years experience in full-stack development...',
            'sections' => [
                'experience' => 'Senior Developer at TechCorp (2020-present)...',
                'skills' => 'PHP, Laravel, Vue.js, MySQL, Docker, AWS, Git...',
                'education' => 'Bachelor of Computer Science...',
                'summary' => 'Full-stack developer with expertise in Laravel and Vue.js...'
            ],
            'extracted_skills' => ['PHP', 'Laravel', 'Vue.js', 'MySQL', 'Docker', 'AWS'],
            'years_experience' => 5
        ];
    }

    /**
     * Match CV with all jobs and calculate ATS scores
     */
    /**
     * Match CV with all jobs and calculate ATS scores
     */
    private function matchJobsWithCV($jobs, $cvData, $preferences = null)
    {
        $matches = [];

        foreach ($jobs as $job) {
            // Calculate ATS score for this job
            $atsScore = $this->calculateATSScore($job, $cvData, $preferences);

            // Only include jobs with score > 40 (reasonable matches)
            if ($atsScore >= 40) {
                $matches[] = [
                    'id' => $job->id,
                    'title' => $job->title,
                    'company' => $job->company->name ?? 'Company Name',
                    'location' => $job->location,
                    'type' => $job->type,
                    'salary' => $job->salary ?? 'Competitive',
                    'ats_score' => $atsScore,
                    'feedback' => $this->generateJobFeedback($job, $cvData, $atsScore)
                ];
            }
        }

        return $matches;
    }

    /**
     * Calculate ATS score for a specific job
     */
    private function calculateATSScore($job, $cvData, $preferences = null)
    {
        // TODO: Replace with actual AI API call for precise scoring
        // For now, return dummy scores based on simple keyword matching

        $score = 0;

        // Base score from skills matching
        $jobDescription = strtolower($job->description ?? '');
        $cvText = strtolower($cvData['full_text'] ?? '');

        // Simple keyword matching (will be replaced with AI)
        $keywords = ['laravel', 'php', 'vue', 'javascript', 'mysql', 'docker', 'aws'];
        foreach ($keywords as $keyword) {
            if (str_contains($jobDescription, $keyword) && str_contains($cvText, $keyword)) {
                $score += 10;
            }
        }

        // Add randomness for demo purposes
        $score += rand(20, 40);

        return min($score, 100); // Cap at 100
    }

    /**
     * Generate detailed feedback for job match
     */
    private function generateJobFeedback($job, $cvData, $atsScore)
    {
        // TODO: Replace with actual AI-generated feedback

        $feedback = [
            'overview' => $this->generateOverview($atsScore),
            'strengths' => $this->generateStrengths($cvData, $job),
            'improvements' => $this->generateImprovements($cvData, $job),
            'skills' => $this->extractMatchingSkills($cvData, $job),
            'courses' => $this->suggestCourses($cvData, $job, $atsScore)
        ];

        return $feedback;
    }

    /**
     * Generate overview text based on score
     */
    private function generateOverview($score)
    {
        if ($score >= 80) {
            return '<strong>Excellent match!</strong> Your profile aligns very well with this role. <em>You have the key skills and experience they\'re looking for.</em>';
        } elseif ($score >= 60) {
            return '<strong>Good match!</strong> Your background fits well, but there are some areas to highlight. <em>Focus on your relevant experience when applying.</em>';
        } else {
            return '<strong>Decent match.</strong> You meet some requirements but may need additional skills. <em>Consider this as a growth opportunity.</em>';
        }
    }

    /**
     * Generate strengths section
     */
    private function generateStrengths($cvData, $job)
    {
        return '<h4>Key Strengths:</h4>
            <ul>
                <li><strong>Technical Skills:</strong> Your tech stack aligns with their requirements</li>
                <li><strong>Experience Level:</strong> Your years of experience match their needs</li>
                <li><strong>Industry Knowledge:</strong> Relevant background in similar projects</li>
            </ul>';
    }

    /**
     * Generate improvements section
     */
    private function generateImprovements($cvData, $job)
    {
        return '<h4>Areas to Highlight:</h4>
            <ul>
                <li>Emphasize your most relevant projects in your application</li>
                <li>Add specific metrics about your achievements</li>
                <li>Tailor your CV to include exact keywords from job description</li>
            </ul>';
    }

    /**
     * Extract matching skills between CV and job
     */
    private function extractMatchingSkills($cvData, $job)
    {
        // TODO: Use AI to extract and match skills accurately

        $dummySkills = ['PHP', 'Laravel', 'Vue.js', 'MySQL', 'Docker', 'AWS', 'Git', 'REST APIs'];

        return array_map(function ($skill) {
            return ['name' => $skill, 'type' => 'skill'];
        }, array_slice($dummySkills, 0, rand(4, 6)));
    }

    /**
     * Suggest relevant courses based on gaps
     */
    private function suggestCourses($cvData, $job, $atsScore)
    {
        // If score is high, suggest fewer courses
        if ($atsScore >= 80) {
            return [
                [
                    'name' => 'Advanced ' . ($job->category->name ?? 'Development'),
                    'type' => 'course',
                    'description' => 'Take your skills to the next level'
                ]
            ];
        }

        // For lower scores, suggest more courses
        return [
            [
                'name' => 'Master the Tech Stack',
                'type' => 'course',
                'description' => 'Deep dive into required technologies'
            ],
            [
                'name' => 'Industry Best Practices',
                'type' => 'course',
                'description' => 'Learn professional development standards'
            ],
            [
                'name' => 'Project Management Fundamentals',
                'type' => 'course',
                'description' => 'Improve your collaboration skills'
            ]
        ];
    }

    /**
     * Generate CV summary
     */
    private function generateCVSummary($cvData)
    {
        return [
            'total_skills' => count($cvData['extracted_skills'] ?? []),
            'years_experience' => $cvData['years_experience'] ?? 0,
            'sections_found' => count($cvData['sections'] ?? [])
        ];
    }

    /**
     * Download feedback for specific job match
     */
    public function downloadFeedback(Request $request, $jobId)
    {
        // TODO: Generate PDF feedback report for the specific job

        return response()->json([
            'success' => true,
            'message' => 'Feedback download will be implemented'
        ]);
    }

    /**
     * Download full matching report
     */
    public function downloadFullReport(Request $request)
    {
        // TODO: Generate comprehensive PDF report with all job matches

        return response()->json([
            'success' => true,
            'message' => 'Full report download will be implemented'
        ]);
    }
}
