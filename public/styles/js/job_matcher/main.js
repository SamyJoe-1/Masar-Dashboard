// Job Matcher Main Script
class JobMatcher {
    constructor() {
        this.selectedFile = null;
        this.cvSource = 'upload';
        this.jobPreferences = '';
        this.initializeElements();
        this.attachEventListeners();
    }

    initializeElements() {
        this.elements = {
            dropZone: document.getElementById('dropZone'),
            fileInput: document.getElementById('cvFileInput'),
            filePreview: document.getElementById('filePreview'),
            matchBtn: document.getElementById('matchBtn'),
            jobPreferences: document.getElementById('jobPreferences'),

            uploadSection: document.getElementById('uploadSection'),
            processingSection: document.getElementById('processingSection'),
            resultsSection: document.getElementById('resultsSection'),

            overallProgress: document.getElementById('overallProgress'),
            progressText: document.querySelector('.progress-text'),

            jobsList: document.getElementById('jobsList')
        };
    }

    attachEventListeners() {
        // CV Source selection
        document.querySelectorAll('input[name="cv_source"]').forEach(radio => {
            radio.addEventListener('change', (e) => this.handleSourceChange(e));
        });

        // File upload
        this.elements.dropZone.addEventListener('click', () => {
            if (this.cvSource === 'upload') {
                this.elements.fileInput.click();
            }
        });

        this.elements.fileInput.addEventListener('change', (e) => {
            this.handleFileSelect(e.target.files[0]);
        });

        // Drag and drop
        this.elements.dropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            this.elements.dropZone.classList.add('drag-over');
        });

        this.elements.dropZone.addEventListener('dragleave', () => {
            this.elements.dropZone.classList.remove('drag-over');
        });

        this.elements.dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            this.elements.dropZone.classList.remove('drag-over');
            if (this.cvSource === 'upload') {
                this.handleFileSelect(e.dataTransfer.files[0]);
            }
        });

        // Job preferences
        this.elements.jobPreferences.addEventListener('input', (e) => {
            this.jobPreferences = e.target.value;
        });

        // Match button
        this.elements.matchBtn.addEventListener('click', () => {
            this.startMatching();
        });
    }

    handleSourceChange(e) {
        this.cvSource = e.target.value;

        if (this.cvSource === 'existing') {
            document.getElementById('uploadArea').style.display = 'none';
            this.elements.matchBtn.disabled = false;
        } else {
            document.getElementById('uploadArea').style.display = 'block';
            this.elements.matchBtn.disabled = !this.selectedFile;
        }
    }

    handleFileSelect(file) {
        if (!file) return;

        const validTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
        const maxSize = 5 * 1024 * 1024;

        if (!validTypes.includes(file.type)) {
            this.showError('Invalid file type. Please upload PDF, DOC, or DOCX.');
            return;
        }

        if (file.size > maxSize) {
            this.showError('File size exceeds 5MB limit.');
            return;
        }

        this.selectedFile = file;
        this.showFilePreview(file);
        this.elements.matchBtn.disabled = false;
    }

    showFilePreview(file) {
        const fileName = this.elements.filePreview.querySelector('.file-name');
        const fileSize = this.elements.filePreview.querySelector('.file-size');

        fileName.textContent = file.name;
        fileSize.textContent = this.formatFileSize(file.size);

        this.elements.dropZone.style.display = 'none';
        this.elements.filePreview.classList.remove('d-none');
    }

    formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
    }

    showError(message) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: message,
            confirmButtonColor: '#3464b0'
        });
    }

    async startMatching() {
        this.elements.uploadSection.classList.add('d-none');
        this.elements.processingSection.classList.remove('d-none');
        window.scrollTo({ top: 0, behavior: 'smooth' });

        try {
            const formData = new FormData();

            if (this.cvSource === 'upload') {
                formData.append('cv_file', this.selectedFile);
            }

            formData.append('cv_source', this.cvSource);
            formData.append('job_preferences', this.jobPreferences);

            // Process with dummy data
            await this.processDummyMatching();

        } catch (error) {
            console.error('Matching error:', error);
            this.showError('An error occurred during matching. Please try again.');
            this.resetToUpload();
        }
    }

    async processDummyMatching() {
        const steps = ['render', 'analyze'];

        for (let i = 0; i < steps.length; i++) {
            await this.processStep(steps[i], i, steps.length);
        }

        setTimeout(() => {
            this.showResults();
        }, 500);
    }

    async processStep(stepName, index, total) {
        const stepElement = document.querySelector(`[data-step="${stepName}"]`);

        stepElement.classList.add('active');

        // Longer delay for render step
        const delay = stepName === 'render' ? 2000 : 3000;
        await this.delay(delay);

        stepElement.classList.remove('active');
        stepElement.classList.add('completed');

        const progress = ((index + 1) / total) * 100;
        this.updateProgress(progress);
    }

    updateProgress(percentage) {
        this.elements.overallProgress.style.width = percentage + '%';
        this.elements.progressText.textContent = Math.round(percentage) + '%';
    }

    delay(ms) {
        return new Promise(resolve => setTimeout(resolve, ms));
    }

    showResults() {
        this.elements.processingSection.classList.add('d-none');
        this.elements.resultsSection.classList.remove('d-none');
        window.scrollTo({ top: 0, behavior: 'smooth' });

        this.renderJobMatches(window.dummyJobMatches.jobs);
    }

    renderJobMatches(jobs) {
        this.elements.jobsList.innerHTML = '';

        jobs.forEach((job, index) => {
            const jobCard = this.createJobCard(job);
            this.elements.jobsList.appendChild(jobCard);

            // Animate score after a slight delay
            setTimeout(() => {
                this.animateJobScore(job.id, job.ats_score);
            }, 300 + (index * 100));
        });
    }

    createJobCard(job) {
        const card = document.createElement('div');
        card.className = 'job-card';
        card.dataset.jobId = job.id;

        const scoreClass = this.getScoreClass(job.ats_score);
        const circumference = 2 * Math.PI * 42; // radius = 42

        card.innerHTML = `
            <div class="job-header ${scoreClass}">
                <div class="job-score-circle">
                    <svg class="score-circle-svg" viewBox="0 0 100 100">
                        <circle class="score-circle-bg" cx="50" cy="50" r="42" />
                        <circle class="score-circle-fill" cx="50" cy="50" r="42"
                                style="stroke: ${this.getScoreColor(job.ats_score)};
                                       stroke-dasharray: ${circumference};
                                       stroke-dashoffset: ${circumference};" />
                    </svg>
                    <div class="score-number">${job.ats_score}</div>
                    <div class="score-label">ATS</div>
                </div>

                <div class="job-info">
                    <h2 class="job-title">${job.title}</h2>
                    <div class="job-company">${job.company}</div>
                    <div class="job-meta">
                        <div class="meta-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>${job.location}</span>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-briefcase"></i>
                            <span>${job.type}</span>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-dollar-sign"></i>
                            <span>${job.salary}</span>
                        </div>
                    </div>
                </div>

                <div class="job-actions">
                    <button class="icon-btn" onclick="downloadJobFeedback(${job.id})" title="Download Feedback">
                        <i class="fas fa-download"></i>
                    </button>
                    <button class="icon-btn toggle-btn" onclick="toggleJobCard(${job.id})" title="Show Details">
                        <i class="fas fa-chevron-down toggle-icon"></i>
                    </button>
                </div>
            </div>

            <div class="job-content">
                <div class="job-content-inner">
                    ${this.createFeedbackHTML(job.feedback)}
                </div>
            </div>
        `;

        return card;
    }

    createFeedbackHTML(feedback) {
        let html = '';

        // Overview
        if (feedback.overview) {
            html += `
                <div class="feedback-section">
                    <h3 class="feedback-title">
                        <i class="fas fa-chart-line"></i>
                        Match Overview
                    </h3>
                    <div class="feedback-text">${feedback.overview}</div>
                </div>
            `;
        }

        // Strengths
        if (feedback.strengths) {
            html += `
                <div class="feedback-section">
                    <h3 class="feedback-title">
                        <i class="fas fa-check-circle"></i>
                        Your Strengths
                    </h3>
                    <div class="feedback-text">${feedback.strengths}</div>
                </div>
            `;
        }

        // Improvements
        if (feedback.improvements) {
            html += `
                <div class="feedback-section">
                    <h3 class="feedback-title">
                        <i class="fas fa-arrow-up"></i>
                        Areas for Improvement
                    </h3>
                    <div class="feedback-text">${feedback.improvements}</div>
                </div>
            `;
        }

        // Skills
        if (feedback.skills && feedback.skills.length > 0) {
            html += `
                <div class="feedback-section">
                    <h3 class="feedback-title">
                        <i class="fas fa-code"></i>
                        Matching Skills
                    </h3>
                    <div class="pills-container">
                        ${feedback.skills.map(skill =>
                `<span class="pill skill">${skill.name}</span>`
            ).join('')}
                    </div>
                </div>
            `;
        }

        // Courses
        if (feedback.courses && feedback.courses.length > 0) {
            html += `
                <div class="feedback-section">
                    <h3 class="feedback-title">
                        <i class="fas fa-graduation-cap"></i>
                        Recommended Courses & Skills
                    </h3>
                    <div class="suggestions-grid">
                        ${feedback.courses.map(course => `
                            <div class="suggestion-card">
                                <div class="suggestion-icon">
                                    <i class="fas fa-book"></i>
                                </div>
                                <div class="suggestion-name">${course.name}</div>
                                <div class="suggestion-desc">${course.description}</div>
                            </div>
                        `).join('')}
                    </div>
                </div>
            `;
        }

        return html;
    }

    getScoreClass(score) {
        if (score >= 80) return 'score-excellent';
        if (score >= 60) return 'score-good';
        if (score >= 40) return 'score-fair';
        return 'score-poor';
    }

    getScoreColor(score) {
        if (score >= 80) return '#22c55e';
        if (score >= 60) return '#3464b0';
        if (score >= 40) return '#f59e0b';
        return '#dc2626';
    }

    animateJobScore(jobId, targetScore) {
        const card = document.querySelector(`[data-job-id="${jobId}"]`);
        const circle = card.querySelector('.score-circle-fill');
        const circumference = 2 * Math.PI * 42;

        const duration = 1500;
        const start = performance.now();

        const animate = (currentTime) => {
            const elapsed = currentTime - start;
            const progress = Math.min(elapsed / duration, 1);
            const easeOutQuart = 1 - Math.pow(1 - progress, 4);

            const currentScore = targetScore * easeOutQuart;
            const offset = circumference - (circumference * currentScore / 100);

            circle.style.strokeDashoffset = offset;

            if (progress < 1) {
                requestAnimationFrame(animate);
            }
        };

        requestAnimationFrame(animate);
    }

    resetToUpload() {
        this.elements.processingSection.classList.add('d-none');
        this.elements.resultsSection.classList.add('d-none');
        this.elements.uploadSection.classList.remove('d-none');
    }
}

// Global functions
function removeFile() {
    const matcher = window.jobMatcher;
    matcher.selectedFile = null;
    matcher.elements.filePreview.classList.add('d-none');
    matcher.elements.dropZone.style.display = 'block';
    matcher.elements.fileInput.value = '';
    matcher.elements.matchBtn.disabled = true;
}

function toggleJobCard(jobId) {
    const card = document.querySelector(`[data-job-id="${jobId}"]`);
    card.classList.toggle('expanded');
}

function downloadJobFeedback(jobId) {
    const job = window.dummyJobMatches.jobs.find(j => j.id === jobId);

    Swal.fire({
        icon: 'success',
        title: 'Downloading Feedback',
        text: `Feedback for "${job.title}" will be downloaded shortly...`,
        timer: 2000,
        showConfirmButton: false
    });
}

function downloadFullReport() {
    Swal.fire({
        icon: 'success',
        title: 'Downloading Full Report',
        text: 'Your complete job matching report will be downloaded shortly...',
        timer: 2000,
        showConfirmButton: false
    });
}

function matchAgain() {
    window.jobMatcher.resetToUpload();
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// Initialize
document.addEventListener('DOMContentLoaded', () => {
    window.jobMatcher = new JobMatcher();
});
