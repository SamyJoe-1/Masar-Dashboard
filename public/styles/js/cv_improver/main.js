// CV Improver Main Script
class CVImprover {
    constructor() {
        this.selectedFile = null;
        this.targetRole = '';
        this.jobDescription = '';
        this.cvData = null;
        this.improvedCVData = null;
        this.initializeElements();
        this.attachEventListeners();
    }

    initializeElements() {
        this.elements = {
            // Upload section
            dropZone: document.getElementById('dropZone'),
            fileInput: document.getElementById('cvFileInput'),
            filePreview: document.getElementById('filePreview'),
            targetRole: document.getElementById('targetRole'),
            jobDescription: document.getElementById('jobDescription'),
            improveBtn: document.getElementById('improveBtn'),

            // Sections
            uploadSection: document.getElementById('uploadSection'),
            processingSection: document.getElementById('processingSection'),
            resultsSection: document.getElementById('resultsSection'),

            // Progress elements
            overallProgress: document.getElementById('overallProgress'),
            progressText: document.querySelector('.progress-text'),

            // Results elements
            improvementsList: document.getElementById('improvementsList'),
            cvPreview: document.getElementById('cvPreview'),
            fullscreenModal: document.getElementById('fullscreenModal'),
            fullscreenPreview: document.getElementById('fullscreenPreview')
        };
    }

    attachEventListeners() {
        // File upload
        this.elements.dropZone.addEventListener('click', () => {
            this.elements.fileInput.click();
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
            this.handleFileSelect(e.dataTransfer.files[0]);
        });

        // Input fields
        this.elements.targetRole.addEventListener('input', (e) => {
            this.targetRole = e.target.value.trim();
            this.validateForm();
        });

        this.elements.jobDescription.addEventListener('input', (e) => {
            this.jobDescription = e.target.value.trim();
            this.validateForm();
        });

        // Improve button
        this.elements.improveBtn.addEventListener('click', () => {
            this.startImprovement();
        });
    }

    handleFileSelect(file) {
        if (!file) return;

        // Validate file
        if (file.type !== 'application/pdf') {
            this.showError(t('error.invalid_file_type'));
            return;
        }

        const maxSize = 5 * 1024 * 1024; // 5MB
        if (file.size > maxSize) {
            this.showError(t('error.file_size_limit'));
            return;
        }

        this.selectedFile = file;
        this.showFilePreview(file);
        this.validateForm();
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

    validateForm() {
        const isValid = this.selectedFile && this.targetRole && this.jobDescription;
        this.elements.improveBtn.disabled = !isValid;
    }

    showError(message) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: message,
            confirmButtonColor: '#3464b0'
        });
    }

    async startImprovement() {
        // Hide upload section, show processing
        this.elements.uploadSection.classList.add('d-none');
        this.elements.processingSection.classList.remove('d-none');

        // Scroll to top
        window.scrollTo({ top: 0, behavior: 'smooth' });

        try {
            // Step 1: Render PDF
            await this.renderPDF();

            // Step 2: Improve CV
            await this.improveCV();

            // Show results
            this.showResults();

        } catch (error) {
            console.error('Improvement error:', error);
            this.showError(t('error.improvement_failed'));
            this.resetToUpload();
        }
    }

    transformResultToCV(result) {
        console.log('Transforming result to CV format:', result);

        const cv = {
            contact: result.contact || {},
            summary: "",
            experience: [],
            education: [],
            skills: [],
            projects: [],
            certifications: [],
            languages: [],
            achievements: []
        };

        // Handle summary - can be string or object with summary property
        if (result.summary) {
            if (typeof result.summary === 'string') {
                cv.summary = result.summary;
            } else if (result.summary.summary) {
                cv.summary = result.summary.summary;
            } else if (result.summary.bullets && Array.isArray(result.summary.bullets)) {
                cv.summary = result.summary.bullets.join(' ');
            }
        }

        // Extract bullets from each section if they exist
        if (result.experience && result.experience.bullets) {
            cv.experience = Array.isArray(result.experience.bullets) ? result.experience.bullets : [];
        }

        if (result.education && result.education.bullets) {
            cv.education = Array.isArray(result.education.bullets) ? result.education.bullets : [];
        }

        if (result.skills && result.skills.bullets) {
            cv.skills = Array.isArray(result.skills.bullets) ? result.skills.bullets : [];
        }

        if (result.projects && result.projects.bullets) {
            cv.projects = Array.isArray(result.projects.bullets) ? result.projects.bullets : [];
        }

        if (result.certifications && result.certifications.bullets) {
            cv.certifications = Array.isArray(result.certifications.bullets) ? result.certifications.bullets : [];
        }

        if (result.languages && result.languages.bullets) {
            cv.languages = Array.isArray(result.languages.bullets) ? result.languages.bullets : [];
        }

        if (result.achievements && result.achievements.bullets) {
            cv.achievements = Array.isArray(result.achievements.bullets) ? result.achievements.bullets : [];
        }

        // Handle role_highlights if present
        if (result.role_highlights && result.role_highlights.bullets) {
            const highlights = Array.isArray(result.role_highlights.bullets) ? result.role_highlights.bullets : [];
            cv.experience = [...cv.experience, ...highlights];
        }

        console.log('Transformed CV:', cv);
        return cv;
    }

    async renderPDF() {
        const stepElement = document.querySelector('[data-step="render"]');
        stepElement.classList.add('active');
        this.updateProgress(25);

        try {
            const formData = new FormData();
            formData.append('file', this.selectedFile);
            formData.append('job_description', '');
            formData.append('target_role', '');
            formData.append('language', 'en');

            console.log('Calling PDF render API...');

            const response = await fetch(window.smartCVUrl + '/v1/improve_all_from_pdf', {
                method: 'POST',
                body: formData
            });

            if (!response.ok) {
                const errorText = await response.text();
                console.error('PDF API Error:', errorText);
                throw new Error(t('error.pdf_rendering_failed') + response.status);
            }

            const data = await response.json();
            console.log('PDF API Response:', data);

            // Check if we have the CV data - handle both response structures
            if (!data || (!data.cv && !data.result)) {
                console.error('Invalid response structure:', data);
                throw new Error(t('error.invalid_response'));
            }

            // The API returns data.result, not data.cv when using improve_all_from_pdf
            // We need to transform it to the expected format
            if (data.result && !data.cv) {
                this.cvData = {
                    cv: this.transformResultToCV(data.result)
                };
            } else {
                this.cvData = data;
            }

            // Complete step
            await this.delay(500);
            stepElement.classList.remove('active');
            stepElement.classList.add('completed');
            this.updateProgress(50);

        } catch (error) {
            console.error('Render PDF Error:', error);
            throw new Error(t('error.failed_to_render') + error.message);
        }
    }

    async improveCV() {
        const stepElement = document.querySelector('[data-step="analyze"]');
        stepElement.classList.add('active');
        this.updateProgress(75);

        try {
            // Check if we have CV data
            if (!this.cvData || !this.cvData.cv) {
                throw new Error(t('error.no_cv_data'));
            }

            // Map the CV data to the correct format for the advice API
            const formattedCV = this.formatCVForAdvice(this.cvData.cv);

            const requestBody = {
                cv: formattedCV,
                job_description: this.jobDescription,
                target_role: this.targetRole,
                language: 'en'
            };

            // console.log('Sending to advice API:', JSON.stringify(requestBody, null, 2));

            const response = await fetch(window.smartCVUrl + '/v1/advice', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(requestBody)
            });

            if (!response.ok) {
                const errorData = await response.json().catch(() => ({ error: 'Unknown error' }));
                console.error('Advice API Error:', errorData);
                throw new Error(t('error.cv_improvement_failed') + JSON.stringify(errorData));
            }

            this.improvedCVData = await response.json();
            console.log('Advice API Response:', this.improvedCVData);

            // Complete step
            await this.delay(500);
            stepElement.classList.remove('active');
            stepElement.classList.add('completed');
            this.updateProgress(100);

        } catch (error) {
            console.error('Improve CV Error:', error);
            throw new Error(t('error.failed_to_improve') + error.message);
        }
    }

    formatCVForAdvice(cvData) {
        console.log('Formatting CV data:', cvData);

        // Handle the case where cvData might be undefined or null
        if (!cvData) {
            throw new Error(t('error.cv_data_empty'));
        }

        // Ensure the CV data matches the expected format
        const formatted = {
            contact: {},
            summary: cvData.summary || "",
            experience: Array.isArray(cvData.experience) ? cvData.experience : [],
            education: Array.isArray(cvData.education) ? cvData.education : [],
            skills: Array.isArray(cvData.skills) ? cvData.skills : [],
            projects: Array.isArray(cvData.projects) ? cvData.projects : [],
            certifications: cvData.certifications ? (Array.isArray(cvData.certifications) ? cvData.certifications : []) : [],
            languages: cvData.languages ? (Array.isArray(cvData.languages) ? cvData.languages : []) : [],
            achievements: cvData.achievements ? (Array.isArray(cvData.achievements) ? cvData.achievements : []) : []
        };

        // Handle contact info - check different possible structures
        if (cvData.contact) {
            // Check if contact has a 'raw' field with JSON string
            if (cvData.contact.raw && typeof cvData.contact.raw === 'string') {
                try {
                    // Extract JSON from markdown code block if present
                    let jsonStr = cvData.contact.raw;
                    if (jsonStr.includes('```json')) {
                        jsonStr = jsonStr.replace(/```json\n?/g, '').replace(/```\n?/g, '').trim();
                    }
                    const parsedContact = JSON.parse(jsonStr);
                    formatted.contact = {
                        additionalProp1: {
                            name: parsedContact.name || "",
                            email: parsedContact.email || "",
                            phone: parsedContact.phone || "",
                            location: parsedContact.location || "",
                            linkedin: parsedContact.linkedin || ""
                        }
                    };
                } catch (e) {
                    console.error('Failed to parse contact.raw:', e);
                    // Fallback to empty contact
                    formatted.contact = {
                        additionalProp1: {
                            name: "",
                            email: "",
                            phone: "",
                            location: "",
                            linkedin: ""
                        }
                    };
                }
            } else if (cvData.contact.additionalProp1) {
                // Already has the additionalProp1 structure
                formatted.contact = cvData.contact;
            } else if (typeof cvData.contact === 'object') {
                // Direct contact object, wrap it
                formatted.contact = {
                    additionalProp1: {
                        name: cvData.contact.name || "",
                        email: cvData.contact.email || "",
                        phone: cvData.contact.phone || "",
                        location: cvData.contact.location || "",
                        linkedin: cvData.contact.linkedin || ""
                    }
                };
            }
        } else {
            // No contact info, create empty structure
            formatted.contact = {
                additionalProp1: {
                    name: "",
                    email: "",
                    phone: "",
                    location: "",
                    linkedin: ""
                }
            };
        }

        console.log('Formatted CV:', formatted);
        return formatted;
    }

    updateProgress(percentage) {
        this.elements.overallProgress.style.width = percentage + '%';
        this.elements.progressText.textContent = Math.round(percentage) + '%';
    }

    delay(ms) {
        return new Promise(resolve => setTimeout(resolve, ms));
    }

    showResults() {
        console.log('Showing results with data:', this.improvedCVData);

        // Hide processing, show results
        this.elements.processingSection.classList.add('d-none');
        this.elements.resultsSection.classList.remove('d-none');

        // Scroll to top
        window.scrollTo({ top: 0, behavior: 'smooth' });

        // Check if we have data to display
        if (!this.improvedCVData) {
            this.showError(t('error.no_improvement_data'));
            this.resetToUpload();
            return;
        }

        // Render improvements list
        this.renderImprovementsList();

        // Render CV preview
        this.renderCVPreview();
    }

    renderImprovementsList() {
        console.log('Rendering improvements list');

        const mainContainer = document.getElementById('improvementsMain');
        if (!mainContainer) {
            console.error('improvementsMain container not found!');
            return;
        }

        if (!this.improvedCVData || !this.improvedCVData.result) {
            mainContainer.innerHTML = `<p class="improvement-text">No improvements data available</p>`;
            return;
        }

        const result = this.improvedCVData.result;
        let html = '';
        let hasImprovements = false;

        // Section feedback
        if (result.section_feedback && typeof result.section_feedback === 'object') {
            Object.keys(result.section_feedback).forEach(section => {
                const feedback = result.section_feedback[section];
                if (feedback && typeof feedback === 'string' && feedback.trim()) {
                    html += `
                    <div class="improvement-section">
                        <h2 class="improvement-section-title">
                            <i class="fas fa-lightbulb"></i> ${t('section_key.' + section.toLowerCase()) || section}
                        </h2>
                        <div class="improvement-text">${feedback}</div>
                    </div>
                `;
                    hasImprovements = true;
                }
            });
        }

        // Missing skills
        if (result.missing_skills && Array.isArray(result.missing_skills) && result.missing_skills.length > 0) {
            html += `
            <div class="improvement-section">
                <h2 class="improvement-section-title">
                    <i class="fas fa-code"></i> ${t('improvement.missing_skills')}
                </h2>
                <div class="improvement-bullets">
                    ${result.missing_skills.map(skill => `
                        <span class="improvement-bullet">
                            <i class="fas fa-plus-circle"></i>
                            ${skill}
                        </span>
                    `).join('')}
                </div>
            </div>
        `;
            hasImprovements = true;
        }

        // Recommended certifications
        if (result.recommended_certifications && Array.isArray(result.recommended_certifications) && result.recommended_certifications.length > 0) {
            html += `
            <div class="improvement-section">
                <h2 class="improvement-section-title">
                    <i class="fas fa-certificate"></i> ${t('improvement.recommended_certifications')}
                </h2>
                <div class="improvement-bullets">
                    ${result.recommended_certifications.map(cert => `
                        <span class="improvement-bullet">
                            <i class="fas fa-award"></i>
                            ${cert}
                        </span>
                    `).join('')}
                </div>
            </div>
        `;
            hasImprovements = true;
        }

        // Recommended projects
        if (result.recommended_projects && Array.isArray(result.recommended_projects) && result.recommended_projects.length > 0) {
            html += `
            <div class="improvement-section">
                <h2 class="improvement-section-title">
                    <i class="fas fa-project-diagram"></i> ${t('improvement.recommended_projects')}
                </h2>
                <div class="improvement-bullets">
                    ${result.recommended_projects.map(project => `
                        <span class="improvement-bullet">
                            <i class="fas fa-rocket"></i>
                            ${project}
                        </span>
                    `).join('')}
                </div>
            </div>
        `;
            hasImprovements = true;
        }

        // Highlight experience
        if (result.highlight_experience && Array.isArray(result.highlight_experience) && result.highlight_experience.length > 0) {
            html += `
            <div class="improvement-section">
                <h2 class="improvement-section-title">
                    <i class="fas fa-star"></i> ${t('improvement.experience_highlights')}
                </h2>
                <div class="improvement-bullets">
                    ${result.highlight_experience.map(exp => `
                        <span class="improvement-bullet">
                            <i class="fas fa-check-circle"></i>
                            ${exp}
                        </span>
                    `).join('')}
                </div>
            </div>
        `;
            hasImprovements = true;
        }

        // ATS tips
        if (result.ats_tips && Array.isArray(result.ats_tips) && result.ats_tips.length > 0) {
            html += `
            <div class="improvement-section">
                <h2 class="improvement-section-title">
                    <i class="fas fa-robot"></i> ${t('improvement.ats_tips')}
                </h2>
                <div class="improvement-bullets">
                    ${result.ats_tips.map(tip => `
                        <span class="improvement-bullet">
                            <i class="fas fa-magic"></i>
                            ${tip}
                        </span>
                    `).join('')}
                </div>
            </div>
        `;
            hasImprovements = true;
        }

        // Career advice
        if (result.career_advice && typeof result.career_advice === 'string' && result.career_advice.trim()) {
            html += `
            <div class="improvement-section">
                <h2 class="improvement-section-title">
                    <i class="fas fa-compass"></i> ${t('improvement.career_advice')}
                </h2>
                <div class="improvement-text">${result.career_advice}</div>
            </div>
        `;
            hasImprovements = true;
        }

        if (!hasImprovements) {
            html = `<p class="improvement-text">${t('improvement.no_improvements')}</p>`;
        }

        mainContainer.innerHTML = html;

        // Wait for render then split content
        setTimeout(() => {
            this.splitImprovementsContent();
        }, 100);
    }

    splitImprovementsContent() {
        const mainContainer = document.getElementById('improvementsMain');
        const overflowContainer = document.getElementById('improvementsOverflow');
        const previewContainer = document.querySelector('.cv-preview-container');

        if (!mainContainer || !overflowContainer || !previewContainer) {
            console.error('Containers not found for splitting content');
            return;
        }

        // Get preview height
        const previewHeight = previewContainer.offsetHeight;

        // Get all sections
        const allSections = Array.from(mainContainer.children);

        let currentHeight = 0;
        let overflowSections = [];

        allSections.forEach((section, index) => {
            const sectionHeight = section.offsetHeight - 50;
            const marginBottom = 40; // gap between sections

            // Allow 95% usage of preview height to maximize space
            const maxAllowedHeight = previewHeight * 0.95;

            if (currentHeight + sectionHeight > maxAllowedHeight && index > 0) {
                overflowSections.push(section);
            } else {
                currentHeight += sectionHeight + marginBottom;
            }
        });

        console.log(`Preview height: ${previewHeight}px, Used: ${currentHeight}px (${Math.round(currentHeight/previewHeight*100)}%)`);

        // Move overflow sections
        overflowSections.forEach(section => {
            overflowContainer.appendChild(section);
        });

        console.log(`Split improvements: ${allSections.length - overflowSections.length} main, ${overflowSections.length} overflow`);
    }

    renderCVPreview() {
        console.log('Rendering CV preview with data:', this.improvedCVData);
        console.log('Original CV data:', this.cvData);

        // The advice API doesn't return the CV, we need to use the original CV data
        if (!this.cvData || !this.cvData.cv) {
            this.elements.cvPreview.innerHTML = `<div class="preview-loading"><p>${t('preview.error_no_data')}</p></div>`;
            return;
        }

        const cv = this.cvData.cv;

        let html = ''; // NO WRAPPER DIV!

        // Contact info
        if (cv.contact) {
            let contact = cv.contact;

            // Handle different contact structures
            if (cv.contact.raw && typeof cv.contact.raw === 'string') {
                try {
                    let jsonStr = cv.contact.raw;
                    if (jsonStr.includes('```json')) {
                        jsonStr = jsonStr.replace(/```json\n?/g, '').replace(/```\n?/g, '').trim();
                    }
                    contact = JSON.parse(jsonStr);
                } catch (e) {
                    console.error('Failed to parse contact.raw in preview:', e);
                }
            } else if (cv.contact.additionalProp1) {
                contact = cv.contact.additionalProp1;
            }

            if (contact.name || contact.email || contact.phone || contact.title) {
                html += `<h1>${contact.name || 'Your Name'}</h1>`;
                if (contact.email) html += `<p>Email: ${contact.email}</p>`;
                if (contact.phone) html += `<p>Phone: ${contact.phone}</p>`;
                if (contact.location) html += `<p>Location: ${contact.location}</p>`;
                if (contact.linkedin || contact.links) html += `<p>LinkedIn: ${contact.linkedin || contact.links}</p>`;
            }
        }

        // Summary
        if (cv.summary && cv.summary.trim()) {
            html += `<h2>Summary</h2><p>${cv.summary}</p>`;
        }

        // Experience
        if (cv.experience && Array.isArray(cv.experience) && cv.experience.length > 0) {
            html += '<h2>Experience</h2>';
            cv.experience.forEach(exp => {
                if (exp && exp.trim()) {
                    html += `<p>${exp}</p>`;
                }
            });
        }

        // Education
        if (cv.education && Array.isArray(cv.education) && cv.education.length > 0) {
            html += '<h2>Education</h2>';
            cv.education.forEach(edu => {
                if (edu && edu.trim()) {
                    html += `<p>${edu}</p>`;
                }
            });
        }

        // Skills
        if (cv.skills && Array.isArray(cv.skills) && cv.skills.length > 0) {
            html += '<h2>Skills</h2><ul>';
            cv.skills.forEach(skill => {
                if (skill && skill.trim()) {
                    html += `<li>${skill}</li>`;
                }
            });
            html += '</ul>';
        }

        // Projects
        if (cv.projects && Array.isArray(cv.projects) && cv.projects.length > 0) {
            html += '<h2>Projects</h2>';
            cv.projects.forEach(project => {
                if (project && project.trim()) {
                    html += `<p>${project}</p>`;
                }
            });
        }

        // Certifications
        if (cv.certifications && Array.isArray(cv.certifications) && cv.certifications.length > 0) {
            const validCerts = cv.certifications.filter(cert => cert && cert.trim());
            if (validCerts.length > 0) {
                html += '<h2>Certifications</h2><ul>';
                validCerts.forEach(cert => {
                    html += `<li>${cert}</li>`;
                });
                html += '</ul>';
            }
        }

        // Languages
        if (cv.languages && Array.isArray(cv.languages) && cv.languages.length > 0) {
            const validLangs = cv.languages.filter(lang => lang && lang.trim());
            if (validLangs.length > 0) {
                html += '<h2>Languages</h2><ul>';
                validLangs.forEach(lang => {
                    html += `<li>${lang}</li>`;
                });
                html += '</ul>';
            }
        }

        // Achievements
        if (cv.achievements && Array.isArray(cv.achievements) && cv.achievements.length > 0) {
            const validAchs = cv.achievements.filter(ach => ach && ach.trim());
            if (validAchs.length > 0) {
                html += '<h2>Achievements</h2><ul>';
                validAchs.forEach(ach => {
                    html += `<li>${ach}</li>`;
                });
                html += '</ul>';
            }
        }

        // NO CLOSING DIV! Just raw HTML elements

        // Store the improved CV data for pagination
        window.improvedCVData = {
            html: html,
            improvements: this.improvedCVData
        };

        console.log('Calling CVPagination with HTML length:', html.length);
        console.log('HTML content preview:', html.substring(0, 500));

        // Use CVPagination to render properly
        if (window.CVPagination) {
            const pages = CVPagination.renderPaginatedCV(html);
            console.log('CVPagination returned pages:', pages.length);
        } else {
            console.error('CVPagination not available!');
            this.elements.cvPreview.innerHTML = `<div class="cv-content">${html}</div>`;
        }
    }

    resetToUpload() {
        this.elements.processingSection.classList.add('d-none');
        this.elements.resultsSection.classList.add('d-none');
        this.elements.uploadSection.classList.remove('d-none');
    }
}

// Global functions
function removeFile() {
    const improver = window.cvImprover;
    improver.selectedFile = null;
    improver.elements.filePreview.classList.add('d-none');
    improver.elements.dropZone.style.display = 'block';
    improver.elements.fileInput.value = '';
    improver.validateForm();
}

async function downloadImprovedCV() {
    try {
        const cvContentElement = document.querySelector('.cv-content');

        if (!cvContentElement) {
            throw new Error(t('error.cv_content_not_found'));
        }

        const cvContent = cvContentElement.innerHTML;

        // Show loading
        Swal.fire({
            title: t('progress.generating_pdf'),
            html: t('progress.please_wait'),
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        const response = await fetch('/api/cv/generate-pdf', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ html: cvContent })
        });

// Treat 204 as success
        if (response.status === 204 || response.ok) {
            // displayImprovedCV({
            //     html: cvContent, // Your CV HTML
            //     improvements: [] // Your improvements array
            // });
            Swal.fire({
                icon: 'success',
                title: t('modal.success'),
                text: t('success.cv_downloaded'),
                timer: 2000,
                showConfirmButton: false
            });
            return; // skip rest — file already downloaded
        }

        const errorText = await response.text();
        console.error('PDF generation error:', errorText);
        throw new Error(t('error.pdf_generation_failed') + response.status);
    } catch (error) {
        console.error('Download error:', error);
        Swal.fire({
            icon: 'error',
            title: t('modal.error'),
            text: t('error.download_failed') + error.message,
            confirmButtonColor: '#3464b0'
        });
    }
}

function togglePreview() {
    const modal = document.getElementById('fullscreenModal');
    const isHidden = modal.classList.contains('d-none');

    if (isHidden) {
        // Show fullscreen
        const cvContent = document.querySelector('.cv-content').cloneNode(true);
        document.getElementById('fullscreenPreview').innerHTML = '';
        document.getElementById('fullscreenPreview').appendChild(cvContent);
        modal.classList.remove('d-none');
        document.body.style.overflow = 'hidden';
    } else {
        // Hide fullscreen
        modal.classList.add('d-none');
        document.body.style.overflow = 'auto';
    }
}

function improveAnother() {
    window.cvImprover.resetToUpload();
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', () => {
    // Set the smart CV API URL
    window.smartCVUrl = document.querySelector('meta[name="smart-cv-url"]')?.content || '';

    window.cvImprover = new CVImprover();
});
