@extends('layouts.app')

@section('header')
    <link href="{{ asset('styles/css/static_pages.css') }}" rel="stylesheet">
@endsection

@section('content')
    <section class="static-hero">
        <div class="container">
            <div class="static-hero-content">
                <h1 class="static-title">{{ __('static_pages.Frequently Asked Questions') }}</h1>
                <p class="static-subtitle">{{ __('static_pages.Answers to the most frequently asked questions about Masar resume screening platform') }}</p>
            </div>
        </div>
    </section>

    <section class="faq-content">
        <div class="container">
            <div class="faq-search">
                <input type="text" placeholder="{{ __('static_pages.Search in FAQ...') }}" class="search-input">
                <button class="search-btn">🔍</button>
            </div>

            <div class="faq-categories">
                <button class="category-btn active" data-category="all">{{ __('static_pages.All Questions') }}</button>
                <button class="category-btn" data-category="general">{{ __('static_pages.General') }}</button>
                <button class="category-btn" data-category="technical">{{ __('static_pages.Technical') }}</button>
                <button class="category-btn" data-category="pricing">{{ __('static_pages.Pricing') }}</button>
                <button class="category-btn" data-category="security">{{ __('static_pages.Security') }}</button>
            </div>

            <div class="faq-sections">
                <!-- General Questions -->
                <div class="faq-section" data-category="general">
                    <h2 class="faq-section-title">{{ __('static_pages.General Questions') }}</h2>

                    <div class="faq-item">
                        <div class="faq-question">
                            <h3>{{ __('static_pages.What is Masar platform and how does it work?') }}</h3>
                            <span class="faq-toggle">+</span>
                        </div>
                        <div class="faq-answer">
                            <p>{{ __('static_pages.Masar is a smart platform that uses artificial intelligence technologies to screen and analyze resumes. The platform uploads hundreds of resumes, analyzes them automatically, and matches them with job requirements to provide a comprehensive report of the best candidates ranked by suitability.') }}</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            <h3>{{ __('static_pages.How long does the resume screening process take?') }}</h3>
                            <span class="faq-toggle">+</span>
                        </div>
                        <div class="faq-answer">
                            <p>{{ __('static_pages.Usually the process of screening hundreds of resumes takes only 5-10 minutes') }}</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            <h3>{{ __('static_pages.What file types are supported?') }}</h3>
                            <span class="faq-toggle">+</span>
                        </div>
                        <div class="faq-answer">
                            <p>{{ __('static_pages.The platform supports the following formats: PDF, DOC, DOCX. These are the most common and used resume formats in the job market.') }}</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            <h3>{{ __('static_pages.Can resumes in English be screened?') }}</h3>
                            <span class="faq-toggle">+</span>
                        </div>
                        <div class="faq-answer">
                            <p>{{ __('static_pages.Absolutely, the platform supports screening resumes in both Arabic and English') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Technical Questions -->
                <div class="faq-section" data-category="technical">
                    <h2 class="faq-section-title">{{ __('static_pages.Technical Questions') }}</h2>

                    <div class="faq-item">
                        <div class="faq-question">
                            <h3>{{ __('static_pages.What is the accuracy of the analysis results?') }}</h3>
                            <span class="faq-toggle">+</span>
                        </div>
                        <div class="faq-answer">
                            <p>{{ __('static_pages.The analysis system accuracy reaches 95% thanks to using advanced machine learning algorithms. The system continuously improves through learning from data and regular updates.') }}</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            <h3>{{ __('static_pages.How are skills and experiences analyzed?') }}</h3>
                            <span class="faq-toggle">+</span>
                        </div>
                        <div class="faq-answer">
                            <p>{{ __('static_pages.The system uses Natural Language Processing (NLP) techniques to extract and analyze skills and experiences from texts, then compares them with job requirements and calculates the match score for each candidate.') }}</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            <h3>{{ __('static_pages.Can evaluation criteria be customized?') }}</h3>
                            <span class="faq-toggle">+</span>
                        </div>
                        <div class="faq-answer">
                            <p>{{ __('static_pages.Yes, evaluation criteria can be customized according to each job requirements. You can specify required skills, years of experience, educational qualifications, and any other criteria specific to the job.') }}</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            <h3>{{ __('static_pages.What if the resume is unclear or has complex formatting?') }}</h3>
                            <span class="faq-toggle">+</span>
                        </div>
                        <div class="faq-answer">
                            <p>{{ __('static_pages.The system is designed to handle different resume formats. In case of difficulty reading a specific file, it will be marked in the report with clarification of the problem type.') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Pricing Questions -->
                <div class="faq-section" data-category="pricing">
                    <h2 class="faq-section-title">{{ __('static_pages.Pricing and Plans') }}</h2>

                    <div class="faq-item">
                        <div class="faq-question">
                            <h3>{{ __('static_pages.Is there a free trial version available?') }}</h3>
                            <span class="faq-toggle">+</span>
                        </div>
                        <div class="faq-answer">
                            <p>{{ __('static_pages.Yes, we provide free screening for a limited number of resumes so you can try the platform and evaluate the quality of results before subscribing to paid plans.') }}</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            <h3>{{ __('static_pages.What are the available plans?') }}</h3>
                            <span class="faq-toggle">+</span>
                        </div>
                        <div class="faq-answer">
                            <p>{{ __('static_pages.We offer several plans to suit different needs: Basic plan for small companies, Advanced plan for medium companies, and Enterprise plan for large companies and government institutions.') }}</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            <h3>{{ __('static_pages.Are there discounts for intensive use?') }}</h3>
                            <span class="faq-toggle">+</span>
                        </div>
                        <div class="faq-answer">
                            <p>{{ __('static_pages.Yes, we offer progressive discounts for companies that use the platform intensively. The more resumes screened monthly, the lower the price per screening.') }}</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            <h3>{{ __('static_pages.Can I cancel the subscription at any time?') }}</h3>
                            <span class="faq-toggle">+</span>
                        </div>
                        <div class="faq-answer">
                            <p>{{ __('static_pages.Yes, you can cancel the subscription at any time without any additional fees. The subscription will remain active until the end of the paid period then will be automatically stopped.') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Security Questions -->
                <div class="faq-section" data-category="security">
                    <h2 class="faq-section-title">{{ __('static_pages.Security and Privacy') }}</h2>

                    <div class="faq-item">
                        <div class="faq-question">
                            <h3>{{ __('static_pages.Is the data safe and protected?') }}</h3>
                            <span class="faq-toggle">+</span>
                        </div>
                        <div class="faq-answer">
                            <p>{{ __('static_pages.Yes, we use the latest encryption technologies to ensure complete protection of candidate data') }}</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            <h3>{{ __('static_pages.Do you keep copies of resumes?') }}</h3>
                            <span class="faq-toggle">+</span>
                        </div>
                        <div class="faq-answer">
                            <p>{{ __('static_pages.No, we do not keep any copies of resumes after the analysis process is completed. All files are immediately deleted from servers after producing the final report.') }}</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            <h3>{{ __('static_pages.Do you share data with third parties?') }}</h3>
                            <span class="faq-toggle">+</span>
                        </div>
                        <div class="faq-answer">
                            <p>{{ __('static_pages.No, we do not share any data with third parties for commercial or marketing purposes. Data is used only to provide the requested service and remains completely confidential.') }}</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            <h3>{{ __('static_pages.What security guarantees are applied?') }}</h3>
                            <span class="faq-toggle">+</span>
                        </div>
                        <div class="faq-answer">
                            <p>{{ __('static_pages.We apply several security layers: data encryption, advanced firewalls, continuous system monitoring, secure backup, and strict access control to data.') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Additional Questions -->
                <div class="faq-section" data-category="general">
                    <h2 class="faq-section-title">{{ __('static_pages.Additional Questions') }}</h2>

                    <div class="faq-item">
                        <div class="faq-question">
                            <h3>{{ __('static_pages.How can results be exported?') }}</h3>
                            <span class="faq-toggle">+</span>
                        </div>
                        <div class="faq-answer">
                            <p>{{ __('static_pages.Reports can be exported in different formats: PDF for viewing and printing, Excel for additional analysis, or JSON for integration with other systems.') }}</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            <h3>{{ __('static_pages.Can the platform be integrated with HR management systems?') }}</h3>
                            <span class="faq-toggle">+</span>
                        </div>
                        <div class="faq-answer">
                            <p>{{ __('static_pages.Yes, we provide APIs for integration with popular HR management systems. Companies can connect the platform with their existing systems.') }}</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            <h3>{{ __('static_pages.What type of technical support is provided?') }}</h3>
                            <span class="faq-toggle">+</span>
                        </div>
                        <div class="faq-answer">
                            <p>{{ __('static_pages.We provide comprehensive technical support via email, phone and live chat. The support team is available during official working hours and responds to inquiries within 24 hours.') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="faq-cta">
                <h3>{{ __('static_pages.Didn\'t find the answer to your question?') }}</h3>
                <p>{{ __('static_pages.Contact us and we will be happy to help') }}</p>
                <a href="{{ route('contact') }}" class="btn btn-primary">{{ __('static_pages.Contact Us') }}</a>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
    <script src="{{ asset('styles/js/static_pages.js') }}"></script>
@endsection
