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

async function improveWithAI(section, index) {
    // Get the button that was clicked
    const button = event.target.closest('.ai-improve-btn');
    const originalHTML = button.innerHTML;

    // Disable button and show loading
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Improving...';

    try {
        // Collect ALL current CV data
        collectData();

        // Get the specific content for this section
        let currentContent = '';
        if (section === 'summary') {
            currentContent = summaryEditor ? summaryEditor.getText().trim() : '';
        } else if (section === 'employment') {
            currentContent = employmentEditors[index] ? employmentEditors[index].getText().trim() : '';
        } else if (section === 'education') {
            currentContent = educationEditors[index] ? educationEditors[index].getText().trim() : '';
        }

        // 🔥 MAP section names to what your API expects
        const sectionMapping = {
            'summary': 'summary',
            'employment': 'experience',  // 👈 API might expect "experience" not "employment"
            'education': 'education'
        };

        // Prepare CV data in the format your API expects
        const cvFormatted = {
            name: `${cvData.personal_details.first_name || ''} ${cvData.personal_details.last_name || ''}`.trim() || 'N/A',
            title: cvData.personal_details.job_title || 'N/A',
            location: cvData.personal_details.city_state && cvData.personal_details.country
                ? `${cvData.personal_details.city_state}, ${cvData.personal_details.country}`
                : 'N/A',
            email: cvData.personal_details.email || 'N/A',
            phone: cvData.personal_details.phone || 'N/A',
            links: cvData.personal_details.linkedin || 'N/A',
            skills: cvData.skills && cvData.skills.length > 0
                ? cvData.skills.map(s => s.skill).filter(Boolean)
                : ['N/A']
        };

        // Prepare the payload matching your API structure
        const payload = {
            section: sectionMapping[section] || section,  // 👈 Use mapped section name
            content: currentContent,
            job_description: cvData.personal_details.job_title || '',
            target_role: cvData.personal_details.job_title || '',
            language: 'en',
            style: 'ats',
            cv: cvFormatted,
            max_items: 8,
            sort_bullets_by_impact: true,
            return_mode: 'raw',
            group_skills: false,
            force_llm: false
        };

        console.log('📤 Sending payload:', payload);  // 👈 Debug log

        // Send request to your API
        const response = await fetch(`${window.api_uri}/v1/improve`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify(payload)
        });

        const result = await response.json();

        console.log('📥 API Response:', result);  // 👈 Debug log

        if (!response.ok) {
            console.error('❌ API Error:', result);  // 👈 Debug log
            throw new Error(result.message || result.error || 'Failed to improve content');
        }

        // Success - update the editor with improved content
        if (result.improved) {
            if (section === 'summary' && summaryEditor) {
                summaryEditor.root.innerHTML = result.improved;
            } else if (section === 'employment' && employmentEditors[index]) {
                employmentEditors[index].root.innerHTML = result.improved;
            } else if (section === 'education' && educationEditors[index]) {
                educationEditors[index].root.innerHTML = result.improved;
            }

            // Re-render preview
            renderPreview();
            updateProgress();
        }

        showNotification(__('content_improved_successfully'), 'success');

    } catch (error) {
        console.error('Error improving with AI:', error);
        showNotification(__('failed_to_improve_content') + ': ' + error.message, 'error');
    } finally {
        // Re-enable button and restore original text
        button.disabled = false;
        button.innerHTML = originalHTML;
    }
}

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

function toggleAdditionalDetails() {
    const details = document.getElementById('additionalDetails');
    details.style.display = details.style.display === 'none' ? 'block' : 'none';
}

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
