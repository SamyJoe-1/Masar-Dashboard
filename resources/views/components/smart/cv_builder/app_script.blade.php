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
            Swal.fire({
                title: 'Authentication Required',
                text: 'Please log in to save your CV',
                icon: 'warning',
                confirmButtonColor: '#2563eb'
            }).then(() => {
                window.location.href = '/login';
            });
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

    function initializeBuilder() {
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
    }

    // ALSO REPLACE THE confirmTemplate() function entirely with this:

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

        // If no token, return empty string (don't redirect here, let the caller handle it)
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
        console.log('START renderPreview');
        collectData();
        console.log('AFTER collectData');

        const container = document.getElementById('cvPreviewContainer');
        console.log('GOT container');
        container.innerHTML = '';
        console.log('CLEARED container');

        const primaryColor = cvData.customize.color;
        const fontFamily = cvData.customize.font_family;
        const fontSize = cvData.customize.font_size;
        const lineHeight = cvData.customize.spacing;
        console.log('GOT customize values');

        const page = createA4Page(primaryColor, fontFamily, fontSize, lineHeight);
        console.log('CREATED page');
        container.appendChild(page);
        console.log('APPENDED page');

        const sidebar = page.querySelector('.cv-sidebar');
        const main = page.querySelector('.cv-main');
        console.log('GOT sidebar and main');

        renderSidebar(sidebar, primaryColor);
        console.log('AFTER renderSidebar');

        renderMainContent(main, primaryColor);
        console.log('AFTER renderMainContent');

        handlePageOverflow(container, primaryColor, fontFamily, fontSize, lineHeight);
        console.log('AFTER handlePageOverflow - DONE');
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
        console.log('renderSidebar() - personal_details:', cvData.personal_details);

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
        overflow: hidden;
        flex-shrink: 0;
    `;

        if (pd.avatar) {
            avatar.innerHTML = `<img src="${pd.avatar}" style="width: 100%; height: 100%; object-fit: cover;">`;
        } else {
            avatar.innerHTML = `<i class="fas fa-user"></i>`;
        }

        sidebar.appendChild(avatar);
        console.log('Avatar added');

        // NAME
        const name = document.createElement('div');
        name.className = 'cv-name';
        name.style.cssText = 'font-size: 1.5rem; font-weight: 700; margin-bottom: 5px; text-align: center; color: white;';
        name.textContent = `${pd.first_name || ''} ${pd.last_name || ''}`.trim() || 'Your Name';
        sidebar.appendChild(name);
        console.log('Name added:', name.textContent);

        // JOB TITLE
        const jobTitle = document.createElement('div');
        jobTitle.className = 'cv-job-title';
        jobTitle.style.cssText = 'text-align: center; opacity: 0.9; margin-bottom: 30px; font-size: 0.95rem; color: white;';
        jobTitle.textContent = pd.job_title || 'Your Job Title';
        sidebar.appendChild(jobTitle);
        console.log('Job title added:', jobTitle.textContent);

        // CONTACT SECTION
        if (pd.email || pd.phone || pd.city_state) {
            const contactSection = document.createElement('div');
            contactSection.className = 'cv-section';
            contactSection.style.cssText = 'margin-bottom: 25px;';

            const contactTitle = document.createElement('div');
            contactTitle.className = 'cv-section-title';
            contactTitle.style.cssText = 'font-size: 1rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 15px; padding-bottom: 8px; border-bottom: 2px solid rgba(255,255,255,0.3); color: white;';
            contactTitle.textContent = 'CONTACT';
            contactSection.appendChild(contactTitle);

            if (pd.email) {
                const emailDiv = document.createElement('div');
                emailDiv.style.cssText = 'margin-bottom: 10px; font-size: 0.85rem; color: white;';
                emailDiv.innerHTML = `<i class="fas fa-envelope" style="width: 20px;"></i> ${pd.email}`;
                contactSection.appendChild(emailDiv);
            }

            if (pd.phone) {
                const phoneDiv = document.createElement('div');
                phoneDiv.style.cssText = 'margin-bottom: 10px; font-size: 0.85rem; color: white;';
                phoneDiv.innerHTML = `<i class="fas fa-phone" style="width: 20px;"></i> ${pd.phone}`;
                contactSection.appendChild(phoneDiv);
            }

            if (pd.city_state && pd.country) {
                const addressDiv = document.createElement('div');
                addressDiv.style.cssText = 'margin-bottom: 10px; font-size: 0.85rem; color: white;';
                addressDiv.innerHTML = `<i class="fas fa-map-marker-alt" style="width: 20px;"></i> ${pd.city_state}, ${pd.country}`;
                contactSection.appendChild(addressDiv);
            }

            sidebar.appendChild(contactSection);
            console.log('Contact section added');
        }

        // SKILLS SECTION
        if (cvData.skills && cvData.skills.length > 0) {
            const skillsSection = document.createElement('div');
            skillsSection.className = 'cv-section';
            skillsSection.style.cssText = 'margin-bottom: 25px;';

            const skillsTitle = document.createElement('div');
            skillsTitle.className = 'cv-section-title';
            skillsTitle.style.cssText = 'font-size: 1rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 15px; padding-bottom: 8px; border-bottom: 2px solid rgba(255,255,255,0.3); color: white;';
            skillsTitle.textContent = 'SKILLS';
            skillsSection.appendChild(skillsTitle);

            const skillsContainer = document.createElement('div');

            cvData.skills.forEach(skill => {
                if (skill.skill) {
                    const skillDiv = document.createElement('div');
                    skillDiv.style.cssText = 'margin-bottom: 12px;';
                    skillDiv.innerHTML = `
                    <div style="font-size: 0.9rem; margin-bottom: 3px; color: white;">${skill.skill}</div>
                    <div style="font-size: 0.75rem; opacity: 0.8; color: white;">${skill.level || 'Experienced'}</div>
                `;
                    skillsContainer.appendChild(skillDiv);
                }
            });

            skillsSection.appendChild(skillsContainer);
            sidebar.appendChild(skillsSection);
            console.log('Skills section added');
        }

        // LANGUAGES SECTION
        if (cvData.additional_sections.languages && cvData.additional_sections.languages.length > 0) {
            const langSection = document.createElement('div');
            langSection.className = 'cv-section';
            langSection.style.cssText = 'margin-bottom: 25px;';

            const langTitle = document.createElement('div');
            langTitle.className = 'cv-section-title';
            langTitle.style.cssText = 'font-size: 1rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 15px; padding-bottom: 8px; border-bottom: 2px solid rgba(255,255,255,0.3); color: white;';
            langTitle.textContent = 'LANGUAGES';
            langSection.appendChild(langTitle);

            const langContainer = document.createElement('div');

            cvData.additional_sections.languages.forEach(lang => {
                if (lang.language && lang.level && lang.level !== 'Select level') {
                    const langDiv = document.createElement('div');
                    langDiv.style.cssText = 'margin-bottom: 12px;';
                    langDiv.innerHTML = `
                    <div style="font-size: 0.9rem; margin-bottom: 3px; color: white;">${lang.language}</div>
                    <div style="font-size: 0.75rem; opacity: 0.8; color: white;">${lang.level}</div>
                `;
                    langContainer.appendChild(langDiv);
                }
            });

            langSection.appendChild(langContainer);
            sidebar.appendChild(langSection);
            console.log('Languages section added');
        }

        console.log('renderSidebar() completed');
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

    function addPageNavigation(container) {
        const pages = container.querySelectorAll('.cv-page');
        if (pages.length <= 1) return;

        const navContainer = document.createElement('div');
        navContainer.id = 'pageNavigation';
        navContainer.style.cssText = `
        margin-top: 1.5rem;
        display: flex;
        justify-content: center;
        gap: 0.5rem;
    `;

        pages.forEach((page, i) => {
            page.style.display = i === 0 ? 'flex' : 'none';

            const dot = document.createElement('div');
            dot.className = `page-dot ${i === 0 ? 'active' : ''}`;
            dot.style.cssText = `
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: ${i === 0 ? '#2563eb' : '#e2e8f0'};
            cursor: pointer;
            transition: all 0.2s ease;
            ${i === 0 ? 'width: 30px; border-radius: 5px;' : ''}
        `;

            dot.onclick = () => {
                pages.forEach((p, idx) => {
                    p.style.display = idx === i ? 'flex' : 'none';
                });

                document.querySelectorAll('.page-dot').forEach((d, idx) => {
                    if (idx === i) {
                        d.style.background = '#2563eb';
                        d.style.width = '30px';
                        d.style.borderRadius = '5px';
                    } else {
                        d.style.background = '#e2e8f0';
                        d.style.width = '10px';
                        d.style.borderRadius = '50%';
                    }
                });
            };

            navContainer.appendChild(dot);
        });

        container.parentElement.appendChild(navContainer);
    }

    function removePageNavigation() {
        const oldNav = document.getElementById('pageNavigation');
        if (oldNav) oldNav.remove();
    }


    function handlePageOverflow(container, color, font, size, spacing) {
        console.log('handlePageOverflow() checking for content overflow');

        let pages = container.querySelectorAll('.cv-page');

        // Process each page to see if it overflows
        let pageIndex = 0;

        while (pageIndex < pages.length) {
            const currentPage = pages[pageIndex];
            const mainContent = currentPage.querySelector('.cv-main');

            // Get the actual scrollHeight (content height)
            const contentHeight = mainContent.scrollHeight;
            const maxHeight = 1000; // Leave room for padding

            console.log(`Page ${pageIndex + 1} - contentHeight: ${contentHeight}, maxHeight: ${maxHeight}`);

            // If content doesn't overflow, move to next page
            if (contentHeight <= maxHeight) {
                pageIndex++;
                continue;
            }

            // Content overflows - we need to move overflow to a new page
            console.log(`Page ${pageIndex + 1} overflows by ${contentHeight - maxHeight}px, creating new page`);

            const overflowContainer = document.createElement('div');
            let movedElements = 0;

            // Move sections one by one until it fits
            while (mainContent.scrollHeight > maxHeight && mainContent.children.length > 0) {
                const lastChild = mainContent.lastChild;
                overflowContainer.insertBefore(lastChild, overflowContainer.firstChild);
                movedElements++;

                // Limit to prevent issues
                if (movedElements > 50) {
                    console.warn('Moved 50 elements, stopping to prevent issues');
                    break;
                }
            }

            console.log(`Moved ${movedElements} elements to overflow container`);

            // If we have overflow content, create a new page for it
            if (overflowContainer.children.length > 0) {
                const newPage = createA4Page(color, font, size, spacing);
                const newPageIndex = pageIndex + 1;

                // Insert new page after current page
                if (newPageIndex < container.children.length) {
                    container.insertBefore(newPage, container.children[newPageIndex]);
                } else {
                    container.appendChild(newPage);
                }

                const newMain = newPage.querySelector('.cv-main');

                // Move overflow content to new page
                while (overflowContainer.children.length > 0) {
                    newMain.appendChild(overflowContainer.firstChild);
                }

                // Refresh pages query since we added a new page
                pages = container.querySelectorAll('.cv-page');
                console.log(`Created new page, total pages now: ${pages.length}`);
            }

            pageIndex++;
        }

        console.log(`handlePageOverflow() completed, total pages: ${container.querySelectorAll('.cv-page').length}`);

        // Add page navigation
        removePageNavigation();
        addPageNavigation(container);
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
            nationality: document.getElementById('nationality')?.value || '',
            avatar: cvData.personal_details?.avatar || ''

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
            if (pd.avatar && document.getElementById('avatarPreview')) {
                document.getElementById('avatarPreview').innerHTML = `<img src="${pd.avatar}" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">`;
            }
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

    function handleAvatarUpload(event) {
        const file = event.target.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = (e) => {
            const base64 = e.target.result;
            cvData.personal_details.avatar = base64;

            const preview = document.getElementById('avatarPreview');
            if (preview) {
                preview.innerHTML = `<img src="${base64}" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">`;
            }

            renderPreview();
        };

        reader.readAsDataURL(file);
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
            Swal.fire({
                title: 'Missing Information',
                text: 'Please fill in your first and last name',
                icon: 'error',
                confirmButtonColor: '#2563eb'
            });
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
            const pages = container.querySelectorAll('.cv-page');

            const pdf = new jsPDF({
                orientation: 'portrait',
                unit: 'mm',
                format: 'a4'
            });

            for (let i = 0; i < pages.length; i++) {
                if (i > 0) pdf.addPage();

                const page = pages[i];
                const sidebar = page.querySelector('.cv-sidebar');
                const main = page.querySelector('.cv-main');

                // Sidebar background color
                const sidebarColor = cvData.customize.color;
                const rgb = hexToRgb(sidebarColor);
                pdf.setFillColor(rgb.r, rgb.g, rgb.b);
                pdf.rect(0, 0, 63.5, 297, 'F');

                // Sidebar text (white)
                pdf.setTextColor(255, 255, 255);
                pdf.setFont('Arial', 'bold');
                pdf.setFontSize(14);

                const nameEl = sidebar.querySelector('div');
                if (nameEl) {
                    const nameText = cvData.personal_details.first_name + ' ' + cvData.personal_details.last_name;
                    const lines = pdf.splitTextToSize(nameText, 55);
                    pdf.text(lines, 31.75, 30, { align: 'center' });
                }

                // Main content as text
                pdf.setTextColor(0, 0, 0);
                pdf.setFont('Arial', 'normal');
                pdf.setFontSize(10);

                let yPosition = 20;
                const sections = main.querySelectorAll('.cv-section');

                sections.forEach(section => {
                    const titleEl = section.querySelector('.cv-section-title');
                    if (titleEl && yPosition < 270) {
                        pdf.setFont('Arial', 'bold');
                        pdf.setFontSize(11);
                        pdf.setTextColor(rgb.r, rgb.g, rgb.b);
                        pdf.text(titleEl.textContent.toUpperCase(), 70, yPosition);
                        yPosition += 7;
                    }

                    const text = section.textContent.replace(titleEl?.textContent || '', '').trim();
                    if (text) {
                        pdf.setFont('Arial', 'normal');
                        pdf.setFontSize(9);
                        pdf.setTextColor(0, 0, 0);

                        const lines = pdf.splitTextToSize(text, 130);
                        lines.forEach(line => {
                            if (yPosition > 280) {
                                yPosition = 20;
                            }
                            pdf.text(line, 70, yPosition);
                            yPosition += 4;
                        });
                    }

                    yPosition += 3;
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
        const iconMap = {
            'success': 'success',
            'error': 'error',
            'info': 'info',
            'warning': 'warning'
        };

        Swal.fire({
            toast: true,
            position: 'bottom-end',
            icon: iconMap[type] || 'info',
            title: message,
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
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
