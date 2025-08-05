<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Upload with Progress</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('styles/css/upload.css') }}" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container">
    <div class="upload-container">
        <h2 class="text-center mb-4">
            <i class="fas fa-cloud-upload-alt me-2"></i>
            File Upload Center
        </h2>

        <form id="uploadForm" action="/upload-files" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="upload-box" id="uploadBox">
                <i class="fas fa-upload fa-3x mb-3 text-primary"></i>
                <h4>Drag & Drop Files Here</h4>
                <p class="mb-3">or click to browse files</p>
                <input type="file" id="fileInput" name="files[]" multiple class="file-input" accept="*/*">
                <button type="button" class="btn btn-outline-primary" onclick="document.getElementById('fileInput').click()">
                    <i class="fas fa-folder-open me-2"></i>Choose Files
                </button>
            </div>

            <div id="fileList" class="mt-4"></div>

            <div class="text-center mt-4">
                <button type="submit" id="submitBtn" class="btn btn-success submit-btn" disabled>
                    <i class="fas fa-paper-plane me-2"></i>
                    Upload Files
                </button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('styles/js/upload.js') }}"></script>
</body>
</html>
