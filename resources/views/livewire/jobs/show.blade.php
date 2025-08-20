<div class="job-show-container">
    {{-- Job Header --}}
    <div class="job-header">
        <div class="job-title-section">
            <h1 class="job-title">{{ $jobApp->title }}</h1>
            <div class="job-meta">
                <span class="job-id">{{ __('words.Job ID') }}: #{{ $jobApp->id }}</span>
                <span class="job-status {{ $jobApp->close ? 'closed' : 'open' }}">
                    {{ $jobApp->close ? __('words.Closed') : __('words.Open') }}
                </span>
            </div>
        </div>
    </div>

    {{-- Job Description --}}

    <div class="job-description-card">
        <h3 class="section-title">{{ __('words.Job Description') }}</h3>
        <div class="job-description-wrapper">
            <div class="job-description collapsed" id="job-description-{{ $jobApp->id }}">
                {!! nl2br(e($jobApp->description)) !!}
            </div>
            <button class="show-more-btn" onclick="toggleDescription({{ $jobApp->id }})">
                <span class="btn-text">{{ __('words.Show more') }}</span>
                <i class="fas fa-chevron-down"></i>
            </button>
        </div>
    </div>

    {{-- Statistics Cards --}}
    <div class="stats-grid">
        <div class="stat-card total">
            <div class="stat-icon">
                <i class="fas fa-file-alt"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number">{{ $this->stats['total'] }}</div>
                <div class="stat-label">{{ __('words.Total Applications') }}</div>
            </div>
        </div>

        <div class="stat-card approved">
            <div class="stat-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number">{{ $this->stats['approved'] }}</div>
                <div class="stat-label">{{ __('words.Approved') }}</div>
            </div>
        </div>

        <div class="stat-card rejected">
            <div class="stat-icon">
                <i class="fas fa-times-circle"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number">{{ $this->stats['rejected'] }}</div>
                <div class="stat-label">{{ __('words.Rejected') }}</div>
            </div>
        </div>

        <div class="stat-card pending">
            <div class="stat-icon">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number">{{ $this->stats['pending'] }}</div>
                <div class="stat-label">{{ __('words.Pending') }}</div>
            </div>
        </div>

        @if($this->stats['under_review'] > 0)
            <div class="stat-card waiting">
                <div class="stat-icon">
                    <i class="fas fa-hourglass-half"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">{{ $this->stats['under_review'] }}</div>
                    <div class="stat-label">{{ __('words.Interview Requested') }}</div>
                </div>
            </div>
        @endif

        @if($this->stats['waiting'] > 0)
            <div class="stat-card review">
                <div class="stat-icon">
                    <i class="fa-solid fa-pen-to-square"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">{{ $this->stats['waiting'] }}</div>
                    <div class="stat-label">{{ __('words.Waiting for Answering') }}</div>
                </div>
            </div>
        @endif
    </div>

    {{-- Applicants List --}}
    <div class="applicants-section">
        <h3 class="section-title">{{ __('words.Applications') }} ({{ $this->stats['total'] }})</h3>

        @if($applicants->count() > 0)
            <div style="overflow: auto;height: 700px">
                <div class="applicants-list">
                    @foreach($applicants as $applicant)
                        @php
                            $info = $applicant->information ?? null;
                        @endphp

                        <div class="applicant-card {{ $expandedApplicant === $applicant->id ? 'expanded' : '' }}">
                            {{-- Applicant Header --}}
                            <div class="applicant-header" wire:click="toggleExpand({{ $applicant->id }})">
                                <div class="applicant-basic-info">
                                    <div class="applicant-name">
                                        <i class="fas fa-user"></i>
                                        {{ $info['Name'] ?? __('words.Unknown') }}
                                    </div>

                                    <div class="applicant-contact">
                                        @if(isset($info['Email']))
                                            <span class="contact-info">
                                            <i class="fas fa-envelope"></i>
                                            {{ $info['Email'] }}
                                        </span>
                                        @endif

                                        @if(isset($info['Phone']))
                                            <span class="contact-info">
                                            <i class="fas fa-phone"></i>
                                            {{ $info['Phone'] }}
                                        </span>
                                        @endif
                                    </div>

                                    @if(isset($info['Score']))
                                        <div class="applicant-score">
                                            <i class="fas fa-star"></i>
                                            {{ __('words.Score') }}: {{ $info['Score'] }}
                                        </div>
                                    @endif
                                </div>

                                <div class="applicant-actions">
                                    <div class="status-badge status-{{ str_replace(' ', '-', $applicant->status) }}">

                                        <i class="{{ $applicant->getIcon() }}"></i>
                                        {{ __(ucfirst(strtolower(__("words.$applicant->status")))) }}
                                    </div>

                                    <div class="expand-icon">
                                        <i class="fas fa-chevron-{{ $expandedApplicant === $applicant->id ? 'up' : 'down' }}"></i>
                                    </div>
                                </div>
                            </div>

                            {{-- Expanded Content --}}
                            @if($expandedApplicant === $applicant->id)
                                <div class="applicant-details">
                                    {{-- Status Actions --}}
                                    <div class="status-actions">
                                        <h4>{{ __('words.Update Status') }}</h4>
                                        <div class="status-buttons">
                                            @foreach(['approved', 'rejected'] as $status)
                                                <button wire:click="updateStatus({{ $applicant->id }}, '{{ $status }}')" class="status-btn status-btn-{{ $status }} {{ $applicant->status === $status ? 'active' : '' }}">
                                                    <i class="fas fa-{{ $status === 'approved' ? 'check' : ($status === 'rejected' ? 'times' : 'clock') }}"></i>
                                                    {{ __('words.' . ucfirst($status)) }}
                                                </button>
                                            @endforeach
                                        </div>
                                    </div>

                                    {{-- Detailed Information --}}
                                    @if($info)
                                        <div class="cv-details">
                                            {{-- Basic Info Section --}}
                                            <div class="info-section">
                                                <h4 class="info-section-title">
                                                    <i class="fas fa-user-circle"></i>
                                                    {{ __('words.Personal Information') }}
                                                </h4>
                                                <div class="info-grid">
                                                    @if(isset($info['Job Title']))
                                                        <div class="info-item">
                                                            <div class="info-badge">
                                                                <i class="fas fa-briefcase"></i>
                                                                {{ $info['Job Title'] }}
                                                            </div>
                                                        </div>
                                                    @endif

                                                    @if(isset($info['Location']))
                                                        <div class="info-item">
                                                            <div class="info-badge">
                                                                <i class="fas fa-map-marker-alt"></i>
                                                                {{ $info['Location'] }}
                                                            </div>
                                                        </div>
                                                    @endif

                                                    @if(isset($info['YOE']))
                                                        <div class="info-item">
                                                            <div class="info-badge">
                                                                <i class="fas fa-calendar-alt"></i>
                                                                {{ $info['YOE'] }} {{ __('words.Years Experience') }}
                                                            </div>
                                                        </div>
                                                    @endif

                                                    @if(isset($info['Category']))
                                                        <div class="info-item">
                                                            <div class="info-badge">
                                                                <i class="fas fa-tag"></i>
                                                                {{ $info['Category'] }}
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>

                                            {{-- Skills Section --}}
                                            @if(isset($info['Matched Skills']) || isset($info['Extra Skills']))
                                                <div class="info-section">
                                                    <h4 class="info-section-title">
                                                        <i class="fas fa-cogs"></i>
                                                        {{ __('words.Skills') }}
                                                    </h4>

                                                    @if(isset($info['Matched Skills']))
                                                        <div class="skills-subsection">
                                                            <h5>{{ __('words.Matched Skills') }}</h5>
                                                            <div class="skills-grid">
                                                                @foreach($info['Matched Skills'] as $skill)
                                                                    <span class="skill-badge matched">
                                                                    <i class="fas fa-check-circle"></i>
                                                                    {{ $skill }}
                                                                </span>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @endif

                                                    @if(isset($info['Extra Skills']))
                                                        <div class="skills-subsection">
                                                            <h5>{{ __('words.Additional Skills') }}</h5>
                                                            <div class="skills-grid">
                                                                @foreach($info['Extra Skills'] as $skill)
                                                                    <span class="skill-badge extra">
                                                                    <i class="fas fa-plus-circle"></i>
                                                                    {{ $skill }}
                                                                </span>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            @endif

                                            {{-- Certificates Section --}}
                                            @if(isset($info['Certificates']))
                                                <div class="info-section">
                                                    <h4 class="info-section-title">
                                                        <i class="fas fa-certificate"></i>
                                                        {{ __('words.Certificates') }} ({{ count($info['Certificates']) }})
                                                    </h4>
                                                    <div class="certificates-list">
                                                        @foreach($info['Certificates'] as $certificate)
                                                            <div class="certificate-item">
                                                                <i class="fas fa-award"></i>
                                                                {{ $certificate }}
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif

                                            {{-- Projects Section --}}
                                            @if(isset($info['Projects']))
                                                <div class="info-section">
                                                    <h4 class="info-section-title">
                                                        <i class="fas fa-project-diagram"></i>
                                                        {{ __('words.Projects') }} ({{ count($info['Projects']) }})
                                                    </h4>
                                                    <div class="projects-list">
                                                        @foreach($info['Projects'] as $project)
                                                            <div class="project-item">
                                                                <i class="fas fa-code"></i>
                                                                {{ $project }}
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif

                                            {{-- Education & Experience --}}
                                            <div class="info-section">
                                                <h4 class="info-section-title">
                                                    <i class="fas fa-graduation-cap"></i>
                                                    {{ __('words.Education & Experience') }}
                                                </h4>
                                                <div class="info-grid">
                                                    @if(isset($info['Graduation']))
                                                        <div class="info-item full-width">
                                                            <div class="info-badge">
                                                                <i class="fas fa-university"></i>
                                                                {{ $info['Graduation'] }}
                                                            </div>
                                                        </div>
                                                    @endif

                                                    @if(isset($info['Last Job']))
                                                        <div class="info-item full-width">
                                                            <div class="info-badge">
                                                                <i class="fas fa-building"></i>
                                                                {{ $info['Last Job'] }}
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>

                                            {{-- Other Information --}}
                                            @php
                                                $displayedKeys = ['Name', 'File', 'Score', 'YOE', 'Category', 'Matched Skills', 'Extra Skills', 'Certificates', 'Projects', 'Suggested Roles', 'Final Decision', 'Email', 'Phone', 'Job Title', 'Location', 'Experience Summary', 'Graduation', 'Last Job'];
                                                $otherInfo = collect($info)->except($displayedKeys)->filter();
                                            @endphp

                                            @if($otherInfo->count() > 0)
                                                <div class="info-section">
                                                    <h4 class="info-section-title">
                                                        <i class="fas fa-info-circle"></i>
                                                        {{ __('words.Additional Information') }}
                                                    </h4>
                                                    <div class="info-grid">
                                                        @foreach($otherInfo as $key => $value)
                                                            <div class="info-item">
                                                                <div class="info-badge other">
                                                                    <i class="fas fa-tag"></i>
                                                                    <strong>{{ $key }}:</strong>
                                                                    @if(is_array($value))
                                                                        {{ implode(', ', $value) }}
                                                                    @else
                                                                        {{ $value }}
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif

                                            {{-- File Download --}}
                                            @if($applicant->file)
                                                <div class="info-section">
                                                    <h4 class="info-section-title">
                                                        <i class="fas fa-file-download"></i>
                                                        {{ __('words.CV File') }}
                                                    </h4>
                                                    <a href="{{ asset(@$applicant->file->fullpath) }}" target="_blank" class="download-btn" download="{{ str_replace(' ', '_', $info['Name'] ?? "applicant") . '.' . @$applicant->file->getType() }}">
                                                        <i class="fas fa-download"></i>
                                                        {{ str_replace(' ', '_', @$info['Name']) . '.' . @$applicant->file->getType() }}
                                                    </a>
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
            <!-- Add this right after the closing </div> of applicants-list and before else -->
            @if($totalApplicants > $perPage)
                <div class="pagination-wrapper" style="margin-top: 10px">
                    <div class="pagination-info">
                        {{ __("words.Showing") }} {{ ($currentPage - 1) * $perPage + 1 }} {{ __("words.to") }} {{ min($currentPage * $perPage, $totalApplicants) }} {{ __("words.of") }} {{ $totalApplicants }} {{ __("words.applicants") }}
                    </div>

                    <div class="pagination-controls">
                        @if($currentPage > 1)
                            <button wire:click="previousPage" class="pagination-btn">
                                <i class="fas fa-chevron-{{ app()->getLocale() != 'ar' ? "left":"right" }}"></i>
                                {{ __('words.Previous') }}
                            </button>
                        @endif

                        @for($i = 1; $i <= $this->getTotalPages(); $i++)
                            @if($i == $currentPage)
                                <button class="pagination-btn active">{{ $i }}</button>
                            @else
                                <button wire:click="goToPage({{ $i }})" class="pagination-btn">{{ $i }}</button>
                            @endif
                        @endfor

                        @if($this->hasMorePages())
                            <button wire:click="nextPage" class="pagination-btn">
                                {{ __('words.Next') }}
                                <i class="fas fa-chevron-{{ app()->getLocale() == 'ar' ? "left":"right" }}"></i>
                            </button>
                        @endif
                    </div>
                </div>
            @endif
        @else
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-inbox"></i>
                </div>
                <h3>{{ __('words.No Applications Yet') }}</h3>
                <p>{{ __('words.No one has applied for this job yet') }}</p>
            </div>
        @endif
    </div>
</div>

@push('scripts')
    <script>
        const showMore = '{{ __("words.Show more") }}'
        const showLess = '{{ __("words.Show less") }}'
    </script>
    <script src="{{ asset('styles/js/descriptionCollapse.js') }}"></script>
@endpush
