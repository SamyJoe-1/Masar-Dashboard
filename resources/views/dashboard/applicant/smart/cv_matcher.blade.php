@extends('layouts.app_dashboard')

@section('header')
    <link href="{{ asset('styles/css/cv_matcher.css') }}" rel="stylesheet">
@endsection

@section('content')
    <div class="matcher-container">
        <!-- Upload Section -->
        <div id="uploadSection" class="section-wrapper">
            <div class="page-header">
                <h1 class="page-title">{{ __('Smart Job Matcher') }}</h1>
                <p class="page-subtitle">{{ __('Upload your CV and discover matching jobs with ATS scores and personalized feedback') }}</p>
            </div>

            <div class="upload-card">
                <!-- CV Source Selection -->
                <div class="cv-source-selector">
                    <label class="source-option">
                        <input type="radio" name="cv_source" value="upload" class="form-check-input" checked>
                        <span>{{ __('Upload New CV') }}</span>
                    </label>
                    <label class="source-option">
                        <input type="radio" name="cv_source" value="existing" class="form-check-input"
                            {{ auth()->user()->profile && auth()->user()->profile->cv ? '' : 'disabled' }}>
                        <span>{{ __('Use Existing CV') }}</span>
                        @if(!auth()->user()->profile || !auth()->user()->profile->cv)
                            <small class="text-secondary">{{ __('No CV uploaded to profile') }}</small>
                        @endif
                    </label>
                </div>

                <!-- Upload Area -->
                <div id="uploadArea" class="upload-area">
                    <div class="upload-zone" id="dropZone">
                        <i class="fas fa-cloud-upload-alt upload-icon"></i>
                        <h3>{{ __('Drag & Drop your CV here') }}</h3>
                        <p>{{ __('or click to browse') }}</p>
                        <span class="file-types">{{ __('Supported: PDF, DOC, DOCX (Max 5MB)') }}</span>
                        <input type="file" id="cvFileInput" accept=".pdf,.doc,.docx" hidden>
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

                <!-- Job Preferences (Optional) -->
                <div class="job-preferences-section">
                    <label for="jobPreferences" class="section-label">
                        <i class="fas fa-sliders-h"></i>
                        {{ __('Job Preferences / Target Role') }}
                        <span class="optional-badge">{{ __('Optional') }}</span>
                    </label>
                    <div class="quiet-textarea-wrapper">
                        <textarea
                            id="jobPreferences"
                            class="quiet-textarea"
                            rows="6"
                            placeholder="{{ __('Describe your ideal role, preferred industry, or specific requirements (e.g., Remote work, Senior level, Fintech industry...)') }}"
                        ></textarea>
                    </div>
                </div>

                <!-- Match Button -->
                <button type="button" id="matchBtn" class="match-btn" disabled>
                    <i class="fas fa-magic"></i>
                    {{ __('Find Matching Jobs') }}
                </button>
            </div>
        </div>

        <!-- Processing Section -->
        <div id="processingSection" class="section-wrapper d-none">
            <div class="processing-card">
                <div class="processing-header">
                    <h2>{{ __('Analyzing Your CV') }}</h2>
                    <p>{{ __('Please wait while we match you with the best opportunities...') }}</p>
                </div>

                <div class="processing-steps">
                    <div class="process-step" data-step="render">
                        <div class="step-icon">
                            <i class="fas fa-file-alt"></i>
                            <div class="step-loader"></div>
                        </div>
                        <div class="step-content">
                            <h4>{{ __('Rendering PDF to Text') }}</h4>
                            <p>{{ __('Extracting content from your CV...') }}</p>
                        </div>
                        <div class="step-status">
                            <i class="fas fa-check status-icon"></i>
                        </div>
                    </div>

                    <div class="process-step" data-step="analyze">
                        <div class="step-icon">
                            <i class="fas fa-brain"></i>
                            <div class="step-loader"></div>
                        </div>
                        <div class="step-content">
                            <h4>{{ __('Analyzing & Matching Jobs') }}</h4>
                            <p>{{ __('Finding the best job matches for your profile...') }}</p>
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
                <h1>{{ __('Your Job Matches') }}</h1>
                <div class="results-actions">
                    <button class="quiet-btn" onclick="downloadFullReport()">
                        <i class="fas fa-download"></i>
                        {{ __('Download Full Report') }}
                    </button>
                    <button class="quiet-btn" onclick="matchAgain()">
                        <i class="fas fa-redo"></i>
                        {{ __('Try Again') }}
                    </button>
                </div>
            </div>

            <!-- Job Matches List -->
            <div class="jobs-list" id="jobsList">
                <!-- Jobs will be dynamically inserted here -->
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('styles/js/job_matcher/main.js') }}"></script>
    <script src="{{ asset('styles/js/job_matcher/processing.js') }}"></script>
@endsection
