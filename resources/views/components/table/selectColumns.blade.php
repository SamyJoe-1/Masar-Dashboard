@if(!empty($columns))
    <div class="d-flex gap-2">
        <button type="button" title="Clear Filter" class="btn btn-danger py-1 p-2 pe-1 rounded-3" onclick="window.location.href = window.location.pathname;">
            <i class="bx bx-refresh"></i>
        </button>
        <button type="button" class="btn btn-secondary py-1 p-2 pe-1 rounded-3" data-bs-toggle="modal" data-bs-target="#exampleModal">
            <i class="bx bx-table"></i>
        </button>
    </div>

    <div wire:ignore class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{ translateWord("Select Columns") }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="row row-cols-md-2 row-cols-1">
                            @foreach($columns as $column)
                                <div class="col">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input"
                                               wire:model="selectedColumns"
                                               value="{{ $column }}"
                                               id="col_{{ $column }}">
                                        <label class="form-check-label" for="col_{{ $column }}">
                                            {{ translateWord(dashToSpace($column)) }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" wire:click="update()" data-bs-dismiss="modal">{{ translateWord("OK") }}</button>
                </div>
            </div>
        </div>
    </div>
@endif
