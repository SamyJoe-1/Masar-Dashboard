<div class="job-offers-container">
    <!-- Filters Section -->
    <div class="filters-section mb-4">
        <div class="row g-3 align-items-end">
            <!-- Search Bar -->
            <div class="col-md-6">
                <label for="search" class="form-label">{{ __('words.Search Jobs') }}</label>
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

            <!-- Status Filter -->
            <div class="col-md-4">
                <label for="statusFilter" class="form-label">{{ __('words.Status') }}</label>
                <select class="form-select" id="statusFilter" wire:model.live="statusFilter">
                    <option value="all">{{ __('words.All Jobs') }}</option>
                    <option value="open">{{ __('words.Open') }}</option>
                    <option value="closed">{{ __('words.Closed') }}</option>
                </select>
            </div>

            <!-- Organization Filter -->
            <div class="col-md-4">
                <label for="organization" class="form-label">{{ __('words.Organization') }}</label>
                <select class="form-select" id="organization" wire:model.live="organizationFilter">
                    <option value="">{{ __('words.select :items', ['items' => __("words.Organization")]) }}</option>
                    @foreach($organizations as $id => $organization)
                        <option value="{{ $id }}">{{ __('words.' . $organization) }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Clear Filters Button -->
            <div class="col-md-2">
                @if($search || $statusFilter !== 'all')
                    <button class="btn btn-outline-danger w-100" wire:click="clearFilters">
                        <i class="fas fa-eraser"></i>
                        {{ __('words.Clear') }}
                    </button>
                @endif
            </div>
        </div>

        <!-- Active Filters Display -->
        @if($search || $statusFilter !== 'all')
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
                            {{ __('words.Status') }}: {{ $statusFilter === 'open' ? __('words.Open') : __('words.Closed') }}
                            <button type="button" class="btn-close btn-close-white ms-1"
                                    wire:click="$set('statusFilter', 'all')" style="font-size: 0.6em;"></button>
                        </span>
                    @endif
                </div>
            </div>
        @endif
    </div>

    <div id="badgeTarget">
        <p id="targetMsg">
            @if(empty(auth()->user()->profile->nationality))
                {{ __("words.All job listings displayed are designated for Omani nationals. If you would like to view opportunities open to non-Omani applicants, please update your profile preferences accordingly.") }}
            @else
                {{ __("words.All job listings displayed are designated for non-Omani nationals. If you would like to view opportunities open to Omani applicants, please update your profile preferences accordingly.") }}
            @endif
            <br>
            <a href="{{ route('profile') }}">{{ __('words.Click Here') }}</a>
        </p>
    </div>

    <div class="offers-grid">
        @forelse($jobs as $job)
            <div class="job-offer-card {{ in_array($job->id, $expandedCards) ? 'expanded' : '' }}" wire:key="job-{{ $job->id }}">

                <!-- Card Header -->
                <div class="job-card-header" wire:click="toggleCard({{ $job->id }})">
                    <div class="job-publisher">
                        <div class="d-flex gap-3">
                            <div class="publisher-avatar">
                                {{ strtoupper(substr($job->organization->name ?? 'U', 0, 2)) }}
                            </div>
                            <div class="publisher-info">
                                <h3 class="job-title">{{ !empty($job->title) ? $job->title:'-' }}</h3>
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

                <!-- Status Badge -->
                <!-- Status Badge -->
                <div class="job-status d-flex justify-content-between">
                    @if($job->close)
                        <span class="status-badge closed">
            <i class="fas fa-lock"></i>
            {{ __('words.Closed') }}
        </span>
                    @else
                        @if($job->isApplied())
                            <span class="status-badge open">
                <i class="fas fa-check-circle"></i>
                {{ __('words.Already Submitted') }}
            </span>
                        @else
                            <span class="status-badge open">
                <i class="bx bx-edit"></i>
                {{ __('words.Open for Applications') }}
            </span>
                        @endif

                        {{-- Only show apply button if job is open, not applied, AND card is NOT expanded --}}
                        @if(!$job->close && !$job->isApplied() && !in_array($job->id, $expandedCards))
                            @if(!empty(auth()->user()->profile->cv))
                                <button class="apply-btn text-decoration-none py-2" onclick="showCVChoiceModal({{ $job->id }})">
                                    <i class="fas fa-paper-plane"></i>
                                    {{ __('words.Apply Now') }}
                                </button>
                            @else
                                <a href="{{ route('dashboard.applicant.jobs.show', $job->id) }}" class="apply-btn text-decoration-none py-2">
                                    <i class="fas fa-paper-plane"></i>
                                    {{ __('words.Apply Now') }}
                                </a>
                            @endif
                        @endif
                    @endif
                </div>

                <!-- Expanded Content -->
                @if(in_array($job->id, $expandedCards))
                    <div class="job-expanded-content">
                        <div class="job-full-description">
                            <h4>{{ __('words.Full Description') }}</h4>
                            <p>{!! nl2br(e($job->description)) !!}</p>
                        </div>

                        <div class="job-actions">
                            @if(!$job->close && !$job->isApplied())
                                @if(!empty(auth()->user()->profile->cv))
                                    <button class="apply-btn text-decoration-none" onclick="showCVChoiceModal({{ $job->id }})">
                                        <i class="fas fa-paper-plane"></i>
                                        {{ __('words.Apply Now') }}
                                    </button>
                                @else
                                    <a href="{{ route('dashboard.applicant.jobs.show', $job->id) }}" class="apply-btn text-decoration-none">
                                        <i class="fas fa-paper-plane"></i>
                                        {{ __('words.Apply Now') }}
                                    </a>
                                @endif
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        @empty
            <div class="no-jobs-message">
                <i class="fas fa-briefcase"></i>
                @if($search || $statusFilter !== 'all')
                    <h3>{{ __('words.No Jobs Found') }}</h3>
                    <p>{{ __('words.Try adjusting your filters or search terms') }}</p>
                    <button class="btn btn-primary mt-2" wire:click="clearFilters">
                        {{ __('words.Clear') }}
                    </button>
                @else
                    <h3>{{ __('words.No Job Offers Available') }}</h3>
                    <p>{{ __('words.Check back later for new opportunities') }}</p>
                @endif
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="pagination-wrapper">
        {{ $jobs->links() }}
    </div>
</div>

@push('scripts')
    <script>
        // Function to show CV choice modal using SweetAlert
        function showCVChoiceModal(jobId) {
            const hasCV = {{ auth()->user()->profile && auth()->user()->profile->getCV() ? 'true' : 'false' }};

            if (hasCV) {
                Swal.fire({
                    title: '{{ __("words.Choose Application Method") }}',
                    text: '{{ __("words.How would you like to apply for this job?") }}',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '{{ __("words.Use Existing CV") }}',
                    cancelButtonText: '{{ __("words.Upload New CV") }}',
                    showCloseButton: true,
                    allowOutsideClick: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Apply with existing CV
                        Livewire.find('{{ $this->getId() }}').call('applyWithExistingCV', jobId);
                    } else if (result.dismiss === Swal.DismissReason.cancel) {
                        // Redirect to apply page for new CV
                        window.location.href = '{{ url("dashboard/applicant/jobs") }}/' + jobId;
                    }
                });
            } else {
                // No existing CV, go directly to apply page
                window.location.href = '{{ url("dashboard/applicant/jobs") }}/' + jobId;
            }
        }

        // Listen for Livewire events to show alerts
        document.addEventListener('livewire:initialized', function() {
            Livewire.on('showAlert', function(event) {
                const data = event;
                Swal.fire({
                    icon: data[0].type,
                    title: data[0].title,
                    text: data[0].text,
                    timer: 3000,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                });
            });
        });
    </script>
@endpush
