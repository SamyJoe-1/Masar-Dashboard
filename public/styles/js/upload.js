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
