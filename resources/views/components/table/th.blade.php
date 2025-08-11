@php($display = empty($columns) ? "":(in_array($column, $columns) ? "":"none"))
@if(!empty($sortDir))
    @php($direction = $sortDir == "desc" ? "6,15 12,9 18,15":"6,9 12,15 18,9")
@endif
<th data-column="{{ $column }}" {{ !$sorting ? "_":"" }}wire:click="sortBy('{{ @$column }}')" style="cursor: {{ !$sorting ? "":"pointer" }};display: {{ $display }}">
    {{ __("words.$slot") }}
    <svg class="sort-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="opacity: 1;display: {{ $sortBy == $column ? 'flex':'none' }}">
        <polyline points="{{ $direction }}"></polyline>
    </svg>
</th>
