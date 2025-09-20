<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="{{ asset('styles/css/errors.css') }}" rel="stylesheet">
    <title>Error Pages</title>
</head>
<body>
<!-- Navigation -->
{{--<div class="nav-top">--}}
{{--    <button class="nav-btn active" onclick="showPage('404')" id="nav-404">404</button>--}}
{{--    <button class="nav-btn" onclick="showPage('500')" id="nav-500">500</button>--}}
{{--    <button class="nav-btn" onclick="showPage('403')" id="nav-403">403</button>--}}
{{--</div>--}}

<!-- 404 Page - Space Theme -->
<div class="page page-404 active" id="page-404">
    <div class="stars"></div>
    <div class="particles" id="particles-404"></div>
    <div class="space-container">
        <div class="astronaut"></div>
        <div class="error-404">404</div>
        <h1 class="space-title">Lost in Space</h1>
        <p class="space-subtitle">The page you're looking for has drifted away into the cosmic void.</p>
        <button class="home-btn" onclick="goHome()">Return to Home</button>
    </div>
</div>

<!-- 500 Page - Glitch Theme -->
<div class="page page-500" id="page-500">
    <div class="particles" id="particles-500"></div>
    <div class="glitch-container">
        <div class="server-icon"></div>
        <div class="error-500">500</div>
        <h1 class="glitch-title">System Malfunction</h1>
        <p class="glitch-subtitle">Our servers are experiencing technical difficulties. Please stand by.</p>
        <button class="home-btn" onclick="goHome()">Back to home</button>
    </div>
</div>

<!-- 403 Page - Lock Theme -->
<div class="page page-403" id="page-403">
    <div class="particles" id="particles-403"></div>
    <div class="lock-container">
        <div class="lock-icon">
            <div class="lock-shackle"></div>
            <div class="lock-body">
                <div class="lock-keyhole"></div>
            </div>
        </div>
        <div class="error-403">403</div>
        <h1 class="lock-title">Access Restricted</h1>
        <p class="lock-subtitle">This area is secured. You need proper authorization to proceed.</p>
        <button class="home-btn" onclick="goHome()">Try Later</button>
    </div>
</div>

<script>let currentPage = '404';</script>
<script src="{{ asset('styles/js/errors.js') }}"></script>
</body>
</html>
