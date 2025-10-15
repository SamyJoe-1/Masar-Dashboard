<script>
    window.authToken = @json(auth()->check() ? auth()->user()->createToken('cv-builder')->plainTextToken : '');
    window.userId = @json(auth()->id());

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
        personal_details: {
            avatar: null
        },
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
                            ${template.image ?
                                        `<img src="${template.image}" alt="${template.name}" style="width: 100%; height: 100%; object-fit: cover;">` :
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

            const localDraft = localStorage.getItem(`cv_draft_template_${selectedTemplate}`);

            if (localDraft) {
                const result = await Swal.fire({
                    title: 'Unfinished Draft Found',
                    text: 'You have an unfinished draft for this template. Continue editing?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Continue',
                    cancelButtonText: 'Start Fresh',
                    confirmButtonColor: '#2563eb'
                });

                if (result.isConfirmed) {
                    cvData = JSON.parse(localDraft);
                }
            } else {
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
                            const result = await Swal.fire({
                                title: 'Saved Draft Found',
                                text: 'You have a saved draft for this template. Continue editing?',
                                icon: 'question',
                                showCancelButton: true,
                                confirmButtonText: 'Continue',
                                cancelButtonText: 'Start Fresh',
                                confirmButtonColor: '#2563eb'
                            });

                            if (result.isConfirmed) {
                                cvData = draft;
                            }
                        }
                    }
                } catch (e) {
                    console.log('No draft found in database');
                }
            }

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
        // Check if we have a token in the window object (from Blade)
        if (window.authToken && window.authToken !== '') {
            return window.authToken;
        }

        // Check localStorage as fallback
        const storedToken = localStorage.getItem('auth_token');
        if (storedToken) {
            return storedToken;
        }

        // If no token, redirect to login
        alert('Please log in to save your CV');
        window.location.href = '/login';
        return '';
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

        // Handle avatar upload
        document.getElementById('avatarInput')?.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    cvData.personal_details.avatar = event.target.result;
                    renderPreview();
                    updateProgress();
                };
                reader.readAsDataURL(file);
            }
        });

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
    background: ${pd.avatar ? `url(${pd.avatar}) center/cover` : 'rgba(255,255,255,0.2)'};
    margin: 0 auto 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 3rem;
    color: white;
`;
        avatar.innerHTML = pd.avatar ? '' : `<i class="fas fa-user"></i>`;
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
        let iterations = 0;
        let pageIndex = 0;

        while (pageIndex < 20 && iterations < 100) {
            iterations++;

            const pages = container.querySelectorAll('.cv-page');
            if (pageIndex >= pages.length) break;

            const currentPage = pages[pageIndex];
            const mainContent = currentPage.querySelector('.cv-main');

            // If doesn't overflow, move to next page
            if (mainContent.scrollHeight <= mainContent.clientHeight + 50) {
                pageIndex++;
                continue;
            }

            const sections = Array.from(mainContent.children);
            if (sections.length === 0) {
                pageIndex++;
                continue;
            }

            // Create next page
            let nextPage = pages[pageIndex + 1];
            if (!nextPage) {
                nextPage = createA4Page(color, font, size, spacing);
                container.appendChild(nextPage);
                nextPage.querySelector('.cv-sidebar').innerHTML = '';
                currentPages++;
            }

            const nextMain = nextPage.querySelector('.cv-main');

            // Get the last section
            const lastSection = sections[sections.length - 1];

            // Find items container inside this section
            const itemsContainer = lastSection.querySelector('#cvEmployment, #cvEducation, #cvCourses');

            if (itemsContainer && itemsContainer.children.length > 0) {
                // MOVE ONE ITEM from current page to next page
                const lastItem = itemsContainer.children[itemsContainer.children.length - 1];

                // Find matching section on next page OR create it
                let nextItemsContainer = nextMain.querySelector(`#${itemsContainer.id}`);

                if (!nextItemsContainer) {
                    // Create new section with same structure
                    const newSection = document.createElement('div');
                    newSection.className = 'cv-section';

                    // Clone only the title
                    const title = lastSection.querySelector('.cv-section-title');
                    if (title) {
                        newSection.appendChild(title.cloneNode(true));
                    }

                    // Create empty items container
                    const newItemsContainer = document.createElement('div');
                    newItemsContainer.id = itemsContainer.id;
                    newSection.appendChild(newItemsContainer);

                    nextMain.insertBefore(newSection, nextMain.firstChild);
                    nextItemsContainer = newItemsContainer;
                }

                // MOVE the item to next page
                nextItemsContainer.insertBefore(lastItem, nextItemsContainer.firstChild);

                // DON'T increment pageIndex - check this page again

            } else {
                // No items to split, move entire section
                nextMain.insertBefore(lastSection, nextMain.firstChild);
            }
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
            avatar: cvData.personal_details.avatar || null,
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
            if (pd.avatar) {cvData.personal_details.avatar = pd.avatar;}
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
            const response = await fetch('/api/cv/draft', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Authorization': 'Bearer ' + getAuthToken()
                },
                body: JSON.stringify({
                    template_id: cvData.template_id,
                    slug: cvData.slug,
                    personal_details: cvData.personal_details,
                    employment_history: cvData.employment_history,
                    education: cvData.education,
                    skills: cvData.skills,
                    summary: cvData.summary,
                    additional_sections: cvData.additional_sections,
                    customize: cvData.customize
                })
            });

            // Check if response is JSON
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                const text = await response.text();
                throw new Error(`Server returned HTML instead of JSON. Status: ${response.status}`);
            }

            const result = await response.json();

            if (!response.ok) {
                throw new Error(result.message || `Server error: ${response.status}`);
            }

            document.getElementById('loadingOverlay').classList.remove('show');
            showNotification('CV saved successfully!', 'success');

        } catch (error) {
            console.error('Error saving CV:', error);
            document.getElementById('loadingOverlay').classList.remove('show');
            showNotification('Failed to save CV: ' + error.message, 'error');
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
            const response = await fetch('/api/cv/finalize', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Authorization': 'Bearer ' + getAuthToken()
                },
                body: JSON.stringify({
                    template_id: cvData.template_id,
                    slug: cvData.slug,
                    personal_details: cvData.personal_details,
                    employment_history: cvData.employment_history,
                    education: cvData.education,
                    skills: cvData.skills,
                    summary: cvData.summary,
                    additional_sections: cvData.additional_sections,
                    customize: cvData.customize
                })
            });

            // Check if response is JSON
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                const text = await response.text();
                throw new Error(`Server returned HTML instead of JSON. Status: ${response.status}`);
            }

            const result = await response.json();

            if (!response.ok) {
                throw new Error(result.message || `Server error: ${response.status}`);
            }

            document.getElementById('loadingOverlay').classList.remove('show');
            localStorage.removeItem(`cv_draft_template_${cvData.template_id}`);
            showDownloadModal(result.cv);

        } catch (error) {
            console.error('Error finalizing CV:', error);
            document.getElementById('loadingOverlay').classList.remove('show');
            showNotification('Failed to finalize CV: ' + error.message, 'error');
        }
    }

    function showDownloadModal(cv) {
        Swal.fire({
            title: 'Your CV is Ready!',
            text: 'What would you like to do?',
            icon: 'success',
            showCancelButton: true,
            confirmButtonText: 'Download PDF',
            denyButtonText: 'View on Profile',
            cancelButtonText: 'View All CVs',
            showDenyButton: true,
            confirmButtonColor: '#2563eb',
            denyButtonColor: '#64748b',
            cancelButtonColor: '#ef4444'
        }).then((result) => {
            if (result.isConfirmed) {
                downloadPDF();
            } else if (result.isDenied) {
                const cvSlug = cv.slug || cv.id;
                window.location.href = `/cv/${cvSlug}`;
            } else if (result.dismiss === Swal.DismissReason.cancel) {
                window.location.href = '/my-cvs';
            }
        });
    }


    function closeDownloadModal() {
        const modal = document.getElementById('downloadModal');
        if (modal) modal.remove();
    }

    function getIconAsBase64(iconType) {
        const canvas = document.createElement('canvas');
        canvas.width = 40;
        canvas.height = 40;
        const ctx = canvas.getContext('2d');

        ctx.fillStyle = '#ffffff';
        ctx.font = '30px "Font Awesome 6 Free"';
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';

        let iconCode = '';
        switch(iconType) {
            case 'email': iconCode = '\uf0e0'; break;
            case 'phone': iconCode = '\uf879'; break;
            case 'location': iconCode = '\uf041'; break;
        }

        ctx.fillText(iconCode, 20, 20);
        return canvas.toDataURL('image/png');
    }

    function hexToRgb(hex) {
        const result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
        return result ? {
            r: parseInt(result[1], 16),
            g: parseInt(result[2], 16),
            b: parseInt(result[3], 16)
        } : { r: 44, g: 62, b: 80 };
    }

    // ==================== DOWNLOAD FUNCTIONS ====================
    async function downloadPDF() {
        const container = document.getElementById('cvPreviewContainer');
        const { jsPDF } = window.jspdf;

        document.getElementById('loadingOverlay').classList.add('show');

        try {
            const pdf = new jsPDF({
                orientation: 'portrait',
                unit: 'mm',
                format: 'a4',
                compress: true
            });

            const sidebarColor = cvData.customize.color;
            const rgb = hexToRgb(sidebarColor);
            const fontFamily = 'helvetica'; // Closest to Inter
            const baseFontSize = cvData.customize.font_size || 14;
            const lineSpacing = cvData.customize.spacing || 1.5;

            // Sidebar background (30% width = 63mm)
            pdf.setFillColor(rgb.r, rgb.g, rgb.b);
            pdf.rect(0, 0, 63, 297, 'F');

            // ========== SIDEBAR ==========
            pdf.setTextColor(255, 255, 255);
            let sidebarY = 40;

            // Avatar
// Avatar
            if (cvData.personal_details.avatar) {
                try {
                    const x = 16.5;
                    const y = 15;
                    const size = 30;

                    const img = cvData.personal_details.avatar;

                    const image = new Image();
                    image.crossOrigin = 'Anonymous';
                    image.src = img;
                    await new Promise((resolve, reject) => {
                        image.onload = resolve;
                        image.onerror = reject;
                    });

                    const canvas = document.createElement('canvas');
                    canvas.width = size * 4;
                    canvas.height = size * 4;
                    const ctx = canvas.getContext('2d');

                    // clear the canvas (ensures transparency)
                    ctx.clearRect(0, 0, canvas.width, canvas.height);

                    // draw circular clip
                    ctx.beginPath();
                    ctx.arc(canvas.width / 2, canvas.height / 2, canvas.width / 2, 0, Math.PI * 2);
                    ctx.closePath();
                    ctx.clip();

                    ctx.drawImage(image, 0, 0, canvas.width, canvas.height);

                    // ✅ Export as PNG (preserves transparency)
                    const roundedImg = canvas.toDataURL('image/png');

                    pdf.addImage(roundedImg, 'PNG', x, y, size, size, undefined, 'FAST');
                    sidebarY = 55;
                } catch (e) {
                    console.log('Could not add avatar');
                }
            } else {
                sidebarY = 30;
            }



            // Name - font-size: 1.5rem (24px preview) = 18pt PDF
            pdf.setFont(fontFamily, 'bold');
            pdf.setFontSize(18);
            const nameText = `${cvData.personal_details.first_name || ''} ${cvData.personal_details.last_name || ''}`.trim();
            if (nameText) {
                const nameLines = pdf.splitTextToSize(nameText, 55);
                nameLines.forEach(line => {
                    pdf.text(line, 31.5, sidebarY, { align: 'center' });
                    sidebarY += 6;
                });
            }

            // Job Title - font-size: 0.95rem (15px preview) = 11pt PDF
            if (cvData.personal_details.job_title) {
                pdf.setFont(fontFamily, 'normal');
                pdf.setFontSize(11);
                const jobLines = pdf.splitTextToSize(cvData.personal_details.job_title, 55);
                jobLines.forEach(line => {
                    pdf.text(line, 31.5, sidebarY, { align: 'center' });
                    sidebarY += 5;
                });
            }

            sidebarY += 10;

            // Contact Section
            if (cvData.personal_details.email || cvData.personal_details.phone || cvData.personal_details.city_state) {
                // Section title - font-size: 1rem (16px) = 12pt, uppercase, letter-spacing: 1px
                pdf.setFont(fontFamily, 'bold');
                pdf.setFontSize(12);
                pdf.text('CONTACT', 10, sidebarY);

                // Border line - border-bottom: 2px
                pdf.setDrawColor(255, 255, 255);
                pdf.setLineWidth(0.5);
                pdf.line(10, sidebarY + 2, 53, sidebarY + 2);
                sidebarY += 10;

                pdf.setFont(fontFamily, 'normal');
                pdf.setFontSize(9);

                // Email with icon
                if (cvData.personal_details.email) {
                    try {
                        const emailIcon = getIconAsBase64('email');
                        pdf.addImage(emailIcon, 'PNG', 9, sidebarY - 2.5, 3, 3);
                    } catch(e) {}

                    const emailLines = pdf.splitTextToSize(cvData.personal_details.email, 40);
                    pdf.text(emailLines, 14, sidebarY);
                    sidebarY += (emailLines.length * 4) + 2;
                }

                // Phone with icon
                if (cvData.personal_details.phone) {
                    try {
                        const phoneIcon = getIconAsBase64('phone');
                        pdf.addImage(phoneIcon, 'PNG', 9, sidebarY - 2.5, 3, 3);
                    } catch(e) {}

                    pdf.text(cvData.personal_details.phone, 14, sidebarY);
                    sidebarY += 6;
                }

                // Address with icon
                if (cvData.personal_details.city_state && cvData.personal_details.country) {
                    try {
                        const locationIcon = getIconAsBase64('location');
                        pdf.addImage(locationIcon, 'PNG', 9, sidebarY - 2.5, 3, 3);
                    } catch(e) {}

                    const addressText = `${cvData.personal_details.city_state}, ${cvData.personal_details.country}`;
                    const addressLines = pdf.splitTextToSize(addressText, 40);
                    pdf.text(addressLines, 14, sidebarY);
                    sidebarY += (addressLines.length * 4) + 2;
                }

                sidebarY += 8;
            }

            // Skills Section
            if (cvData.skills && cvData.skills.length > 0 && cvData.skills.some(s => s.skill)) {
                pdf.setFont(fontFamily, 'bold');
                pdf.setFontSize(12);
                pdf.text('SKILLS', 10, sidebarY);
                pdf.setLineWidth(0.5);
                pdf.line(10, sidebarY + 2, 53, sidebarY + 2);
                sidebarY += 10;

                cvData.skills.forEach(skill => {
                    if (skill.skill && sidebarY < 275) {
                        // Skill name - font-size: 0.9rem = 10pt, bold
                        pdf.setFont(fontFamily, 'bold');
                        pdf.setFontSize(10);
                        const skillLines = pdf.splitTextToSize(skill.skill, 43);
                        pdf.text(skillLines, 10, sidebarY);
                        sidebarY += (skillLines.length * 4.5);

                        // Skill level - font-size: 0.75rem = 8pt, normal
                        if (skill.level) {
                            pdf.setFont(fontFamily, 'normal');
                            pdf.setFontSize(8);
                            pdf.text(skill.level, 10, sidebarY);
                            sidebarY += 5;
                        }
                        sidebarY += 2;
                    }
                });

                sidebarY += 5;
            }

            // Languages Section
            if (cvData.additional_sections.languages && cvData.additional_sections.languages.length > 0 &&
                cvData.additional_sections.languages.some(l => l.language && l.level && l.level !== 'Select level')) {

                if (sidebarY < 270) {
                    pdf.setFont(fontFamily, 'bold');
                    pdf.setFontSize(12);
                    pdf.text('LANGUAGES', 10, sidebarY);
                    pdf.setLineWidth(0.5);
                    pdf.line(10, sidebarY + 2, 53, sidebarY + 2);
                    sidebarY += 10;

                    cvData.additional_sections.languages.forEach(lang => {
                        if (lang.language && lang.level && lang.level !== 'Select level' && sidebarY < 275) {
                            pdf.setFont(fontFamily, 'bold');
                            pdf.setFontSize(10);
                            const langLines = pdf.splitTextToSize(lang.language, 43);
                            pdf.text(langLines, 10, sidebarY);
                            sidebarY += (langLines.length * 4.5);

                            pdf.setFont(fontFamily, 'normal');
                            pdf.setFontSize(8);
                            pdf.text(lang.level, 10, sidebarY);
                            sidebarY += 6;
                        }
                    });
                }
            }

            // ========== MAIN CONTENT (70% = 147mm width, starts at 63mm) ==========
            pdf.setTextColor(0, 0, 0);
            let mainY = 20;
            const mainX = 70;
            const mainWidth = 130;

            // Professional Summary
            if (cvData.summary && cvData.summary.trim()) {
                // Section title - font-size: 1rem = 12pt, uppercase, bold
                pdf.setFont(fontFamily, 'bold');
                pdf.setFontSize(12);
                pdf.setTextColor(rgb.r, rgb.g, rgb.b);
                pdf.text('PROFESSIONAL SUMMARY', mainX, mainY);

                pdf.setDrawColor(rgb.r, rgb.g, rgb.b);
                pdf.setLineWidth(0.5);
                pdf.line(mainX, mainY + 2, mainX + mainWidth, mainY + 2);
                mainY += 9;

                // Content - font-size: 0.9rem = 10pt, line-height: 1.6
                pdf.setFont(fontFamily, 'normal');
                pdf.setFontSize(10);
                pdf.setTextColor(51, 51, 51);

                const summaryText = cvData.summary.replace(/<[^>]*>/g, '\n').trim();
                const summaryLines = pdf.splitTextToSize(summaryText, mainWidth);
                summaryLines.forEach(line => {
                    pdf.text(line, mainX, mainY);
                    mainY += 5;
                });
                mainY += 6;
            }

            // Employment History
            if (cvData.employment_history && cvData.employment_history.length > 0 &&
                cvData.employment_history.some(e => e.job_title || e.company)) {

                // Helper function to add section title
                const addExperienceTitle = () => {
                    pdf.setFont(fontFamily, 'bold');
                    pdf.setFontSize(12);
                    pdf.setTextColor(rgb.r, rgb.g, rgb.b);
                    pdf.text('EXPERIENCE', mainX, mainY);
                    pdf.setLineWidth(0.5);
                    pdf.line(mainX, mainY + 2, mainX + mainWidth, mainY + 2);
                    mainY += 9;
                };

                addExperienceTitle(); // First time

                cvData.employment_history.forEach(emp => {
                    if ((emp.job_title || emp.company)) {
                        // Job Title
                        if (mainY > 270) {
                            pdf.addPage();
                            pdf.setFillColor(rgb.r, rgb.g, rgb.b);
                            pdf.rect(0, 0, 63, 297, 'F');
                            mainY = 20;
                            addExperienceTitle();
                        }

                        pdf.setFont(fontFamily, 'bold');
                        pdf.setFontSize(11);
                        pdf.setTextColor(34, 34, 34);
                        pdf.text(emp.job_title || 'Position', mainX, mainY);
                        mainY += 5;

                        // Company & City
                        if (mainY > 280) {
                            pdf.addPage();
                            pdf.setFillColor(rgb.r, rgb.g, rgb.b);
                            pdf.rect(0, 0, 63, 297, 'F');
                            mainY = 20;
                            addExperienceTitle();
                        }

                        pdf.setFont(fontFamily, 'normal');
                        pdf.setFontSize(10);
                        pdf.setTextColor(102, 102, 102);
                        let companyText = emp.company || 'Company';
                        if (emp.city) companyText += ` • ${emp.city}`;
                        pdf.text(companyText, mainX, mainY);
                        mainY += 5;

                        // Dates
                        if (mainY > 280) {
                            pdf.addPage();
                            pdf.setFillColor(rgb.r, rgb.g, rgb.b);
                            pdf.rect(0, 0, 63, 297, 'F');
                            mainY = 20;
                            addExperienceTitle();
                        }

                        pdf.setFontSize(9);
                        pdf.setTextColor(136, 136, 136);
                        const startDate = emp.start_date ? formatDateForPDF(emp.start_date) : '';
                        const endDate = emp.end_date ? formatDateForPDF(emp.end_date) : 'Present';
                        if (startDate || endDate) {
                            pdf.text(`${startDate} - ${endDate}`, mainX, mainY);
                            mainY += 5;
                        }

                        // Description - THIS IS THE KEY PART
                        if (emp.description) {
                            if (mainY > 280) {
                                pdf.addPage();
                                pdf.setFillColor(rgb.r, rgb.g, rgb.b);
                                pdf.rect(0, 0, 63, 297, 'F');
                                mainY = 20;
                                addExperienceTitle();
                            }

                            pdf.setFont(fontFamily, 'normal');
                            pdf.setFontSize(9);
                            pdf.setTextColor(68, 68, 68);

                            const descText = emp.description
                                .replace(/<\/p>/g, '\n')
                                .replace(/<br\s*\/?>/g, '\n')
                                .replace(/<\/li>/g, '\n')
                                .replace(/<[^>]*>/g, '')
                                .replace(/&nbsp;/g, ' ')
                                .trim();

                            const descLines = pdf.splitTextToSize(descText, mainWidth);
                            descLines.forEach(line => {
                                if (mainY > 280) {
                                    pdf.addPage();
                                    pdf.setFillColor(rgb.r, rgb.g, rgb.b);
                                    pdf.rect(0, 0, 63, 297, 'F');
                                    mainY = 20;
                                    addExperienceTitle();

                                    // Keep description formatting after page break
                                    pdf.setFont(fontFamily, 'normal');
                                    pdf.setFontSize(9);
                                    pdf.setTextColor(68, 68, 68);
                                }
                                pdf.text(line, mainX, mainY);
                                mainY += 4.5;
                            });
                        }

                        mainY += 6;
                    }
                });

                mainY += 3;
            }

            // Education
            if (cvData.education && cvData.education.length > 0 &&
                cvData.education.some(e => e.school || e.degree)) {

                // Check if need new page before starting
                if (mainY > 250) {
                    pdf.addPage();
                    pdf.setFillColor(rgb.r, rgb.g, rgb.b);
                    pdf.rect(0, 0, 63, 297, 'F');
                    mainY = 20;
                }

                // Helper function to add section title
                const addEducationTitle = () => {
                    pdf.setFont(fontFamily, 'bold');
                    pdf.setFontSize(12);
                    pdf.setTextColor(rgb.r, rgb.g, rgb.b);
                    pdf.text('EDUCATION', mainX, mainY);
                    pdf.setLineWidth(0.5);
                    pdf.line(mainX, mainY + 2, mainX + mainWidth, mainY + 2);
                    mainY += 9;
                };

                addEducationTitle(); // First time

                cvData.education.forEach(edu => {
                    if ((edu.school || edu.degree) && mainY < 270) {
                        // Check if need new page BEFORE adding content
                        if (mainY > 250) {
                            pdf.addPage();
                            pdf.setFillColor(rgb.r, rgb.g, rgb.b);
                            pdf.rect(0, 0, 63, 297, 'F');
                            mainY = 20;
                            addEducationTitle(); // Add title again
                        }

                        pdf.setFont(fontFamily, 'bold');
                        pdf.setFontSize(11);
                        pdf.setTextColor(34, 34, 34);
                        pdf.text(edu.degree || 'Degree', mainX, mainY);
                        mainY += 5;

                        pdf.setFont(fontFamily, 'normal');
                        pdf.setFontSize(10);
                        pdf.setTextColor(102, 102, 102);
                        let schoolText = edu.school || 'School';
                        if (edu.city) schoolText += ` • ${edu.city}`;
                        pdf.text(schoolText, mainX, mainY);
                        mainY += 5;

                        pdf.setFontSize(9);
                        pdf.setTextColor(136, 136, 136);
                        const startDate = edu.start_date ? formatDateForPDF(edu.start_date) : '';
                        const endDate = edu.end_date ? formatDateForPDF(edu.end_date) : '';
                        if (startDate || endDate) {
                            pdf.text(`${startDate} - ${endDate}`, mainX, mainY);
                            mainY += 5;
                        }

                        if (edu.description) {
                            pdf.setFont(fontFamily, 'normal');
                            pdf.setFontSize(9);
                            pdf.setTextColor(68, 68, 68);

                            const descText = edu.description
                                .replace(/<\/p>/g, '\n')
                                .replace(/<br\s*\/?>/g, '\n')
                                .replace(/<\/li>/g, '\n')
                                .replace(/<[^>]*>/g, '')
                                .replace(/&nbsp;/g, ' ')
                                .trim();

                            const descLines = pdf.splitTextToSize(descText, mainWidth);
                            descLines.forEach(line => {
                                if (mainY > 280) {
                                    pdf.addPage();
                                    pdf.setFillColor(rgb.r, rgb.g, rgb.b);
                                    pdf.rect(0, 0, 63, 297, 'F');
                                    mainY = 20;
                                    addEducationTitle(); // Add title on overflow
                                }
                                pdf.text(line, mainX, mainY);
                                mainY += 4.5;
                            });
                        }

                        mainY += 6;
                    }
                });

                mainY += 3;
            }

            // Courses
            if (cvData.additional_sections.courses && cvData.additional_sections.courses.length > 0 &&
                cvData.additional_sections.courses.some(c => c.course || c.institution)) {

                if (mainY > 250) {
                    pdf.addPage();
                    pdf.setFillColor(rgb.r, rgb.g, rgb.b);
                    pdf.rect(0, 0, 63, 297, 'F');
                    mainY = 20;
                }

                const addCoursesTitle = () => {
                    pdf.setFont(fontFamily, 'bold');
                    pdf.setFontSize(12);
                    pdf.setTextColor(rgb.r, rgb.g, rgb.b);
                    pdf.text('COURSES', mainX, mainY);
                    pdf.setLineWidth(0.5);
                    pdf.line(mainX, mainY + 2, mainX + mainWidth, mainY + 2);
                    mainY += 9;
                };

                addCoursesTitle();

                cvData.additional_sections.courses.forEach(course => {
                    if ((course.course || course.institution) && mainY < 270) {
                        if (mainY > 260) {
                            pdf.addPage();
                            pdf.setFillColor(rgb.r, rgb.g, rgb.b);
                            pdf.rect(0, 0, 63, 297, 'F');
                            mainY = 20;
                            addCoursesTitle();
                        }

                        pdf.setFont(fontFamily, 'bold');
                        pdf.setFontSize(10);
                        pdf.setTextColor(34, 34, 34);
                        pdf.text(course.course || 'Course', mainX, mainY);
                        mainY += 5;

                        if (course.institution) {
                            pdf.setFont(fontFamily, 'normal');
                            pdf.setFontSize(9);
                            pdf.setTextColor(102, 102, 102);
                            pdf.text(course.institution, mainX, mainY);
                            mainY += 4;
                        }

                        const startDate = course.start_date ? formatDateForPDF(course.start_date) : '';
                        const endDate = course.end_date ? formatDateForPDF(course.end_date) : '';
                        if (startDate || endDate) {
                            pdf.setFontSize(8);
                            pdf.setTextColor(136, 136, 136);
                            pdf.text(`${startDate} - ${endDate}`, mainX, mainY);
                            mainY += 5;
                        }

                        mainY += 3;
                    }
                });

                mainY += 3;
            }

            // Hobbies
            if (cvData.additional_sections.hobbies && cvData.additional_sections.hobbies.trim()) {
                if (mainY > 260) {
                    pdf.addPage();
                    pdf.setFillColor(rgb.r, rgb.g, rgb.b);
                    pdf.rect(0, 0, 63, 297, 'F');
                    mainY = 20;
                }

                pdf.setFont(fontFamily, 'bold');
                pdf.setFontSize(12);
                pdf.setTextColor(rgb.r, rgb.g, rgb.b);
                pdf.text('HOBBIES', mainX, mainY);
                pdf.setLineWidth(0.5);
                pdf.line(mainX, mainY + 2, mainX + mainWidth, mainY + 2);
                mainY += 9;

                pdf.setFont(fontFamily, 'normal');
                pdf.setFontSize(9);
                pdf.setTextColor(68, 68, 68);
                const hobbiesLines = pdf.splitTextToSize(cvData.additional_sections.hobbies, mainWidth);
                hobbiesLines.forEach(line => {
                    pdf.text(line, mainX, mainY);
                    mainY += 4.5;
                });
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

    function formatDateForPDF(dateString) {
        if (!dateString) return '';
        const date = new Date(dateString + '-01');
        return date.toLocaleDateString('en-US', { month: 'short', year: 'numeric' });
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
