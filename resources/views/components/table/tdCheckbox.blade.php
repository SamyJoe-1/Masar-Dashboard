<td class="border-top-style">
    @if(empty($hide))
        <div class="d-flex align-items-center justify-content-center p-0 mt-1">
            <input type="checkbox" class="form-check-input fs-6 p-0" wire:model.live.debounce.100ms="selected" value="{{ @$value }}">
        </div>
    @endif
</td>
