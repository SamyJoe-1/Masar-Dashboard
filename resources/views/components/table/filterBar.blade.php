<div id="filterBarContent" class="collapse mt-3" dir="ltr" style="margin: 10px">
    <div class="d-flex justify-content-start align-content-start">
        <div class="flex-fill">
            <div class="row row-cols-lg-4 row-cols-md-3 row-cols-sm-2 row-cols-1 align-items-end g-3 mb-3">
                @if(!empty($search))
                    <div class="col">
                        <div class="input-group">
                            <span class="input-group-text p-1 rounded-start-pill">
                                <i class="bx bx-search-alt-2"></i>
                            </span>
                            <input wire:model.live.debounce.300ms="search" type="text" class="form-control rounded-end-pill" placeholder="{{ __('words.searching') }}">
                        </div>
                    </div>
                @endif
                @if(!empty($statuses))
                    <div class="col">
                        <div class="quiet-select-wrapper">
                            <label for="status">{{ __("words.Status") }}</label>
                            <select id="status" class="quiet-select" wire:model.live.debounce.300ms="selectedStatus">
                                <option value="">{{ __("words.select :items", ["items" => __("words.Status")]) }}</option>
                                @foreach($statuses as $status)
                                    <option value="{{ $status }}">{{ __("words.$status") }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
