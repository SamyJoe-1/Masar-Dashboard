<header>
    <nav class="container">
        <a href="/" class="logo">
            <img src="{{ asset('assets/images/logo2.png') }}" width="100">
        </a>
        <ul class="nav-links">
            <li><a href="/#features">المميزات</a></li>
            <li><a href="/#process">كيف يعمل</a></li>
            <li><a href="{{ route('services') }}">خدماتنا</a></li>
            <li><a href="{{ route('contact') }}">تواصل معنا</a></li>
            <li><a href="{{ route('about') }}">المزيد عنا</a></li>
        </ul>
        <div class="beside-nav-items">
            @if(app()->getLocale() == 'ar')
                <a href="?locale=en" class="btn-lang text-decoration-none">
                    <img src="{{ asset('assets/images/flags/usa.svg') }}" width="27">
                    English
                </a>
            @else
                <a href="?locale=ar" class="btn-lang text-decoration-none">
                    <img src="{{ asset('assets/images/flags/oman.svg') }}" width="27">
                    العربية
                </a>
            @endif
            <div>
                @guest
                    <a href="{{ route('login') }}" class="btn-primary-2 text-decoration-none">{{ __("words.Login") }}</a>
                @else
                    <a href="{{ route('home') }}" class="btn-primary-2 text-decoration-none">{{ __("words.Dashboard") }}</a>
                @endguest
            </div>
        </div>

    </nav>
</header>
