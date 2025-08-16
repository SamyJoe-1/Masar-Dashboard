<div class="job-offers-container">
    <div class="offers-grid">
        @forelse($jobs as $job)
            <div class="job-offer-card {{ in_array($job->id, $expandedCards) ? 'expanded' : '' }}"
                 wire:key="job-{{ $job->id }}">

                <!-- Card Header -->
                <div class="job-card-header" wire:click="toggleCard({{ $job->id }})">
                    <div class="job-publisher">
                        <div class="d-flex gap-3">
                            <div class="publisher-avatar">
                                {{ strtoupper(substr($job->user->name ?? 'U', 0, 2)) }}
                            </div>
                            <div class="publisher-info">
                                <h3 class="job-title">{{ $job->title }}</h3>
                                <span class="publisher-name">{{ $job->user->name ?? 'Unknown Publisher' }}</span>
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
                <div class="job-status">
                    @if($job->close)
                        <span class="status-badge closed">
                            <i class="fas fa-lock"></i>
                            {{ __('words.Closed') }}
                        </span>
                    @else
                        <span class="status-badge open">
                            <i class="fas fa-check-circle"></i>
                            {{ __('words.Open for Applications') }}
                        </span>
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
                            @if(!$job->close)
                                <a href="{{ route('dashboard.applicant.jobs.show', $job->id) }}" class="apply-btn text-decoration-none">
                                    <i class="fas fa-paper-plane"></i>
                                    {{ __('words.Apply Now') }}
                                </a>
                            @endif

                            <div class="job-stats">
                                <span class="stat-item">
                                    <i class="fas fa-calendar-alt"></i>
                                    {{ $job->created_at->diffForHumans() }}
                                </span>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        @empty
            <div class="no-jobs-message">
                <i class="fas fa-briefcase"></i>
                <h3>{{ __('words.No Job Offers Available') }}</h3>
                <p>{{ __('words.Check back later for new opportunities') }}</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="pagination-wrapper">
        {{ $jobs->links() }}
    </div>
</div>
