<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Complete - Files Ready!</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: #3464b0;
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .success-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            margin: 50px auto;
            max-width: 1000px;
            padding: 40px;
        }

        .success-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .success-icon {
            font-size: 4rem;
            color: #28a745;
            margin-bottom: 20px;
            animation: bounce 2s infinite;
        }

        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
            40% { transform: translateY(-10px); }
            60% { transform: translateY(-5px); }
        }

        .directory-info {
            background: #f8f9fa;
            border: 2px solid #28a745;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
        }

        .file-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }

        .file-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            border: 1px solid #e9ecef;
            transition: all 0.3s ease;
        }

        .file-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }

        .file-icon {
            font-size: 2rem;
            margin-bottom: 10px;
        }

        .action-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 30px;
        }

        .btn-custom {
            border-radius: 25px;
            padding: 12px 30px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
        }

        .btn-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .directory-link {
            word-break: break-all;
            font-family: 'Courier New', monospace;
            background: #e9ecef;
            padding: 10px;
            border-radius: 8px;
            margin: 10px 0;
        }

        .stats {
            display: flex;
            justify-content: space-around;
            text-align: center;
            margin: 20px 0;
        }

        .stat-item {
            padding: 15px;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: #28a745;
        }

        .copy-button {
            cursor: pointer;
            color: #007bff;
        }

        .copy-button:hover {
            color: #0056b3;
        }

        .uuid-display {
            font-family: 'Courier New', monospace;
            font-size: 0.9rem;
            color: #495057;
            background: #e9ecef;
            padding: 8px 12px;
            border-radius: 6px;
            display: inline-block;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="success-container">
        <!-- Success Header -->
        <div class="success-header">
            <i class="fas fa-check-circle success-icon"></i>
            <h1 class="display-4 text-success mb-3">Upload Complete!</h1>
            <p class="lead text-muted">Your files have been successfully uploaded and organized</p>
        </div>

        <!-- Directory Information -->
        <div class="directory-info">
            <h3 class="text-center mb-3">
                <i class="fas fa-folder-open me-2"></i>
                Your Directory
            </h3>

            <div class="stats">
                <div class="stat-item">
                    <div class="stat-number">{{ count($files) }}</div>
                    <div class="text-muted">Files</div>
                </div>
                <div class="stat-item">
                    <div class="uuid-display">{{ $directory_uuid }}</div>
                    <div class="text-muted">Directory UUID</div>
                </div>
            </div>

            <div class="directory-link">
                <strong>Directory Path:</strong>
                <span id="directoryPath">{{ $directory_path }}</span>
                <i class="fas fa-copy copy-button ms-2" onclick="copyToClipboard('directoryPath')" title="Copy to clipboard"></i>
            </div>

            <div class="directory-link">
                <strong>Access URL:</strong>
                <a href="{{ $directory_url }}" target="_blank" id="directoryUrl">{{ $directory_url }}</a>
                <i class="fas fa-copy copy-button ms-2" onclick="copyToClipboard('directoryUrl')" title="Copy to clipboard"></i>
            </div>

            <div class="directory-link">
                <strong>Direct Link:</strong>
                <a href="{{ url('/result/preview/' . $directory_uuid) }}" target="_blank" id="directLink">{{ url('/result/preview/' . $directory_uuid) }}</a>
                <i class="fas fa-copy copy-button ms-2" onclick="copyToClipboard('directLink')" title="Copy to clipboard"></i>
            </div>
        </div>

        <!-- Files List -->
        <h4 class="mb-4">
            <i class="fas fa-files me-2"></i>
            Uploaded Files ({{ count($files) }})
        </h4>

        <div class="file-grid">
            @foreach($files as $file)
                <div class="file-card">
                    <div class="text-center">
                        @php
                            $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                            $iconClass = 'fas fa-file';
                            $iconColor = '#6c757d';

                            if(in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'])) {
                                $iconClass = 'fas fa-image';
                                $iconColor = '#28a745';
                            } elseif(in_array($extension, ['pdf'])) {
                                $iconClass = 'fas fa-file-pdf';
                                $iconColor = '#dc3545';
                            } elseif(in_array($extension, ['doc', 'docx'])) {
                                $iconClass = 'fas fa-file-word';
                                $iconColor = '#007bff';
                            } elseif(in_array($extension, ['xls', 'xlsx'])) {
                                $iconClass = 'fas fa-file-excel';
                                $iconColor = '#28a745';
                            } elseif(in_array($extension, ['ppt', 'pptx'])) {
                                $iconClass = 'fas fa-file-powerpoint';
                                $iconColor = '#fd7e14';
                            } elseif(in_array($extension, ['mp4', 'avi', 'mov', 'wmv'])) {
                                $iconClass = 'fas fa-file-video';
                                $iconColor = '#6f42c1';
                            } elseif(in_array($extension, ['mp3', 'wav', 'flac'])) {
                                $iconClass = 'fas fa-file-audio';
                                $iconColor = '#e83e8c';
                            } elseif(in_array($extension, ['zip', 'rar', '7z'])) {
                                $iconClass = 'fas fa-file-archive';
                                $iconColor = '#fd7e14';
                            } elseif(in_array($extension, ['txt', 'rtf'])) {
                                $iconClass = 'fas fa-file-alt';
                                $iconColor = '#6c757d';
                            }
                        @endphp

                        <i class="{{ $iconClass }} file-icon" style="color: {{ $iconColor }}"></i>
                        <h6 class="mb-2">{{ $file['name'] }}</h6>
                        <p class="text-muted mb-2">
                            <small>
                                <i class="fas fa-weight me-1"></i>{{ $file['size'] }}
                                <br>
                                <i class="fas fa-clock me-1"></i>{{ $file['modified'] }}
                            </small>
                        </p>
                        <a href="{{ $file['url'] }}" target="_blank" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-eye me-1"></i>View
                        </a>
                        <a href="{{ $file['url'] }}" download class="btn btn-sm btn-outline-success">
                            <i class="fas fa-download me-1"></i>Download
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Action Buttons -->
        <div class="action-buttons">
            <a href="{{ route('upload.form') }}" class="btn btn-primary btn-custom">
                <i class="fas fa-plus me-2"></i>Upload More Files
            </a>
            <a href="{{ $directory_url }}" target="_blank" class="btn btn-success btn-custom">
                <i class="fas fa-folder-open me-2"></i>Open Directory
            </a>
            <button onclick="downloadAll()" class="btn btn-info btn-custom">
                <i class="fas fa-download me-2"></i>Download All
            </button>
            <button onclick="shareDirectory()" class="btn btn-warning btn-custom">
                <i class="fas fa-share me-2"></i>Share Directory
            </button>
            <button onclick="deleteDirectory()" class="btn btn-danger btn-custom">
                <i class="fas fa-trash me-2"></i>Delete Directory
            </button>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
<script>
    // Copy to clipboard function
    function copyToClipboard(elementId) {
        const element = document.getElementById(elementId);
        const text = element.textContent || element.href;

        navigator.clipboard.writeText(text).then(() => {
            // Show success feedback
            const icon = element.nextElementSibling;
            const originalClass = icon.className;
            icon.className = 'fas fa-check text-success ms-2';
            setTimeout(() => {
                icon.className = originalClass;
            }, 2000);
        }).catch(err => {
            console.error('Failed to copy: ', err);
            alert('Failed to copy to clipboard');
        });
    }

    // Download all files
    function downloadAll() {
        const files = @json($files);
        files.forEach((file, index) => {
            setTimeout(() => {
                const link = document.createElement('a');
                link.href = file.url;
                link.download = file.name;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }, index * 500); // Stagger downloads
        });
    }

    // Share directory
    function shareDirectory() {
        const directoryUrl = '{{ url("/result/preview/" . $directory_uuid) }}';

        if (navigator.share) {
            navigator.share({
                title: 'File Directory - {{ $directory_uuid }}',
                text: 'Check out these uploaded files',
                url: directoryUrl
            });
        } else {
            copyToClipboard('directLink');
            alert('Directory URL copied to clipboard!');
        }
    }

    // Delete directory
    function deleteDirectory() {
        const directoryUuid = '{{ $directory_uuid }}';

        if (confirm('Are you sure you want to delete this directory and all files? This action cannot be undone!')) {
            fetch(`/directory/${directoryUuid}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Directory deleted successfully!');
                        window.location.href = '{{ route("upload.form") }}';
                    } else {
                        alert('Failed to delete directory: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while deleting the directory');
                });
        }
    }
</script>
</body>
</html>
