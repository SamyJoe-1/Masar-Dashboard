// Timer functionality with translation support
let sessionTimer;
let timeRemaining = 1800; // 30 minutes in seconds

function startTimer() {
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
    const timeString = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;

    const timerText = document.getElementById('timerProgress');
    timerText.textContent = `${getTranslatedMessage('time-remaining')} ${timeString}`;

    const progressPercent = ((1800 - timeRemaining) / 1800) * 100;
    document.getElementById('timerProgress').style.width = progressPercent + '%';

    // Change color based on time remaining
    const progressBar = document.getElementById('timerProgress');
    if (timeRemaining < 300) { // Less than 5 minutes
        progressBar.style.background = '#ef4444';
    } else if (timeRemaining < 900) { // Less than 15 minutes
        progressBar.style.background = '#f59e0b';
    }
}

async function expireSession() {
    clearInterval(sessionTimer);
    hideRecordingAlert();

    document.getElementById('expiredOverlay').style.display = 'flex';

    // Call Laravel API to close session
    try {
        const id = document.querySelector('input[name=interview_slug]')?.value;

        await fetch(`/api/session/close/${id}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        location.reload();
    } catch (error) {
        console.error('Error closing session:', error);
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
