<div>
    <div class="d-flex justify-content-between" wire:ignore>
        <div class="flex-fill">
            <x-table.filterBar search="1"></x-table.filterBar>
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
                    <x-table.td :columns="$selectedColumns" column="job_id">{{ $applicant->job_id }}</x-table.td>
                    <x-table.td :columns="$selectedColumns" column="processing">
                        <x-badge.active :status="$applicant->processing"></x-badge.active>
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
{{--                            <a href="{{ route('dashboard.hr.applicants.show', $applicant) }}" wire:navigate class="ms-1"><i class='text-success bx bx-show'></i></a>--}}
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
