@extends('layouts.app')

@section('header')
    <link href="{{ asset('styles/css/static_pages.css') }}" rel="stylesheet">
@endsection

@section('content')
    <section class="static-hero">
        <div class="container">
            <div class="static-hero-content">
                <h1 class="static-title">{{ __('static_pages.Our Services') }}</h1>
                <p class="static-subtitle">{{ __('static_pages.Smart and advanced solutions for all recruitment and resume screening needs') }}</p>
            </div>
        </div>
    </section>

    <section class="services-overview">
        <div class="container">
            <div class="services-stats">
                <div class="stat-item">
                    <div class="stat-number">50,000+</div>
                    <div class="stat-label">{{ __('static_pages.Resume screened') }}</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">95%</div>
                    <div class="stat-label">{{ __('static_pages.Analysis accuracy') }}</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">500+</div>
                    <div class="stat-label">{{ __('static_pages.Companies trust us') }}</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">10x</div>
                    <div class="stat-label">{{ __('static_pages.Recruitment process acceleration') }}</div>
                </div>
            </div>
        </div>
    </section>

    <section class="main-services">
        <div class="container">
            <h2 class="section-title">{{ __('static_pages.Our Main Services') }}</h2>
            <div class="services-grid">
                <div class="service-card featured">
                    <div class="service-icon">🤖</div>
                    <h3>{{ __('static_pages.Smart Resume Screening') }}</h3>
                    <p>{{ __('static_pages.Advanced analysis of resumes using artificial intelligence with detailed reports and accurate evaluation scores') }}</p>
                    <ul class="service-features">
                        <li>{{ __('static_pages.Screen hundreds of resumes in minutes') }}</li>
                        <li>{{ __('static_pages.Analyze skills and experiences') }}</li>
                        <li>{{ __('static_pages.Rank candidates by suitability') }}</li>
                        <li>{{ __('static_pages.Detailed exportable reports') }}</li>
                    </ul>
                    <div class="service-pricing">{{ __('static_pages.Starting from 0.5 SAR per resume') }}</div>
                    <a href="{{ route('upload.form') }}" class="btn btn-primary">{{ __('static_pages.Start Now') }}</a>
                </div>

                <div class="service-card">
                    <div class="service-icon">📊</div>
                    <h3>{{ __('static_pages.Advanced Analytics') }}</h3>
                    <p>{{ __('static_pages.Deep analysis of the job market and matching job requirements with available skills in the market') }}</p>
                    <ul class="service-features">
                        <li>{{ __('static_pages.Market trend analysis') }}</li>
                        <li>{{ __('static_pages.Expected salary rates') }}</li>
                        <li>{{ __('static_pages.Rare skills analysis') }}</li>
                        <li>{{ __('static_pages.Market comparison reports') }}</li>
                    </ul>
                    <div class="service-pricing">{{ __('static_pages.Custom packages') }}</div>
                    <a href="{{ route('contact') }}" class="btn btn-secondary">{{ __('static_pages.Inquire Now') }}</a>
                </div>

                <div class="service-card">
                    <div class="service-icon">🎯</div>
                    <h3>{{ __('static_pages.Specialized Smart Filter') }}</h3>
                    <p>{{ __('static_pages.Precise filtering of candidates using custom criteria and specific job requirements for your company') }}</p>
                    <ul class="service-features">
                        <li>{{ __('static_pages.Custom evaluation criteria') }}</li>
                        <li>{{ __('static_pages.Multi-level filtering') }}</li>
                        <li>{{ __('static_pages.Automatic exclusion of unsuitable candidates') }}</li>
                        <li>{{ __('static_pages.Recommendation of best candidates') }}</li>
                    </ul>
                    <div class="service-pricing">{{ __('static_pages.Based on requirements') }}</div>
                    <a href="{{ route('contact') }}" class="btn btn-secondary">{{ __('static_pages.Contact Us') }}</a>
                </div>
            </div>
        </div>
    </section>

    <section class="specialized-services">
        <div class="container">
            <h2 class="section-title">{{ __('static_pages.Specialized Services') }}</h2>
            <div class="specialized-grid">
                <div class="specialized-item">
                    <div class="specialized-icon">🏢</div>
                    <h3>{{ __('static_pages.For Large Companies') }}</h3>
                    <p>{{ __('static_pages.Enterprise solutions with full integration with human resource management systems') }}</p>
                    <ul>
                        <li>{{ __('static_pages.Integration with HR systems') }}</li>
                        <li>{{ __('static_pages.Custom programming interfaces') }}</li>
                        <li>{{ __('static_pages.Dedicated technical support') }}</li>
                        <li>{{ __('static_pages.Team training') }}</li>
                    </ul>
                </div>

                <div class="specialized-item">
                    <div class="specialized-icon">🎓</div>
                    <h3>{{ __('static_pages.For Universities and Training Centers') }}</h3>
                    <p>{{ __('static_pages.Comprehensive evaluation for university graduates and trainees to help them in the job market') }}</p>
                    <ul>
                        <li>{{ __('static_pages.Analyze strengths and weaknesses') }}</li>
                        <li>{{ __('static_pages.Development guidance') }}</li>
                        <li>{{ __('static_pages.Comparison with market requirements') }}</li>
                        <li>{{ __('static_pages.Reports for educational institutions') }}</li>
                    </ul>
                </div>

                <div class="specialized-item">
                    <div class="specialized-icon">💼</div>
                    <h3>{{ __('static_pages.For Recruitment Companies') }}</h3>
                    <p>{{ __('static_pages.Advanced tools for recruitment companies to accelerate search and selection processes') }}</p>
                    <ul>
                        <li>{{ __('static_pages.Batch resume processing') }}</li>
                        <li>{{ __('static_pages.Specialized evaluation templates') }}</li>
                        <li>{{ __('static_pages.Classification by sectors') }}</li>
                        <li>{{ __('static_pages.Preferential prices for quantities') }}</li>
                    </ul>
                </div>

                <div class="specialized-item">
                    <div class="specialized-icon">🏛️</div>
                    <h3>{{ __('static_pages.For Government Sector') }}</h3>
                    <p>{{ __('static_pages.Solutions compliant with government sector requirements and high security standards') }}</p>
                    <ul>
                        <li>{{ __('static_pages.Full compliance with regulations') }}</li>
                        <li>{{ __('static_pages.Enhanced security and privacy') }}</li>
                        <li>{{ __('static_pages.Detailed reports for recruitment committees') }}</li>
                        <li>{{ __('static_pages.Full Arabic language support') }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <section class="service-process">
        <div class="container">
            <h2 class="section-title">{{ __('static_pages.How We Work') }}</h2>
            <div class="process-timeline">
                <div class="timeline-item">
                    <div class="timeline-number">1</div>
                    <div class="timeline-content">
                        <h3>{{ __('static_pages.Define Requirements') }}</h3>
                        <p>{{ __('static_pages.We understand your needs and specific job requirements') }}</p>
                    </div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-number">2</div>
                    <div class="timeline-content">
                        <h3>{{ __('static_pages.Upload Resumes') }}</h3>
                        <p>{{ __('static_pages.Upload resume collection in different formats') }}</p>
                    </div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-number">3</div>
                    <div class="timeline-content">
                        <h3>{{ __('static_pages.Smart Analysis') }}</h3>
                        <p>{{ __('static_pages.Advanced processing using artificial intelligence') }}</p>
                    </div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-number">4</div>
                    <div class="timeline-content">
                        <h3>{{ __('static_pages.Results and Reports') }}</h3>
                        <p>{{ __('static_pages.Comprehensive reports with candidate ranking and recommendations') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="service-benefits">
        <div class="container">
            <h2 class="section-title">{{ __('static_pages.Why Choose Masar?') }}</h2>
            <div class="benefits-grid">
                <div class="benefit-item">
                    <div class="benefit-icon">⚡</div>
                    <h3>{{ __('static_pages.Superior Speed') }}</h3>
                    <p>{{ __('static_pages.Save 80% of time spent manually screening resumes') }}</p>
                </div>
                <div class="benefit-item">
                    <div class="benefit-icon">🎯</div>
                    <h3>{{ __('static_pages.High Accuracy') }}</h3>
                    <p>{{ __('static_pages.Advanced algorithms ensure analysis accuracy up to 95%') }}</p>
                </div>
                <div class="benefit-item">
                    <div class="benefit-icon">💰</div>
                    <h3>{{ __('static_pages.Cost Savings') }}</h3>
                    <p>{{ __('static_pages.Reduce recruitment costs by up to 60%') }}</p>
                </div>
                <div class="benefit-item">
                    <div class="benefit-icon">🔒</div>
                    <h3>{{ __('static_pages.Advanced Security') }}</h3>
                    <p>{{ __('static_pages.Comprehensive data protection with no file retention') }}</p>
                </div>
                <div class="benefit-item">
                    <div class="benefit-icon">🌍</div>
                    <h3>{{ __('static_pages.Multi-language Support') }}</h3>
                    <p>{{ __('static_pages.Process resumes in Arabic and English') }}</p>
                </div>
                <div class="benefit-item">
                    <div class="benefit-icon">📱</div>
                    <h3>{{ __('static_pages.Ease of Use') }}</h3>
                    <p>{{ __('static_pages.Simple and easy interface suitable for all user levels') }}</p>
                </div>
            </div>
        </div>
    </section>

    <section class="pricing-preview" id="pricing-preview">
        <div class="container">
            <h2 class="section-title">{{ __('static_pages.Pricing Plans') }}</h2>
            <div class="pricing-cards">
                <div class="pricing-card">
                    <h3>{{ __('static_pages.Basic Plan') }}</h3>
                    <div class="price">
                        <span class="currency">{{ __('static_pages.SAR') }}</span>
                        <span class="amount">199</span>
                        <span class="period">{{ __('static_pages./monthly') }}</span>
                    </div>
                    <ul class="features">
                        <li>{{ __('static_pages.Up to 500 resumes monthly') }}</li>
                        <li>{{ __('static_pages.Basic reports') }}</li>
                        <li>{{ __('static_pages.Email support') }}</li>
                        <li>{{ __('static_pages.PDF export') }}</li>
                    </ul>
                    <a href="{{ route('contact') }}" class="btn btn-outline">{{ __('static_pages.Choose Plan') }}</a>
                </div>

                <div class="pricing-card featured">
                    <div class="popular-badge">{{ __('static_pages.Most Popular') }}</div>
                    <h3>{{ __('static_pages.Advanced Plan') }}</h3>
                    <div class="price">
                        <span class="currency">{{ __('static_pages.SAR') }}</span>
                        <span class="amount">499</span>
                        <span class="period">{{ __('static_pages./monthly') }}</span>
                    </div>
                    <ul class="features">
                        <li>{{ __('static_pages.Up to 2000 resumes monthly') }}</li>
                        <li>{{ __('static_pages.Detailed reports') }}</li>
                        <li>{{ __('static_pages.Phone and email support') }}</li>
                        <li>{{ __('static_pages.Multi-format export') }}</li>
                        <li>{{ __('static_pages.Advanced analytics') }}</li>
                    </ul>
                    <a href="{{ route('contact') }}" class="btn btn-primary">{{ __('static_pages.Choose Plan') }}</a>
                </div>

                <div class="pricing-card">
                    <h3>{{ __('static_pages.Enterprise Plan') }}</h3>
                    <div class="price">
                        <span class="currency">{{ __('static_pages.SAR') }}</span>
                        <span class="amount">1299</span>
                        <span class="period">{{ __('static_pages./monthly') }}</span>
                    </div>
                    <ul class="features">
                        <li>{{ __('static_pages.Unlimited resumes') }}</li>
                        <li>{{ __('static_pages.Integration with HR systems') }}</li>
                        <li>{{ __('static_pages.Dedicated support 24/7') }}</li>
                        <li>{{ __('static_pages.Custom programming interfaces') }}</li>
                        <li>{{ __('static_pages.Team training') }}</li>
                    </ul>
                    <a href="{{ route('contact') }}" class="btn btn-outline">{{ __('static_pages.Contact Us') }}</a>
                </div>
            </div>
            <p class="pricing-note">{{ __('static_pages.All plans include a free 14-day trial') }}</p>
        </div>
    </section>

    <section class="service-cta">
        <div class="container">
            <div class="cta-content">
                <h2>{{ __('static_pages.Ready to try Masar?') }}</h2>
                <p>{{ __('static_pages.Start now and discover how Masar can improve the recruitment process in your company') }}</p>
                <div class="cta-buttons">
                    <a href="{{ route('upload.form') }}" class="btn btn-primary">{{ __('static_pages.Try for Free') }}</a>
                    <a href="{{ route('contact') }}" class="btn btn-secondary">{{ __('static_pages.Talk to Expert') }}</a>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
    <script src="{{ asset('styles/js/static_pages.js') }}"></script>
@endsection
