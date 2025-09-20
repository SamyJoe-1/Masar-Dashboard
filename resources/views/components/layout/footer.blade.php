<footer>
    <div class="container">
        <div class="footer-content">
            <div class="footer-section">
                <h3>{{ __("words.Masar") }}</h3>
                <p>{{ __("words.smart_platform_description") }}</p>
            </div>
            <div class="footer-section">
                <h3>{{ __("words.About_Us") }}</h3>
                <a href="{{ route('about') }}">{{ __("words.More_About_Us") }}</a>
                <a href="{{ route('contact') }}">{{ __("words.Contact_Us") }}</a>
                <a href="{{ route('terms') }}">{{ __("words.Terms") }}</a>
            </div>

            <div class="footer-section">
                <h3>{{ __("words.Our_Services") }}</h3>
                <a href="{{ route('services') }}">{{ __("words.Services") }}</a>
                <a href="{{ route('privacy') }}">{{ __("words.Privacy_Protection") }}</a>
                <a href="{{ route('faq') }}">{{ __("words.FAQ") }}</a>
            </div>

            <div class="footer-section">
                <h3>{{ __("words.Contact_Information") }}</h3>
                <a href="mailto:support@massar.biz">support@massar.biz</a>
                <a href="tel:+96895160789">+968 95 160 789</a>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2025 {{ __("words.Masar") }}. {{ __("words.All_Rights_Reserved") }}</p>
        </div>
    </div>
</footer>
