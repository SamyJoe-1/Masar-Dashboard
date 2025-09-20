// validation.js - Fixed version

function validateForm() {
    const activeStep = document.querySelector('.step-container.active');
    if (!activeStep) {
        console.log('No active step found');
        return false;
    }

    const textarea = activeStep.querySelector('.answer-textarea');
    const errorElement = activeStep.querySelector('.validation-error');

    if (!textarea) {
        console.log('No textarea found in active step');
        return false;
    }

    // Clear previous errors
    if (errorElement) {
        errorElement.textContent = '';
        errorElement.style.display = 'none';
    }

    // Check if answer exists (either from voice or manual input)
    const stepIndex = parseInt(activeStep.id.split('-')[1]);
    const hasVoiceAnswer = voiceAnswers[stepIndex] && voiceAnswers[stepIndex].trim();
    const hasTextAnswer = textarea.value && textarea.value.trim();

    if (!hasVoiceAnswer && !hasTextAnswer) {
        if (errorElement) {
            errorElement.textContent = getTranslatedMessage('answer-required') || 'Please provide an answer to continue.';
            errorElement.style.display = 'block';
        }
        return false;
    }

    return true;
}

function validateCurrentStep() {
    return validateForm();
}

function validateAllSteps() {
    // Check if all textareas have content
    const allTextareas = document.querySelectorAll('.answer-textarea');

    for (let i = 0; i < allTextareas.length; i++) {
        const textarea = allTextareas[i];
        const textValue = textarea.value ? textarea.value.trim() : '';

        // Also check voice answers for this step
        const hasVoiceAnswer = voiceAnswers[i] && voiceAnswers[i].trim();

        // If neither textarea nor voice answer has content, validation fails
        if (!textValue && !hasVoiceAnswer) {
            console.log(`Step ${i} is empty - no text or voice answer`);
            return false;
        }
    }

    console.log('All steps validated successfully');
    return true;
}

function initializeValidation() {
    console.log('Initializing validation...');

    // Don't run validation errors on init, just set up the system
    console.log('Validation system ready');

    // Validate when moving between steps
    const nextButton = document.getElementById('nextButton');
    const submitButton = document.getElementById('submitBtn');

    if (nextButton) {
        nextButton.addEventListener('click', function(e) {
            if (!validateCurrentStep()) {
                e.preventDefault();
                e.stopPropagation();
                return false;
            }
        });
    }

    if (submitButton) {
        submitButton.addEventListener('click', function(e) {
            if (!validateAllSteps()) {
                return true;
                e.preventDefault();
                e.stopPropagation();
                swal({
                    title: getTranslatedMessage('error') || 'Error',
                    text: getTranslatedMessage('complete-all-questions') || 'Please complete all questions before submitting.',
                    icon: "error",
                    button: "OK"
                });
                return false;
            }
        });
    }
}

// Helper function for error messages
function showValidationError(stepIndex, message) {
    const step = document.getElementById(`step-${stepIndex}`);
    if (step) {
        const errorElement = step.querySelector('.validation-error');
        if (errorElement) {
            errorElement.textContent = message;
            errorElement.style.display = 'block';
        }
    }
}

function clearValidationError(stepIndex) {
    const step = document.getElementById(`step-${stepIndex}`);
    if (step) {
        const errorElement = step.querySelector('.validation-error');
        if (errorElement) {
            errorElement.textContent = '';
            errorElement.style.display = 'none';
        }
    }
}
