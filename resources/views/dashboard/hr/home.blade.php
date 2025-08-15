@extends('layouts.app_dashboard')

@section('content')
    <div class="page-header">
        <h1 class="page-title">{{ __('words.welcome_title') }}</h1>
        <p class="page-subtitle">{{ __('words.welcome_subtitle') }}</p>
    </div>

    <div class="dashboard-grid">
        <div class="dashboard-card">
            <div class="card-header">
                <div class="card-icon">
                    <i class="fas fa-users"></i>
                </div>
            </div>
            <div class="card-title">{{ __('words.total_users') }}</div>
            <div class="card-value">1,247</div>
            <div class="card-trend">{{ __('words.percent_from_last_month', ['percent' => '+12%']) }}</div>
        </div>

        <div class="dashboard-card">
            <div class="card-header">
                <div class="card-icon">
                    <i class="fas fa-briefcase"></i>
                </div>
            </div>
            <div class="card-title">{{ __('words.active_jobs') }}</div>
            <div class="card-value">328</div>
            <div class="card-trend">{{ __('words.percent_from_last_month', ['percent' => '+8%']) }}</div>
        </div>

        <div class="dashboard-card">
            <div class="card-header">
                <div class="card-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
            </div>
            <div class="card-title">{{ __('words.this_month_growth') }}</div>
            <div class="card-value">+15.3%</div>
            <div class="card-trend">{{ __('words.percent_from_last_month', ['percent' => '+2.1%']) }}</div>
        </div>

        <div class="dashboard-card">
            <div class="card-header">
                <div class="card-icon">
                    <i class="fas fa-star"></i>
                </div>
            </div>
            <div class="card-title">{{ __('words.overall_rating') }}</div>
            <div class="card-value">4.8/5</div>
            <div class="card-trend down">{{ __('words.rating_change_from_last_month', ['change' => '-0.2']) }}</div>
        </div>

        <div class="dashboard-card">
            <div class="card-header">
                <div class="card-icon">
                    <i class="fas fa-dollar-sign"></i>
                </div>
            </div>
            <div class="card-title">{{ __('words.total_revenue') }}</div>
            <div class="card-value">$24,500</div>
            <div class="card-trend">{{ __('words.percent_from_last_month', ['percent' => '+18%']) }}</div>
        </div>

        <div class="dashboard-card">
            <div class="card-header">
                <div class="card-icon">
                    <i class="fas fa-eye"></i>
                </div>
            </div>
            <div class="card-title">{{ __('words.website_visits') }}</div>
            <div class="card-value">15,642</div>
            <div class="card-trend">{{ __('words.percent_from_last_month', ['percent' => '+25%']) }}</div>
        </div>
    </div>
@endsection
