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
        @if(app()->getLocale() == 'ar')
            <a href="?locale=en" class="lang-switch text-decoration-none">English</a>
        @else
            <a href="?locale=ar" class="lang-switch text-decoration-none">العربية</a>
        @endif
    </nav>
</header>
