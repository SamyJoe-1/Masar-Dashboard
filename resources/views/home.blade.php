@extends('layouts.app')

@section('content')
    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <div class="hero-text">
                    <h1 class="hero-title">{{ __('words.smart_resume_screening_ai') }}</h1>
                    <p class="hero-subtitle">{{ __('words.upload_hundreds_resumes_comprehensive_report') }}</p>
                    <div class="cta-buttons">
                        <a href="{{ route('upload.form') }}" class="btn btn-primary">{{ __('words.start_free_scan') }}</a>
                        <a href="{{ route('register') }}" class="btn btn-secondary">{{ __('words.join_us') }}</a>
                    </div>
                </div>
                <div>
                    <img id="heroSectionImage" src="{{ asset('assets/images/home.webp') }}">
                </div>
{{--                <div class="hero-dashboard">--}}
{{--                    <div class="dashboard-header">--}}
{{--                        <h3>{{ __('words.smart_scan_dashboard') }}</h3>--}}
{{--                        <span>🚀</span>--}}
{{--                    </div>--}}
{{--                    <div class="upload-zone" onclick="window.location.href = '{{ route('upload.form') }}'">--}}
{{--                        <div class="upload-icon">📄</div>--}}
{{--                        <h4>{{ __('words.drag_drop_resumes_here') }}</h4>--}}
{{--                        <p>{{ __('words.or_click_browse_pdf_doc') }}</p>--}}
{{--                    </div>--}}
{{--                    <div class="stats-grid">--}}
{{--                        <div class="stat-card">--}}
{{--                            <span class="stat-number">156</span>--}}
{{--                            <span class="stat-label">{{ __('words.scanned') }}</span>--}}
{{--                        </div>--}}
{{--                        <div class="stat-card">--}}
{{--                            <span class="stat-number">89</span>--}}
{{--                            <span class="stat-label">{{ __('words.accepted') }}</span>--}}
{{--                        </div>--}}
{{--                        <div class="stat-card">--}}
{{--                            <span class="stat-number">67</span>--}}
{{--                            <span class="stat-label">{{ __('words.rejected') }}</span>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
            </div>
        </div>
    </section>

    <section class="features" id="features">
        <div class="container">
            <h2 class="section-title">{{ __('words.exceptional_features') }}</h2>
            <p class="section-subtitle">{{ __('words.advanced_techniques_resume_analysis') }}</p>

            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">🤖</div>
                    <h3 class="feature-title">{{ __('words.advanced_ai') }}</h3>
                    <p class="feature-description">{{ __('words.advanced_ml_algorithms_95_accuracy') }}</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">⚡</div>
                    <h3 class="feature-title">{{ __('words.fast_processing') }}</h3>
                    <p class="feature-description">{{ __('words.scan_hundreds_resumes_minutes') }}</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">📊</div>
                    <h3 class="feature-title">{{ __('words.comprehensive_reports') }}</h3>
                    <p class="feature-description">{{ __('words.deep_analysis_strengths_weaknesses') }}</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🌐</div>
                    <h3 class="feature-title">{{ __('words.multilingual_support') }}</h3>
                    <p class="feature-description">{{ __('words.handles_arabic_english_resumes') }}</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🔒</div>
                    <h3 class="feature-title">{{ __('words.security_privacy') }}</h3>
                    <p class="feature-description">{{ __('words.advanced_encryption_data_protection') }}</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🎯</div>
                    <h3 class="feature-title">{{ __('words.precise_matching') }}</h3>
                    <p class="feature-description">{{ __('words.smart_skills_experience_matching') }}</p>
                </div>
            </div>
        </div>
    </section>

    <section class="process" id="process">
        <div class="container">
            <h2 class="section-title">{{ __('words.how_system_works') }}</h2>
            <p class="section-subtitle">{{ __('words.three_simple_steps_best_candidates') }}</p>

            <div class="process-steps">
                <div class="process-step">
                    <div class="step-number">1</div>
                    <h3 class="step-title">{{ __('words.upload_resumes') }}</h3>
                    <p class="step-description">{{ __('words.upload_pdf_word_hundreds_files') }}</p>
                </div>
                <div class="process-step">
                    <div class="step-number">2</div>
                    <h3 class="step-title">{{ __('words.smart_processing') }}</h3>
                    <p class="step-description">{{ __('words.ai_analyzes_compares_job_criteria') }}</p>
                </div>
                <div class="process-step">
                    <div class="step-number">3</div>
                    <h3 class="step-title">{{ __('words.get_report') }}</h3>
                    <p class="step-description">{{ __('words.comprehensive_report_prioritized_candidates') }}</p>
                </div>
            </div>
        </div>
    </section>
@endsection
