@if($status == 'pending')
    <span class="badge badge-warning">
        <i class="{{ @$icon }}"></i>
        {{ __("words.Pending") }}
    </span>
@elseif($status == 'rejected')
    <span class="badge badge-danger">
        <i class="{{ @$icon }}"></i>
        {{ __("words.Rejected") }}
    </span>
@elseif($status == 'waiting for answering')
    @empty($form)
        <span class="badge badge-primary">
            <i class="{{ @$icon }}"></i>
            {{ __("words.Waiting for Answering") }}
        </span>
    @else
        @if($form == 'answered')
            <span class="badge badge-warning">
                <i class="{{ @$icon }}"></i>
                {{ __("words.pending") }}
            </span>
        @else
            <span class="badge badge-danger">
                <i class="{{ @$icon }}"></i>
                {{ __("words.Not Answered") }}
            </span>
        @endif
    @endempty

@elseif($status == 'interview requested')
    <span class="badge badge-info">
        <i class="{{ @$icon }}"></i>
        {{ __("words.Interview Requested") }}
    </span>
@elseif($status == 'approved')
    <span class="badge badge-success">
        <i class="{{ @$icon }}"></i>
        {{ __("words.Approved") }}
    </span>
@else
    <span class="badge badge-secondary">
        <i class="bx bx-x-circle"></i>
        {{ __("words.Unknown") }}
    </span>
@endif
