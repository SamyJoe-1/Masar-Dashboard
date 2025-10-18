// Run on load and resize
window.addEventListener('load', scaleCV);
window.addEventListener('resize', scaleCV);

// Also run after rendering
const originalRenderPreview = window.renderPreview;
window.renderPreview = function() {
    if (originalRenderPreview) originalRenderPreview();
    setTimeout(scaleCV, 100);
};

// ==================== INITIALIZATION ====================
document.addEventListener('DOMContentLoaded', function() {
    loadTemplates();
    setupAutoSaveInterval();
});

function scaleCV() {
    const container = document.querySelector('.builder-right');
    const cvContainer = document.getElementById('cvPreviewContainer');

    if (!container || !cvContainer) return;

    const containerWidth = container.clientWidth;
    const containerHeight = container.clientHeight;

    const cvWidth = 794;
    const cvHeight = 1123;

    const margin = 0.9;
    const scaleX = (containerWidth * margin) / cvWidth;
    const scaleY = (containerHeight * margin) / cvHeight;

    const scale = Math.min(scaleX, scaleY);

    cvContainer.style.transform = `scale(${scale})`;
}

// A4 Configuration
const A4_WIDTH = 794;
const A4_HEIGHT = 1123;
const A4_RATIO = 210 / 297;
let currentPreviewPage = 0;

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
        showNotification(__('failed_to_load_templates'), 'error');
    }
}

async function confirmTemplate() {
    if (!selectedTemplate) {
        showNotification(__('template_selection_required'), 'error');
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
                title: __('unfinished_draft_found'),
                text: __('unfinished_draft_text'),
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: __('continue'),
                cancelButtonText: __('start_fresh'),
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
                            title: __('saved_draft_found'),
                            text: __('saved_draft_text'),
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonText: __('continue'),
                            cancelButtonText: __('start_fresh'),
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
        showNotification(__('failed_to_load_template'), 'error');
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

function checkAuth() {
    const authToken = window.authToken || localStorage.getItem('auth_token');
    if (!authToken) {
        alert(__('please_login'));
        window.location.href = '/login';
        return false;
    }
    return true;
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
    alert(__('please_login'));
    window.location.href = '/login';
    return '';
}

function generateUUID() {
    return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
        const r = Math.random() * 16 | 0;
        const v = c === 'x' ? r : (r & 0x3 | 0x8);
        return v.toString(16);
    });
}

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
