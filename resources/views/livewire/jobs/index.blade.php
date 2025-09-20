<div>
    <div class="d-flex justify-content-between" wire:ignore>
        <div class="flex-fill">
            <x-table.filterBar search="1" oman="1" :organizations="$organizations"></x-table.filterBar>
        </div>
    </div>
    <x-table.bulkActions :selected="$selected">
        <option value="">{{ __("words.select action") }}</option>
        <option value="delete">{{ __("words.delete") }}</option>
    </x-table.bulkActions>
    <div class="d-flex justify-content-between align-items-center">
        <x-table.perPage :per-page-options="$perPageOptions"></x-table.perPage>
        <div>
            <button class="btn btn-primary rounded-5 p-1 px-3" data-bs-toggle="collapse" data-bs-target="#filterBarContent" style="margin: 10px">
                <i class="bx bx-filter-alt"></i>
                {{ __("words.Filter") }}
            </button>
        </div>
    </div>
    @if(count($jobs))
        <x-table.default>
            <thead>
            <tr>
                <x-table.thCheckbox></x-table.thCheckbox>
                @foreach($tableColumns as $title => $columnInfo)
                    <x-table.th :columns="$selectedColumns" :column="@$columnInfo['column']" :sorting="@$columnInfo['sorting']" :sort-by="$sortField" :sort-dir="$sortDirection">{{ $title }}</x-table.th>
                @endforeach
            </tr>
            </thead>
            <tbody>
            @foreach($jobs as $job)
                <tr>
                    <x-table.tdCheckbox :value="$job->id"></x-table.tdCheckbox>
                    <x-table.td :columns="$selectedColumns" column="id">{{ $job->id }}</x-table.td>
                    <x-table.td :columns="$selectedColumns" column="title">{{ $job->title }}</x-table.td>
                    <x-table.td :columns="$selectedColumns" column="organization">{{ __("words." . @$job->organization->name) }}</x-table.td>
                    <x-table.td :columns="$selectedColumns" column="target">
                        <x-badge.oman :oman="$job->target"></x-badge.oman>
                    </x-table.td>
                    <x-table.td :columns="$selectedColumns" column="description">
                        {{ showLess($job->description, 25) }}
                    </x-table.td>
                    <x-table.td :columns="$selectedColumns" column="applicants_count">
                        <span class="text-{{ $job->applicants_count ? "":"danger" }}">{{ $job->applicants_count }} {{ __("words.applicant") }}</span>
                    </x-table.td>
                    <x-table.td :columns="$selectedColumns" column="approved_applicants_count" :danger="$job->approved_applicants_count">
                        <span class="text-{{ $job->approved_applicants_count ? "success":"danger" }}">{{ $job->approved_applicants_count }} {{ __("words.applicant") }}</span>
                    </x-table.td>
                    <x-table.td :columns="$selectedColumns" column="rejected_applicants_count" :danger="$job->approved_applicants_count">
                        <span class="text-{{ $job->rejected_applicants_count ? "":"danger" }}">{{ $job->rejected_applicants_count }} {{ __("words.applicant") }}</span>
                    </x-table.td>
                    <x-table.td :columns="$selectedColumns" column="close">
                        <x-badge.active :status="!$job->close"></x-badge.active>
                    </x-table.td>
                    <x-table.td :columns="$selectedColumns" column="public">
                        @if($job->public)
                            <i class="bx bx-planet fs-5"></i>
                        @else
                            <i class="bx bx-lock fs-5"></i>
                        @endif
                    </x-table.td>
                    <x-table.td :columns="$selectedColumns" column="created_at">{{ dateFormat_1($job->created_at) }}</x-table.td>
                    <x-table.td :columns="$selectedColumns" column="updated_at">{{ dateFormat_1($job->updated_at) }}</x-table.td>
                    <x-table.td :columns="$selectedColumns" column="action">
                        <div class="d-flex card-actions">
                            <a wire:click="Delete({{ $job->id }})" class="cursor-pointer">
                                <i class='bx bx-trash text-danger'></i>
                            </a>
                            <a wire:click="Close({{ $job->id }})" class="cursor-pointer">
                                <i class='bx bx-radio-circle-marked fs-4 text-{{ $job->close ? "primary":"secondary" }}'></i>
                            </a>
                            <a wire:click="Public({{ $job->id }})" class="cursor-pointer">
                                <i class='bx bx-{{ $job->public ? "lock":"planet" }} text-{{ $job->public ? "secondary":"primary" }}'></i>
                            </a>
                            <a href="{{ route('dashboard.hr.jobs.show', $job) }}" wire:navigate class="ms-1">
                                <i class='text-success bx bx-show'></i>
                            </a>
                        </div>
                    </x-table.td>
                </tr>
            @endforeach
            </tbody>
        </x-table.default>
    @else
        <h5 class="text-secondary" style="text-align: center">{{ __("words.No records found.") }}</h5>
    @endif
    {{ $jobs->links() }}
</div>

@push('scripts')
    <x-alerts.livewireSweetAlert></x-alerts.livewireSweetAlert>
@endpush
