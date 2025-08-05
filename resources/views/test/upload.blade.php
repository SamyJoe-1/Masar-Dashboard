<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Upload with Progress</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        .upload-container {
            max-width: 800px;
            margin: 50px auto;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .upload-box {
            background: rgb(255 255 255 / 56%);
            color: #333;
            border: 3px dashed #667eea;
            border-radius: 10px;
            padding: 40px;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .upload-box:hover {
            border-color: #764ba2;
            background: rgba(255,255,255,1);
            transform: translateY(-2px);
        }

        .upload-box.dragover {
            border-color: #28a745;
            background: rgba(40, 167, 69, 0.1);
        }

        .file-input {
            display: none;
        }

        .file-item {
            background: rgba(255,255,255,0.9);
            color: #333;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .progress {
            height: 8px;
            border-radius: 4px;
            overflow: hidden;
        }

        .progress-bar {
            transition: width 0.3s ease;
        }

        .file-size {
            font-size: 0.85em;
            color: #666;
        }

        .remove-file {
            cursor: pointer;
            color: #dc3545;
            font-size: 1.2em;
        }

        .remove-file:hover {
            color: #c82333;
        }

        .submit-btn {
            background: linear-gradient(45deg, #28a745, #20c997);
            border: none;
            padding: 12px 30px;
            border-radius: 25px;
            font-weight: bold;
            transition: all 0.3s ease;
        }

        .submit-btn:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.4);
        }

        .submit-btn:disabled {
            background: #6c757d;
            cursor: not-allowed;
        }
    </style>
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
<script>
    class FileUploader {
        constructor() {
            this.files = [];
            this.uploadBox = document.getElementById('uploadBox');
            this.fileInput = document.getElementById('fileInput');
            this.fileList = document.getElementById('fileList');
            this.submitBtn = document.getElementById('submitBtn');
            this.uploadForm = document.getElementById('uploadForm');

            this.initEventListeners();
        }

        initEventListeners() {
            // File input change
            this.fileInput.addEventListener('change', (e) => {
                this.handleFiles(Array.from(e.target.files));
            });

            // Drag and drop
            this.uploadBox.addEventListener('dragover', (e) => {
                e.preventDefault();
                this.uploadBox.classList.add('dragover');
            });

            this.uploadBox.addEventListener('dragleave', () => {
                this.uploadBox.classList.remove('dragover');
            });

            this.uploadBox.addEventListener('drop', (e) => {
                e.preventDefault();
                this.uploadBox.classList.remove('dragover');
                this.handleFiles(Array.from(e.dataTransfer.files));
            });

            // Form submit
            this.uploadForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.uploadFiles();
            });
        }

        handleFiles(newFiles) {
            newFiles.forEach(file => {
                if (!this.files.find(f => f.name === file.name && f.size === file.size)) {
                    this.files.push(file);
                    this.addFileToList(file);
                }
            });
            this.updateSubmitButton();
        }

        addFileToList(file) {
            const fileId = 'file-' + Date.now() + '-' + Math.random().toString(36).substr(2, 9);
            const fileSize = this.formatFileSize(file.size);

            const fileItem = document.createElement('div');
            fileItem.className = 'file-item';
            fileItem.id = fileId;
            fileItem.innerHTML = `
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <strong>${file.name}</strong>
                            <span class="file-size ms-2">(${fileSize})</span>
                        </div>
                        <i class="fas fa-times remove-file" onclick="fileUploader.removeFile('${fileId}', '${file.name}')"></i>
                    </div>
                    <div class="progress">
                        <div class="progress-bar bg-primary" role="progressbar" style="width: 0%"></div>
                    </div>
                    <div class="d-flex justify-content-between mt-1">
                        <small class="text-muted">Ready to upload</small>
                        <small class="text-muted">0%</small>
                    </div>
                `;

            this.fileList.appendChild(fileItem);
        }

        removeFile(fileId, fileName) {
            this.files = this.files.filter(f => f.name !== fileName);
            document.getElementById(fileId).remove();
            this.updateSubmitButton();
        }

        updateSubmitButton() {
            this.submitBtn.disabled = this.files.length === 0;
        }

        formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        async uploadFiles() {
            this.submitBtn.disabled = true;
            this.submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Uploading...';

            const formData = new FormData();

            // Add CSRF token
            const csrfToken = document.querySelector('input[name="_token"]').value;
            formData.append('_token', csrfToken);

            // Add all files
            this.files.forEach((file, index) => {
                formData.append('files[]', file);
            });

            // Simulate upload progress for each file
            const fileItems = this.fileList.querySelectorAll('.file-item');
            fileItems.forEach((item, index) => {
                this.simulateUploadProgress(item, index);
            });

            try {
                // Wait for all progress bars to complete
                await new Promise(resolve => setTimeout(resolve, 3000));

                // Actually submit the form
                const response = await fetch('/upload-files', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (response.ok) {
                    const result = await response.json();
                    this.showSuccess(result);
                } else {
                    throw new Error('Upload failed');
                }
            } catch (error) {
                this.showError(error.message);
            } finally {
                this.submitBtn.innerHTML = '<i class="fas fa-paper-plane me-2"></i>Upload Files';
                this.submitBtn.disabled = false;
            }
        }

        simulateUploadProgress(fileItem, index) {
            const progressBar = fileItem.querySelector('.progress-bar');
            const statusText = fileItem.querySelectorAll('small')[0];
            const percentText = fileItem.querySelectorAll('small')[1];

            let progress = 0;
            const interval = setInterval(() => {
                progress += Math.random() * 15;
                if (progress >= 100) {
                    progress = 100;
                    clearInterval(interval);
                    statusText.textContent = 'Upload complete';
                    statusText.className = 'text-success';
                    progressBar.className = 'progress-bar bg-success';
                } else {
                    statusText.textContent = 'Uploading...';
                    statusText.className = 'text-primary';
                }

                progressBar.style.width = progress + '%';
                percentText.textContent = Math.round(progress) + '%';
            }, 100 + (index * 50));
        }

        showSuccess(result) {
            alert('Files uploaded successfully! ' + result.message);
            // Optionally redirect or refresh
            // window.location.reload();
        }

        showError(message) {
            alert('Upload failed: ' + message);
        }
    }

    // Initialize the file uploader
    const fileUploader = new FileUploader();
</script>
</body>
</html>
