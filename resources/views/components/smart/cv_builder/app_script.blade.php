<script>
    // A4 Configuration
    const A4_WIDTH = 794;
    const A4_HEIGHT = 1123;
    const A4_RATIO = 210 / 297;

    // Global State
    let selectedTemplate = null;
    let templateData = null;
    let currentStep = 1;
    const totalSteps = 7;
    let cvData = {
        template_id: null,
        slug: null,
        personal_details: {},
        employment_history: [],
        education: [],
        skills: [],
        summary: '',
        additional_sections: {
            courses: [],
            internships: [],
            languages: [],
            hobbies: ''
        },
        customize: {
            color: '#2c3e50',
            font_family: "'Inter', sans-serif",
            font_size: 14,
            spacing: 1.5
        },
        ready: false
    };

    let summaryEditor = null;
    let employmentEditors = [];
    let educationEditors = [];
    let currentPages = 1;

    // ==================== INITIALIZATION ====================
    document.addEventListener('DOMContentLoaded', function() {
        loadTemplates();
        setupAutoSaveInterval();
    });

    // Check if user is authenticated
    function checkAuth() {
        const authToken = window.authToken || localStorage.getItem('auth_token');
        if (!authToken) {
            alert('Please log in to save your CV');
            window.location.href = '/login';
            return false;
        }
        return true;
    }

    function generateUUID() {
        return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
            const r = Math.random() * 16 | 0;
            const v = c === 'x' ? r : (r & 0x3 | 0x8);
            return v.toString(16);
        });
    }

    // ==================== TEMPLATE LOADING ====================
    async function loadTemplates() {
        try {
            const response = await fetch('/api/templates', {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            if (!response.ok) throw new Error('Failed to load templates');

            const templates = await response.json();
            const grid = document.getElementById('templatesGrid');
            grid.innerHTML = '';

            templates.forEach(template => {
                const card = document.createElement('div');
                card.className = 'col-md-4';
                card.innerHTML = `
                <div class="template-card" data-template-id="${template.id}" onclick="selectTemplate(${template.id})">
                    <div class="template-preview">
                        ${template.file ?
                    `<img src="${template.file.fullpath}" alt="${template.name}" style="width: 100%; height: 100%; object-fit: cover;">` :
                    `<div style="display: flex; align-items: center; justify-content: center; height: 100%; background: #f0f0f0;">
                                <i class="fas fa-file-alt" style="font-size: 4rem; color: #ccc;"></i>
                            </div>`
                }
                    </div>
                    <div class="template-name">${template.name}</div>
                </div>
            `;
                grid.appendChild(card);
            });
        } catch (error) {
            console.error('Error loading templates:', error);
            showNotification('Failed to load templates', 'error');
        }
    }

    function selectTemplate(id) {
        document.querySelectorAll('.template-card').forEach(card => {
            card.classList.remove('selected');
        });
        const card = document.querySelector(`[data-template-id="${id}"]`);
        if (card) card.classList.add('selected');
        selectedTemplate = id;
    }

    async function confirmTemplate() {
        if (!selectedTemplate) {
            showNotification('Please select a template first!', 'error');
            return;
        }

        try {
            const response = await fetch(`/api/templates/${selectedTemplate}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            if (!response.ok) throw new Error('Failed to load template');

            templateData = await response.json();
            cvData.template_id = selectedTemplate;
            cvData.slug = generateUUID();

            // Check localStorage first
            const localDraft = localStorage.getItem(`cv_draft_template_${selectedTemplate}`);

            if (localDraft) {
                if (confirm('You have an unfinished draft for this template. Continue editing?')) {
                    cvData = JSON.parse(localDraft);
                }
            } else {
                // Check database for draft
                try {
                    const draftResponse = await fetch(`/api/cv/drafts/template/${selectedTemplate}`, {
                        headers: {
                            'Accept': 'application/json',
                            'Authorization': 'Bearer ' + getAuthToken(),
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });

                    if (draftResponse.ok) {
                        const draft = await draftResponse.json();
                        if (draft && draft.id) {
                            if (confirm('You have a saved draft for this template. Continue editing?')) {
                                cvData = draft;
                            }
                        }
                    }
                } catch (e) {
                    console.log('No draft found in database');
                }
            }

            // Initialize customize colors from template data
            if (templateData.data && templateData.data.colors) {
                cvData.customize.color = templateData.data.colors[0];
                initializeColorPicker(templateData.data.colors);
            } else {
                initializeColorPicker(['#2c3e50', '#34495e', '#1abc9c', '#3498db', '#9b59b6', '#e74c3c']);
            }

            document.getElementById('templateSelection').style.display = 'none';
            document.getElementById('cvBuilder').style.display = 'flex';
            document.getElementById('actionsBar').style.display = 'flex';

            initializeEditors();
            populateFormFromData();
            renderPreview();
            updateProgress();

        } catch (error) {
            console.error('Error confirming template:', error);
            showNotification('Failed to load template', 'error');
        }
    }

    function getAuthToken() {
        return localStorage.getItem('auth_token') || '';
    }

    function initializeColorPicker(colors) {
        const picker = document.getElementById('colorPicker');
        picker.innerHTML = '';

        colors.forEach((color, index) => {
            const option = document.createElement('div');
            option.className = 'color-option' + (index === 0 ? ' selected' : '');
            option.style.background = color;
            option.onclick = () => changeColor(color);
            picker.appendChild(option);
        });
    }

    // ==================== QUILL EDITORS ====================
    function initializeEditors() {
        summaryEditor = new Quill('#summaryEditor', {
            theme: 'snow',
            modules: {
                toolbar: [
                    ['bold', 'italic', 'underline'],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    ['clean']
                ]
            }
        });

        summaryEditor.on('text-change', () => {
            renderPreview();
            updateProgress();
        });

        const firstEmpEditor = document.querySelector('.employment-editor');
        if (firstEmpEditor) {
            employmentEditors[0] = initializeEmploymentEditor(firstEmpEditor);
        }

        const firstEduEditor = document.querySelector('.education-editor');
        if (firstEduEditor) {
            educationEditors[0] = initializeEducationEditor(firstEduEditor);
        }

        document.querySelectorAll('input, select, textarea').forEach(element => {
            element.addEventListener('input', () => {
                renderPreview();
                updateProgress();
            });
        });
    }

    function initializeEmploymentEditor(element) {
        const editor = new Quill(element, {
            theme: 'snow',
            modules: {
                toolbar: [
                    ['bold', 'italic', 'underline'],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    ['clean']
                ]
            }
        });

        editor.on('text-change', () => {
            renderPreview();
            updateProgress();
        });

        return editor;
    }

    function initializeEducationEditor(element) {
        const editor = new Quill(element, {
            theme: 'snow',
            modules: {
                toolbar: [
                    ['bold', 'italic', 'underline'],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    ['clean']
                ]
            }
        });

        editor.on('text-change', () => {
            renderPreview();
            updateProgress();
        });

        return editor;
    }

    // ==================== STEP NAVIGATION ====================
    function goToStep(step) {
        if (step >= 1 && step <= totalSteps) {
            currentStep = step;
            updateSteps();
        }
    }

    function nextStep() {
        if (currentStep < totalSteps) {
            currentStep++;
            updateSteps();
        }
    }

    function previousStep() {
        if (currentStep > 1) {
            currentStep--;
            updateSteps();
        }
    }

    function updateSteps() {
        document.querySelectorAll('.form-section').forEach(section => {
            section.classList.remove('active');
        });
        document.getElementById(`section${currentStep}`).classList.add('active');

        document.querySelectorAll('.step-circle').forEach((circle, index) => {
            circle.classList.remove('active', 'completed');
            if (index + 1 < currentStep) {
                circle.classList.add('completed');
            } else if (index + 1 === currentStep) {
                circle.classList.add('active');
            }
        });

        const progressWidth = ((currentStep - 1) / (totalSteps - 1)) * 100;
        document.getElementById('stepProgress').style.width = `${progressWidth}%`;

        document.getElementById('prevBtn').style.display = currentStep === 1 ? 'none' : 'block';
        document.getElementById('nextBtn').style.display = currentStep === totalSteps ? 'none' : 'block';
        document.getElementById('finishBtn').style.display = currentStep === totalSteps ? 'block' : 'none';

        renderPreview();
        updateProgress();
    }

    // ==================== ADD MORE ITEMS ====================
    function toggleAdditionalDetails() {
        const details = document.getElementById('additionalDetails');
        details.style.display = details.style.display === 'none' ? 'block' : 'none';
    }

    function addEmployment() {
        const container = document.getElementById('employmentList');
        const index = employmentEditors.length;
        const newItem = document.createElement('div');
        newItem.className = 'employment-item border rounded p-3 mb-3';
        newItem.dataset.index = index;
        newItem.innerHTML = `
        <button class="btn btn-sm btn-danger float-end" onclick="this.parentElement.remove(); renderPreview();">
            <i class="fas fa-times"></i>
        </button>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label class="form-label">Job Title</label>
                    <input type="text" class="form-control emp-job-title" placeholder="Software Engineer" oninput="renderPreview(); updateProgress();">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label class="form-label">Company</label>
                    <input type="text" class="form-control emp-company" placeholder="Tech Corp" oninput="renderPreview(); updateProgress();">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label class="form-label">Start Date</label>
                    <input type="month" class="form-control emp-start" oninput="renderPreview(); updateProgress();">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label class="form-label">End Date</label>
                    <input type="month" class="form-control emp-end" oninput="renderPreview(); updateProgress();">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label class="form-label">City</label>
                    <input type="text" class="form-control emp-city" placeholder="San Francisco" oninput="renderPreview(); updateProgress();">
                </div>
            </div>
        </div>
        <div class="form-group position-relative">
            <label class="form-label">Description</label>
            <button class="ai-improve-btn" onclick="improveWithAI('employment', ${index})">
                <i class="fas fa-magic"></i> Improve with AI
            </button>
            <div class="editor-container">
                <div class="employment-editor quill-editor" data-editor-index="${index}"></div>
            </div>
        </div>
    `;
        container.appendChild(newItem);

        const editorElement = newItem.querySelector('.employment-editor');
        employmentEditors[index] = initializeEmploymentEditor(editorElement);
    }

    function addEducation() {
        const container = document.getElementById('educationList');
        const index = educationEditors.length;
        const newItem = document.createElement('div');
        newItem.className = 'education-item border rounded p-3 mb-3';
        newItem.dataset.index = index;
        newItem.innerHTML = `
        <button class="btn btn-sm btn-danger float-end" onclick="this.parentElement.remove(); renderPreview();">
            <i class="fas fa-times"></i>
        </button>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label class="form-label">School/University</label>
                    <input type="text" class="form-control edu-school" placeholder="MIT" oninput="renderPreview(); updateProgress();">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label class="form-label">Degree</label>
                    <input type="text" class="form-control edu-degree" placeholder="Bachelor of Computer Science" oninput="renderPreview(); updateProgress();">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label class="form-label">Start Date</label>
                    <input type="month" class="form-control edu-start" oninput="renderPreview(); updateProgress();">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label class="form-label">End Date</label>
                    <input type="month" class="form-control edu-end" oninput="renderPreview(); updateProgress();">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label class="form-label">City</label>
                    <input type="text" class="form-control edu-city" placeholder="Cambridge" oninput="renderPreview(); updateProgress();">
                </div>
            </div>
        </div>
        <div class="form-group position-relative">
            <label class="form-label">Description</label>
            <button class="ai-improve-btn" onclick="improveWithAI('education', ${index})">
                <i class="fas fa-magic"></i> Improve with AI
            </button>
            <div class="editor-container">
                <div class="education-editor quill-editor" data-editor-index="${index}"></div>
            </div>
        </div>
    `;
        container.appendChild(newItem);

        const editorElement = newItem.querySelector('.education-editor');
        educationEditors[index] = initializeEducationEditor(editorElement);
    }

    function addSkill() {
        const container = document.getElementById('skillsList');
        const newItem = document.createElement('div');
        newItem.className = 'skill-item border rounded p-3 mb-3';
        newItem.innerHTML = `
        <button class="btn btn-sm btn-danger float-end" onclick="this.parentElement.remove(); renderPreview();">
            <i class="fas fa-times"></i>
        </button>
        <div class="row">
            <div class="col-md-8">
                <div class="form-group">
                    <label class="form-label">Skill</label>
                    <input type="text" class="form-control skill-name" placeholder="JavaScript" oninput="renderPreview(); updateProgress();">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label class="form-label">Level</label>
                    <select class="form-select skill-level" onchange="renderPreview(); updateProgress();">
                        <option>Novice</option>
                        <option>Beginner</option>
                        <option>Skillful</option>
                        <option selected>Experienced</option>
                        <option>Expert</option>
                    </select>
                </div>
            </div>
        </div>
    `;
        container.appendChild(newItem);
    }

    function addCourse() {
        const container = document.getElementById('coursesList');
        const newItem = document.createElement('div');
        newItem.className = 'course-item border rounded p-3 mb-3';
        newItem.innerHTML = `
        <button class="btn btn-sm btn-danger float-end" onclick="this.parentElement.remove(); renderPreview();">
            <i class="fas fa-times"></i>
        </button>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label class="form-label">Course</label>
                    <input type="text" class="form-control course-name" placeholder="Advanced Machine Learning" oninput="renderPreview(); updateProgress();">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label class="form-label">Institution</label>
                    <input type="text" class="form-control course-institution" placeholder="Coursera" oninput="renderPreview(); updateProgress();">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label class="form-label">Start Date</label>
                    <input type="month" class="form-control course-start" oninput="renderPreview(); updateProgress();">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label class="form-label">End Date</label>
                    <input type="month" class="form-control course-end" oninput="renderPreview(); updateProgress();">
                </div>
            </div>
        </div>
    `;
        container.appendChild(newItem);
    }

    function addLanguage() {
        const container = document.getElementById('languagesList');
        const newItem = document.createElement('div');
        newItem.className = 'language-item border rounded p-3 mb-3';
        newItem.innerHTML = `
        <button class="btn btn-sm btn-danger float-end" onclick="this.parentElement.remove(); renderPreview();">
            <i class="fas fa-times"></i>
        </button>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label class="form-label">Language</label>
                    <input type="text" class="form-control lang-name" placeholder="Spanish" oninput="renderPreview(); updateProgress();">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label class="form-label">Level</label>
                    <select class="form-select lang-level" onchange="renderPreview(); updateProgress();">
                        <option>Select level</option>
                        <option>Native speaker</option>
                        <option>Highly proficient</option>
                        <option>Very good command</option>
                        <option>Good working knowledge</option>
                        <option>Working knowledge</option>
                        <option>C2</option>
                        <option>C1</option>
                        <option>B2</option>
                        <option>B1</option>
                        <option>A2</option>
                        <option>A1</option>
                    </select>
                </div>
            </div>
        </div>
    `;
        container.appendChild(newItem);
    }

    function improveWithAI(section, index) {
        showNotification('AI improvement feature coming soon!', 'info');
    }

    // ==================== RENDER A4 PREVIEW ====================
    function renderPreview() {
        collectData();

        const container = document.getElementById('cvPreviewContainer');
        container.innerHTML = '';

        const primaryColor = cvData.customize.color;
        const fontFamily = cvData.customize.font_family;
        const fontSize = cvData.customize.font_size;
        const lineHeight = cvData.customize.spacing;

        const page = createA4Page(primaryColor, fontFamily, fontSize, lineHeight);
        container.appendChild(page);

        const sidebar = page.querySelector('.cv-sidebar');
        const main = page.querySelector('.cv-main');

        renderSidebar(sidebar, primaryColor);
        renderMainContent(main, primaryColor);

        handlePageOverflow(container, primaryColor, fontFamily, fontSize, lineHeight);
    }

    function createA4Page(color, font, size, spacing) {
        const page = document.createElement('div');
        page.className = 'cv-page';
        page.style.cssText = `
        width: ${A4_WIDTH}px;
        height: ${A4_HEIGHT}px;
        background: white;
        display: flex;
        overflow: hidden;
        position: relative;
        font-family: ${font};
        font-size: ${size}px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        margin-bottom: 20px;
    `;

        const sidebar = document.createElement('div');
        sidebar.className = 'cv-sidebar';
        sidebar.style.cssText = `
        width: 30%;
        background: ${color};
        color: white;
        padding: 40px 30px;
        overflow: hidden;
    `;

        const main = document.createElement('div');
        main.className = 'cv-main';
        main.style.cssText = `
        width: 70%;
        padding: 40px;
        background: white;
        overflow: hidden;
        line-height: ${spacing};
    `;

        page.appendChild(sidebar);
        page.appendChild(main);

        return page;
    }

    function renderSidebar(sidebar, color) {
        const pd = cvData.personal_details;

        const avatar = document.createElement('div');
        avatar.className = 'cv-avatar';
        avatar.style.cssText = `
        width: 120px;
        height: 120px;
        border-radius: 50%;
        background: rgba(255,255,255,0.2);
        margin: 0 auto 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3rem;
        color: white;
    `;
        avatar.innerHTML = `<i class="fas fa-user"></i>`;
        sidebar.appendChild(avatar);

        const name = document.createElement('div');
        name.id = 'cvName';
        name.style.cssText = 'font-size: 1.5rem; font-weight: 700; margin-bottom: 5px; text-align: center;';
        name.textContent = `${pd.first_name || ''} ${pd.last_name || ''}`.trim() || 'Your Name';
        sidebar.appendChild(name);

        const jobTitle = document.createElement('div');
        jobTitle.id = 'cvJobTitle';
        jobTitle.style.cssText = 'text-align: center; opacity: 0.9; margin-bottom: 30px; font-size: 0.95rem;';
        jobTitle.textContent = pd.job_title || 'Your Job Title';
        sidebar.appendChild(jobTitle);

        if (pd.email || pd.phone || pd.city_state) {
            const contactSection = document.createElement('div');
            contactSection.className = 'cv-section';
            contactSection.innerHTML = `
            <div class="cv-section-title" style="font-size: 1rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 15px; padding-bottom: 8px; border-bottom: 2px solid rgba(255,255,255,0.3);">CONTACT</div>
            <div id="cvEmail" style="margin-bottom: 10px; font-size: 0.85rem;">${pd.email ? `<i class="fas fa-envelope" style="width: 20px;"></i> ${pd.email}` : ''}</div>
            <div id="cvPhone" style="margin-bottom: 10px; font-size: 0.85rem;">${pd.phone ? `<i class="fas fa-phone" style="width: 20px;"></i> ${pd.phone}` : ''}</div>
            <div id="cvAddress" style="margin-bottom: 10px; font-size: 0.85rem;">${pd.city_state && pd.country ? `<i class="fas fa-map-marker-alt" style="width: 20px;"></i> ${pd.city_state}, ${pd.country}` : ''}</div>
        `;
            sidebar.appendChild(contactSection);
        }

        if (cvData.skills && cvData.skills.length > 0) {
            const skillsSection = document.createElement('div');
            skillsSection.className = 'cv-section';
            skillsSection.innerHTML = `
            <div class="cv-section-title" style="font-size: 1rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 15px; padding-bottom: 8px; border-bottom: 2px solid rgba(255,255,255,0.3);">SKILLS</div>
            <div id="cvSkills"></div>
        `;
            sidebar.appendChild(skillsSection);

            const skillsContainer = skillsSection.querySelector('#cvSkills');
            cvData.skills.forEach(skill => {
                if (skill.skill) {
                    const skillDiv = document.createElement('div');
                    skillDiv.style.cssText = 'margin-bottom: 12px;';
                    skillDiv.innerHTML = `
                    <div style="font-size: 0.9rem; margin-bottom: 3px;">${skill.skill}</div>
                    <div style="font-size: 0.75rem; opacity: 0.8;">${skill.level || 'Experienced'}</div>
                `;
                    skillsContainer.appendChild(skillDiv);
                }
            });
        }

        if (cvData.additional_sections.languages && cvData.additional_sections.languages.length > 0) {
            const langSection = document.createElement('div');
            langSection.className = 'cv-section';
            langSection.innerHTML = `
            <div class="cv-section-title" style="font-size: 1rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 15px; padding-bottom: 8px; border-bottom: 2px solid rgba(255,255,255,0.3);">LANGUAGES</div>
            <div id="cvLanguages"></div>
        `;
            sidebar.appendChild(langSection);

            const langContainer = langSection.querySelector('#cvLanguages');
            cvData.additional_sections.languages.forEach(lang => {
                if (lang.language && lang.level && lang.level !== 'Select level') {
                    const langDiv = document.createElement('div');
                    langDiv.style.cssText = 'margin-bottom: 12px;';
                    langDiv.innerHTML = `
                    <div style="font-size: 0.9rem; margin-bottom: 3px;">${lang.language}</div>
                    <div style="font-size: 0.75rem; opacity: 0.8;">${lang.level}</div>
                `;
                    langContainer.appendChild(langDiv);
                }
            });
        }
    }

    function renderMainContent(main, primaryColor) {
        if (cvData.summary && cvData.summary.trim()) {
            const summarySection = document.createElement('div');
            summarySection.className = 'cv-section';
            summarySection.innerHTML = `
            <div class="cv-section-title" style="font-size: 1rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 15px; padding-bottom: 8px; border-bottom: 2px solid ${primaryColor}; color: ${primaryColor};">PROFESSIONAL SUMMARY</div>
            <div id="cvSummary" style="font-size: 0.9rem; line-height: 1.6; color: #333;">${cvData.summary}</div>
        `;
            main.appendChild(summarySection);
        }

        if (cvData.employment_history && cvData.employment_history.length > 0) {
            const empSection = document.createElement('div');
            empSection.className = 'cv-section';
            empSection.innerHTML = `
            <div class="cv-section-title" style="font-size: 1rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 15px; padding-bottom: 8px; border-bottom: 2px solid ${primaryColor}; color: ${primaryColor};">EXPERIENCE</div>
            <div id="cvEmployment"></div>
        `;
            main.appendChild(empSection);

            const empContainer = empSection.querySelector('#cvEmployment');
            cvData.employment_history.forEach(emp => {
                if (emp.job_title || emp.company) {
                    const empDiv = document.createElement('div');
                    empDiv.style.cssText = 'margin-bottom: 20px;';
                    empDiv.innerHTML = `
                    <div style="font-weight: 600; color: #222; font-size: 1rem;">${emp.job_title || 'Position'}</div>
                    <div style="color: #666; font-size: 0.9rem; margin-bottom: 3px;">
                        ${emp.company || 'Company'} ${emp.city ? `• ${emp.city}` : ''}
                    </div>
                    <div style="color: #888; font-size: 0.85rem; margin-bottom: 8px;">
                        ${formatDate(emp.start_date)} - ${emp.end_date ? formatDate(emp.end_date) : 'Present'}
                    </div>
                    <div style="font-size: 0.9rem; line-height: 1.6; color: #444;">
                        ${emp.description || ''}
                    </div>
                `;
                    empContainer.appendChild(empDiv);
                }
            });
        }

        if (cvData.education && cvData.education.length > 0) {
            const eduSection = document.createElement('div');
            eduSection.className = 'cv-section';
            eduSection.innerHTML = `
            <div class="cv-section-title" style="font-size: 1rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 15px; padding-bottom: 8px; border-bottom: 2px solid ${primaryColor}; color: ${primaryColor};">EDUCATION</div>
            <div id="cvEducation"></div>
        `;
            main.appendChild(eduSection);

            const eduContainer = eduSection.querySelector('#cvEducation');
            cvData.education.forEach(edu => {
                if (edu.school || edu.degree) {
                    const eduDiv = document.createElement('div');
                    eduDiv.style.cssText = 'margin-bottom: 20px;';
                    eduDiv.innerHTML = `
                    <div style="font-weight: 600; color: #222; font-size: 1rem;">${edu.degree || 'Degree'}</div>
                    <div style="color: #666; font-size: 0.9rem; margin-bottom: 3px;">
                        ${edu.school || 'School'} ${edu.city ? `• ${edu.city}` : ''}
                    </div>
                    <div style="color: #888; font-size: 0.85rem; margin-bottom: 8px;">
                        ${formatDate(edu.start_date)} - ${formatDate(edu.end_date)}
                    </div>
                    <div style="font-size: 0.9rem; line-height: 1.6; color: #444;">
                        ${edu.description || ''}
                    </div>
                `;
                    eduContainer.appendChild(eduDiv);
                }
            });
        }

        if (cvData.additional_sections.courses && cvData.additional_sections.courses.length > 0) {
            const coursesSection = document.createElement('div');
            coursesSection.className = 'cv-section';
            coursesSection.innerHTML = `
            <div class="cv-section-title" style="font-size: 1rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 15px; padding-bottom: 8px; border-bottom: 2px solid ${primaryColor}; color: ${primaryColor};">COURSES</div>
            <div id="cvCourses"></div>
        `;
            main.appendChild(coursesSection);

            const coursesContainer = coursesSection.querySelector('#cvCourses');
            cvData.additional_sections.courses.forEach(course => {
                if (course.course || course.institution) {
                    const courseDiv = document.createElement('div');
                    courseDiv.style.cssText = 'margin-bottom: 15px;';
                    courseDiv.innerHTML = `
                    <div style="font-weight: 600; color: #222; font-size: 0.95rem;">${course.course || 'Course'}</div>
                    <div style="color: #666; font-size: 0.85rem;">${course.institution || 'Institution'}</div>
                    <div style="color: #888; font-size: 0.8rem;">
                        ${formatDate(course.start_date)} - ${formatDate(course.end_date)}
                    </div>
                `;
                    coursesContainer.appendChild(courseDiv);
                }
            });
        }

        if (cvData.additional_sections.hobbies && cvData.additional_sections.hobbies.trim()) {
            const hobbiesSection = document.createElement('div');
            hobbiesSection.className = 'cv-section';
            hobbiesSection.innerHTML = `
            <div class="cv-section-title" style="font-size: 1rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 15px; padding-bottom: 8px; border-bottom: 2px solid ${primaryColor}; color: ${primaryColor};">HOBBIES</div>
            <div style="font-size: 0.9rem; line-height: 1.6; color: #444;">${cvData.additional_sections.hobbies}</div>
        `;
            main.appendChild(hobbiesSection);
        }
    }

    function handlePageOverflow(container, color, font, size, spacing) {
        const pages = container.querySelectorAll('.cv-page');
        if (pages.length === 0) return;

        const lastPage = pages[pages.length - 1];
        const mainContent = lastPage.querySelector('.cv-main');

        if (mainContent.scrollHeight > mainContent.clientHeight) {
            const newPage = createA4Page(color, font, size, spacing);
            container.appendChild(newPage);

            currentPages++;
        }
    }

    function formatDate(dateString) {
        if (!dateString) return '';
        const date = new Date(dateString + '-01');
        return date.toLocaleDateString('en-US', { month: 'short', year: 'numeric' });
    }

    // ==================== DATA COLLECTION ====================
    function collectData() {
        cvData.personal_details = {
            first_name: document.getElementById('firstName')?.value || '',
            last_name: document.getElementById('lastName')?.value || '',
            job_title: document.getElementById('jobTitle')?.value || '',
            email: document.getElementById('email')?.value || '',
            phone: document.getElementById('phone')?.value || '',
            address: document.getElementById('address')?.value || '',
            city_state: document.getElementById('cityState')?.value || '',
            country: document.getElementById('country')?.value || '',
            zip_code: document.getElementById('zipCode')?.value || '',
            driving_license: document.getElementById('drivingLicense')?.value || '',
            place_of_birth: document.getElementById('placeOfBirth')?.value || '',
            date_of_birth: document.getElementById('dateOfBirth')?.value || '',
            nationality: document.getElementById('nationality')?.value || ''
        };

        cvData.summary = summaryEditor ? summaryEditor.root.innerHTML : '';

        cvData.employment_history = [];
        document.querySelectorAll('.employment-item').forEach((item, index) => {
            const jobTitle = item.querySelector('.emp-job-title')?.value || '';
            const company = item.querySelector('.emp-company')?.value || '';
            const startDate = item.querySelector('.emp-start')?.value || '';
            const endDate = item.querySelector('.emp-end')?.value || '';
            const city = item.querySelector('.emp-city')?.value || '';
            const description = employmentEditors[index] ? employmentEditors[index].root.innerHTML : '';

            cvData.employment_history.push({
                job_title: jobTitle,
                company: company,
                start_date: startDate,
                end_date: endDate,
                city: city,
                description: description
            });
        });

        cvData.education = [];
        document.querySelectorAll('.education-item').forEach((item, index) => {
            const school = item.querySelector('.edu-school')?.value || '';
            const degree = item.querySelector('.edu-degree')?.value || '';
            const startDate = item.querySelector('.edu-start')?.value || '';
            const endDate = item.querySelector('.edu-end')?.value || '';
            const city = item.querySelector('.edu-city')?.value || '';
            const description = educationEditors[index] ? educationEditors[index].root.innerHTML : '';

            cvData.education.push({
                school: school,
                degree: degree,
                start_date: startDate,
                end_date: endDate,
                city: city,
                description: description
            });
        });

        cvData.skills = [];
        document.querySelectorAll('.skill-item').forEach(item => {
            const skill = item.querySelector('.skill-name')?.value || '';
            const level = item.querySelector('.skill-level')?.value || '';
            if (skill) {
                cvData.skills.push({ skill, level });
            }
        });

        cvData.additional_sections.courses = [];
        document.querySelectorAll('.course-item').forEach(item => {
            const course = item.querySelector('.course-name')?.value || '';
            const institution = item.querySelector('.course-institution')?.value || '';
            const startDate = item.querySelector('.course-start')?.value || '';
            const endDate = item.querySelector('.course-end')?.value || '';

            cvData.additional_sections.courses.push({
                course: course,
                institution: institution,
                start_date: startDate,
                end_date: endDate
            });
        });

        cvData.additional_sections.languages = [];
        document.querySelectorAll('.language-item').forEach(item => {
            const language = item.querySelector('.lang-name')?.value || '';
            const level = item.querySelector('.lang-level')?.value || '';
            if (language && level !== 'Select level') {
                cvData.additional_sections.languages.push({ language, level });
            }
        });

        cvData.additional_sections.hobbies = document.getElementById('hobbies')?.value || '';
    }

    function populateFormFromData() {
        if (cvData.personal_details) {
            const pd = cvData.personal_details;
            if (pd.first_name) document.getElementById('firstName').value = pd.first_name;
            if (pd.last_name) document.getElementById('lastName').value = pd.last_name;
            if (pd.job_title) document.getElementById('jobTitle').value = pd.job_title;
            if (pd.email) document.getElementById('email').value = pd.email;
            if (pd.phone) document.getElementById('phone').value = pd.phone;
            if (pd.address) document.getElementById('address').value = pd.address;
            if (pd.city_state) document.getElementById('cityState').value = pd.city_state;
            if (pd.country) document.getElementById('country').value = pd.country;
            if (pd.zip_code) document.getElementById('zipCode').value = pd.zip_code;
            if (pd.driving_license) document.getElementById('drivingLicense').value = pd.driving_license;
            if (pd.place_of_birth) document.getElementById('placeOfBirth').value = pd.place_of_birth;
            if (pd.date_of_birth) document.getElementById('dateOfBirth').value = pd.date_of_birth;
            if (pd.nationality) document.getElementById('nationality').value = pd.nationality;
        }

        if (cvData.summary && summaryEditor) {
            summaryEditor.root.innerHTML = cvData.summary;
        }

        if (cvData.employment_history && cvData.employment_history.length > 0) {
            cvData.employment_history.forEach((emp, index) => {
                if (index > 0) addEmployment();

                const item = document.querySelectorAll('.employment-item')[index];
                if (item) {
                    if (emp.job_title) item.querySelector('.emp-job-title').value = emp.job_title;
                    if (emp.company) item.querySelector('.emp-company').value = emp.company;
                    if (emp.start_date) item.querySelector('.emp-start').value = emp.start_date;
                    if (emp.end_date) item.querySelector('.emp-end').value = emp.end_date;
                    if (emp.city) item.querySelector('.emp-city').value = emp.city;
                    if (emp.description && employmentEditors[index]) {
                        employmentEditors[index].root.innerHTML = emp.description;
                    }
                }
            });
        }

        if (cvData.education && cvData.education.length > 0) {
            cvData.education.forEach((edu, index) => {
                if (index > 0) addEducation();

                const item = document.querySelectorAll('.education-item')[index];
                if (item) {
                    if (edu.school) item.querySelector('.edu-school').value = edu.school;
                    if (edu.degree) item.querySelector('.edu-degree').value = edu.degree;
                    if (edu.start_date) item.querySelector('.edu-start').value = edu.start_date;
                    if (edu.end_date) item.querySelector('.edu-end').value = edu.end_date;
                    if (edu.city) item.querySelector('.edu-city').value = edu.city;
                    if (edu.description && educationEditors[index]) {
                        educationEditors[index].root.innerHTML = edu.description;
                    }
                }
            });
        }

        if (cvData.skills && cvData.skills.length > 0) {
            cvData.skills.forEach((skill, index) => {
                if (index > 0) addSkill();

                const item = document.querySelectorAll('.skill-item')[index];
                if (item) {
                    if (skill.skill) item.querySelector('.skill-name').value = skill.skill;
                    if (skill.level) item.querySelector('.skill-level').value = skill.level;
                }
            });
        }

        if (cvData.additional_sections.courses && cvData.additional_sections.courses.length > 0) {
            cvData.additional_sections.courses.forEach((course, index) => {
                if (index > 0) addCourse();

                const item = document.querySelectorAll('.course-item')[index];
                if (item) {
                    if (course.course) item.querySelector('.course-name').value = course.course;
                    if (course.institution) item.querySelector('.course-institution').value = course.institution;
                    if (course.start_date) item.querySelector('.course-start').value = course.start_date;
                    if (course.end_date) item.querySelector('.course-end').value = course.end_date;
                }
            });
        }

        if (cvData.additional_sections.languages && cvData.additional_sections.languages.length > 0) {
            cvData.additional_sections.languages.forEach((lang, index) => {
                if (index > 0) addLanguage();

                const item = document.querySelectorAll('.language-item')[index];
                if (item) {
                    if (lang.language) item.querySelector('.lang-name').value = lang.language;
                    if (lang.level) item.querySelector('.lang-level').value = lang.level;
                }
            });
        }

        if (cvData.additional_sections.hobbies) {
            document.getElementById('hobbies').value = cvData.additional_sections.hobbies;
        }
    }

    // ==================== PROGRESS ====================
    function updateProgress() {
        let totalFields = 0;
        let filledFields = 0;

        const countInput = (selector) => {
            const element = document.querySelector(selector);
            if (element && element.value && element.value.trim()) {
                filledFields++;
            }
            totalFields++;
        };

        countInput('#firstName');
        countInput('#lastName');
        countInput('#jobTitle');
        countInput('#email');
        countInput('#phone');

        if (summaryEditor && summaryEditor.getText().trim().length > 10) {
            filledFields++;
        }
        totalFields++;

        document.querySelectorAll('.employment-item').forEach(item => {
            const inputs = item.querySelectorAll('input');
            totalFields += inputs.length;
            inputs.forEach(input => {
                if (input.value && input.value.trim()) filledFields++;
            });
        });

        document.querySelectorAll('.education-item').forEach(item => {
            const inputs = item.querySelectorAll('input');
            totalFields += inputs.length;
            inputs.forEach(input => {
                if (input.value && input.value.trim()) filledFields++;
            });
        });

        document.querySelectorAll('.skill-item').forEach(item => {
            totalFields++;
            const input = item.querySelector('.skill-name');
            if (input && input.value && input.value.trim()) filledFields++;
        });

        const percentage = totalFields > 0 ? Math.round((filledFields / totalFields) * 100) : 0;

        document.getElementById('progressPercentage').textContent = `${percentage}%`;
        document.getElementById('progressBar').style.width = `${percentage}%`;
    }

    // ==================== CUSTOMIZATION ====================
    function openCustomizeModal() {
        document.getElementById('customizeModal').classList.add('show');
    }

    function closeCustomizeModal() {
        document.getElementById('customizeModal').classList.remove('show');
    }

    function changeColor(color) {
        document.querySelectorAll('.color-option').forEach(option => {
            option.classList.remove('selected');
        });
        event.target.classList.add('selected');
        cvData.customize.color = color;
    }

    function changeFont(fontFamily) {
        cvData.customize.font_family = fontFamily;
        document.getElementById('fontSizeValue').parentElement.previousElementSibling.value = fontFamily;
    }

    function changeFontSize(size) {
        cvData.customize.font_size = parseInt(size);
        document.getElementById('fontSizeValue').textContent = size + 'px';
    }

    function changeSpacing(spacing) {
        cvData.customize.spacing = parseFloat(spacing);
        document.getElementById('spacingValue').textContent = spacing;
    }

    function applyCustomization() {
        closeCustomizeModal();
        renderPreview();
    }

    // ==================== AUTO SAVE ====================
    function setupAutoSaveInterval() {
        setInterval(() => {
            if (cvData.template_id) {
                collectData();
                localStorage.setItem(`cv_draft_template_${cvData.template_id}`, JSON.stringify(cvData));
            }
        }, 30000); // Every 30 seconds
    }

    // ==================== SAVE TO DATABASE ====================
    async function saveToDatabase() {
        if (!checkAuth()) return;
        collectData();

        document.getElementById('loadingOverlay').classList.add('show');

        try {
// Get the actual data from your existing form collection logic
            const cvData = {
                template_id: selectedTemplate,
                personal_details: {
                    first_name: document.getElementById('firstName').value,
                    last_name: document.getElementById('lastName').value,
                    job_title: document.getElementById('jobTitle').value,
                    email: document.getElementById('email').value,
                    phone: document.getElementById('phone').value,
                    address: document.getElementById('address').value,
                    city_state: document.getElementById('cityState').value,
                    country: document.getElementById('country').value
                },
                employment_history: [],
                education: [],
                skills: [],
                summary: summaryEditor ? summaryEditor.root.innerHTML : '',
                additional_sections: {},
                customize: {}
            }
            ; // Use your existing function
            const templateId = window.selectedTemplateId; // Use your existing variable

            const data = {
                template_id: templateId,
                personal_details: cvData.personalDetails,
                employment_history: cvData.employmentHistory,
                education: cvData.education,
                skills: cvData.skills,
                summary: cvData.summary,
                additional_sections: cvData.additionalSections,
                customize: cvData.customization
            };

            const response = await fetch('/api/cv/draft', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(data)
            });

            if (response.ok) {
                const result = await response.json();
                console.log('Draft saved successfully:', result);
                showNotification('CV saved successfully!', 'success');

                // Store CV ID for future updates
                if (result.cv && result.cv.id) {
                    currentCvId = result.cv.id;
                }
            } else {
                const errorData = await response.json();
                throw new Error(errorData.message || `Server error: ${response.status}`);
            }

        } catch (error) {
            console.error('Error saving CV:', error);
            document.getElementById('loadingOverlay').classList.remove('show');
            showNotification('Failed to save CV', 'error');
        }
    }

    // ==================== FINISH CV ====================
    async function finishCV() {
        collectData();

        if (!cvData.personal_details.first_name || !cvData.personal_details.last_name) {
            showNotification('Please fill in your first and last name', 'error');
            goToStep(1);
            return;
        }

        cvData.ready = true;

        document.getElementById('loadingOverlay').classList.add('show');

        try {
            // Get the actual data from your existing form collection logic
            collectData(); // ✅ Use the function that actually exists in your code
            templateId = selectedTemplate; // ✅ Use the actual variable

            data = {
                template_id: templateId,
                personal_details: cvData.personal_details, // ✅ Use the actual data structure
                employment_history: cvData.employment_history,
                education: cvData.education,
                skills: cvData.skills,
                summary: cvData.summary,
                additional_sections: cvData.additional_sections,
                customize: cvData.customize
            };
            const templateId = window.selectedTemplateId; // Use your existing variable

            const data = {
                template_id: templateId,
                personal_details: cvData.personalDetails,
                employment_history: cvData.employmentHistory,
                education: cvData.education,
                skills: cvData.skills,
                summary: cvData.summary,
                additional_sections: cvData.additionalSections,
                customize: cvData.customization
            };

            const response = await fetch('/api/cv/finalize', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Authorization': 'Bearer ' + (window.authToken || '')
                },
                body: JSON.stringify(data)
            });

            if (!response.ok) {
                throw new Error('Failed to finalize CV');
            }

            const result = await response.json();

            document.getElementById('loadingOverlay').classList.remove('show');

            localStorage.removeItem(`cv_draft_template_${cvData.template_id}`);

            showDownloadModal(result.cv);

        } catch (error) {
            console.error('Error finalizing CV:', error);
            document.getElementById('loadingOverlay').classList.remove('show');
            showNotification('Failed to finalize CV', 'error');
        }
    }

    function showDownloadModal(cv) {
        const modal = document.createElement('div');
        modal.id = 'downloadModal';
        modal.style.cssText = `
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
    `;

        modal.innerHTML = `
        <div style="background: white; padding: 3rem; border-radius: 1rem; max-width: 500px; text-align: center; box-shadow: 0 20px 60px rgba(0,0,0,0.3);">
            <div style="font-size: 4rem; margin-bottom: 1rem;">🎉</div>
            <h2 style="margin-bottom: 1rem; color: #10b981;">Your CV is Ready!</h2>
            <p style="color: #666; margin-bottom: 2rem;">Download your professional CV now</p>
            <div style="display: flex; gap: 1rem; justify-content: center; margin-bottom: 1rem;">
                <button class="btn btn-primary" onclick="downloadPDF()" style="padding: 0.75rem 1.5rem;">
                    <i class="fas fa-file-pdf"></i> Download PDF
                </button>
                <button class="btn btn-secondary" onclick="downloadImage()" style="padding: 0.75rem 1.5rem;">
                    <i class="fas fa-file-image"></i> Download Image
                </button>
            </div>
            <button class="btn btn-outline-secondary" onclick="closeDownloadModal()" style="padding: 0.5rem 1rem;">
                Close
            </button>
        </div>
    `;

        document.body.appendChild(modal);
    }

    function closeDownloadModal() {
        const modal = document.getElementById('downloadModal');
        if (modal) modal.remove();
    }

    // ==================== DOWNLOAD FUNCTIONS ====================
    async function downloadPDF() {
        const container = document.getElementById('cvPreviewContainer');
        const { jsPDF } = window.jspdf;

        document.getElementById('loadingOverlay').classList.add('show');

        try {
            const pages = container.querySelectorAll('.cv-page');
            const pdf = new jsPDF({
                orientation: 'portrait',
                unit: 'px',
                format: [A4_WIDTH, A4_HEIGHT]
            });

            for (let i = 0; i < pages.length; i++) {
                if (i > 0) pdf.addPage();

                const canvas = await html2canvas(pages[i], {
                    scale: 2,
                    useCORS: true,
                    logging: false,
                    width: A4_WIDTH,
                    height: A4_HEIGHT
                });

                const imgData = canvas.toDataURL('image/png');
                pdf.addImage(imgData, 'PNG', 0, 0, A4_WIDTH, A4_HEIGHT);
            }

            const fileName = `CV_${cvData.personal_details.first_name}_${cvData.personal_details.last_name}.pdf`;
            pdf.save(fileName);

            document.getElementById('loadingOverlay').classList.remove('show');
            showNotification('PDF downloaded successfully!', 'success');

        } catch (error) {
            console.error('Error generating PDF:', error);
            document.getElementById('loadingOverlay').classList.remove('show');
            showNotification('Failed to generate PDF', 'error');
        }
    }

    async function downloadImage() {
        const container = document.getElementById('cvPreviewContainer');

        document.getElementById('loadingOverlay').classList.add('show');

        try {
            const page = container.querySelector('.cv-page');

            const canvas = await html2canvas(page, {
                scale: 2,
                useCORS: true,
                logging: false,
                width: A4_WIDTH,
                height: A4_HEIGHT
            });

            canvas.toBlob((blob) => {
                const url = URL.createObjectURL(blob);
                const link = document.createElement('a');
                link.href = url;
                link.download = `CV_${cvData.personal_details.first_name}_${cvData.personal_details.last_name}.png`;
                link.click();
                URL.revokeObjectURL(url);

                document.getElementById('loadingOverlay').classList.remove('show');
                showNotification('Image downloaded successfully!', 'success');
            });

        } catch (error) {
            console.error('Error generating image:', error);
            document.getElementById('loadingOverlay').classList.remove('show');
            showNotification('Failed to generate image', 'error');
        }
    }

    // ==================== NOTIFICATIONS ====================
    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.style.cssText = `
        position: fixed;
        bottom: 2rem;
        right: 2rem;
        background: ${type === 'success' ? '#10b981' : type === 'error' ? '#ef4444' : '#3b82f6'};
        color: white;
        padding: 1rem 1.5rem;
        border-radius: 0.5rem;
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        z-index: 10000;
        animation: slideIn 0.3s ease;
        font-weight: 500;
    `;
        notification.textContent = message;

        document.body.appendChild(notification);

        setTimeout(() => {
            notification.style.animation = 'slideOut 0.3s ease';
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }

    const style = document.createElement('style');
    style.textContent = `
    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    @keyframes slideOut {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
    .customize-icon-btn {
        position: absolute;
        top: 10px;
        left: 10px;
        width: 45px;
        height: 45px;
        border-radius: 50%;
        background: white;
        border: 2px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        transition: all 0.2s ease;
        z-index: 100;
        font-size: 1.2rem;
        color: #2563eb;
    }
    .customize-icon-btn:hover {
        background: #2563eb;
        color: white;
        transform: scale(1.1);
    }
    .cv-preview-wrapper {
        position: relative;
        width: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
    }
`;
    document.head.appendChild(style);
</script>
