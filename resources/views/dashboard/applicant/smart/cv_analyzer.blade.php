@extends('layouts.app_dashboard')

@section('header')
    <link href="{{ asset('styles/css/cv_analyzer.css') }}" rel="stylesheet">
@endsection

@section('content')
    <div class="analyzer-container">
        <!-- Upload Section -->
        <div id="uploadSection" class="section-wrapper">
            <div class="page-header">
                <h1 class="page-title">{{ __('CV ATS Analyzer') }}</h1>
                <p class="page-subtitle">{{ __('Analyze your CV and get detailed ATS feedback with improvement suggestions') }}</p>
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

                <!-- Job Description -->
                <div class="job-description-section">
                    <label for="jobDescription" class="section-label">
                        <i class="fas fa-briefcase"></i>
                        {{ __('Job Description') }}
                        <span class="optional-badge">{{ __('Optional') }}</span>
                    </label>
                    <div class="quiet-textarea-wrapper">
                    <textarea
                        id="jobDescription"
                        class="quiet-textarea"
                        rows="8"
                        placeholder="{{ __('Paste the job description here to get tailored feedback and keyword matching...') }}"
                    ></textarea>
                    </div>
                </div>

                <!-- Analyze Button -->
                <button type="button" id="analyzeBtn" class="analyze-btn" disabled>
                    <i class="fas fa-chart-line"></i>
                    {{ __('Analyze CV') }}
                </button>
            </div>
        </div>

        <!-- Processing Section -->
        <div id="processingSection" class="section-wrapper d-none">
            <div class="processing-card">
                <div class="processing-header">
                    <h2>{{ __('Analyzing Your CV') }}</h2>
                    <p>{{ __('Please wait while our AI analyzes your resume...') }}</p>
                </div>

                <div class="processing-steps">
                    <div class="process-step" data-step="upload">
                        <div class="step-icon">
                            <i class="fas fa-upload"></i>
                            <div class="step-loader"></div>
                        </div>
                        <div class="step-content">
                            <h4>{{ __('Uploading CV') }}</h4>
                            <p>{{ __('Receiving your file...') }}</p>
                        </div>
                        <div class="step-status">
                            <i class="fas fa-check status-icon"></i>
                        </div>
                    </div>

                    <div class="process-step" data-step="render">
                        <div class="step-icon">
                            <i class="fas fa-file-alt"></i>
                            <div class="step-loader"></div>
                        </div>
                        <div class="step-content">
                            <h4>{{ __('Extracting Content') }}</h4>
                            <p>{{ __('Reading your CV content...') }}</p>
                        </div>
                        <div class="step-status">
                            <i class="fas fa-check status-icon"></i>
                        </div>
                    </div>

                    <div class="process-step" data-step="ats">
                        <div class="step-icon">
                            <i class="fas fa-robot"></i>
                            <div class="step-loader"></div>
                        </div>
                        <div class="step-content">
                            <h4>{{ __('Calculating ATS Score') }}</h4>
                            <p>{{ __('Analyzing ATS compatibility...') }}</p>
                        </div>
                        <div class="step-status">
                            <i class="fas fa-check status-icon"></i>
                        </div>
                    </div>

                    <div class="process-step" data-step="content">
                        <div class="step-icon">
                            <i class="fas fa-align-left"></i>
                            <div class="step-loader"></div>
                        </div>
                        <div class="step-content">
                            <h4>{{ __('Analyzing Content Quality') }}</h4>
                            <p>{{ __('Evaluating content structure...') }}</p>
                        </div>
                        <div class="step-status">
                            <i class="fas fa-check status-icon"></i>
                        </div>
                    </div>

                    <div class="process-step" data-step="format">
                        <div class="step-icon">
                            <i class="fas fa-paint-brush"></i>
                            <div class="step-loader"></div>
                        </div>
                        <div class="step-content">
                            <h4>{{ __('Analyzing Formatting') }}</h4>
                            <p>{{ __('Checking layout and structure...') }}</p>
                        </div>
                        <div class="step-status">
                            <i class="fas fa-check status-icon"></i>
                        </div>
                    </div>

                    <div class="process-step" data-step="skills">
                        <div class="step-icon">
                            <i class="fas fa-code"></i>
                            <div class="step-loader"></div>
                        </div>
                        <div class="step-content">
                            <h4>{{ __('Analyzing Skills Match') }}</h4>
                            <p>{{ __('Evaluating technical skills...') }}</p>
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
                <h1>{{ __('ATS Analysis Report') }}</h1>
                <div class="results-actions">
                    <button class="quiet-btn" onclick="downloadCV()">
                        <i class="fas fa-download"></i>
                        {{ __('Download CV') }}
                    </button>
                    <button class="quiet-btn" onclick="shareReport()">
                        <i class="fas fa-share-alt"></i>
                        {{ __('Share Report') }}
                    </button>
                    <button class="quiet-btn" onclick="analyzeAnother()">
                        <i class="fas fa-redo"></i>
                        {{ __('Analyze Another') }}
                    </button>
                </div>
            </div>

            <div class="results-grid">
                <!-- Left Sidebar - Scores -->
                <div class="scores-sidebar">
                    <!-- Main ATS Score -->
                    <div class="main-score-card">
                        <h3>{{ __('ATS Score') }}</h3>
                        <div class="semicircle-progress">
                            <svg viewBox="0 0 200 120" class="score-svg">
                                <path class="score-bg" d="M 20 100 A 80 80 0 0 1 180 100" fill="none" stroke="#e2e8f0" stroke-width="12"/>
                                <path class="score-fill" d="M 20 100 A 80 80 0 0 1 180 100" fill="none" stroke="#3464b0" stroke-width="12" stroke-dasharray="251.2" stroke-dashoffset="251.2" id="mainScorePath"/>
                            </svg>
                            <div class="score-value">
                                <span class="score-number" id="mainScore">0</span>
                                <span class="score-total">/100</span>
                            </div>
                        </div>
                        <p class="score-status" id="scoreStatus">{{ __('Calculating...') }}</p>
                    </div>

                    <!-- Sub Scores -->
                    <div class="sub-scores">
                        <div class="sub-score-item">
                            <div class="sub-score-header">
                                <i class="fas fa-align-left"></i>
                                <span>{{ __('Content') }}</span>
                            </div>
                            <div class="sub-score-bar">
                                <div class="sub-score-fill" data-score="0" style="width: 0%"></div>
                            </div>
                            <span class="sub-score-value">0%</span>
                        </div>

                        <div class="sub-score-item">
                            <div class="sub-score-header">
                                <i class="fas fa-paint-brush"></i>
                                <span>{{ __('Format') }}</span>
                            </div>
                            <div class="sub-score-bar">
                                <div class="sub-score-fill" data-score="0" style="width: 0%"></div>
                            </div>
                            <span class="sub-score-value">0%</span>
                        </div>

                        <div class="sub-score-item">
                            <div class="sub-score-header">
                                <i class="fas fa-code"></i>
                                <span>{{ __('Skills') }}</span>
                            </div>
                            <div class="sub-score-bar">
                                <div class="sub-score-fill" data-score="0" style="width: 0%"></div>
                            </div>
                            <span class="sub-score-value">0%</span>
                        </div>
                    </div>
                </div>

                <!-- Right Content - Detailed Feedback -->
                <div class="feedback-content">
                    <div id="feedbackSections"></div>

                    <!-- Suggested Roles -->
                    <div class="suggested-roles-section">
                        <h3>
                            <i class="fas fa-briefcase"></i>
                            {{ __('Suggested Roles Based on Your Profile') }}
                        </h3>
                        <div class="roles-pills" id="suggestedRoles"></div>
                        <button class="quiet-btn mt-3" onclick="window.location.href='{{ route('career.matcher') }}'">
                            <i class="fas fa-compass"></i>
                            {{ __('Discover More Career Matches') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('styles/js/cv_analyzer/main.js') }}"></script>
    <script src="{{ asset('styles/js/cv_analyzer/analyzing.js') }}"></script>
@endsection
