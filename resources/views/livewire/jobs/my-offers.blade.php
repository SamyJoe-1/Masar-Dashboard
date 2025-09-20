<div class="job-offers-container">
    <!-- Filters Section -->
    <div class="filters-section mb-4">
        <div class="row g-3 align-items-end">
            <!-- Search Bar -->
            <div class="col-md-4">
                <label for="search" class="form-label">{{ __('words.Search Orders') }}</label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" class="form-control" id="search" placeholder="{{ __('words.searching') }}" wire:model.live.debounce.500ms="search">
                    @if($search)
                        <button class="btn btn-outline-secondary" type="button" wire:click="$set('search', '')">
                            <i class="fas fa-times"></i>
                        </button>
                    @endif
                </div>
            </div>

            <!-- Job Status Filter -->
            <div class="col-md-3">
                <label for="statusFilter" class="form-label">{{ __('words.Job Status') }}</label>
                <select class="form-select" id="statusFilter" wire:model.live="statusFilter">
                    <option value="all">{{ __('words.All Jobs') }}</option>
                    <option value="open">{{ __('words.Open') }}</option>
                    <option value="closed">{{ __('words.Closed') }}</option>
                </select>
            </div>

            <!-- Applicant Status Filter -->
            <div class="col-md-3">
                <label for="applicantStatusFilter" class="form-label">{{ __('words.Application Status') }}</label>
                <select class="form-select" id="applicantStatusFilter" wire:model.live="applicantStatusFilter">
                    <option value="all">{{ __('words.All Orders') }}</option>
                    @foreach($applicantStatuses as $status)
                        <option value="{{ $status }}">{{ ucfirst(str_replace('_', ' ', __('words.' . $status))) }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Clear Filters Button -->
            <div class="col-md-2">
                @if($search || $statusFilter !== 'all' || $applicantStatusFilter !== 'all')
                    <button class="btn btn-outline-danger w-100" wire:click="clearFilters">
                        <i class="fas fa-eraser"></i>
                        {{ __('words.Clear') }}
                    </button>
                @endif
            </div>
        </div>

        <!-- Active Filters Display -->
        @if($search || $statusFilter !== 'all' || $applicantStatusFilter !== 'all')
            <div class="active-filters mt-3 d-none">
                <small class="text-muted">{{ __('words.Active Filters') }}:</small>
                <div class="d-flex gap-2 flex-wrap mt-1">
                    @if($search)
                        <span class="badge bg-primary">
                            {{ __('words.Search') }}: "{{ $search }}"
                            <button type="button" class="btn-close btn-close-white ms-1"
                                    wire:click="$set('search', '')" style="font-size: 0.6em;"></button>
                        </span>
                    @endif
                    @if($statusFilter !== 'all')
                        <span class="badge bg-info">
                            {{ __('words.Job Status') }}: {{ $statusFilter === 'open' ? __('words.Open') : __('words.Closed') }}
                            <button type="button" class="btn-close btn-close-white ms-1"
                                    wire:click="$set('statusFilter', 'all')" style="font-size: 0.6em;"></button>
                        </span>
                    @endif
                    @if($applicantStatusFilter !== 'all')
                        <span class="badge bg-success">
                            {{ __('words.Application') }}: {{ ucfirst(str_replace('_', ' ', __('words.' . $applicantStatusFilter))) }}
                            <button type="button" class="btn-close btn-close-white ms-1"
                                    wire:click="$set('applicantStatusFilter', 'all')" style="font-size: 0.6em;"></button>
                        </span>
                    @endif
                </div>
            </div>
        @endif
    </div>

    <div class="offers-grid">
        @forelse($jobs as $job)
            @php
                $applicant = $job->applicants->first(); // Get the user's application
            @endphp
            <div class="job-offer-card {{ in_array($job->id, $expandedCards) ? 'expanded' : '' }}" wire:key="job-{{ $job->id }}">

                <!-- Card Header -->
                <div class="job-card-header" wire:click="toggleCard({{ $job->id }})">
                    <div class="job-publisher">
                        <div class="d-flex gap-3">
                            <div class="publisher-avatar">
                                {{ strtoupper(substr($job->organization->name ?? 'U', 0, 2)) }}
                            </div>
                            <div class="publisher-info">
                                <h3 class="job-title">{{ $job->title }}</h3>
                                <div class="d-flex align-items-center gap-1 publisher-name">
                                    <span>
                                    @empty($job->organization->name)
                                            {{ __("words.Unknown") }}
                                        @else
                                            {{ __("words." . @$job->organization->name) }}
                                        @endempty
                                    </span>
                                    <span>-</span>
                                    <span class="stat-item">
                                        <i class="fas fa-calendar-alt"></i>
                                        {{ $job->created_at->diffForHumans() }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="job-meta">
                        <div class="applicants-count">
                            <i class="fas fa-users"></i>
                            {{ $job->applicants_count }} {{ __('words.applicants') }}
                        </div>
                        <div class="expand-icon">
                            <i class="fas fa-chevron-{{ in_array($job->id, $expandedCards) ? 'up' : 'down' }}"></i>
                        </div>
                    </div>
                </div>

                <!-- Short Description (Always Visible) -->
                <div class="job-short-description">
                    {{ Str::limit($job->description, 120) }}
                </div>

                <!-- Application Status Badge -->
                <div class="job-status">
                    @if($applicant)
                        <x-badge.applicant :status="$applicant->status" :icon="$applicant->getIcon()" :form="@$applicant->form->status"></x-badge.applicant>
                    @endif
                </div>

                <!-- Expanded Content -->
                @if(in_array($job->id, $expandedCards))
                    <div class="job-expanded-content">
                        <div class="job-full-description">
                            <h4>{{ __('words.Full Description') }}</h4>
                            <p>{!! nl2br(e($job->description)) !!}</p>
                        </div>
                        @if($applicant->feedback)
                            <div class="job-full-description">
                                <h4>{{ __('words.Rejection Reason') }}</h4>
                                <p>{!! writeFeedback($applicant->feedback) !!}</p>
                            </div>
                        @endif

                        <div class="job-actions">
                            <div class="d-flex gap-2 flex-wrap">
                                <!-- Download CV Button -->
                                @if($applicant && $applicant->file)
                                    <a href="{{ asset(@$applicant->file->fullpath) }}" class="btn btn-warning" download="CV.{{ $applicant->file->getType() }}">
                                        <i class="fas fa-download"></i>
                                        {{ __('words.Download') }}
                                    </a>
                                @endif

                                <!-- Continue Application Button (only for waiting for answering status) -->
                                @if($applicant && $applicant->status === 'waiting for answering' && !empty($applicant->form))
                                    @if($applicant->form->status == 'waiting')
                                        <a target="_blank" href="{{ route('interview.show', @$applicant->form->slug) }}" class="btn btn-primary">
                                            <i class="fas fa-arrow-right"></i>
                                            {{ __('words.Start Session') }}
                                        </a>
                                    @endif
                                @endif
                            </div>

                            <!-- Application Date -->
                            @if($applicant)
                                <div class="mt-2">
                                    <small class="text-muted">
                                        <i class="fas fa-clock"></i>
                                        {{ __('words.Applied') }}: {{ $applicant->created_at->diffForHumans() }}
                                    </small>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        @empty
            <div class="no-jobs-message">
                <i class="fas fa-briefcase"></i>
                @if($search || $statusFilter !== 'all' || $applicantStatusFilter !== 'all')
                    <h3>{{ __('words.No Applications Found') }}</h3>
                    <p>{{ __('words.Try adjusting your filters or search terms') }}</p>
                    <button class="btn btn-primary mt-2" wire:click="clearFilters">
                        {{ __('words.Clear') }}
                    </button>
                @else
                    <h3>{{ __('words.No Applications Yet') }}</h3>
                    <p>{{ __('words.You haven\'t applied to any jobs yet') }}</p>
                @endif
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="pagination-wrapper">
        {{ $jobs->links() }}
    </div>
</div>
