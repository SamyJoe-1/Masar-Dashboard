// API Integration for Interview Form
// Add this to your main.js or create a new api.js file

// Get interview ID from the form or URL
function getInterviewId() {
    const slugInput = document.querySelector('input[name="interview_slug"]');
    return slugInput ? slugInput.value : null;
}

function showRecordingAlert() {
    const alert = document.getElementById('recordingAlert');
    alert.style.display = 'block';

    setTimeout(() => {
        alert.classList.add('show');
    }, 100);
}

function hideRecordingAlert() {
    const alert = document.getElementById('recordingAlert');
    alert.classList.remove('show');

    setTimeout(() => {
        alert.style.display = 'none';
    }, 300);
}

// API call to check interview status
async function checkInterviewStatus() {
    const interviewId = getInterviewId();
    if (!interviewId) {
        console.error('Interview ID not found');
        return;
    }

    try {
        const response = await fetch(`/api/session/check/${interviewId}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });

        const result = await response.json();

        switch(result.status) {
            case 200: // Ready to start
                console.log('Interview ready to start');
                // Show welcome card if not already shown
                if (document.getElementById('formContainer').style.display === 'block') {
                    // User was already in session, continue where they left off
                    continueSession(result.data);
                }
                break;

            case 401: // Already active
                console.log('Interview already active, continuing session');
                continueSession(result.data);
                break;

            case 403: // Expired
                console.log('Interview expired');
                showExpiredSession();
                break;

            case 404: // Not found
                console.log('Interview not found');
                showInterviewNotFound();
                break;

            default:
                console.log('Unknown status:', result.status);
        }
    } catch (error) {
        console.error('Error checking interview status:', error);
    }
}

// API call to start interview
async function startInterviewSession() {
    const interviewId = getInterviewId();
    if (!interviewId) {
        console.error('Interview ID not found');
        return false;
    }

    try {
        const response = await fetch(`/api/session/start/${interviewId}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        console.log(response)

        const result = await response.json();

        if (result.status === 200) {
            console.log('Interview session started successfully');
            return true;
        } else {
            console.error('Failed to start interview session:', result.message);
            swal({
                title: getTranslatedMessage('error'),
                text: result.message,
                icon: "error",
                button: "OK"
            });
            return false;
        }
    } catch (error) {
        console.error('Error starting interview session:', error);
        swal({
            title: getTranslatedMessage('error'),
            text: getTranslatedMessage('failed-initialize'),
            icon: "error",
            button: "OK"
        });
        return false;
    }
}

// API call to finish interview
async function finishInterviewSession() {
    const interviewId = getInterviewId();
    if (!interviewId) {
        console.error('Interview ID not found');
        return false;
    }

    try {
        // Collect all inputs with class "fucker"
        const QAS = document.querySelectorAll('.answer-textarea');

        let answers = {};

        QAS.forEach((input, index) => {
            // Use the actual question text as key (from the name attribute)
            let key = input.name; // This will be the urlencode($question) value
            answers[key] = input.value;
        });

        console.log(answers);

        // answer-textarea
        const response = await fetch(`/api/session/finish/${interviewId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(answers) // answers = collected q1..qN
        });


        const result = await response.json();

        if (result.status === 200) {
            console.log(result)
            console.log('Interview session finished successfully');
            return true;
        } else {
            console.error('Failed to finish interview session:', result.message);
            return false;
        }
    } catch (error) {
        console.error('Error finishing interview session:', error);
        return false;
    }
}

// Request fake screen permission (the missing function)
async function requestFakeScreenPermission() {
    return new Promise((resolve) => {
        swal({
            title: getTranslatedMessage('screen-recording-required'),
            text: getTranslatedMessage('screen-recording-text'),
            icon: "warning",
            buttons: {
                cancel: {
                    text: getTranslatedMessage('cancel'),
                    value: false,
                    visible: true,
                    className: "swal-button--cancel"
                },
                confirm: {
                    text: getTranslatedMessage('allow-screen-recording'),
                    value: true,
                    visible: true,
                    className: "swal-button--confirm"
                }
            },
            dangerMode: false,
            closeOnClickOutside: false,
            closeOnEsc: false
        }).then((willAllow) => {
            if (willAllow) {
                resolve(true);
            } else {
                swal({
                    title: getTranslatedMessage('permission-denied'),
                    text: getTranslatedMessage('permission-required'),
                    icon: "error",
                    button: "OK"
                });
                resolve(false);
            }
        });
    });
}

// Continue existing session
// Continue existing session
// Continue existing session
function continueSession(sessionData) {
    document.getElementById('welcomeCard').style.display = 'none';
    document.getElementById('formContainer').style.display = 'block';

    // Calculate remaining time based on started_at
    if (sessionData.started_at) {
        const startTime = new Date(sessionData.started_at);
        const currentTime = new Date();
        const elapsedSeconds = Math.floor((currentTime - startTime) / 1000);
        timeRemaining = Math.max(1800 - elapsedSeconds, 0); // 30 minutes minus elapsed time

        if (timeRemaining <= 0) {
            expireSession();
            return;
        }
    }

    // Make sure timer functions exist before calling
    if (typeof startTimer === 'function') {
        startTimer();
    } else {
        console.error('startTimer function not found');
    }

    if (typeof initializeValidation === 'function') {
        initializeValidation();
    }

    showRecordingAlert();
}

// Show expired session overlay
function showExpiredSession() {
    clearInterval(sessionTimer);
    clearInterval(statusCheckInterval);
    hideRecordingAlert();
    document.getElementById('expiredOverlay').style.display = 'flex';
}

// Show interview not found message
function showInterviewNotFound() {
    swal({
        title: getTranslatedMessage('error'),
        text: 'Interview not found or invalid.',
        icon: "error",
        button: "OK"
    });
}

// Fixed startSession function with proper flow
async function startSession() {
    // First, request screen permission
    const screenPermissionGranted = await requestFakeScreenPermission();
    if (!screenPermissionGranted) {
        return; // User denied permission, stop here
    }

    // Show loading state
    const startButton = document.querySelector('.start-button');
    const originalText = startButton.textContent;
    startButton.disabled = true;
    startButton.textContent = getTranslatedMessage('initializing') || 'Initializing...';

    try {
        // Start the API session
        const sessionStarted = await startInterviewSession();
        if (!sessionStarted) {
            // Reset button state on failure
            startButton.disabled = false;
            startButton.textContent = originalText;
            return;
        }

        // Success - hide welcome card and show form
        document.getElementById('welcomeCard').style.display = 'none';
        document.getElementById('formContainer').style.display = 'block';

        // Initialize the interview components
        startTimer();

        // Wait a bit to ensure all scripts are loaded before initializing validation
        setTimeout(() => {
            initializeValidation();
        }, 100);

        showRecordingAlert();

    } catch (error) {
        console.error('Error starting session:', error);
        // Reset button state on error
        startButton.disabled = false;
        startButton.textContent = originalText;

        swal({
            title: getTranslatedMessage('error') || 'Error',
            text: getTranslatedMessage('failed-initialize') || 'Failed to initialize interview session.',
            icon: "error",
            button: "OK"
        });
    }
}

// Status check interval
let statusCheckInterval;

// Start periodic status checks (every 5 minutes)
function startStatusChecks() {
    statusCheckInterval = setInterval(checkInterviewStatus, 5 * 60 * 1000); // 5 minutes
}

// Stop status checks
function stopStatusChecks() {
    if (statusCheckInterval) {
        clearInterval(statusCheckInterval);
        statusCheckInterval = null;
    }
}

// Modified form submission handler
document.addEventListener('DOMContentLoaded', function() {
    // Initial status check when page loads
    checkInterviewStatus();

    // Start periodic status checks
    startStatusChecks();
});

// Clean up intervals when page unloads
let allowRedirect = false;

window.addEventListener('beforeunload', (e) => {
    stopStatusChecks();

    if (sessionTimer && !allowRedirect) {
        const message = getTranslatedMessage('leave-warning');
        e.preventDefault();
        e.returnValue = message;
        return message;
    }
});

// Before submit/redirect
document.querySelector("form").addEventListener("submit", function () {
    allowRedirect = true;
});

