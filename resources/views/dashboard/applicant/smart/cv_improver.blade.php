@extends('layouts.app_dashboard')

@section('header')
    <link href="{{ asset('styles/css/cv_improver.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
    <meta name="smart-cv-url" content="{{ config('app.smart_cv_url') }}">
    <meta name="locale" content="{{ app()->getLocale() }}">
@endsection

@section('content')
    <div class="improver-container">
        <!-- Upload Section -->
        <div id="uploadSection" class="section-wrapper">
            <div class="page-header">
                <h1 class="page-title">{{ __('words.CV Improver') }}</h1>
                <p class="page-subtitle">{{ __('words.Upload your CV and let AI enhance it for your target role') }}</p>
            </div>

            <div class="upload-card">
                <!-- Upload Area -->
                <div class="upload-area">
                    <div class="upload-zone" id="dropZone">
                        <i class="fas fa-cloud-upload-alt upload-icon"></i>
                        <h3>{{ __('words.Drag & Drop your CV here') }}</h3>
                        <p>{{ __('words.or click to browse') }}</p>
                        <span class="file-types">{{ __('words.Supported: PDF only (Max 5MB)') }}</span>
                        <input type="file" id="cvFileInput" accept=".pdf" hidden>
                    </div>
                    <div id="filePreview" class="file-preview d-none">
                        <i class="fas fa-file-pdf file-icon"></i>
                        <div class="file-info">
                            <span class="file-name"></span>
                            <span class="file-size"></span>
                        </div>
                        <button type="button" class="remove-file-btn" onclick="removeFile()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>

                <!-- Target Role -->
                <div class="input-section">
                    <label for="targetRole" class="section-label">
                        <i class="fas fa-bullseye"></i>
                        {{ __('words.Target Role') }}
                        <span class="required-badge">{{ __('words.Required') }}</span>
                    </label>
                    <div class="quiet-input-wrapper">
                        <input
                            type="text"
                            id="targetRole"
                            class="quiet-input"
                            placeholder="{{ __('words.e.g., Senior Laravel Developer, Full Stack Engineer...') }}"
                            required
                        />
                    </div>
                </div>

                <!-- Job Description -->
                <div class="input-section">
                    <label for="jobDescription" class="section-label">
                        <i class="fas fa-briefcase"></i>
                        {{ __('words.Job Description') }}
                        <span class="required-badge">{{ __('words.Required') }}</span>
                    </label>
                    <div class="quiet-textarea-wrapper">
                        <textarea
                            id="jobDescription"
                            class="quiet-textarea"
                            rows="8"
                            placeholder="{{ __('words.Paste the full job description here...') }}"
                            required
                        ></textarea>
                    </div>
                </div>

                <!-- Improve Button -->
                <button type="button" id="improveBtn" class="improve-btn" disabled>
                    <i class="fas fa-magic"></i>
                    {{ __('words.Improve My CV') }}
                </button>
            </div>
        </div>

        <!-- Processing Section -->
        <div id="processingSection" class="section-wrapper d-none">
            <div class="processing-card">
                <div class="processing-header">
                    <h2>{{ __('words.Improving Your CV') }}</h2>
                    <p>{{ __('words.Please wait while our AI enhances your resume...') }}</p>
                </div>

                <div class="processing-steps">
                    <div class="process-step" data-step="render">
                        <div class="step-icon">
                            <i class="fas fa-file-pdf"></i>
                            <div class="step-loader"></div>
                        </div>
                        <div class="step-content">
                            <h4>{{ __('words.Rendering PDF') }}</h4>
                            <p>{{ __('words.Extracting CV content from PDF...') }}</p>
                        </div>
                        <div class="step-status">
                            <i class="fas fa-check status-icon"></i>
                        </div>
                    </div>

                    <div class="process-step" data-step="analyze">
                        <div class="step-icon">
                            <i class="fas fa-robot"></i>
                            <div class="step-loader"></div>
                        </div>
                        <div class="step-content">
                            <h4>{{ __('words.Analyzing & Improving') }}</h4>
                            <p>{{ __('words.AI is enhancing your CV for the target role...') }}</p>
                        </div>
                        <div class="step-status">
                            <i class="fas fa-check status-icon"></i>
                        </div>
                    </div>
                </div>

                <div class="overall-progress">
                    <div class="progress-bar">
                        <div class="progress-fill" id="overallProgress"></div>
                    </div>
                    <span class="progress-text">0%</span>
                </div>
            </div>
        </div>

        <!-- Results Section -->
        <div id="resultsSection" class="section-wrapper d-none">
            <div class="results-header">
                <h1>{{ __('words.Your Improved CV') }}</h1>
                <div class="results-actions">
                    <button class="action-btn download-btn" onclick="downloadImprovedCV()">
                        <i class="fas fa-download"></i>
                        {{ __('words.Download Improved CV') }}
                    </button>
                    <button class="quiet-btn" onclick="improveAnother()">
                        <i class="fas fa-redo"></i>
                        {{ __('words.Improve Another') }}
                    </button>
                </div>
            </div>

            <div class="results-content">
                <!-- CV Preview Container (RIGHT SIDE) -->
                <div class="cv-preview-container">
                    <div class="preview-header">
                        <h3>
                            <i class="fas fa-eye"></i>
                            {{ __('words.Preview') }}
                        </h3>
                        <div class="preview-controls">
                            <button class="quiet-btn pagination-btn" id="prevPage" disabled>
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <span class="page-indicator">
                                <span id="currentPage">1</span> / <span id="totalPages">1</span>
                            </span>
                            <button class="quiet-btn pagination-btn" id="nextPage" disabled>
                                <i class="fas fa-chevron-right"></i>
                            </button>
                            <button class="quiet-btn" onclick="togglePreview()">
                                <i class="fas fa-expand"></i>
                            </button>
                        </div>
                    </div>

                    <div class="a4-preview-wrapper">
                        <div class="swiper a4-swiper">
                            <div class="swiper-wrapper" id="cvPreview">
                                <div class="swiper-slide">
                                    <div class="a4-page">
                                        <div class="preview-loading">
                                            <i class="fas fa-spinner fa-spin"></i>
                                            <p>{{ __('words.Loading preview...') }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Improvements Summary (LEFT SIDE) -->
                <div class="improvements-summary">
                    <h3>
                        <i class="fas fa-list-check"></i>
                        {{ __('words.What We Improved') }}
                    </h3>
                    <div id="improvementsList"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Fullscreen Preview Modal -->
    <div id="fullscreenModal" class="fullscreen-modal d-none">
        <div class="modal-header">
            <h3>{{ __('words.CV Preview') }}</h3>
            <div class="modal-controls">
                <button class="quiet-btn pagination-btn" id="prevPageModal" disabled>
                    <i class="fas fa-chevron-left"></i>
                </button>
                <span class="page-indicator">
                    <span id="currentPageModal">1</span> / <span id="totalPagesModal">1</span>
                </span>
                <button class="quiet-btn pagination-btn" id="nextPageModal" disabled>
                    <i class="fas fa-chevron-right"></i>
                </button>
                <button class="close-modal-btn" onclick="togglePreview()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
        <div class="modal-content-wrapper">
            <div class="swiper a4-swiper-modal">
                <div class="swiper-wrapper" id="fullscreenPreview"></div>
            </div>
        </div>
    </div>

    <!-- Hidden container for pagination calculation -->
    <div id="paginationHelper" class="pagination-helper"></div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script src="{{ asset('styles/js/cv_improver/translations.js') }}"></script>
    <script src="{{ asset('styles/js/cv_improver/main.js') }}"></script>
    <script src="{{ asset('styles/js/cv_improver/pagination.js') }}"></script>
@endsection
