<div class="job-apply-container">
    <!-- Job Information Card -->
    <div class="job-info-card">
        <div class="job-header">
            <div class="job-publisher">
                <div class="publisher-avatar">
                    {{ strtoupper(substr($job->user->name ?? 'U', 0, 2)) }}
                </div>
                <div class="job-details">
                    <h1 class="job-title">{{ $job->title }}</h1>
                    <p class="publisher-name">{{ __('words.Published by') }}: {{ $job->user->name ?? 'Unknown Publisher' }}</p>
                </div>
            </div>

            <div class="job-meta-info">
                <div class="meta-item">
                    <i class="fas fa-users"></i>
                    <span>{{ $job->applicants_count }} {{ __('words.applicants') }}</span>
                </div>

                <div class="meta-item">
                    <i class="fas fa-calendar-alt"></i>
                    <span>{{ $job->created_at->diffForHumans() }}</span>
                </div>

                <div class="job-status-meta">
                    @if($job->close)
                        <span class="status-badge closed">
                            <i class="fas fa-lock"></i>
                            {{ __('words.Closed') }}
                        </span>
                    @else
                        <span class="status-badge open">
                            <i class="fas fa-check-circle"></i>
                            {{ __('words.Open') }}
                        </span>
                    @endif

                    @if($job->public)
                        <span class="visibility-badge public">
                            <i class="fas fa-globe"></i>
                            {{ __('words.Public') }}
                        </span>
                    @else
                        <span class="visibility-badge private">
                            <i class="fas fa-lock"></i>
                            {{ __('words.Private') }}
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <div class="job-description">
            <h3>{{ __('words.Job Description') }}</h3>
            <div class="description-content">
                {{ $job->description }}
            </div>
        </div>
    </div>

    @if(!$job->close && !$hasApplied)
        <!-- Application Form -->
        <div class="application-form-card">
            <div class="form-header">
                <h2>{{ __('words.Submit Your Application') }}</h2>
                <p>{{ __('words.Upload your resume to apply for this position') }}</p>
            </div>

            <form wire:submit.prevent="submitApplication">
                <!-- File Upload Area -->
                <div class="file-upload-section">
                    <div class="drag-drop-area {{ $resume ? 'has-file' : '' }}"
                         x-data="{
                             isDragOver: false,
                             handleDrop(e) {
                                 this.isDragOver = false;
                                 const files = e.dataTransfer.files;
                                 if (files.length > 0) {
                                     @this.set('resume', files[0]);
                                 }
                             }
                         }"
                         x-on:dragover.prevent="isDragOver = true"
                         x-on:dragleave.prevent="isDragOver = false"
                         x-on:drop.prevent="handleDrop"
                         x-bind:class="{ 'drag-over': isDragOver }">

                        @if(!$resume)
                            <div class="upload-placeholder">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <h4>{{ __('words.Drag & Drop your resume here') }}</h4>
                                <p>{{ __('words.or click to browse files') }}</p>
                                <span class="file-requirements">{{ __('words.Supported formats: PDF, DOC, DOCX (Max 5MB)') }}</span>

                                <input type="file" wire:model.live.debounce.200ms="resume" accept=".pdf,.doc,.docx" class="file-input">
                            </div>
                        @else
                            <div class="uploaded-file">
                                <div class="file-info">
                                    <i class="fas fa-file-alt file-icon"></i>
                                    <div class="file-details">
                                        <h5>{{ $uploadedFileName }}</h5>
                                        <span class="file-size">{{ __('words.Ready to upload') }}</span>
                                    </div>
                                </div>

                                <div class="file-actions">
                                    <button type="button"
                                            wire:click="downloadFile"
                                            class="btn-icon download"
                                            title="{{ __('words.Preview File') }}">
                                        <i class="fas fa-download"></i>
                                    </button>

                                    <button type="button"
                                            wire:click="removeFile"
                                            class="btn-icon remove"
                                            title="{{ __('words.Remove File') }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        @endif

                        <!-- Upload Progress -->
                        @if($isUploading)
                            <div class="upload-progress">
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: {{ $uploadProgress }}%"></div>
                                </div>
                                <span class="progress-text">{{ __('words.Uploading') }}... {{ $uploadProgress }}%</span>
                            </div>
                        @endif
                    </div>

                    @error('resume')
                    <div class="error-message">
                        <i class="fas fa-exclamation-triangle"></i>
                        {{ $message }}
                    </div>
                    @enderror
                </div>

                <!-- Submit Button -->
                <div class="form-actions">
                    <button type="submit"
                            class="submit-application-btn {{ !$resume || $isUploading ? 'disabled' : '' }}"
                        {{ !$resume || $isUploading ? 'disabled' : '' }}>
                        @if($isUploading)
                            <i class="fas fa-spinner fa-spin"></i>
                            {{ __('words.Submitting') }}...
                        @else
                            <i class="fas fa-paper-plane"></i>
                            {{ __('words.Submit Application') }}
                        @endif
                    </button>
                </div>
            </form>
        </div>
    @elseif($hasApplied)
        <!-- Already Applied Message -->
        <div class="already-applied-card">
            <div class="applied-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <h3>{{ __('words.Application Already Submitted') }}</h3>
            <p>{{ __('words.You have already applied to this job position. You will be notified about the status of your application.') }}</p>

            <a href="{{ route('dashboard.applicant.jobs.index') }}" class="back-to-jobs-btn">
                <i class="fas fa-arrow-left"></i>
                {{ __('words.Back to Job Offers') }}
            </a>
        </div>
    @else
        <!-- Job Closed Message -->
        <div class="job-closed-card">
            <div class="closed-icon">
                <i class="fas fa-lock"></i>
            </div>
            <h3>{{ __('words.Job Applications Closed') }}</h3>
            <p>{{ __('words.This job is no longer accepting applications. Please check other available positions.') }}</p>

            <a href="{{ route('dashboard.applicant.jobs.index') }}" class="back-to-jobs-btn">
                <i class="fas fa-arrow-left"></i>
                {{ __('words.Back to Job Offers') }}
            </a>
        </div>
    @endif
</div>

@push('scripts')

    <script>
        // Handle SweetAlert events
        window.addEventListener('swal', event => {
            swal({
                title: event.detail[0].title,
                text: event.detail[0].text,
                icon: event.detail[0].icon,
                button: event.detail.confirmButtonText || 'OK',
            });
        });

        // Handle redirect after successful application
        window.addEventListener('redirect-after-success', () => {
            setTimeout(() => {
                window.location.href = "{{ route('dashboard.applicant.jobs.index') }}";
            }, 2000);
        });

        // Simulate upload progress
        document.addEventListener('livewire:load', function () {
            Livewire.hook('message.processed', (message, component) => {
                if (component.get('isUploading')) {
                    simulateProgress();
                }
            });
        });

        function simulateProgress() {
            let progress = 0;
            const interval = setInterval(() => {
                progress += Math.random() * 30;
                if (progress >= 100) {
                    progress = 100;
                    clearInterval(interval);
                }
                @this.set('uploadProgress', Math.floor(progress));
            }, 200);
        }
        console.log(123)
    </script>
@endpush
