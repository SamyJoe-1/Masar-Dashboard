// CV Analyzer Main Script
class CVAnalyzer {
    constructor() {
        this.selectedFile = null;
        this.cvSource = 'upload';
        this.jobDescription = '';
        this.initializeElements();
        this.attachEventListeners();
    }

    initializeElements() {
        this.elements = {
            // Upload section
            dropZone: document.getElementById('dropZone'),
            fileInput: document.getElementById('cvFileInput'),
            filePreview: document.getElementById('filePreview'),
            analyzeBtn: document.getElementById('analyzeBtn'),
            jobDescription: document.getElementById('jobDescription'),

            // Sections
            uploadSection: document.getElementById('uploadSection'),
            processingSection: document.getElementById('processingSection'),
            resultsSection: document.getElementById('resultsSection'),

            // Progress elements
            overallProgress: document.getElementById('overallProgress'),
            progressText: document.querySelector('.progress-text'),

            // Results elements
            mainScore: document.getElementById('mainScore'),
            mainScorePath: document.getElementById('mainScorePath'),
            scoreStatus: document.getElementById('scoreStatus'),
            feedbackSections: document.getElementById('feedbackSections'),
            suggestedRoles: document.getElementById('suggestedRoles')
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

        // Job description
        this.elements.jobDescription.addEventListener('input', (e) => {
            this.jobDescription = e.target.value;
        });

        // Analyze button
        this.elements.analyzeBtn.addEventListener('click', () => {
            this.startAnalysis();
        });
    }

    handleSourceChange(e) {
        this.cvSource = e.target.value;

        if (this.cvSource === 'existing') {
            document.getElementById('uploadArea').style.display = 'none';
            this.elements.analyzeBtn.disabled = false;
        } else {
            document.getElementById('uploadArea').style.display = 'block';
            this.elements.analyzeBtn.disabled = !this.selectedFile;
        }
    }

    handleFileSelect(file) {
        if (!file) return;

        // Validate file
        const validTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
        const maxSize = 5 * 1024 * 1024; // 5MB

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
        this.elements.analyzeBtn.disabled = false;
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

    async startAnalysis() {
        // Hide upload section, show processing
        this.elements.uploadSection.classList.add('d-none');
        this.elements.processingSection.classList.remove('d-none');

        // Scroll to top
        window.scrollTo({ top: 0, behavior: 'smooth' });

        try {
            // Create FormData
            const formData = new FormData();

            if (this.cvSource === 'upload') {
                formData.append('cv_file', this.selectedFile);
            }

            formData.append('cv_source', this.cvSource);
            formData.append('job_description', this.jobDescription);

            // In production, replace with actual API call
            // const response = await this.sendToAPI(formData);

            // For now, use dummy data
            await this.processDummyAnalysis();

        } catch (error) {
            console.error('Analysis error:', error);
            this.showError('An error occurred during analysis. Please try again.');
            this.resetToUpload();
        }
    }

    async sendToAPI(formData) {
        const response = await fetch('/api/cv/analyze', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: formData
        });

        if (!response.ok) {
            throw new Error('API request failed');
        }

        return await response.json();
    }

    async processDummyAnalysis() {
        const steps = ['upload', 'render', 'ats', 'content', 'format', 'skills'];

        for (let i = 0; i < steps.length; i++) {
            await this.processStep(steps[i], i, steps.length);
        }

        // Show results after all steps complete
        setTimeout(() => {
            this.showResults();
        }, 500);
    }

    async processStep(stepName, index, total) {
        const stepElement = document.querySelector(`[data-step="${stepName}"]`);

        // Activate step
        stepElement.classList.add('active');

        // Simulate processing time
        await this.delay(1000 + Math.random() * 1000);

        // Complete step
        stepElement.classList.remove('active');
        stepElement.classList.add('completed');

        // Update overall progress
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
        // Hide processing, show results
        this.elements.processingSection.classList.add('d-none');
        this.elements.resultsSection.classList.remove('d-none');

        // Scroll to top
        window.scrollTo({ top: 0, behavior: 'smooth' });

        // Load dummy results (will be replaced with actual API data)
        this.renderResults(window.dummyATSResults);
    }

    renderResults(data) {
        // Animate main score
        this.animateMainScore(data.ats_score);

        // Animate sub scores
        this.animateSubScores(data.content_score, data.formatting_score, data.skills_score);

        // Render feedback sections
        this.renderFeedbackSections(data.feedback);

        // Render suggested roles
        this.renderSuggestedRoles(data.suggested_roles);
    }

    animateMainScore(targetScore) {
        const duration = 2000;
        const start = performance.now();
        const circumference = 251.2;

        const animate = (currentTime) => {
            const elapsed = currentTime - start;
            const progress = Math.min(elapsed / duration, 1);

            // Easing function
            const easeOutQuart = 1 - Math.pow(1 - progress, 4);
            const currentScore = Math.round(targetScore * easeOutQuart);

            // Update score number
            this.elements.mainScore.textContent = currentScore;

            // Update semicircle
            const offset = circumference - (circumference * currentScore / 100);
            this.elements.mainScorePath.style.strokeDashoffset = offset;

            // Update color
            const color = this.getScoreColor(currentScore);
            this.elements.mainScorePath.style.stroke = color;

            if (progress < 1) {
                requestAnimationFrame(animate);
            } else {
                // Update status text
                this.elements.scoreStatus.textContent = this.getScoreStatus(targetScore);
            }
        };

        requestAnimationFrame(animate);
    }

    animateSubScores(contentScore, formatScore, skillsScore) {
        const scores = [contentScore, formatScore, skillsScore];
        const subScoreItems = document.querySelectorAll('.sub-score-item');

        subScoreItems.forEach((item, index) => {
            const fill = item.querySelector('.sub-score-fill');
            const value = item.querySelector('.sub-score-value');
            const targetScore = scores[index];

            setTimeout(() => {
                this.animateSubScore(fill, value, targetScore);
            }, index * 200);
        });
    }

    animateSubScore(fillElement, valueElement, targetScore) {
        const duration = 1500;
        const start = performance.now();

        const animate = (currentTime) => {
            const elapsed = currentTime - start;
            const progress = Math.min(elapsed / duration, 1);

            const easeOutQuart = 1 - Math.pow(1 - progress, 4);
            const currentScore = Math.round(targetScore * easeOutQuart);

            fillElement.style.width = currentScore + '%';
            fillElement.style.background = this.getScoreGradient(currentScore);
            valueElement.textContent = currentScore + '%';

            if (progress < 1) {
                requestAnimationFrame(animate);
            }
        };

        requestAnimationFrame(animate);
    }

    getScoreColor(score) {
        if (score >= 80) return '#22c55e';
        if (score >= 60) return '#3464b0';
        if (score >= 40) return '#f59e0b';
        return '#dc2626';
    }

    getScoreGradient(score) {
        if (score >= 80) return 'linear-gradient(90deg, #22c55e, #16a34a)';
        if (score >= 60) return 'linear-gradient(90deg, #3464b0, #2d5497)';
        if (score >= 40) return 'linear-gradient(90deg, #f59e0b, #d97706)';
        return 'linear-gradient(90deg, #dc2626, #b91c1c)';
    }

    getScoreStatus(score) {
        if (score >= 80) return 'Excellent! Your CV is ATS-ready';
        if (score >= 60) return 'Good, but needs improvement';
        if (score >= 40) return 'Fair, requires significant changes';
        return 'Poor, major improvements needed';
    }

    renderFeedbackSections(feedback) {
        this.elements.feedbackSections.innerHTML = '';

        Object.keys(feedback).forEach(sectionKey => {
            const section = feedback[sectionKey];
            const sectionHTML = this.createFeedbackSection(section);
            this.elements.feedbackSections.innerHTML += sectionHTML;
        });
    }

    createFeedbackSection(section) {
        let pointsHTML = '';

        if (section.type === 'points') {
            pointsHTML = '<div class="feedback-points">';
            section.items.forEach(item => {
                const iconClass = item.passed ? 'passed' : 'failed';
                const icon = item.passed ? 'fa-check' : 'fa-times';

                pointsHTML += `
                    <div class="feedback-point">
                        <div class="point-icon ${iconClass}">
                            <i class="fas ${icon}"></i>
                        </div>
                        <div class="point-content">
                            <div class="point-title">${item.title}</div>
                            <div class="point-description">${item.description}</div>
                        </div>
                    </div>
                `;
            });
            pointsHTML += '</div>';
        } else if (section.type === 'badges') {
            pointsHTML = '<div class="skills-badges">';
            section.items.forEach(item => {
                const badgeClass = item.relevant ? 'relevant' : 'irrelevant';
                pointsHTML += `<span class="skill-badge ${badgeClass}">${item.name}</span>`;
            });
            pointsHTML += '</div>';
        } else if (section.type === 'paragraph') {
            pointsHTML = `<p class="feedback-paragraph">${section.content}</p>`;
        }

        return `
            <div class="feedback-section">
                <h3>
                    <i class="${section.icon}"></i>
                    ${section.title}
                </h3>
                ${pointsHTML}
            </div>
        `;
    }

    renderSuggestedRoles(roles) {
        this.elements.suggestedRoles.innerHTML = '';

        roles.forEach(role => {
            const pill = document.createElement('span');
            pill.className = 'role-pill';
            pill.textContent = role;
            this.elements.suggestedRoles.appendChild(pill);
        });
    }

    resetToUpload() {
        this.elements.processingSection.classList.add('d-none');
        this.elements.resultsSection.classList.add('d-none');
        this.elements.uploadSection.classList.remove('d-none');
    }
}

// Global functions for button actions
function removeFile() {
    const analyzer = window.cvAnalyzer;
    analyzer.selectedFile = null;
    analyzer.elements.filePreview.classList.add('d-none');
    analyzer.elements.dropZone.style.display = 'block';
    analyzer.elements.fileInput.value = '';
    analyzer.elements.analyzeBtn.disabled = true;
}

function downloadCV() {
    // Implement download functionality
    Swal.fire({
        icon: 'info',
        title: 'Download CV',
        text: 'CV download will start shortly...',
        confirmButtonColor: '#3464b0'
    });
}

function shareReport() {
    const url = window.location.href;
    navigator.clipboard.writeText(url).then(() => {
        Swal.fire({
            icon: 'success',
            title: 'Link Copied!',
            text: 'Report link has been copied to clipboard',
            timer: 2000,
            showConfirmButton: false
        });
    });
}

function analyzeAnother() {
    window.cvAnalyzer.resetToUpload();
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', () => {
    window.cvAnalyzer = new CVAnalyzer();
});
