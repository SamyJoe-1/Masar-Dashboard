@if($oman == 1)
    <span class="badge badge-success">
        <i class="bx bx-check"></i>
        {{ __("words.Omani") }}
    </span>
@elseif($oman == 2)
    <span class="badge badge-warning">
        <i class="bx bx-star"></i>
        {{ __("words.All") }}
    </span>
@else
    <span class="badge badge-danger">
        <i class="bx bx-x"></i>
        {{ __("words.Non-Omani") }}
    </span>
@endif
