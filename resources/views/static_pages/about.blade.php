@extends('layouts.app')

@section('header')
    <link href="{{ asset('styles/css/static_pages.css') }}" rel="stylesheet">
@endsection

@section('content')
    <section class="static-hero">
        <div class="container">
            <div class="static-hero-content">
                <h1 class="static-title">{{ __('static_pages.More About Us') }}</h1>
                <p class="static-subtitle">{{ __('static_pages.We are a team of specialists in artificial intelligence and recruitment, seeking a real revolution in the world of talent selection') }}</p>
            </div>
        </div>
    </section>

    <section class="about-intro">
        <div class="container">
            <div class="intro-content">
                <div class="intro-text">
                    <h2>{{ __('static_pages.Masar Story') }}</h2>
                    <p>{{ __('static_pages.In a world where growth is accelerating and companies\' needs for suitable talents are increasing, the idea of "Masar" was born from a simple and ambitious vision: making the recruitment process smarter, more efficient and accurate.') }}</p>

                    <p>{{ __('static_pages.Our journey began when we noticed that companies face major challenges in manually screening thousands of resumes, which takes a long time and carries the risk of losing suitable candidates amid this massive amount of data.') }}</p>

                    <p>{{ __('static_pages.Today, "Masar" stands as an advanced solution that combines the power of artificial intelligence with a deep understanding of local labor market needs, providing an unparalleled recruitment experience.') }}</p>
                </div>
                <div class="intro-image">
                    <div class="image-placeholder">
                        <div class="placeholder-icon">🚀</div>
                        <p>{{ __('static_pages.Development and Innovation Journey') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="mission-vision">
        <div class="container">
            <div class="mission-vision-grid">
                <div class="mission-card">
                    <div class="card-icon">🎯</div>
                    <h3>{{ __('static_pages.Our Mission') }}</h3>
                    <p>{{ __('static_pages.Empowering companies and institutions to make smarter and faster recruitment decisions through advanced artificial intelligence technologies, while ensuring fairness and transparency in selection processes.') }}</p>
                </div>
                <div class="vision-card">
                    <div class="card-icon">🌟</div>
                    <h3>{{ __('static_pages.Our Vision') }}</h3>
                    <p>{{ __('static_pages.To become the first and most reliable choice for all companies in the region for screening and analyzing resumes, and to contribute to building a more efficient and fair job market.') }}</p>
                </div>
            </div>
        </div>
    </section>

    <section class="our-values">
        <div class="container">
            <h2 class="section-title">{{ __('static_pages.Our Core Values') }}</h2>
            <div class="values-grid">
                <div class="value-item">
                    <div class="value-icon">🔍</div>
                    <h3>{{ __('static_pages.Accuracy and Quality') }}</h3>
                    <p>{{ __('static_pages.We are committed to the highest standards of accuracy in analysis and continuously strive to improve the quality of our results') }}</p>
                </div>
                <div class="value-item">
                    <div class="value-icon">🤝</div>
                    <h3>{{ __('static_pages.Trust and Transparency') }}</h3>
                    <p>{{ __('static_pages.We build relationships based on mutual trust and complete clarity in all our dealings') }}</p>
                </div>
                <div class="value-item">
                    <div class="value-icon">⚡</div>
                    <h3>{{ __('static_pages.Continuous Innovation') }}</h3>
                    <p>{{ __('static_pages.We keep up with the latest technological developments and continuously develop our solutions to meet our clients\' needs') }}</p>
                </div>
                <div class="value-item">
                    <div class="value-icon">🔒</div>
                    <h3>{{ __('static_pages.Security and Privacy') }}</h3>
                    <p>{{ __('static_pages.We put the protection of our clients\' and candidates\' data at the forefront and apply the highest security standards') }}</p>
                </div>
                <div class="value-item">
                    <div class="value-icon">🌍</div>
                    <h3>{{ __('static_pages.Positive Impact') }}</h3>
                    <p>{{ __('static_pages.We strive to create a positive impact on the job market and improve the recruitment experience for everyone') }}</p>
                </div>
                <div class="value-item">
                    <div class="value-icon">📈</div>
                    <h3>{{ __('static_pages.Excellence in Service') }}</h3>
                    <p>{{ __('static_pages.We provide exceptional customer service and put our clients\' satisfaction at the top of our priorities') }}</p>
                </div>
            </div>
        </div>
    </section>

    <section class="our-team">
        <div class="container">
            <h2 class="section-title">{{ __('static_pages.Team') }}</h2>
            <p class="section-subtitle">{{ __('static_pages.A group of experts and specialists in various fields') }}</p>

            <div class="team-categories">
                <div class="team-category">
                    <h3>{{ __('static_pages.Development and Technology Team') }}</h3>
                    <p>{{ __('static_pages.Engineers specialized in artificial intelligence, natural language processing and systems development') }}</p>
                    <div class="team-stats">
                        <span class="stat">{{ __('static_pages.10+ developers') }}</span>
                        <span class="stat">{{ __('static_pages.15+ years average experience') }}</span>
                        <span class="stat">{{ __('static_pages.5+ AI experts') }}</span>
                    </div>
                </div>

                <div class="team-category">
                    <h3>{{ __('static_pages.Human Resources Team') }}</h3>
                    <p>{{ __('static_pages.Experts in recruitment and talent management who understand local and global market challenges') }}</p>
                    <div class="team-stats">
                        <span class="stat">{{ __('static_pages.8+ HR experts') }}</span>
                        <span class="stat">{{ __('static_pages.20+ years combined experience') }}</span>
                        <span class="stat">{{ __('static_pages.500+ companies dealt with') }}</span>
                    </div>
                </div>

                <div class="team-category">
                    <h3>{{ __('static_pages.Customer Service Team') }}</h3>
                    <p>{{ __('static_pages.Specialists in providing support and ensuring the best customer experience around the clock') }}</p>
                    <div class="team-stats">
                        <span class="stat">{{ __('static_pages.6+ consultants') }}</span>
                        <span class="stat">{{ __('static_pages.24/7 support') }}</span>
                        <span class="stat">{{ __('static_pages.98% customer satisfaction') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="our-achievements">
        <div class="container">
            <h2 class="section-title">{{ __('static_pages.Our Achievements') }}</h2>
            <div class="achievements-timeline">
                <div class="achievement-item">
                    <div class="achievement-year">2023</div>
                    <div class="achievement-content">
                        <h3>{{ __('static_pages.Masar Launch') }}</h3>
                        <p>{{ __('static_pages.Official platform launch after two years of research and development') }}</p>
                    </div>
                </div>
                <div class="achievement-item">
                    <div class="achievement-year">2024</div>
                    <div class="achievement-content">
                        <h3>{{ __('static_pages.10,000 resumes') }}</h3>
                        <p>{{ __('static_pages.We reached processing our first 10,000 resumes successfully') }}</p>
                    </div>
                </div>
                <div class="achievement-item">
                    <div class="achievement-year">2024</div>
                    <div class="achievement-content">
                        <h3>{{ __('static_pages.100+ companies') }}</h3>
                        <p>{{ __('static_pages.More than 100 companies joined our distinguished clients list') }}</p>
                    </div>
                </div>
                <div class="achievement-item">
                    <div class="achievement-year">2025</div>
                    <div class="achievement-content">
                        <h3>{{ __('static_pages.Regional Expansion') }}</h3>
                        <p>{{ __('static_pages.Beginning of expansion to serve companies in Gulf countries') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="technology-stack">
        <div class="container">
            <h2 class="section-title">{{ __('static_pages.Technologies Used') }}</h2>
            <div class="tech-categories">
                <div class="tech-category">
                    <h3>{{ __('static_pages.Artificial Intelligence') }}</h3>
                    <div class="tech-items">
                        <span class="tech-item">{{ __('static_pages.Natural Language Processing') }}</span>
                        <span class="tech-item">{{ __('static_pages.Machine Learning') }}</span>
                        <span class="tech-item">{{ __('static_pages.Neural Networks') }}</span>
                        <span class="tech-item">{{ __('static_pages.Semantic Analysis') }}</span>
                    </div>
                </div>
                <div class="tech-category">
                    <h3>{{ __('static_pages.Security and Protection') }}</h3>
                    <div class="tech-items">
                        <span class="tech-item">{{ __('static_pages.AES-256 encryption') }}</span>
                        <span class="tech-item">{{ __('static_pages.Server protection') }}</span>
                        <span class="tech-item">{{ __('static_pages.Secure backup') }}</span>
                        <span class="tech-item">{{ __('static_pages.Continuous monitoring') }}</span>
                    </div>
                </div>
                <div class="tech-category">
                    <h3>{{ __('static_pages.Infrastructure') }}</h3>
                    <div class="tech-items">
                        <span class="tech-item">{{ __('static_pages.Cloud computing') }}</span>
                        <span class="tech-item">{{ __('static_pages.Parallel processing') }}</span>
                        <span class="tech-item">{{ __('static_pages.Advanced databases') }}</span>
                        <span class="tech-item">{{ __('static_pages.RESTful APIs') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="join-us">
        <div class="container">
            <div class="join-content">
                <h2>{{ __('static_pages.Join the Success Journey') }}</h2>
                <p>{{ __('static_pages.Are you looking for smart solutions to improve the recruitment process in your company? We are here to help you achieve your goals') }}</p>
                <div class="join-stats">
                    <div class="join-stat">
                        <div class="stat-number">500+</div>
                        <div class="stat-label">{{ __('static_pages.happy companies') }}</div>
                    </div>
                    <div class="join-stat">
                        <div class="stat-number">50,000+</div>
                        <div class="stat-label">{{ __('static_pages.resumes screened') }}</div>
                    </div>
                    <div class="join-stat">
                        <div class="stat-number">95%</div>
                        <div class="stat-label">{{ __('static_pages.results accuracy') }}</div>
                    </div>
                    <div class="join-stat">
                        <div class="stat-number">24/7</div>
                        <div class="stat-label">{{ __('static_pages.technical support') }}</div>
                    </div>
                </div>
                <div class="join-buttons">
                    <a href="{{ route('upload.form') }}" class="btn btn-primary">{{ __('static_pages.Try for Free') }}</a>
                    <a href="{{ route('contact') }}" class="btn btn-secondary">{{ __('static_pages.Contact Us') }}</a>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
    <script src="{{ asset('styles/js/static_pages.js') }}"></script>
@endsection
