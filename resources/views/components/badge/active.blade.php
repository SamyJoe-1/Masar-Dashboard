@if($status)
    <span class="badge badge-success">
        <i class="bx bxs-circle"></i>
        {{ __("words.Active") }}
    </span>
@else
    <span class="badge badge-secondary">
        <i class="bx bx-x-circle"></i>
        {{ __("words.Closed") }}
    </span>
@endif
