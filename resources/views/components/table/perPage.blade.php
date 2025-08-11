<div class="controls">
    <div class="entries-control">
        <span>عرض</span>
        <select id="entriesSelect" wire:model.live="perPage">
            @foreach($perPageOptions as $option)
                <option>{{ $option }}</option>
            @endforeach
        </select>
        <span>إدخال</span>
    </div>
</div>
