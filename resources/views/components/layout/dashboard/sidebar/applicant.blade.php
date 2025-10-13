<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="sidebar-logo">
            <div align="center">
                <img src="{{ asset('assets/images/logo.png') }}" width="170" onclick="window.location.href = '/home';">
            </div>
{{--            <a href="/home">--}}
{{--                <img src="{{ asset('assets/images/logo_oman.svg') }}" width="170">--}}
{{--            </a>--}}
        </div>
    </div>

    <div class="sidebar-section">
        <div class="sidebar-title">{{ __('words.control_panel') }}</div>
        <a href="{{ route('dashboard.applicant.home') }}" class="sidebar-item {{ request()->routeIs('dashboard.applicant.home') ? "active":"" }}">
            <i class="fas fa-users"></i>
            {{ __('words.statistics') }}
        </a>
    </div>

    <div class="sidebar-section">
        <div class="sidebar-title">{{ __('words.jobs') }}</div>
        <a href="{{ route('dashboard.applicant.jobs.index') }}" class="sidebar-item {{ request()->routeIs('dashboard.applicant.jobs.index') ? "active":"" }}">
            <i class="fas fa-briefcase"></i>
            {{ __('words.Job Offers') }}
        </a>
        <a href="{{ route('dashboard.applicant.orders.index') }}" class="sidebar-item {{ request()->routeIs('dashboard.applicant.orders.index') ? "active":"" }}">
            <i class="fas fa-clipboard-check"></i>
            {{ __('words.My Orders') }}
        </a>
    </div>

    <div class="sidebar-section">
        <div class="sidebar-title">{{ __('words.Smart Hiring') }}</div>
        <a href="{{ route('dashboard.applicant.smart.cv.builder') }}" class="sidebar-item {{ request()->routeIs('dashboard.applicant.smart.cv.builder') && !request()->has('status') ? 'active' : '' }}">
            <i class="fa-regular fa-id-card"></i>
            {{ __('words.Build your CV') }}
        </a>
        <a href="{{ route('dashboard.applicant.smart.cv.analyzer', 'status=approved') }}" class="sidebar-item {{ request()->routeIs('dashboard.applicant.smart.cv.analyzer') && strtolower(request()->query('status')) === 'approved' ? 'active' : '' }}">
            <i class="fa-solid fa-magnifying-glass-chart"></i>
            {{ __('words.CV Analyzer') }}
        </a>
        <a href="{{ route('dashboard.applicant.smart.cv.matcher', 'status=rejected') }}" class="sidebar-item {{ request()->routeIs('dashboard.applicant.smart.cv.matcher') && strtolower(request()->query('status')) === 'rejected' ? 'active' : '' }}">
            <i class="fa-solid fa-users-viewfinder"></i>
            {{ __('words.Role Matcher') }}
        </a>
    </div>

    <div class="sidebar-section">
        <div class="sidebar-title">{{ __('words.system') }}</div>
        <a href="{{ route('profile') }}" class="sidebar-item {{ request()->routeIs('profile') ? "active":"" }}">
            <i class="fa-solid fa-address-card"></i>
            {{ __('words.profile') }}
        </a>
        <a href="{{ route('about') }}" target="_blank" class="sidebar-item {{ request()->routeIs('about') ? "active":"" }}">
            <i class="fa-solid fa-building"></i>
            {{ __('words.about_us') }}
        </a>
        <a href="{{ route('logout') }}" class="sidebar-item" style="color: #ff6060;text-shadow: 0 0 6px;font-weight: bolder;">
            <i class="fa-solid fa-arrow-right-from-bracket"></i>
            {{ __('words.logout') }}
        </a>
    </div>
{{--    <div align="center">--}}
{{--        <img src="{{ asset('assets/images/logo.png') }}" width="170">--}}
{{--    </div>--}}
</div>
