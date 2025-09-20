<!DOCTYPE html>
<html lang="{{ app()->getLocale() == 'ar' ? "ar":"en" }}" dir="{{ app()->getLocale() == 'ar' ? "rtl":"ltr" }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة التحكم</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@200;300;400;500;700;800;900&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/boxicons/2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link href="{{ asset('styles/css/app_dashboard.css') }}" rel="stylesheet">
    <link href="{{ asset('styles/css/formControl.css') }}" rel="stylesheet">
    <link href="{{ asset('styles/css/table.css') }}" rel="stylesheet">
    @yield('header')
</head>
<body class="{{ app()->getLocale() }}">
@if(auth()->user()->isHR())
    <x-layout.dashboard.sidebar.hr></x-layout.dashboard.sidebar.hr>
@elseif(auth()->user()->isApplicant())
    <x-layout.dashboard.sidebar.applicant></x-layout.dashboard.sidebar.applicant>
@endif

<!-- Top Navbar -->
<nav class="navbar">
    <div class="navbar-left">
        <button class="mobile-menu-btn" onclick="toggleSidebar()">
            <i class="fas fa-bars"></i>
        </button>
    </div>

    <div class="navbar-right">
        @if(app()->getLocale() == 'ar')
            <a class="quiet-btn text-decoration-none" href="?locale=en">
                <img src="{{ asset('assets/images/flags/usa.svg') }}" width="35">
                English
            </a>
        @else
            <a class="quiet-btn text-decoration-none" href="?locale=ar">
                <img src="{{ asset('assets/images/flags/oman.svg') }}" width="35">
                العربية
            </a>
        @endif
        <x-layout.dashboard.dropdownAvatar></x-layout.dashboard.dropdownAvatar>
    </div>
</nav>

<!-- Mobile Overlay -->
<div class="mobile-overlay" id="mobileOverlay" onclick="closeSidebar()"></div>

<!-- Main Content -->
<div class="main-content">
    <div class="content-wrapper">
        @yield('content')
    </div>
</div>

<script src="{{ asset('styles/js/app_dashboard.js') }}"></script>
@yield('scripts')
</body>
</html>
