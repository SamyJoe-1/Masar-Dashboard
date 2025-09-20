@extends('layouts.app')

@section('header')
    <link href="{{ asset('styles/css/static_pages.css') }}" rel="stylesheet">
@endsection

@section('content')
    <section class="static-hero">
        <div class="container">
            <div class="static-hero-content">
                <h1 class="static-title">{{ __('static_pages.Terms and Conditions') }}</h1>
                <p class="static-subtitle">{{ __('static_pages.Read the terms and conditions for using Masar resume screening platform') }}</p>
            </div>
        </div>
    </section>

    <section class="terms-content">
        <div class="container">
            <div class="terms-document">
                <div class="terms-header">
                    <p class="last-updated">{{ __('static_pages.Last updated: January 2025') }}</p>
                </div>

                <div class="terms-section">
                    <h2>{{ __('static_pages.1. Acceptance of Terms') }}</h2>
                    <p>{{ __('static_pages.By using Masar platform, you agree to comply with these terms and conditions. If you do not agree to any of these terms, please do not use the platform.') }}</p>
                </div>

                <div class="terms-section">
                    <h2>{{ __('static_pages.2. Service Definition') }}</h2>
                    <p>{{ __('static_pages.Masar is a smart platform for screening and analyzing resumes using artificial intelligence technologies. The platform provides resume analysis services and detailed reports about candidates.') }}</p>
                </div>

                <div class="terms-section">
                    <h2>{{ __('static_pages.3. Platform Usage') }}</h2>
                    <h3>{{ __('static_pages.3.1 Permitted Use') }}</h3>
                    <ul>
                        <li>{{ __('static_pages.Using the platform for legitimate recruitment and professional selection purposes') }}</li>
                        <li>{{ __('static_pages.Uploading resumes in supported formats (PDF, DOC, DOCX)') }}</li>
                        <li>{{ __('static_pages.Obtaining reports and candidate analytics') }}</li>
                        <li>{{ __('static_pages.Exporting results and using them in recruitment processes') }}</li>
                    </ul>

                    <h3>{{ __('static_pages.3.2 Prohibited Use') }}</h3>
                    <ul>
                        <li>{{ __('static_pages.Uploading inappropriate or illegal content') }}</li>
                        <li>{{ __('static_pages.Attempting to hack or disable the platform') }}</li>
                        <li>{{ __('static_pages.Using the platform for unlawful discrimination purposes') }}</li>
                        <li>{{ __('static_pages.Selling or distributing data obtained from the platform') }}</li>
                    </ul>
                </div>

                <div class="terms-section">
                    <h2>{{ __('static_pages.4. Intellectual Property Rights') }}</h2>
                    <p>{{ __('static_pages.All intellectual property rights of the platform are reserved to Masar company. Users may not copy, distribute or modify any part of the platform without prior written permission.') }}</p>
                </div>

                <div class="terms-section">
                    <h2>{{ __('static_pages.5. Data Protection') }}</h2>
                    <ul>
                        <li>{{ __('static_pages.We are committed to protecting the privacy and security of uploaded data') }}</li>
                        <li>{{ __('static_pages.We do not keep copies of resumes after the analysis session ends') }}</li>
                        <li>{{ __('static_pages.All data is protected with advanced encryption technologies') }}</li>
                        <li>{{ __('static_pages.We do not share data with third parties without explicit consent') }}</li>
                    </ul>
                </div>

                <div class="terms-section">
                    <h2>{{ __('static_pages.6. Responsibility') }}</h2>
                    <h3>{{ __('static_pages.6.1 User Responsibility') }}</h3>
                    <p>{{ __('static_pages.The user is responsible for the accuracy of uploaded data and for using results appropriately and lawfully.') }}</p>

                    <h3>{{ __('static_pages.6.2 Platform Responsibility') }}</h3>
                    <p>{{ __('static_pages.We do our best to ensure accuracy of results, but we do not guarantee 100% accuracy and do not take responsibility for decisions based on results.') }}</p>
                </div>

                <div class="terms-section">
                    <h2>{{ __('static_pages.7. Billing and Payment') }}</h2>
                    <ul>
                        <li>{{ __('static_pages.Service prices advertised on the website apply at the time of use') }}</li>
                        <li>{{ __('static_pages.Payment is required before providing the service for paid plans') }}</li>
                        <li>{{ __('static_pages.No refunds for completed services') }}</li>
                        <li>{{ __('static_pages.Prices may change with prior notice') }}</li>
                    </ul>
                </div>

                <div class="terms-section">
                    <h2>{{ __('static_pages.8. Service Termination') }}</h2>
                    <p>{{ __('static_pages.We reserve the right to terminate or suspend the service in case of violation of these terms or for any other reason we deem appropriate.') }}</p>
                </div>

                <div class="terms-section">
                    <h2>{{ __('static_pages.9. Terms Modification') }}</h2>
                    <p>{{ __('static_pages.We may modify these terms from time to time. Users will be notified of any material changes with adequate time for review.') }}</p>
                </div>

                <div class="terms-section">
                    <h2>{{ __('static_pages.10. Applicable Law') }}</h2>
                    <p>{{ __('static_pages.These terms are subject to the laws of Saudi Arabia, and any dispute arising in connection with them will be resolved according to Saudi law.') }}</p>
                </div>

                <div class="terms-section">
                    <h2>{{ __('static_pages.11. Communication') }}</h2>
                    <p>{{ __('static_pages.For any inquiries about these terms and conditions, please contact us via:') }}</p>
                    <ul>
                        <li>{{ __('static_pages.Email: legal@masar.com') }}</li>
                        <li>{{ __('static_pages.Phone: +966 11 234 5678') }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
    <script src="{{ asset('styles/js/static_pages.js') }}"></script>
@endsection
