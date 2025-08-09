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
                        <h3 class="text-dark">مركز رفع الملفات</h3>
                        <span>📤</span>
                    </div>

                    <div class="upload-container">
                        <form id="uploadForm" action="{{ route('upload.files') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="quiet-textarea-wrapper" style="margin-bottom: 10px">
                                <textarea id="job-description" class="quiet-textarea" placeholder="برجاء إدخال وصف الوظيفة..." rows="3"></textarea>
                            </div>

                            <div class="upload-box" id="uploadBox">
                                <i class="fas fa-upload fa-3x mb-3 text-primary"></i>
                                <h4>اسحب وأفلت السير الذاتية هنا</h4>
                                <p class="mb-3">
                                    أو انقر للتصفح من جهازك: doc, docx, pdf
                                </p>
                                <input type="file" id="fileInput" name="files[]" multiple class="file-input" accept="*/*">
                                <button type="button" class="btn btn-primary" onclick="document.getElementById('fileInput').click()">
                                    <i class="fas fa-folder-open me-2"></i>اختر الملفات
                                </button>
                            </div>

                            <div id="fileList" class="mt-4"></div>

                            <div class="text-center d-flex justify-content-center mt-4">
                                <button type="submit" id="submitBtn" class="btn btn-success submit-btn mt-4" disabled>
                                    <i class="fas fa-paper-plane me-2"></i>
                                    رفع الملفات
                                </button>
                            </div>
                        </form>
                    </div>

                    <div class="stats-grid">
                        <div class="stat-card">
                            <span class="stat-number text-dark" id="totalFiles">0</span>
                            <span class="stat-label text-dark">ملف محدد</span>
                        </div>
                        <div class="stat-card">
                            <span class="stat-number text-dark" id="totalSize">0 MB</span>
                            <span class="stat-label text-dark">الحجم الإجمالي</span>
                        </div>
                        <div class="stat-card">
                            <span class="stat-number text-dark" id="uploadProgress">0%</span>
                            <span class="stat-label text-dark">تقدم الرفع</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
    <script src="{{ asset('styles/js/upload.js') }}"></script>
@endsection
