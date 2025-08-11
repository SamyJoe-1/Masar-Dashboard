<!DOCTYPE html>
<html lang="ar" dir="{{ app()->getLocale() == 'ar' ? "rtl":"ltr" }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>فحص المرشحين الذكي - Masar</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@200;300;400;500;700;800;900&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="{{ asset('styles/css/app.css') }}" rel="stylesheet">

    @yield('header')
</head>
<body>
<x-layout.header></x-layout.header>

@yield('content')

<x-layout.footer></x-layout.footer>

<script src="{{ asset('styles/js/app.js') }}"></script>
@yield('scripts')
</body>
</html>
