@if($status == 'waiting for answering' && !$answering)
    <span class="badge badge-primary">
        <i class="bx bxs-phone bx-tada"></i>
        {{ __("words.Pending") }}
    </span>
@elseif($status == 'interview requested')
    <span class="badge badge-info">
        <i class="bx bx-check"></i>
        {{ __("words.Interview Requested") }}
    </span>
@elseif($status == 'waiting for answering' && $answering)
    <span class="badge badge-success">
        <i class="bx bx-check"></i>
        {{ __("words.Answered") }}
    </span>
@else
    <span class="badge badge-danger">
        <i class="bx bx-x-circle"></i>
        {{ __("words.Not Answered") }}
    </span>
@endif
