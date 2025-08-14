@if($processing)
    <span class="badge badge-success">
        <i class="bx bx-check"></i>
        {{ __("words.Done") }}
    </span>

@else
    <span class="badge badge-warning">
        <i class="bx bx-loader-alt bx-spin"></i>
        {{ __("words.Pending") }}
    </span>
@endif
