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
            </div>
        </div>
    </div>
</div>
