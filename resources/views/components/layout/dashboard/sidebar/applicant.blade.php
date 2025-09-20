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
