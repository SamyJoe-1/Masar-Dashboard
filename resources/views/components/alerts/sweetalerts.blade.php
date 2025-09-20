<div class="position-relative" align="center">
    <div class="d-inline-block">
        @if(count($errors) > 0)
            @foreach($errors->all() as $i)
                <x-alerts.sweetalert.validate msg="{{ $i }}"></x-alerts.sweetalert.validate>
                @break
            @endforeach
        @endif
        @if(\Session::has('success'))
            <x-alerts.sweetalert.success msg="{{ \Session::get('success') }}"></x-alerts.sweetalert.success>
        @endif
        @if(\Session::has('warning'))
            <x-alerts.sweetalert.validate msg="{{ \Session::get('warning') }}"></x-alerts.sweetalert.validate>
        @endif
        @if(\Session::has('error'))
            <x-alerts.sweetalert.error msg="{{ \Session::get('error') }}"></x-alerts.sweetalert.error>
        @endif
    </div>
</div>
