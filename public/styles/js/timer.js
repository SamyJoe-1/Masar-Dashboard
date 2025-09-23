// Timer functionality with translation support
let sessionTimer = null;
let timeRemaining = 1800; // 30 minutes in seconds

// Add this function after the existing functions (around line 400)
function startTimer() {
    if (sessionTimer) {
        clearInterval(sessionTimer);
    }

    updateTimerDisplay();

    sessionTimer = setInterval(() => {
        timeRemaining--;
        updateTimerDisplay();

        if (timeRemaining <= 0) {
            expireSession();
        }
    }, 1000);
}

function updateTimerDisplay() {
    const minutes = Math.floor(timeRemaining / 60);
    const seconds = timeRemaining % 60;
    const display = `${minutes}:${seconds.toString().padStart(2, '0')}`;

    const timerDisplay = document.getElementById('timerDisplay');
    if (timerDisplay) {
        timerDisplay.textContent = display;
    }

    // Update progress bar
    const progress = ((1800 - timeRemaining) / 1800) * 100;
    const timerProgress = document.getElementById('timerProgress');
    if (timerProgress) {
        timerProgress.style.width = `${progress}%`;
    }

    // Change color when time is running low
    if (timerDisplay) {
        if (timeRemaining <= 300) { // 5 minutes remaining
            timerDisplay.style.color = '#ef4444'; // red
        } else if (timeRemaining <= 600) { // 10 minutes remaining
            timerDisplay.style.color = '#f59e0b'; // orange
        } else {
            timerDisplay.style.color = '#10b981'; // green
        }
    }
}

function expireSession() {
    clearInterval(sessionTimer);
    if (stopStatusChecks && typeof stopStatusChecks === 'function') {
        stopStatusChecks();
    }

    // Stop camera and audio
    if (videoStream) {
        videoStream.getTracks().forEach(track => track.stop());
    }
    if (window.userMediaStream) {
        window.userMediaStream.getTracks().forEach(track => track.stop());
    }

    // Show expired overlay
    const expiredOverlay = document.getElementById('expiredOverlay');
    if (expiredOverlay) {
        expiredOverlay.style.display = 'flex';
    }

    // Hide recording alert
    const recordingAlert = document.getElementById('recordingAlert');
    if (recordingAlert) {
        recordingAlert.classList.remove('show');
    }
}

function getSessionId() {
    // Generate or retrieve session ID from memory
    let sessionId = window.interviewSessionId;
    if (!sessionId) {
        sessionId = 'sess_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
        window.interviewSessionId = sessionId;
    }
    return sessionId;
}
