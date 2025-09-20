<div class="controls">
    <div class="entries-control">
        <span>{{ __('words.show') }}</span>
        <select id="entriesSelect" wire:model.live="perPage">
            @foreach($perPageOptions as $option)
                <option>{{ $option }}</option>
            @endforeach
        </select>
        <span>{{ __('words.entries') }}</span>
    </div>
</div>
