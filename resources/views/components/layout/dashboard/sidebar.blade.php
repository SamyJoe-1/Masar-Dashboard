<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="sidebar-logo">
            <a href="/home">
                <img src="{{ asset('assets/images/logo.png') }}" width="170">
            </a>
        </div>
    </div>

    <div class="sidebar-section">
        <div class="sidebar-title">{{ __('words.control_panel') }}</div>
        <a href="#" class="sidebar-item {{ request()->routeIs('dashboard.hr.home') ? "active":"" }}">
            <i class="fas fa-users"></i>
            {{ __('words.statistics') }}
        </a>
    </div>

    <div class="sidebar-section">
        <div class="sidebar-title">{{ __('words.jobs') }}</div>
        <a href="{{ route('dashboard.hr.jobs.index') }}" class="sidebar-item">
            <i class="fas fa-briefcase"></i>
            {{ __('words.all_jobs') }}
        </a>
        <a href="{{ route('upload.form') }}" class="sidebar-item {{ request()->routeIs('upload.form') ? "active":"" }}">
            <i class="fas fa-plus-circle"></i>
            {{ __('words.create_job') }}
        </a>
    </div>

    <div class="sidebar-section">
        <div class="sidebar-title">{{ __('words.applicants') }}</div>
        <a href="#" class="sidebar-item">
            <i class="fas fa-user-tie"></i>
            {{ __('words.all_applicants') }}
        </a>
        <a href="#" class="sidebar-item">
            <i class="fa-solid fa-circle-check"></i>
            {{ __('words.accepted_applicants') }}
        </a>
        <a href="#" class="sidebar-item">
            <i class="fa-solid fa-circle-xmark"></i>
            {{ __('words.rejected_applicants') }}
        </a>
    </div>

    <div class="sidebar-section">
        <div class="sidebar-title">{{ __('words.system') }}</div>
        <a href="#" class="sidebar-item">
            <i class="fa-solid fa-address-card"></i>
            {{ __('words.profile') }}
        </a>
        <a href="#" class="sidebar-item">
            <i class="fa-solid fa-building"></i>
            {{ __('words.about_us') }}
        </a>
        <a href="#" class="sidebar-item" style="color: #ff6060;text-shadow: 0 0 6px;font-weight: bolder;">
            <i class="fa-solid fa-arrow-right-from-bracket"></i>
            {{ __('words.logout') }}
        </a>
    </div>
</div>
