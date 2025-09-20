<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\Applicant;
use App\Models\JobApp;
use App\Traits\Reports\HR;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    use HR;

    public function index()
    {
        return view('home');
    }

    public function home()
    {
        if (auth()->check()) {
            $user = auth()->user();
            if ($user->role == 'hr'){
                return redirect()->route('dashboard.hr.home');
            }elseif ($user->role == 'applicant') {
                return redirect()->route('dashboard.applicant.home');
            }else{
                return "Who the fuck are you!!!";
            }
        }else{
            return redirect('/');
        }
    }

    public function dashboard_hr(Request $request)
    {
        $filter = $request->get('filter', 'all_time');

        // Get date range based on filter
        $dateRange = $this->getDateRange($filter);

        // Build queries with date filters if needed
        $jobsQuery = JobApp::query();
        $applicantsQuery = Applicant::query();

        if ($dateRange) {
            $jobsQuery->whereBetween('created_at', $dateRange);
            $applicantsQuery->whereBetween('created_at', $dateRange);
        }

//        $jobsQuery->where('user_id', auth()->id() ?? 0);
        $applicantsQuery->whereHas('job_app', function ($query) use ($jobsQuery) {
//            $query->where('user_id', auth()->id() ?? 0);
        });

        // Get analytics data
        $analytics = [
            'jobs_count' => $jobsQuery->count(),
            'total_applicants' => $applicantsQuery->count(),
            'approved_applicants' => (clone $applicantsQuery)->where('status', 'approved')->count(),
            'rejected_applicants' => (clone $applicantsQuery)->where('status', 'rejected')->count(),
        ];

        // Calculate previous period for comparison
        $previousAnalytics = $this->getPreviousAnalytics($filter);

        // Calculate percentage changes
        $trends = [
            'jobs_trend' => $this->calculateTrend($analytics['jobs_count'], $previousAnalytics['jobs_count']),
            'applicants_trend' => $this->calculateTrend($analytics['total_applicants'], $previousAnalytics['total_applicants']),
            'approved_trend' => $this->calculateTrend($analytics['approved_applicants'], $previousAnalytics['approved_applicants']),
            'rejected_trend' => $this->calculateTrend($analytics['rejected_applicants'], $previousAnalytics['rejected_applicants']),
        ];

        return view('dashboard.hr.home', compact('analytics', 'trends', 'filter'));
    }

    public function dashboard_applicant(Request $request)
    {
        $filter = $request->get('filter', 'all_time');

        // Get date range based on filter
        $dateRange = $this->getDateRange($filter);

        // Build applicant query - filter by current user's applications only
        $applicantsQuery = Applicant::query()->where('user_id', auth()->id() ?? 0);

        if ($dateRange) {
            $applicantsQuery->whereBetween('created_at', $dateRange);
        }

        // Get analytics data for applicant
        $analytics = [
            'my_applications' => $applicantsQuery->count(),
            'pending_applications' => (clone $applicantsQuery)->where('status', 'pending')->count(),
            'approved_applications' => (clone $applicantsQuery)->where('status', 'approved')->count(),
            'rejected_applications' => (clone $applicantsQuery)->where('status', 'rejected')->count(),
            'waiting_applications' => (clone $applicantsQuery)->where('status', 'waiting for answering')->count(),
        ];

        // Calculate previous period for comparison
        $previousAnalytics = $this->getPreviousApplicantAnalytics($filter);

        // Calculate percentage changes
        $trends = [
            'applications_trend' => $this->calculateTrend($analytics['my_applications'], $previousAnalytics['my_applications']),
            'pending_trend' => $this->calculateTrend($analytics['pending_applications'], $previousAnalytics['pending_applications']),
            'approved_trend' => $this->calculateTrend($analytics['approved_applications'], $previousAnalytics['approved_applications']),
            'rejected_trend' => $this->calculateTrend($analytics['rejected_applications'], $previousAnalytics['rejected_applications']),
            'waiting_trend' => $this->calculateTrend($analytics['waiting_applications'], $previousAnalytics['waiting_applications']),
        ];

        return view('dashboard.applicant.home', compact('analytics', 'trends', 'filter'));
    }

    private function getPreviousApplicantAnalytics($filter)
    {
        // Check if the method exists in the HR trait, if not return default values
        if (!method_exists($this, 'getPreviousDateRange')) {
            return [
                'my_applications' => 0,
                'pending_applications' => 0,
                'approved_applications' => 0,
                'rejected_applications' => 0,
                'waiting_applications' => 0,
            ];
        }

        $previousDateRange = $this->getPreviousDateRange($filter);

        if (!$previousDateRange) {
            return [
                'my_applications' => 0,
                'pending_applications' => 0,
                'approved_applications' => 0,
                'rejected_applications' => 0,
                'waiting_applications' => 0,
            ];
        }

        $applicantsQuery = Applicant::query()
            ->where('user_id', auth()->id() ?? 0)
            ->whereBetween('created_at', $previousDateRange);

        return [
            'my_applications' => $applicantsQuery->count(),
            'pending_applications' => (clone $applicantsQuery)->where('status', 'pending')->count(),
            'approved_applications' => (clone $applicantsQuery)->where('status', 'approved')->count(),
            'rejected_applications' => (clone $applicantsQuery)->where('status', 'rejected')->count(),
            'waiting_applications' => (clone $applicantsQuery)->where('status', 'waiting for answering')->count(),
        ];
    }
}
