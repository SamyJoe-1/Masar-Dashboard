<?php

namespace App\Traits\Reports;

use App\Models\JobApp;
use App\Models\Applicant;
use Illuminate\Http\Request;
use Carbon\Carbon;

trait HR
{
    public function index(Request $request)
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

        // Get analytics data
        $analytics = [
            'jobs_count' => $jobsQuery->count(),
            'total_applicants' => $applicantsQuery->count(),
            'approved_applicants' => $applicantsQuery->where('status', 'approved')->count(),
            'rejected_applicants' => $applicantsQuery->where('status', 'rejected')->count(),
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

        return view('dashboard.home', compact('analytics', 'trends', 'filter'));
    }

    private function getDateRange($filter)
    {
        $now = Carbon::now();

        switch ($filter) {
            case 'last_24h':
                return [$now->copy()->subDay(), $now];
            case 'last_7_days':
                return [$now->copy()->subDays(7), $now];
            case 'last_30_days':
                return [$now->copy()->subDays(30), $now];
            case 'last_12_months':
                return [$now->copy()->subMonths(12), $now];
            case 'all_time':
            default:
                return null;
        }
    }

    private function getPreviousAnalytics($filter)
    {
        $previousRange = $this->getPreviousDateRange($filter);

        if (!$previousRange) {
            return [
                'jobs_count' => 0,
                'total_applicants' => 0,
                'approved_applicants' => 0,
                'rejected_applicants' => 0,
            ];
        }

        $jobsQuery = JobApp::whereBetween('created_at', $previousRange);
        $applicantsQuery = Applicant::whereBetween('created_at', $previousRange);

        return [
            'jobs_count' => $jobsQuery->count(),
            'total_applicants' => $applicantsQuery->count(),
            'approved_applicants' => $applicantsQuery->where('status', 'approved')->count(),
            'rejected_applicants' => $applicantsQuery->where('status', 'rejected')->count(),
        ];
    }

    private function getPreviousDateRange($filter)
    {
        $now = Carbon::now();

        switch ($filter) {
            case 'last_24h':
                return [$now->copy()->subDays(2), $now->copy()->subDay()];
            case 'last_7_days':
                return [$now->copy()->subDays(14), $now->copy()->subDays(7)];
            case 'last_30_days':
                return [$now->copy()->subDays(60), $now->copy()->subDays(30)];
            case 'last_12_months':
                return [$now->copy()->subMonths(24), $now->copy()->subMonths(12)];
            default:
                return null;
        }
    }

    private function calculateTrend($current, $previous)
    {
        if ($previous == 0) {
            return $current > 0 ? '+100%' : '0%';
        }

        $percentage = (($current - $previous) / $previous) * 100;
        $sign = $percentage >= 0 ? '+' : '';

        return $sign . number_format($percentage, 1) . '%';
    }
}
