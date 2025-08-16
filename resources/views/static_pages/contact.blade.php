@extends('layouts.app')

@section('header')
    <link href="{{ asset('styles/css/static_pages.css') }}" rel="stylesheet">
@endsection

@section('content')
    <section class="static-hero">
        <div class="container">
            <div class="static-hero-content">
                <h1 class="static-title">{{ __('static_pages.Contact Us') }}</h1>
                <p class="static-subtitle">{{ __('static_pages.We are here to help and answer all your inquiries about Masar platform') }}</p>
            </div>
        </div>
    </section>

    <section class="contact-section">
        <div class="container">
            <div class="contact-content">
                <div class="contact-info">
                    <h2>{{ __('static_pages.Contact Information') }}</h2>
                    <div class="contact-cards">
                        <div class="contact-card">
                            <div class="contact-icon">📧</div>
                            <h3>{{ __('static_pages.Email') }}</h3>
                            <p>info@masar.com</p>
                            <p>support@masar.com</p>
                        </div>
                        <div class="contact-card">
                            <div class="contact-icon">📞</div>
                            <h3>{{ __('static_pages.Phone') }}</h3>
                            <p>+966 11 234 5678</p>
                            <p>+966 50 123 4567</p>
                        </div>
                        <div class="contact-card">
                            <div class="contact-icon">📍</div>
                            <h3>{{ __('static_pages.Address') }}</h3>
                            <p>{{ __('static_pages.Riyadh, Saudi Arabia') }}</p>
                            <p>{{ __('static_pages.King Fahd District, Prince Mohammed bin Abdulaziz Street') }}</p>
                        </div>
                        <div class="contact-card">
                            <div class="contact-icon">🕒</div>
                            <h3>{{ __('static_pages.Working Hours') }}</h3>
                            <p>{{ __('static_pages.Sunday - Thursday: 9:00 AM - 6:00 PM') }}</p>
                            <p>{{ __('static_pages.Friday: Closed') }}</p>
                        </div>
                    </div>
                </div>

                <div class="contact-form-section">
                    <h2>{{ __('static_pages.Send us a message') }}</h2>
                    <form class="contact-form">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="name">{{ __('static_pages.Full Name') }}</label>
                                <input type="text" id="name" name="name" required>
                            </div>
                            <div class="form-group">
                                <label for="email">{{ __('static_pages.Email') }}</label>
                                <input type="email" id="email" name="email" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="phone">{{ __('static_pages.Phone Number') }}</label>
                                <input type="tel" id="phone" name="phone">
                            </div>
                            <div class="form-group">
                                <label for="subject">{{ __('static_pages.Subject') }}</label>
                                <select id="subject" name="subject" required>
                                    <option value="">{{ __('static_pages.Choose Subject') }}</option>
                                    <option value="general">{{ __('static_pages.General Inquiry') }}</option>
                                    <option value="technical">{{ __('static_pages.Technical Support') }}</option>
                                    <option value="billing">{{ __('static_pages.Billing and Payment') }}</option>
                                    <option value="partnership">{{ __('static_pages.Partnerships') }}</option>
                                    <option value="other">{{ __('static_pages.Other') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="message">{{ __('static_pages.Message') }}</label>
                            <textarea id="message" name="message" rows="6" required placeholder="{{ __('static_pages.Write your message here...') }}"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">{{ __('static_pages.Send Message') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <section class="faq-preview">
        <div class="container">
            <h2 class="section-title">{{ __('static_pages.Frequently Asked Questions') }}</h2>
            <p class="section-subtitle">{{ __('static_pages.You may find the answer to your question here before contacting us') }}</p>
            <div class="faq-items">
                <div class="faq-item">
                    <h3>{{ __('static_pages.How long does the resume screening process take?') }}</h3>
                    <p>{{ __('static_pages.Usually the process of screening hundreds of resumes takes only 5-10 minutes') }}</p>
                </div>
                <div class="faq-item">
                    <h3>{{ __('static_pages.Is the data safe and protected?') }}</h3>
                    <p>{{ __('static_pages.Yes, we use the latest encryption technologies to ensure complete protection of candidate data') }}</p>
                </div>
                <div class="faq-item">
                    <h3>{{ __('static_pages.Can resumes in English be screened?') }}</h3>
                    <p>{{ __('static_pages.Absolutely, the platform supports screening resumes in both Arabic and English') }}</p>
                </div>
            </div>
            <a href="{{ route('faq') }}" class="btn btn-secondary">{{ __('static_pages.More Questions') }}</a>
        </div>
    </section>
@endsection

@section('scripts')
    <script src="{{ asset('styles/js/static_pages.js') }}"></script>
@endsection
