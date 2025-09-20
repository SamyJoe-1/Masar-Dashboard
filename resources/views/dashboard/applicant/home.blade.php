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
            <a href="{{ route('dashboard.applicant.home', ['filter' => 'last_24h']) }}"
               class="filter-btn {{ $filter === 'last_24h' ? 'active' : '' }}">
                {{ __('words.last_24h') }}
            </a>
            <a href="{{ route('dashboard.applicant.home', ['filter' => 'last_7_days']) }}"
               class="filter-btn {{ $filter === 'last_7_days' ? 'active' : '' }}">
                {{ __('words.last_7_days') }}
            </a>
            <a href="{{ route('dashboard.applicant.home', ['filter' => 'last_30_days']) }}"
               class="filter-btn {{ $filter === 'last_30_days' ? 'active' : '' }}">
                {{ __('words.last_30_days') }}
            </a>
            <a href="{{ route('dashboard.applicant.home', ['filter' => 'last_12_months']) }}"
               class="filter-btn {{ $filter === 'last_12_months' ? 'active' : '' }}">
                {{ __('words.last_12_months') }}
            </a>
            <a href="{{ route('dashboard.applicant.home', ['filter' => 'all_time']) }}"
               class="filter-btn {{ $filter === 'all_time' ? 'active' : '' }}">
                {{ __('words.all_time') }}
            </a>
        </div>
    </div>

    <div class="dashboard-grid">
        <!-- My Applications -->
        <div class="dashboard-card">
            <div class="card-header">
                <div class="card-icon">
                    <i class="fas fa-file-alt"></i>
                </div>
            </div>
            <div class="card-title">{{ __('words.All Orders') }}</div>
            <div class="card-value">{{ number_format($analytics['my_applications']) }}</div>
            <div class="card-trend {{ strpos($trends['applications_trend'], '-') === 0 ? 'down' : '' }}">
                {{ __('words.trend_from_previous_period', ['trend' => $trends['applications_trend']]) }}
            </div>
        </div>

        <!-- Pending Applications -->
        <div class="dashboard-card">
            <div class="card-header">
                <div class="card-icon">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
            <div class="card-title">{{ __('words.pending_applications') }}</div>
            <div class="card-value">{{ number_format($analytics['pending_applications']) }}</div>
            <div class="card-trend {{ strpos($trends['pending_trend'], '-') === 0 ? 'down' : '' }}">
                {{ __('words.trend_from_previous_period', ['trend' => $trends['pending_trend']]) }}
            </div>
        </div>

        <!-- Approved Applications -->
        <div class="dashboard-card">
            <div class="card-header">
                <div class="card-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
            <div class="card-title">{{ __('words.approved_applications') }}</div>
            <div class="card-value">{{ number_format($analytics['approved_applications']) }}</div>
            <div class="card-trend {{ strpos($trends['approved_trend'], '-') === 0 ? 'down' : '' }}">
                {{ __('words.trend_from_previous_period', ['trend' => $trends['approved_trend']]) }}
            </div>
        </div>

        <!-- Rejected Applications -->
        <div class="dashboard-card">
            <div class="card-header">
                <div class="card-icon">
                    <i class="fas fa-times-circle"></i>
                </div>
            </div>
            <div class="card-title">{{ __('words.rejected_applications') }}</div>
            <div class="card-value">{{ number_format($analytics['rejected_applications']) }}</div>
            <div class="card-trend {{ strpos($trends['rejected_trend'], '-') === 0 ? 'down' : '' }}">
                {{ __('words.trend_from_previous_period', ['trend' => $trends['rejected_trend']]) }}
            </div>
        </div>

        <!-- Waiting for Answer Applications -->
        <div class="dashboard-card">
            <div class="card-header">
                <div class="card-icon">
                    <i class="fas fa-question-circle"></i>
                </div>
            </div>
            <div class="card-title">{{ __('words.waiting_applications') }}</div>
            <div class="card-value">{{ number_format($analytics['waiting_applications']) }}</div>
            <div class="card-trend {{ strpos($trends['waiting_trend'], '-') === 0 ? 'down' : '' }}">
                {{ __('words.trend_from_previous_period', ['trend' => $trends['waiting_trend']]) }}
            </div>
        </div>

        <!-- Success Rate -->
        <div class="dashboard-card">
            <div class="card-header">
                <div class="card-icon">
                    <i class="fas fa-chart-pie"></i>
                </div>
            </div>
            <div class="card-title">{{ __('words.success_rate') }}</div>
            <div class="card-value">
                @if($analytics['my_applications'] > 0)
                    {{ number_format(($analytics['approved_applications'] / $analytics['my_applications']) * 100, 1) }}%
                @else
                    0%
                @endif
            </div>
            <div class="card-trend">
                {{ __('words.of_my_applications') }}
            </div>
        </div>
    </div>
@endsection
