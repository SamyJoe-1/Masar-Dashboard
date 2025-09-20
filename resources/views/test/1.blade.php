<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Form with SweetAlert</title>
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<h2>Simple Contact Form</h2>

<form id="myForm">
    <label>Name:</label><br>
    <input type="text" name="name" required><br><br>

    <label>Email:</label><br>
    <input type="email" name="email" required><br><br>

    <button type="submit">Submit</button>
</form>

<script>
    document.getElementById('myForm').addEventListener('submit', function(e) {
        e.preventDefault(); // Prevent form submission

        const name = this.name.value;
        const email = this.email.value;

        Swal.fire({
            title: 'Submitted!',
            html: `Name: <b>${name}</b><br>Email: <b>${email}</b>`,
            icon: 'success'
        });

        // Optionally reset the form
        this.reset();
    });
</script>
</body>
</html>
