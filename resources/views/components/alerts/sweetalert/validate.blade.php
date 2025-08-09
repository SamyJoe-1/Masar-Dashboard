<script>
    Swal.fire({
        title: "{{ __('alerts.Validate error') }}",
        text: "{{ $msg ?? null }}",
        // color: 'white',
        icon: "warning"
    });
</script>
