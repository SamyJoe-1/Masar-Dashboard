class FileUploader {
    constructor() {
        // Configuration - Easy to modify
        this.maxTotalSizeMB = 200; // Set your limit here in MB
        this.allowedExtensions = [
            // 'jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg', // Images
            // 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', // Documents
            // 'txt', 'rtf', 'csv', // Text files
            // 'mp4', 'avi', 'mov', 'wmv', 'flv', 'webm', // Videos
            // 'mp3', 'wav', 'flac', 'aac', 'ogg', // Audio
            // 'zip', 'rar', '7z', 'tar', 'gz', // Archives
            // 'exe',
            // 'js', 'css', 'html', 'php', 'py', 'java', 'cpp', 'c' // Code files
            'doc', 'docx', 'pdf'
        ];

        this.files = [];
        this.currentXHR = null; // Store current upload request
        this.uploadBox = document.getElementById('uploadBox');
        this.fileInput = document.getElementById('fileInput');
        this.fileList = document.getElementById('fileList');
        this.submitBtn = document.getElementById('submitBtn');
        this.uploadForm = document.getElementById('uploadForm');
        this.isUploading = false;
        this.uploadedDirectory = null;

        this.initEventListeners();
        this.addControlButtons();
    }

    addControlButtons() {
        // Add cancel button next to submit button
        const buttonContainer = this.submitBtn.parentNode;

        this.cancelBtn = document.createElement('button');
        this.cancelBtn.type = 'button';
        this.cancelBtn.className = 'btn btn-success submit-btn mt-4';
        this.cancelBtn.id = 'cancelBtn';
        this.cancelBtn.style.display = 'none';
        this.cancelBtn.innerHTML = `<i class="fas fa-times me-2"></i>${window.translations.cancel_upload}`;
        this.cancelBtn.onclick = () => this.cancelUpload();

        this.finalSubmitBtn = document.createElement('button');
        this.finalSubmitBtn.type = 'button';
        this.finalSubmitBtn.className = 'btn btn-primary ms-2';
        this.finalSubmitBtn.id = 'finalSubmitBtn';
        this.finalSubmitBtn.style.display = 'none';
        this.finalSubmitBtn.innerHTML = `<i class="fas fa-check me-2"></i>${window.translations.confirm}`;
        this.finalSubmitBtn.onclick = () => this.finalSubmit();

        buttonContainer.appendChild(this.cancelBtn);
        buttonContainer.appendChild(this.finalSubmitBtn);
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
        const validFiles = [];
        const errors = [];

        newFiles.forEach(file => {
            // Check if file already exists
            if (this.files.find(f => f.name === file.name && f.size === file.size)) {
                errors.push(`${window.translations.file} "${file.name}" ${window.translations.already_added}`);
                return;
            }

            // Check extension
            const extension = file.name.split('.').pop().toLowerCase();
            if (!this.allowedExtensions.includes(extension)) {
                errors.push(`${window.translations.file} "${file.name}" ${window.translations.invalid_extension}. ${window.translations.allowed}: ${this.allowedExtensions.join(', ')}`);
                return;
            }

            validFiles.push(file);
        });

        // Check total size limit
        const currentSize = this.files.reduce((sum, file) => sum + file.size, 0);
        const newSize = validFiles.reduce((sum, file) => sum + file.size, 0);
        const totalSize = currentSize + newSize;
        const maxSizeBytes = this.maxTotalSizeMB * 1024 * 1024;

        if (totalSize > maxSizeBytes) {
            const currentSizeMB = (currentSize / 1024 / 1024).toFixed(2);
            const newSizeMB = (newSize / 1024 / 1024).toFixed(2);
            errors.push(`${window.translations.size_limit_exceeded}! ${window.translations.current}: ${currentSizeMB}MB, ${window.translations.adding}: ${newSizeMB}MB, ${window.translations.limit}: ${this.maxTotalSizeMB}MB`);
        } else {
            // Add valid files
            validFiles.forEach(file => {
                this.files.push(file);
                this.addFileToList(file);
            });
        }

        // Show errors if any
        if (errors.length > 0) {
            Swal.fire({
                title: window.translations.some_files_not_added,
                text: `${window.translations.error}: ${errors}\n`,
                icon: 'error',
            });
        }

        this.updateSubmitButton();
        this.updateSizeDisplay();
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
                        <small class="text-muted">${window.translations.ready_to_upload}</small>
                        <small class="text-muted">0%</small>
                    </div>
                `;

        this.fileList.appendChild(fileItem);
    }

    removeFile(fileId, fileName) {
        this.files = this.files.filter(f => f.name !== fileName);
        document.getElementById(fileId).remove();
        this.updateSubmitButton();
        this.updateSizeDisplay();
    }

    updateSubmitButton() {
        this.submitBtn.disabled = this.files.length === 0 || this.isUploading;
    }

    updateSizeDisplay() {
        const totalSize = this.files.reduce((sum, file) => sum + file.size, 0);
        const totalSizeMB = (totalSize / 1024 / 1024).toFixed(2);

        // Add or update size display
        let sizeDisplay = document.getElementById('sizeDisplay');
        if (!sizeDisplay) {
            sizeDisplay = document.createElement('div');
            sizeDisplay.id = 'sizeDisplay';
            sizeDisplay.className = 'text-center mt-2';
            this.fileList.parentNode.insertBefore(sizeDisplay, this.fileList.nextSibling);
        }

        const percentage = (totalSize / (this.maxTotalSizeMB * 1024 * 1024)) * 100;
        const colorClass = percentage > 90 ? 'text-danger' : percentage > 70 ? 'text-warning' : 'text-success';

        sizeDisplay.innerHTML = `
            <small class="${colorClass}">
                ${window.translations.total_size}: ${totalSizeMB}MB / ${this.maxTotalSizeMB}MB (${percentage.toFixed(1)}%)
            </small>
        `;
    }

    formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = [window.translations.bytes, window.translations.kb, window.translations.mb, window.translations.gb];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    cancelUpload() {
        if (this.currentXHR && this.isUploading) {
            this.currentXHR.abort();
            this.showError(window.translations.upload_cancelled);
            this.resetAfterUpload();
        }
    }

    async uploadFiles() {
        this.isUploading = true;
        this.submitBtn.disabled = true;
        this.submitBtn.innerHTML = `<i class="fas fa-spinner fa-spin me-2"></i>${window.translations.uploading}`;
        this.cancelBtn.style.display = 'inline-block';

        const formData = new FormData();

        // Add CSRF token
        const csrfToken = document.querySelector('input[name="_token"]').value;
        formData.append('_token', csrfToken);

        // Add all files
        this.files.forEach((file, index) => {
            formData.append('files[]', file);
        });

        try {
            // Create XMLHttpRequest for real progress tracking
            this.currentXHR = new XMLHttpRequest();

            // Track upload progress
            this.currentXHR.upload.addEventListener('progress', (e) => {
                if (e.lengthComputable) {
                    const totalProgress = (e.loaded / e.total) * 100;
                    this.updateAllFilesProgress(totalProgress, e.loaded, e.total);
                }
            });

            // Handle completion
            this.currentXHR.addEventListener('load', () => {
                if (this.currentXHR.status === 200) {
                    try {
                        const result = JSON.parse(this.currentXHR.responseText);
                        this.markAllFilesComplete();
                        this.uploadedDirectory = result.directory; // Server should return directory path
                        this.showUploadComplete(result);
                    } catch (error) {
                        this.showError(window.translations.invalid_server_response);
                        this.resetAfterUpload();
                    }
                } else {
                    this.showError(`${window.translations.upload_failed}: ${this.currentXHR.status}`);
                    this.resetAfterUpload();
                }
            });

            // Handle errors
            this.currentXHR.addEventListener('error', () => {
                this.showError(window.translations.network_error);
                this.resetAfterUpload();
            });

            // Handle abort
            this.currentXHR.addEventListener('abort', () => {
                this.showError(window.translations.upload_cancelled);
                this.resetAfterUpload();
            });

            // Send the request
            this.currentXHR.open('POST', '/upload-files');
            this.currentXHR.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            this.currentXHR.send(formData);

        } catch (error) {
            this.showError(error.message);
            this.resetAfterUpload();
        }
    }

    updateAllFilesProgress(percentage, loaded, total) {
        const fileItems = this.fileList.querySelectorAll('.file-item');
        const loadedFormatted = this.formatFileSize(loaded);
        const totalFormatted = this.formatFileSize(total);

        fileItems.forEach((item) => {
            const progressBar = item.querySelector('.progress-bar');
            const statusText = item.querySelectorAll('small')[0];
            const percentText = item.querySelectorAll('small')[1];

            progressBar.style.width = percentage + '%';
            percentText.textContent = Math.round(percentage) + '%';

            if (percentage < 100) {
                statusText.textContent = `${window.translations.uploading}... (${loadedFormatted} / ${totalFormatted})`;
                statusText.className = 'text-primary';
            }
        });
    }

    markAllFilesComplete() {
        const fileItems = this.fileList.querySelectorAll('.file-item');

        fileItems.forEach((item) => {
            const progressBar = item.querySelector('.progress-bar');
            const statusText = item.querySelectorAll('small')[0];
            const percentText = item.querySelectorAll('small')[1];

            progressBar.style.width = '100%';
            progressBar.className = 'progress-bar bg-success';
            statusText.textContent = window.translations.upload_complete;
            statusText.className = 'text-success';
            percentText.textContent = '100%';
        });
    }

    showUploadComplete(result) {
        this.isUploading = false;
        this.cancelBtn.style.display = 'none';
        this.submitBtn.style.display = 'none';
        this.finalSubmitBtn.style.display = 'inline-block';

        Swal.fire({
            title: window.translations.files_uploaded_successfully,
            icon: 'success',
        });
    }

    resetAfterUpload() {
        this.isUploading = false;
        this.cancelBtn.style.display = 'none';
        this.submitBtn.innerHTML = `<i class="fas fa-paper-plane me-2"></i>${window.translations.upload_files}`;
        this.submitBtn.disabled = false;
        this.currentXHR = null;
    }

    async finalSubmit() {
        const jobDescriptionTextarea = document.getElementById('job-description');
        const jobDescription = jobDescriptionTextarea.value;

        if (!jobDescription){
            Swal.fire({
                title: window.translations.validation_error,
                text: window.translations.description_required,
                icon: 'warning',
            });
            throw new Error(window.translations.validation_error);
        }

        this.finalSubmitBtn.disabled = true;
        this.finalSubmitBtn.innerHTML = `<i class="fas fa-spinner fa-spin me-2"></i>${window.translations.generating_directory}`;

        try {
            const response = await fetch('/generate-directory', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                },
                body: JSON.stringify({
                    uploaded_files: this.files.map(f => f.name)
                })
            });

            if (response.ok) {
                const result = await response.json();
                this.showFinalSuccess(result, jobDescription);
            } else {
                throw new Error(window.translations.failed_generate_directory);
            }
        } catch (error) {
            this.showError(window.translations.failed_generate_directory + ': ' + error.message);
            this.finalSubmitBtn.disabled = false;
            this.finalSubmitBtn.innerHTML = `<i class="fas fa-check me-2"></i>${window.translations.confirm}`;
        }
    }

    async showFinalSuccess(result, jobDescription) {
        const directoryUuid = result.directory_uuid;

        try {
            const response = await fetch(`/result/preview/${directoryUuid}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                },
                body: JSON.stringify({
                    description: jobDescription
                })
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            // Handle the response if needed
            const previewData = await response.json();

            // Show success dialog
            Swal.fire({
                title: window.translations.cv_sent_successfully,
                text: window.translations.explore_cv_page,
                icon: 'success',
                showCancelButton: true,
                confirmButtonText: window.translations.view,
                cancelButtonText: window.translations.skip,
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `/dashboard/hr/jobs/${previewData['application']}`;
                } else {
                    window.location.href = '/dashboard/hr/jobs';
                }
            });

        } catch (error) {
            console.error('Preview request failed:', error);

            // Still show success for the upload, but mention preview issue
            Swal.fire({
                title: window.translations.cv_uploaded_successfully,
                text: window.translations.preview_issue,
                icon: 'warning',
            });
        }
    }

    showError(message) {
        Swal.fire({
            title: window.translations.process_failed,
            text: window.translations.share_issue,
            icon: 'error',
            showCancelButton: true,
            confirmButtonText: window.translations.view,
            cancelButtonText: window.translations.skip,
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = `/contact`;
            } else {
                window.location.href = '/';
            }
        });

        // Reset progress bars on error
        const fileItems = this.fileList.querySelectorAll('.file-item');
        fileItems.forEach((item) => {
            const progressBar = item.querySelector('.progress-bar');
            const statusText = item.querySelectorAll('small')[0];
            const percentText = item.querySelectorAll('small')[1];

            progressBar.style.width = '0%';
            progressBar.className = 'progress-bar bg-danger';
            statusText.textContent = window.translations.upload_failed;
            statusText.className = 'text-danger';
            percentText.textContent = '0%';
        });
    }
}

// Initialize the file uploader
const fileUploader = new FileUploader();

// Enhanced stats updating
const originalUpdateSizeDisplay = FileUploader.prototype.updateSizeDisplay;
FileUploader.prototype.updateSizeDisplay = function() {
    // Call original function
    originalUpdateSizeDisplay.call(this);

    // Update dashboard stats
    const totalFiles = this.files.length;
    const totalSize = this.files.reduce((sum, file) => sum + file.size, 0);
    const totalSizeMB = (totalSize / 1024 / 1024).toFixed(1);

    document.getElementById('totalFiles').textContent = totalFiles;
    document.getElementById('totalSize').textContent = totalSizeMB + ' MB';
};

// Update progress in stats
const originalUpdateProgress = FileUploader.prototype.updateAllFilesProgress;
FileUploader.prototype.updateAllFilesProgress = function(percentage, loaded, total) {
    // Call original function
    originalUpdateProgress.call(this, percentage, loaded, total);

    // Update progress stat
    document.getElementById('uploadProgress').textContent = Math.round(percentage) + '%';
};

// Reset stats on completion
const originalMarkComplete = FileUploader.prototype.markAllFilesComplete;
FileUploader.prototype.markAllFilesComplete = function() {
    // Call original function
    originalMarkComplete.call(this);

    // Set progress to 100%
    document.getElementById('uploadProgress').textContent = '100%';
};
