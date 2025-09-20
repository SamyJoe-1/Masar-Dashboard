@extends('layouts.app')

@section('header')
    <link href="{{ asset('styles/css/static_pages.css') }}" rel="stylesheet">
@endsection

@section('content')
    <section class="static-hero">
        <div class="container">
            <div class="static-hero-content">
                <h1 class="static-title">{{ __('static_pages.Privacy Policy') }}</h1>
                <p class="static-subtitle">{{ __('static_pages.We are committed to protecting your privacy and data security on Masar platform') }}</p>
            </div>
        </div>
    </section>

    <section class="privacy-content">
        <div class="container">
            <div class="privacy-document">
                <div class="privacy-header">
                    <p class="last-updated">{{ __('static_pages.Last updated: January 2025') }}</p>
                    <div class="privacy-highlights">
                        <h3>{{ __('static_pages.Core Commitments') }}</h3>
                        <div class="highlight-cards">
                            <div class="highlight-card">
                                <div class="highlight-icon">🔒</div>
                                <h4>{{ __('static_pages.Data Protection') }}</h4>
                                <p>{{ __('static_pages.Advanced encryption for all data') }}</p>
                            </div>
                            <div class="highlight-card">
                                <div class="highlight-icon">🚫</div>
                                <h4>{{ __('static_pages.No Storage') }}</h4>
                                <p>{{ __('static_pages.We do not keep resumes') }}</p>
                            </div>
                            <div class="highlight-card">
                                <div class="highlight-icon">🤝</div>
                                <h4>{{ __('static_pages.No Sharing') }}</h4>
                                <p>{{ __('static_pages.We do not share data with others') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="privacy-section">
                    <h2>{{ __('static_pages.1. Information We Collect') }}</h2>
                    <h3>{{ __('static_pages.1.1 Uploaded Data') }}</h3>
                    <ul>
                        <li>{{ __('static_pages.Resume files (PDF, DOC, DOCX)') }}</li>
                        <li>{{ __('static_pages.Information extracted from resumes for analysis') }}</li>
                        <li>{{ __('static_pages.Job criteria specified by the user') }}</li>
                    </ul>

                    <h3>{{ __('static_pages.1.2 Usage Information') }}</h3>
                    <ul>
                        <li>{{ __('static_pages.Login data (in case of account creation)') }}</li>
                        <li>{{ __('static_pages.Technical information about device and browser') }}</li>
                        <li>{{ __('static_pages.Usage statistics to improve the service') }}</li>
                        <li>{{ __('static_pages.IP address (temporarily for security purposes)') }}</li>
                    </ul>
                </div>

                <div class="privacy-section">
                    <h2>{{ __('static_pages.2. How We Use Information') }}</h2>
                    <div class="usage-grid">
                        <div class="usage-item">
                            <h4>{{ __('static_pages.Smart Analysis') }}</h4>
                            <p>{{ __('static_pages.Processing resumes using artificial intelligence to provide accurate and detailed analysis') }}</p>
                        </div>
                        <div class="usage-item">
                            <h4>{{ __('static_pages.Report Generation') }}</h4>
                            <p>{{ __('static_pages.Creating comprehensive reports that include candidate evaluation and ranking by suitability') }}</p>
                        </div>
                        <div class="usage-item">
                            <h4>{{ __('static_pages.Service Improvement') }}</h4>
                            <p>{{ __('static_pages.Using anonymous usage data to develop and improve analysis algorithms') }}</p>
                        </div>
                        <div class="usage-item">
                            <h4>{{ __('static_pages.Technical Support') }}</h4>
                            <p>{{ __('static_pages.Providing assistance and technical support when needed and solving technical problems') }}</p>
                        </div>
                    </div>
                </div>

                <div class="privacy-section">
                    <h2>{{ __('static_pages.3. Data Protection') }}</h2>
                    <div class="security-measures">
                        <div class="security-item">
                            <h4>{{ __('static_pages.🔐 Advanced Encryption') }}</h4>
                            <p>{{ __('static_pages.All data is protected with AES-256 encryption during transmission and processing') }}</p>
                        </div>
                        <div class="security-item">
                            <h4>{{ __('static_pages.🏢 Secure Servers') }}</h4>
                            <p>{{ __('static_pages.Data hosting on protected and certified servers with latest security technologies') }}</p>
                        </div>
                        <div class="security-item">
                            <h4>{{ __('static_pages.🚪 Access Control') }}</h4>
                            <p>{{ __('static_pages.Limited data access only to authorized employees according to "need to know" principle') }}</p>
                        </div>
                        <div class="security-item">
                            <h4>{{ __('static_pages.🔍 Continuous Monitoring') }}</h4>
                            <p>{{ __('static_pages.Continuous monitoring of systems and networks to detect any hacking attempts') }}</p>
                        </div>
                    </div>
                </div>

                <div class="privacy-section">
                    <h2>{{ __('static_pages.4. Data Retention') }}</h2>
                    <div class="retention-timeline">
                        <div class="timeline-item">
                            <div class="timeline-marker">⚡</div>
                            <h4>{{ __('static_pages.⚡ During Processing') }}</h4>
                            <p>{{ __('static_pages.Data is protected in temporary memory only during the analysis process') }}</p>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-marker">🗑️</div>
                            <h4>{{ __('static_pages.🗑️ After Analysis') }}</h4>
                            <p>{{ __('static_pages.Immediate deletion of all resume files after report generation') }}</p>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-marker">📊</div>
                            <h4>{{ __('static_pages.📊 Reports') }}</h4>
                            <p>{{ __('static_pages.Reports are available to the user for a limited time then permanently deleted') }}</p>
                        </div>
                    </div>
                </div>

                <div class="privacy-section">
                    <h2>{{ __('static_pages.5. Information Sharing') }}</h2>
                    <div class="sharing-policy">
                        <div class="no-sharing">
                            <h3>{{ __('static_pages.❌ We do not share data with:') }}</h3>
                            <ul>
                                <li>{{ __('static_pages.Marketing or advertising companies') }}</li>
                                <li>{{ __('static_pages.Data brokers or information selling companies') }}</li>
                                <li>{{ __('static_pages.Social media networks') }}</li>
                                <li>{{ __('static_pages.Any third party for commercial purposes') }}</li>
                            </ul>
                        </div>
                        <div class="limited-sharing">
                            <h3>{{ __('static_pages.⚖️ Limited sharing only in case of:') }}</h3>
                            <ul>
                                <li>{{ __('static_pages.Legal obligation or court order') }}</li>
                                <li>{{ __('static_pages.Protecting our rights or users\' rights') }}</li>
                                <li>{{ __('static_pages.Dealing with technical service providers (with strict confidentiality agreements)') }}</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="privacy-section">
                    <h2>{{ __('static_pages.6. User Rights') }}</h2>
                    <div class="user-rights">
                        <div class="right-item">
                            <h4>{{ __('static_pages.🔍 Access to Information') }}</h4>
                            <p>{{ __('static_pages.The right to know what data is collected about you and how it is used') }}</p>
                        </div>
                        <div class="right-item">
                            <h4>{{ __('static_pages.✏️ Edit and Correct') }}</h4>
                            <p>{{ __('static_pages.The right to edit or correct any incorrect information') }}</p>
                        </div>
                        <div class="right-item">
                            <h4>{{ __('static_pages.🗑️ Deletion') }}</h4>
                            <p>{{ __('static_pages.The right to request deletion of all your data from our systems') }}</p>
                        </div>
                        <div class="right-item">
                            <h4>{{ __('static_pages.📤 Transfer') }}</h4>
                            <p>{{ __('static_pages.The right to transfer your data to another service in a readable format') }}</p>
                        </div>
                    </div>
                </div>

                <div class="privacy-section">
                    <h2>{{ __('static_pages.7. Cookies') }}</h2>
                    <p>{{ __('static_pages.We use cookies to improve user experience and ensure security:') }}</p>
                    <ul>
                        <li>{{ __('static_pages.Necessary cookies: to ensure the platform works properly') }}</li>
                        <li>{{ __('static_pages.Analytical cookies: to understand how the platform is used and improve it') }}</li>
                        <li>{{ __('static_pages.Security cookies: to protect the platform from security threats') }}</li>
                    </ul>
                </div>

                <div class="privacy-section">
                    <h2>{{ __('static_pages.8. Policy Updates') }}</h2>
                    <p>{{ __('static_pages.We may update this policy from time to time. We will notify users of any important changes via:') }}</p>
                    <ul>
                        <li>{{ __('static_pages.Notification on the platform when logging in') }}</li>
                        <li>{{ __('static_pages.Email message (for registered users)') }}</li>
                        <li>{{ __('static_pages.Announcement on the website') }}</li>
                    </ul>
                </div>

                <div class="privacy-section">
                    <h2>{{ __('static_pages.9. Privacy Communication') }}</h2>
                    <p>{{ __('static_pages.For any questions or concerns regarding the privacy policy, please contact us:') }}</p>
                    <div class="contact-privacy">
                        <div class="contact-method">
                            <strong>{{ __('static_pages.📧 Email: privacy@masar.com') }}</strong>
                        </div>
                        <div class="contact-method">
                            <strong>{{ __('static_pages.📞 Phone: +966 11 234 5678') }}</strong>
                        </div>
                        <div class="contact-method">
                            <strong>{{ __('static_pages.⏰ Response time: 24-48 hours') }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
    <script src="{{ asset('styles/js/static_pages.js') }}"></script>
@endsection
