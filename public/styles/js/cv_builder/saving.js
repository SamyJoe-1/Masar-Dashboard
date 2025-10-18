
function setupAutoSaveInterval() {
    setInterval(() => {
        if (cvData.template_id) {
            collectData();
            localStorage.setItem(`cv_draft_template_${cvData.template_id}`, JSON.stringify(cvData));
        }
    }, 30000); // Every 30 seconds
}
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
        showNotification(window.lang('cv_saved_successfully'), 'success');

    } catch (error) {
        console.error('Error saving CV:', error);
        document.getElementById('loadingOverlay').classList.remove('show');
        showNotification(__('failed_to_save_cv') + ': ' + error.message, 'error');
    }
}
async function finishCV() {
    collectData();

    if (!cvData.personal_details.first_name || !cvData.personal_details.last_name) {
        showNotification(__('please_fill_name'), 'error');
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
        showNotification(__('failed_to_finalize_cv') + ': ' + error.message, 'error');
    }
}
function showDownloadModal(cv) {
    Swal.fire({
        title: __('your_cv_is_ready'),
        text: __('what_would_you_like'),
        icon: 'success',
        showCancelButton: true,
        confirmButtonText: __('download_pdf'),
        denyButtonText: __('view_on_profile'),
        cancelButtonText: __('view_all_cvs'),
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
