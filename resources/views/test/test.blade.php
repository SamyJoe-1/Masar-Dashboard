<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Uploaded Files</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        .files-container {
            max-width: 1000px;
            margin: 50px auto;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .file-card {
            background: rgba(255,255,255,0.95);
            color: #333;
            border-radius: 10px;
            margin-bottom: 15px;
            overflow: hidden;
            transition: transform 0.3s ease;
        }

        .file-card:hover {
            transform: translateY(-2px);
        }

        .file-icon {
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            border-radius: 8px;
        }

        .file-size {
            color: #666;
            font-size: 0.9em;
        }

        .btn-download {
            background: linear-gradient(45deg, #007bff, #0056b3);
            border: none;
            color: white;
        }

        .btn-delete {
            background: linear-gradient(45deg, #dc3545, #c82333);
            border: none;
            color: white;
        }
    </style>
</head>
<body class="bg-light">
<div class="container">
    <div class="files-container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>
                <i class="fas fa-files me-2"></i>
                Uploaded Files
            </h2>
            <a href="{{ route('upload.form') }}" class="btn btn-light">
                <i class="fas fa-plus me-2"></i>Upload More
            </a>
        </div>

        @if(isset($uploadedFiles) && count($uploadedFiles) > 0)
            <div class="row">
                @foreach($uploadedFiles as $file)
                    <div class="col-md-6 col-lg-4 mb-3">
                        <div class="file-card">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="file-icon bg-primary text-white me-3">
                                        @php
                                            $extension = strtolower(pathinfo($file['original_name'], PATHINFO_EXTENSION));
                                            $iconClass = match($extension) {
                                                'pdf' => 'fas fa-file-pdf',
                                                'doc', 'docx' => 'fas fa-file-word',
                                                'xls', 'xlsx' => 'fas fa-file-excel',
                                                'ppt', 'pptx' => 'fas fa-file-powerpoint',
                                                'jpg', 'jpeg', 'png', 'gif' => 'fas fa-file-image',
                                                'mp4', 'avi', 'mov' => 'fas fa-file-video',
                                                'mp3', 'wav' => 'fas fa-file-audio',
                                                'zip', 'rar' => 'fas fa-file-archive',
                                                default => 'fas fa-file'
                                            };
                                        @endphp
                                        <i class="{{ $iconClass }}"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1 text-truncate" title="{{ $file['original_name'] }}">
                                            {{ $file['original_name'] }}
                                        </h6>
                                        <small class="file-size">
                                            {{ number_format($file['size'] / 1024, 2) }} KB
                                        </small>
                                    </div>
                                </div>

                                <div class="d-flex gap-2">
                                    <a href="{{ $file['url'] }}"
                                       class="btn btn-download btn-sm flex-fill"
                                       download="{{ $file['original_name'] }}">
                                        <i class="fas fa-download me-1"></i>Download
                                    </a>
                                    <button class="btn btn-delete btn-sm"
                                            onclick="deleteFile('{{ $file['stored_name'] }}')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="text-center mt-4">
                <div class="alert alert-success d-inline-block">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ count($uploadedFiles) }} file(s) uploaded successfully!
                </div>
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-folder-open fa-4x mb-3 opacity-50"></i>
                <h4>No files uploaded yet</h4>
                <p class="mb-4">Upload some files to see them here.</p>
                <a href="{{ route('upload.form') }}" class="btn btn-light btn-lg">
                    <i class="fas fa-upload me-2"></i>Start Uploading
                </a>
            </div>
        @endif
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
<script>
    function deleteFile(filename) {
        if (confirm('Are you sure you want to delete this file?')) {
            fetch(`/delete-file/${filename}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Error deleting file: ' + data.message);
                    }
                })
                .catch(error => {
                    alert('Error deleting file: ' + error.message);
                });
        }
    }
</script>
</body>
</html>
