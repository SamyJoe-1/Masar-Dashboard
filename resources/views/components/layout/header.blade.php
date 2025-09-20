<header>
    <nav class="container">
        <a href="/" class="logo">
{{--            <img src="{{ asset('assets/images/logo_oman.svg') }}" width="140">--}}
            <img src="{{ asset('assets/images/logo2.png') }}" width="130">
        </a>

        <!-- Desktop Navigation -->
        <ul class="nav-links">
            <li><a href="/#features">{{ __("words.Features") }}</a></li>
            <li><a href="/#process">{{ __("words.How_It_Works") }}</a></li>
            <li><a href="{{ route('services') }}">{{ __("words.Our_Services") }}</a></li>
            <li><a href="{{ route('contact') }}">{{ __("words.Contact_Us") }}</a></li>
            <li><a href="{{ route('about') }}">{{ __("words.More_About_Us") }}</a></li>
        </ul>

        <div class="beside-nav-items">
            @if(app()->getLocale() == 'ar')
                <a href="?locale=en" class="btn-lang text-decoration-none" id="langSwitcher">
                    <img src="{{ asset('assets/images/flags/usa.svg') }}" width="27">
                    English
                </a>
            @else
                <a href="?locale=ar" class="btn-lang text-decoration-none" id="langSwitcher">
                    <img src="{{ asset('assets/images/flags/oman.svg') }}" width="27">
                    {{ __("words.Arabic") }}
                </a>
            @endif

            <!-- Desktop Auth Button -->
            <div class="desktop-auth">
                @guest
                    <a href="{{ route('login') }}" class="btn-primary-2 text-decoration-none" id="dashboardBtn">{{ __("words.Login") }}</a>
                @else
                    <a href="{{ route('home') }}" class="btn-primary-2 text-decoration-none" id="dashboardBtn">{{ __("words.Dashboard") }}</a>
                @endguest
            </div>

            <!-- Mobile Menu Toggle -->
            <button class="mobile-menu-toggle" onclick="toggleMobileMenu()">
                ☰
            </button>
        </div>
    </nav>

    <!-- Mobile Sidebar Overlay -->
    <div class="mobile-sidebar-overlay" id="mobileOverlay" onclick="closeMobileMenu()"></div>

    <!-- Mobile Sidebar -->
    <div class="mobile-sidebar" id="mobileSidebar">
        <div class="mobile-sidebar-header">
            <div class="logo">
                <img src="{{ asset('assets/images/logo2.png') }}" width="100">
{{--                <img src="{{ asset('assets/images/logo_oman.svg') }}" width="130">--}}
            </div>
            <button class="mobile-sidebar-close" onclick="closeMobileMenu()">
                ✕
            </button>
        </div>

        <ul class="mobile-nav-links">
            <li><a href="/#features" onclick="closeMobileMenu()">{{ __("words.Features") }}</a></li>
            <li><a href="/#process" onclick="closeMobileMenu()">{{ __("words.How_It_Works") }}</a></li>
            <li><a href="{{ route('services') }}" onclick="closeMobileMenu()">{{ __("words.Our_Services") }}</a></li>
            <li><a href="{{ route('contact') }}" onclick="closeMobileMenu()">{{ __("words.Contact_Us") }}</a></li>
            <li><a href="{{ route('about') }}" onclick="closeMobileMenu()">{{ __("words.More_About_Us") }}</a></li>
        </ul>

        <div class="mobile-auth-section d-flex gap-3">
            @guest
                <a href="{{ route('login') }}" class="btn-primary-2 text-decoration-none" onclick="closeMobileMenu()">{{ __("words.Login") }}</a>
            @else
                <a href="{{ route('home') }}" class="btn-primary-2 text-decoration-none" onclick="closeMobileMenu()">{{ __("words.Dashboard") }}</a>
            @endguest
            @if(app()->getLocale() == 'ar')
                <a href="?locale=en" class="btn-lang text-decoration-none">
                    <img src="{{ asset('assets/images/flags/usa.svg') }}" width="27">
                    English
                </a>
            @else
                <a href="?locale=ar" class="btn-lang text-decoration-none d-flex">
                    <img src="{{ asset('assets/images/flags/oman.svg') }}" width="27">
                    العربية
                </a>
            @endif
        </div>
    </div>
</header>
