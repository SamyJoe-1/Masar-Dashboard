<script>
    theme = {
        background: "{{ session()->get('theme') == 'dark-theme' ? '#1e1e1e' : '#ffffff' }}",
        color: "{{ session()->get('theme') == 'dark-theme' ? '#ffffff' : '#545454' }}",
        buttonColor: "#3085d6",
    };

    window.addEventListener('closeModal', event => {
        $('#addProductModal').modal('hide');
        $('.btn-close').click();
    });
    window.addEventListener('swal:success', event => {

        Swal.fire({
            title: event.detail[0].title,
            text: event.detail[0].text,
            icon: event.detail[0].icon,
            color: theme.color,
            background: theme.background,
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'OK'
        });
    });

    window.addEventListener('swal:confirm', event => {
        Swal.fire({
            title: event.detail[0].title,
            text: event.detail[0].text,
            icon: event.detail[0].icon,
            background: theme.background,
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: event.detail[0].confirmButtonText ?? 'Confirm',
            cancelButtonText: event.detail[0].cancelButtonText ?? "Cancel"
        }).then((result) => {
            if (result.isConfirmed) {
                Livewire.dispatch('confirmDelete', { id: event.detail[0].id }); // no errors no logs
            }
        });
    });

    window.addEventListener('swal:success_redirect', event => {

        Swal.fire({
            title: event.detail[0].title,
            text: event.detail[0].text,
            icon: event.detail[0].icon,
            color: theme.color,
            background: theme.background,
            showCancelButton: true,  // Show the cancel (Close) button
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: event.detail[0].confirmButtonText ?? 'Explore',
            cancelButtonText: event.detail[0].cancelButtonText ?? 'Close'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = `${event.detail[0].onConfirm}`; // Replace with the actual URL
            }
        });
    });

    window.addEventListener('swal:error', event => {
        Swal.fire({
            title: event.detail[0].title,
            text: event.detail[0].text,
            icon: event.detail[0].icon,
            color: theme.color,
            background: theme.background,
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'OK'
        });
    });

    window.addEventListener('swal:festival', event => {
        Swal.fire({
            title: event.detail[0].title,
            imageUrl: '{{ asset('assets/gif/birthday.gif') }}',
            imageWidth: 150,
            imageHeight: 150,
            confirmButtonColor: '#ff4081',
            confirmButtonText: event.detail[0].text,
            background: '#fff3cd',
        });
    });
</script>
