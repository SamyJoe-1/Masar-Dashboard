@extends('layouts.app_dashboard')

@section('header')
    <link href="{{ asset('styles/css/analytics.css') }}" rel="stylesheet">
@endsection

@section('content')
    <div class="page-header">
        <h1 class="page-title">{{ __('words.welcome_title') }}</h1>
        <p class="page-subtitle">{{ __('words.welcome_subtitle') }}</p>
    </div>

    <!-- Filter Bar -->
    <div class="filter-bar mb-4">
        <div class="filter-buttons">
            <a href="{{ route('dashboard.hr.home', ['filter' => 'last_24h']) }}"
               class="filter-btn {{ $filter === 'last_24h' ? 'active' : '' }}">
                {{ __('words.last_24h') }}
            </a>
            <a href="{{ route('dashboard.hr.home', ['filter' => 'last_7_days']) }}"
               class="filter-btn {{ $filter === 'last_7_days' ? 'active' : '' }}">
                {{ __('words.last_7_days') }}
            </a>
            <a href="{{ route('dashboard.hr.home', ['filter' => 'last_30_days']) }}"
               class="filter-btn {{ $filter === 'last_30_days' ? 'active' : '' }}">
                {{ __('words.last_30_days') }}
            </a>
            <a href="{{ route('dashboard.hr.home', ['filter' => 'last_12_months']) }}"
               class="filter-btn {{ $filter === 'last_12_months' ? 'active' : '' }}">
                {{ __('words.last_12_months') }}
            </a>
            <a href="{{ route('dashboard.hr.home', ['filter' => 'all_time']) }}"
               class="filter-btn {{ $filter === 'all_time' ? 'active' : '' }}">
                {{ __('words.all_time') }}
            </a>
        </div>
    </div>

    <div class="dashboard-grid">
        <!-- Jobs Count -->
        <div class="dashboard-card">
            <div class="card-header">
                <div class="card-icon">
                    <i class="fas fa-briefcase"></i>
                </div>
            </div>
            <div class="card-title">{{ __('words.jobs_count') }}</div>
            <div class="card-value">{{ number_format($analytics['jobs_count']) }}</div>
            <div class="card-trend {{ strpos($trends['jobs_trend'], '-') === 0 ? 'down' : '' }}">
                {{ __('words.trend_from_previous_period', ['trend' => $trends['jobs_trend']]) }}
            </div>
        </div>

        <!-- Total Applicants -->
        <div class="dashboard-card">
            <div class="card-header">
                <div class="card-icon">
                    <i class="fas fa-users"></i>
                </div>
            </div>
            <div class="card-title">{{ __('words.total_applicants') }}</div>
            <div class="card-value">{{ number_format($analytics['total_applicants']) }}</div>
            <div class="card-trend {{ strpos($trends['applicants_trend'], '-') === 0 ? 'down' : '' }}">
                {{ __('words.trend_from_previous_period', ['trend' => $trends['applicants_trend']]) }}
            </div>
        </div>

        <!-- Approved Applicants -->
        <div class="dashboard-card">
            <div class="card-header">
                <div class="card-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
            <div class="card-title">{{ __('words.approved_applicants') }}</div>
            <div class="card-value">{{ number_format($analytics['approved_applicants']) }}</div>
            <div class="card-trend {{ strpos($trends['approved_trend'], '-') === 0 ? 'down' : '' }}">
                {{ __('words.trend_from_previous_period', ['trend' => $trends['approved_trend']]) }}
            </div>
        </div>

        <!-- Rejected Applicants -->
        <div class="dashboard-card">
            <div class="card-header">
                <div class="card-icon">
                    <i class="fas fa-times-circle"></i>
                </div>
            </div>
            <div class="card-title">{{ __('words.rejected_applicants') }}</div>
            <div class="card-value">{{ number_format($analytics['rejected_applicants']) }}</div>
            <div class="card-trend {{ strpos($trends['rejected_trend'], '-') === 0 ? 'down' : '' }}">
                {{ __('words.trend_from_previous_period', ['trend' => $trends['rejected_trend']]) }}
            </div>
        </div>

        <!-- Approval Rate -->
        <div class="dashboard-card">
            <div class="card-header">
                <div class="card-icon">
                    <i class="fas fa-chart-pie"></i>
                </div>
            </div>
            <div class="card-title">{{ __('words.approval_rate') }}</div>
            <div class="card-value">
                @if($analytics['total_applicants'] > 0)
                    {{ number_format(($analytics['approved_applicants'] / $analytics['total_applicants']) * 100, 1) }}%
                @else
                    0%
                @endif
            </div>
            <div class="card-trend">
                {{ __('words.from_total_applicants') }}
            </div>
        </div>

        <!-- Applications per Job -->
        <div class="dashboard-card">
            <div class="card-header">
                <div class="card-icon">
                    <i class="fas fa-chart-bar"></i>
                </div>
            </div>
            <div class="card-title">{{ __('words.applications_per_job') }}</div>
            <div class="card-value">
                @if($analytics['jobs_count'] > 0)
                    {{ number_format($analytics['total_applicants'] / $analytics['jobs_count'], 1) }}
                @else
                    0
                @endif
            </div>
            <div class="card-trend">
                {{ __('words.average_applications') }}
            </div>
        </div>
    </div>
@endsection
