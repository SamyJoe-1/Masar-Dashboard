<?php

namespace App\Http\Controllers;

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
            return redirect()->route('dashboard.hr.home');
        }else{
            return redirect('/');
        }
    }

    public function dashboard(Request $request)
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

        $jobsQuery->where('user_id', auth()->id() ?? 0);
        $applicantsQuery->whereHas('job_app', function ($query) use ($jobsQuery) {
            $query->where('user_id', auth()->id() ?? 0);
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


}
