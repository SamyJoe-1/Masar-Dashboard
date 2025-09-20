@extends('layouts.app_dashboard')

@section('header')
    <link href="{{ asset('styles/css/upload.css') }}" rel="stylesheet">
    <link href="{{ asset('styles/css/formControl.css') }}" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection

@section('content')
    <section class="hero-upload hero">
        <div class="container-fluid">
            <div class="upload-hero-content">
                <div class="upload-dashboard">
                    <div class="dashboard-header">
                        <h3 class="text-dark">{{ __('words.Publish a new job') }}</h3>
                        <span>📤</span>
                    </div>

                    <div class="upload-container">
                        <form id="uploadForm" action="{{ route('upload.files') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="quiet-textarea-wrapper" style="margin-bottom: 10px">
                                <textarea id="job-description" class="quiet-textarea" placeholder="{{ __('words.enter_job_description') }}" rows="5"></textarea>
                            </div>
                            <div class="form-section">
                                <div class="d-flex" style="gap: 30px">
                                    <div>
                                        <h5>{{ __("words.Language") }}</h5>
                                        <div class="language-toggle">
                                            <input type="radio" id="lang-ar" name="language" value="ar" checked>
                                            <label for="lang-ar">العربية</label>

                                            <input type="radio" id="lang-en" name="language" value="en">
                                            <label for="lang-en">English</label>
                                        </div>
                                    </div>
                                    <div>
                                        <h5>{{ __("words.Visibility") }}</h5>
                                        <div class="language-toggle">
                                            <input type="radio" id="public" name="visibility" value="1" checked>
                                            <label for="public">{{ __("words.Public") }}</label>

                                            <input type="radio" id="private" name="visibility" value="0">
                                            <label for="private">{{ __("words.Private") }}</label>
                                        </div>
                                    </div>
                                    <div>
                                        <h5>{{ __("words.Target Nationality") }}</h5>
                                        <div class="language-toggle">
                                            <input type="radio" id="all" name="oman" value="2" checked>
                                            <label for="all">{{ __("words.All") }}</label>

                                            <input type="radio" id="oman" name="oman" value="1" checked>
                                            <label for="oman">{{ __("words.Omani") }}</label>

                                            <input type="radio" id="non_oman" name="oman" value="0">
                                            <label for="non_oman">{{ __("words.Non-Omani") }}</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="organization-select">
                                    <br>
                                    <label for="organization">{{ __("words.Organization") }}:</label>
                                    <select id="organization" name="organization">
                                        <option value="">{{ __("words.select :items", ["items" => __("words.Organization")]) }}</option>
                                        @foreach($orgs as $id => $org)
                                            <option value="{{ $id }}">{{ __("words.$org") }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="organization-select">
                                    <br>
                                    <label for="formType">{{ __("words.Job Type") }}:</label>
                                    <select id="formType" name="formType">
                                        <option value="1">{{ __("words.Upload CVs") }}</option>
                                        <option value="2">{{ __("words.Without CVs") }}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="upload-box" id="uploadBox">
                                <i class="fas fa-upload fa-3x mb-3 text-primary"></i>
                                <h4>{{ __('words.drag_drop_resumes') }}</h4>
                                <p class="mb-3">
                                    {{ __('words.click_browse_device') }}
                                </p>
                                <input type="file" id="fileInput" name="files[]" multiple class="file-input" accept="*/*">
                                <button type="button" class="btn btn-primary" onclick="document.getElementById('fileInput').click()">
                                    <i class="fas fa-folder-open me-2"></i>{{ __('words.select_files') }}
                                </button>
                            </div>

                            <div id="fileList" class="mt-4"></div>

                            <div class="text-center d-flex justify-content-center mt-4">
                                <button type="submit" id="submitBtn" class="btn btn-success submit-btn mt-4" disabled>
                                    <i class="fas fa-paper-plane me-2"></i>
                                    {{ __('words.Publish') }}
                                </button>
                            </div>
                        </form>
                    </div>

                    <div class="stats-grid">
                        <div class="stat-card">
                            <span class="stat-number text-dark" id="totalFiles">0</span>
                            <span class="stat-label text-dark">{{ __('words.selected_file') }}</span>
                        </div>
                        <div class="stat-card">
                            <span class="stat-number text-dark" id="totalSize">0 MB</span>
                            <span class="stat-label text-dark">{{ __('words.total_size') }}</span>
                        </div>
                        <div class="stat-card">
                            <span class="stat-number text-dark" id="uploadProgress">0%</span>
                            <span class="stat-label text-dark">{{ __('words.upload_progress') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
    <x-script.translations></x-script.translations>
    <script src="{{ asset('styles/js/upload.js') }}"></script>
@endsection
