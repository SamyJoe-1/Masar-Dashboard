<div>
    <div class="d-flex justify-content-between" wire:ignore>
        <div class="flex-fill">
            <x-table.filterBar search="1" :statuses="$statuses"></x-table.filterBar>
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
    @if(count($applicants))
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
            @foreach($applicants as $applicant)
                <tr>
                    <x-table.tdCheckbox :value="$applicant->id"></x-table.tdCheckbox>
                    <x-table.td :columns="$selectedColumns" column="id">{{ $applicant->id }}</x-table.td>
                    <x-table.td :columns="$selectedColumns" column="job_title">
                        <a class="hyperText" href="{{ route('dashboard.hr.jobs.show', $applicant->job_id) }}">
                            <i class="bx bx-arrow-to-right"></i>
                            {{ $applicant->job_title ?? '-' }}
                        </a>
                    </x-table.td>
                    <x-table.td :columns="$selectedColumns" column="name">
                        <span class="truncate-text size-2" title="{{ @$applicant->name }}">
                            {{ $applicant->name ?? '-' }}
                        </span>
                    </x-table.td>
                    <x-table.td :columns="$selectedColumns" column="email">
                        <a class="hyperText" href="mailto:{{ $applicant->email }}">
                            <i class="bx bx-envelope"></i>
                            <span class="truncate-text size-2" title="{{ @$applicant->email }}">
                                {{ $applicant->email ?? "-" }}
                            </span>
                        </a>
                    </x-table.td>
                    <x-table.td :columns="$selectedColumns" column="phone">
                        <a class="hyperText" href="tel:{{ $applicant->phone }}">
                            <i class="bx bx-phone"></i>
                            <span class="truncate-text size-1" title="{{ @$applicant->phone }}">
                                {{ $applicant->phone ?? "-" }}
                            </span>
                        </a>
                    </x-table.td>
                    <x-table.td :columns="$selectedColumns" column="processing">
                        <x-badge.processing :status="$applicant->status" :processing="$applicant->processing"></x-badge.processing>
                    </x-table.td>
                    <x-table.td :columns="$selectedColumns" column="answering">
                        <x-badge.answering :status="$applicant->status" :answering="$applicant->answering"></x-badge.answering>
                    </x-table.td>
                    <x-table.td :columns="$selectedColumns" column="status">
                        <x-badge.applicant :status="$applicant->status" :icon="$applicant->getIcon()"></x-badge.applicant>
                    </x-table.td>
                    <x-table.td :columns="$selectedColumns" column="created_at">{{ dateFormat_1($applicant->created_at) }}</x-table.td>
                    <x-table.td :columns="$selectedColumns" column="updated_at">{{ dateFormat_1($applicant->updated_at) }}</x-table.td>
                    <x-table.td :columns="$selectedColumns" column="action">
                        <div class="d-flex card-actions">
                            <a wire:click="Delete({{ $applicant->id }})" class="cursor-pointer"><i class='bx bx-trash text-danger'></i></a>
                            <a href="{{ asset(@$applicant->file->fullpath) }}" target="_blank" class="ms-1"><i class='text-success bx bx-show'></i></a>
                            <a href="{{ asset(@$applicant->file->fullpath) }}" download="{{ $applicant->getFileName() }}" class="ms-1"><i class='text-primary bx bx-download'></i></a>
                        </div>
                    </x-table.td>
                </tr>
            @endforeach
            </tbody>
        </x-table.default>
    @else
        <h5 class="text-secondary" style="text-align: center">{{ __("words.No records found.") }}</h5>
    @endif
    {{ $applicants->links() }}
</div>

@push('scripts')
    <x-alerts.livewireSweetAlert></x-alerts.livewireSweetAlert>
@endpush
