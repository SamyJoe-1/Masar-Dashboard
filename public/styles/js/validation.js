// Form validation functionality with translation support
function validateForm() {
    // Safety check - make sure questions variable exists
    if (typeof questions === 'undefined' || !questions) {
        console.warn('Questions array not yet loaded, skipping validation');
        return false;
    }

    let isValid = true;
    let allFilled = true;

    questions.forEach(questionName => {
        const textarea = document.querySelector(`textarea[name="${questionName}"]`);
        const errorDiv = document.getElementById(`error-${questionName}`);

        if (!textarea || !errorDiv) {
            console.warn(`Elements not found for question: ${questionName}`);
            return;
        }

        const value = textarea.value.trim();

        // Clear previous error state
        textarea.classList.remove('error');
        errorDiv.style.display = 'none';

        if (value === '') {
            allFilled = false;
            if (textarea.hasAttribute('data-touched')) {
                textarea.classList.add('error');
                errorDiv.textContent = getTranslatedMessage('field-required');
                errorDiv.style.display = 'block';
                isValid = false;
            }
        } else if (value.length < 5) {
            textarea.classList.add('error');
            errorDiv.textContent = getTranslatedMessage('detailed-answer');
            errorDiv.style.display = 'block';
            isValid = false;
            allFilled = false;
        }
    });

    // Enable/disable submit button based on all fields being filled
    const submitButton = document.getElementById('submitBtn');
    if (submitButton) {
        submitButton.disabled = !allFilled;
    }

    return isValid && allFilled;
}

function initializeValidation() {
    // Safety check - make sure questions variable exists
    if (typeof questions === 'undefined' || !questions) {
        console.warn('Questions array not yet loaded, retrying in 200ms...');
        setTimeout(initializeValidation, 200);
        return;
    }

    const textareas = document.querySelectorAll('.answer-textarea');

    textareas.forEach(textarea => {
        // Mark field as touched when user starts typing
        textarea.addEventListener('input', function() {
            this.setAttribute('data-touched', 'true');
            validateForm();
        });

        // Also mark as touched on blur
        textarea.addEventListener('blur', function() {
            this.setAttribute('data-touched', 'true');
            validateForm();
        });
    });

    // Initial validation check
    validateForm();
}
