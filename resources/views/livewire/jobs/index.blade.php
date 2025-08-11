<div>
    <div class="d-flex justify-content-between mb-3" wire:ignore>
        <div class="flex-fill">
            <x-table.filterBar search="1"></x-table.filterBar>
        </div>
    </div>
    <x-table.bulkActions :selected="$selected">
        <option value="">اختر الحدث</option>
        <option value="delete">حذف</option>
    </x-table.bulkActions>
    <div class="d-flex justify-content-between align-items-center">
        <x-table.perPage :per-page-options="$perPageOptions"></x-table.perPage>
        <div>
            <button class="btn btn-primary rounded-5 p-1 px-3" data-bs-toggle="collapse" data-bs-target="#filterBarContent" style="margin: 10px">
                <i class="bx bx-filter-alt"></i>
                فرز
            </button>
        </div>
    </div>
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
                <x-table.td :columns="$selectedColumns" column="description">{{ $job->description }}</x-table.td>
                <x-table.td :columns="$selectedColumns" column="applicants_count">{{ $job->applicants_count }}</x-table.td>
                <x-table.td :columns="$selectedColumns" column="approved_applicants_count">{{ $job->approved_applicants_count }}</x-table.td>
                <x-table.td :columns="$selectedColumns" column="rejected_applicants_count">{{ $job->rejected_applicants_count }}</x-table.td>
                <x-table.td :columns="$selectedColumns" column="close">{{ $job->close }}</x-table.td>
                <x-table.td :columns="$selectedColumns" column="public">{{ $job->public }}</x-table.td>
                <x-table.td :columns="$selectedColumns" column="created_at">{{ dateFormat_1($job->created_at) }}</x-table.td>
                <x-table.td :columns="$selectedColumns" column="updated_at">{{ dateFormat_1($job->updated_at) }}</x-table.td>
                <x-table.td :columns="$selectedColumns" column="action">
                    <div class="d-flex order-actions">
                        <a wire:click="Delete({{ $job->id }})" class="cursor-pointer"><i class='bx bx-trash text-danger'></i></a>
                        <a href="{{ route('dashboard.hr.jobs.edit', $job) }}" wire:navigate class="ms-1"><i class='text-primary bx bx-edit'></i></a>
                    </div>
                </x-table.td>
            </tr>
        @endforeach
        </tbody>
    </x-table.default>
    {{ $jobs->links() }}
</div>

@push('scripts')
    <x-alerts.livewireSweetAlert></x-alerts.livewireSweetAlert>
@endpush
