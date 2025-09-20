@extends('layouts.app')

@section('header')
    <link href="{{ asset('styles/css/static_pages.css') }}" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
                            <p>support@massar.biz</p>
                        </div>
                        <div class="contact-card">
                            <div class="contact-icon">📞</div>
                            <h3>{{ __('static_pages.Phone') }}</h3>
                            <p>+968 95 160 789</p>
                        </div>
                        <div class="contact-card">
                            <div class="contact-icon">📍</div>
                            <h3>{{ __('static_pages.Address') }}</h3>
                            <p>{{ __('static_pages. Muscat, Al-Khoudh 1334, OM') }}</p>
                        </div>
                        <div class="contact-card">
                            <div class="contact-icon">🕑</div>
                            <h3>{{ __('static_pages.Working Hours') }}</h3>
                            <p>{{ __('static_pages.All The Time') }}</p>
                        </div>
                    </div>
                </div>

                <div class="contact-form-section">
                    <h2>{{ __('static_pages.Send us a message') }}</h2>
                    <form class="contact-form" method="POST" action="{{ route('contact.submit') }}">
                        @csrf
                        <div class="form-row">
                            <div class="form-group">
                                <label for="name">{{ __('static_pages.Full Name') }} <span class="required">*</span></label>
                                <input
                                    type="text"
                                    id="name"
                                    name="name"
                                    value="{{ old('name') }}"
                                    class="@error('name') error @enderror"
                                    required
                                >
                                @error('name')
                                <span class="field-error">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="email">{{ __('static_pages.Email') }} <span class="required">*</span></label>
                                <input
                                    type="email"
                                    id="email"
                                    name="email"
                                    value="{{ old('email') }}"
                                    class="@error('email') error @enderror"
                                    required
                                >
                                @error('email')
                                <span class="field-error">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="phone">{{ __('static_pages.Phone Number') }}</label>
                                <input
                                    type="tel"
                                    id="phone"
                                    name="phone"
                                    value="{{ old('phone') }}"
                                    class="@error('phone') error @enderror"
                                >
                                @error('phone')
                                <span class="field-error">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="subject">{{ __('static_pages.Subject') }} <span class="required">*</span></label>
                                <select
                                    id="subject"
                                    name="subject"
                                    class="@error('subject') error @enderror"
                                    required
                                >
                                    <option value="">{{ __('static_pages.Choose Subject') }}</option>
                                    <option value="general" {{ old('subject') == 'general' ? 'selected' : '' }}>
                                        {{ __('static_pages.General Inquiry') }}
                                    </option>
                                    <option value="technical" {{ old('subject') == 'technical' ? 'selected' : '' }}>
                                        {{ __('static_pages.Technical Support') }}
                                    </option>
                                    <option value="billing" {{ old('subject') == 'billing' ? 'selected' : '' }}>
                                        {{ __('static_pages.Billing and Payment') }}
                                    </option>
                                    <option value="partnership" {{ old('subject') == 'partnership' ? 'selected' : '' }}>
                                        {{ __('static_pages.Partnerships') }}
                                    </option>
                                    <option value="other" {{ old('subject') == 'other' ? 'selected' : '' }}>
                                        {{ __('static_pages.Other') }}
                                    </option>
                                </select>
                                @error('subject')
                                <span class="field-error">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="message">{{ __('static_pages.Message') }} <span class="required">*</span></label>
                            <textarea
                                id="message"
                                name="message"
                                rows="6"
                                class="@error('message') error @enderror"
                                required
                                placeholder="{{ __('static_pages.Write your message here...') }}"
                            >{{ old('message') }}</textarea>
                            @error('message')
                            <span class="field-error">{{ $message }}</span>
                            @enderror
                            <div class="character-count">
                                <span id="char-count">0</span>/2000 characters
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary" id="submit-btn">
                            <span class="btn-text">{{ __('static_pages.Send Message') }}</span>
                            <span class="btn-loading" style="display: none;">
                                <span class="spinner"></span>
                                {{ __('static_pages.Sending') }}...
                            </span>
                        </button>
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
    <x-alerts.sweetalerts></x-alerts.sweetalerts>
@endsection

@section('scripts')
    <script src="{{ asset('styles/js/contact.js') }}"></script>
@endsection
