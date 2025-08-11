<!-- Bulk Actions -->
<div class="{{ count($selected) > 0 ? "d-flex":"d-none" }} align-items-center mb-3 bg-light p-2 rounded">
    <span class="me-2"><strong>{{ count($selected) }}</strong> {{ __('words.item') }}</span>
    <select wire:model="bulkAction" class="form-select form-select-sm me-2" id="bulkActionSelect" onchange="changeOptionBulk(this)" style="width: auto">
        {{ @$slot }}
    </select>
    <div id="bulkButtonContent">
        <button class="btn btn-sm btn-primary" aria-label="none" wire:click="applyBulkAction">{{ __('words.confirm') }}</button>
    </div>
</div>
