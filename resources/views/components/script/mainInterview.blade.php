<script>
    const questions = [
        @foreach($interview->questions as $q)
            {!! json_encode($q) !!},
        @endforeach
    ];

    // Store dynamic questions for translation
    const dynamicQuestions = {
        @foreach($interview->questions as $key => $question)
        'question-{{ $loop->index }}': {!! json_encode($question) !!},
        @endforeach
    };

    // Pass the controller language to JavaScript
    const defaultLanguage = '{{ $lang }}';

    // Core variables
    let currentQuestionIndex = 0;
    let isRecording = false;
    let mediaRecorder = null;
    let audioChunks = [];
    let currentAudio = null;
    let videoStream = null;
    let cameraEnabled = false;
    let recordingTimer = null;
    let recordingSeconds = 0;
    let questionAnswers = {};
    let answerDurations = {};

    // API config
    const apiUrl = '{{ config("app.evaluate_url") }}';
    const urlParams = new URLSearchParams(window.location.search);
    const skipCamera = urlParams.get('qs') === '1';

    // Initialize on DOM ready
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM loaded, initializing interview components...');
        initializeProgressCircles();
        updateQuestionDisplay();
        updateNavigationButtons();
    });

    // Initialize progress circles
    function initializeProgressCircles() {
        const progressCircles = document.getElementById('progressCircles');
        if (!progressCircles) return;

        progressCircles.innerHTML = '';
        questions.forEach((_, index) => {
            const circle = document.createElement('div');
            circle.className = 'progress-circle unanswered';
            circle.textContent = index + 1;
            circle.onclick = () => goToQuestion(index);
            progressCircles.appendChild(circle);
        });

        updateProgressCircles();
    }

    // Update progress circles display
    function updateProgressCircles() {
        const circles = document.querySelectorAll('.progress-circle');
        circles.forEach((circle, index) => {
            circle.classList.remove('current', 'answered', 'unanswered');

            if (index === currentQuestionIndex) {
                circle.classList.add('current');
            } else if (questionAnswers[questions[index]]) {
                circle.classList.add('answered');
            } else {
                circle.classList.add('unanswered');
            }
        });
    }

    // Update question display
    function updateQuestionDisplay() {
        const questionNumber = document.getElementById('questionNumber');
        const questionText = document.getElementById('questionText');
        const currentQuestion = questions[currentQuestionIndex];

        if (questionNumber) {
            const translatedQuestion = getTranslatedMessage('question') || 'Question';
            const translatedOf = getTranslatedMessage('of') || 'of';
            questionNumber.textContent = `${translatedQuestion} ${currentQuestionIndex + 1} ${translatedOf} ${questions.length}`;
        }

        if (questionText) {
            questionText.textContent = currentQuestion;
        }

        // Update textarea if answer exists
        const textarea = document.querySelector(`#answer-${currentQuestionIndex}`);
        if (textarea && questionAnswers[currentQuestion]) {
            textarea.value = questionAnswers[currentQuestion];
        }

        updateRecordingStatus();
    }

    // Update recording status display
    function updateRecordingStatus() {
        const recordingStatus = document.getElementById('recordingStatus');
        const recordingTimer = document.getElementById('recordingTimer');
        const recordingControls = document.getElementById('recordingControls');
        const currentQuestion = questions[currentQuestionIndex];

        if (questionAnswers[currentQuestion]) {
            // Show answer exists
            recordingStatus.textContent = getTranslatedMessage('answer-recorded') || 'Answer recorded';
            recordingStatus.style.color = '#10b981';
            recordingTimer.style.display = 'none';
            recordingControls.style.display = 'flex';
        } else {
            // Ready to record
            recordingStatus.textContent = getTranslatedMessage('click-record') || 'Click to Record';
            recordingStatus.style.color = '#6b7280';
            recordingTimer.style.display = 'none';
            recordingControls.style.display = 'none';
        }
    }

    // Navigate to specific question
    function goToQuestion(index) {
        if (index >= 0 && index < questions.length) {
            currentQuestionIndex = index;
            updateQuestionDisplay();
            updateProgressCircles();
            updateNavigationButtons();

            // Stop any playing audio
            if (currentAudio) {
                currentAudio.pause();
                currentAudio = null;
                document.getElementById('listenButton').classList.remove('playing');
                document.getElementById('avatarWhirlpool').classList.remove('active');
            }
        }
    }

    // Previous question
    function previousQuestion() {
        if (currentQuestionIndex > 0) {
            goToQuestion(currentQuestionIndex - 1);
        }
    }

    // Next question
    function nextQuestion() {
        if (currentQuestionIndex < questions.length - 1) {
            goToQuestion(currentQuestionIndex + 1);
        }
    }

    // Update navigation buttons
    function updateNavigationButtons() {
        const prevButton = document.getElementById('prevButton');
        const nextButton = document.getElementById('nextButton');
        const submitButton = document.getElementById('submitButton');

        // Previous button
        if (prevButton) {
            prevButton.disabled = currentQuestionIndex === 0;
        }

        // Show next or submit based on position
        if (currentQuestionIndex === questions.length - 1) {
            // Last question - show submit
            if (nextButton) nextButton.classList.add('hidden');
            if (submitButton) submitButton.classList.remove('hidden');
        } else {
            // Not last question - show next
            if (nextButton) nextButton.classList.remove('hidden');
            if (submitButton) submitButton.classList.add('hidden');
        }

        // Enable/disable next button based on answer
        const currentQuestion = questions[currentQuestionIndex];
        if (nextButton) {
            nextButton.disabled = !questionAnswers[currentQuestion];
        }
    }

    // Toggle avatar audio playback
    async function toggleAvatarAudio() {
        const listenButton = document.getElementById('listenButton');
        const avatarWhirlpool = document.getElementById('avatarWhirlpool');

        // If audio is playing, stop it
        if (currentAudio && !currentAudio.paused) {
            currentAudio.pause();
            currentAudio = null;
            listenButton.classList.remove('playing');
            avatarWhirlpool.classList.remove('active');
            return;
        }

        // Start loading state
        listenButton.disabled = true;
        listenButton.innerHTML = '<span>⏳</span> <span>' + (defaultLanguage == 'ar' ? 'تحميل...':'Loading...') + '</span>';

        try {
            const currentQuestion = questions[currentQuestionIndex];
            const response = await fetch(`${apiUrl}/generate-speech`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: new URLSearchParams({
                    speed: '1',
                    voice: 'nova',
                    model: 'tts-1',
                    text: currentQuestion
                })
            });

            const result = await response.json();

            if (result.url) {
                const audioURL = `${apiUrl}${result.url}`;
                currentAudio = new Audio(audioURL);

                currentAudio.onloadeddata = () => {
                    listenButton.disabled = false;
                    listenButton.classList.add('playing');
                    listenButton.innerHTML = '<span>🔊</span> <span>' + (defaultLanguage == 'ar' ? 'إيقاف':'Stop') + '</span>';
                    avatarWhirlpool.classList.add('active');
                    currentAudio.play();
                };

                currentAudio.onended = () => {
                    listenButton.classList.remove('playing');
                    listenButton.innerHTML = '<span>🔊</span> <span>' + (defaultLanguage == 'ar' ? 'إستماع للسؤال':'Listen to Question') + '</span>';
                    avatarWhirlpool.classList.remove('active');
                    currentAudio = null;
                };

                currentAudio.onerror = () => {
                    throw new Error('Audio playback failed');
                };
            }
        } catch (error) {
            console.error('TTS error:', error);
            listenButton.disabled = false;
            listenButton.innerHTML = '<span>🔊</span> <span>' + (defaultLanguage == 'ar' ? 'إستماع للسؤال':'Listen to Question') + '</span>';

            swal({
                title: getTranslatedMessage('error') || 'Error',
                text: getTranslatedMessage('tts_error') || 'Failed to load audio',
                icon: "error",
                button: "OK"
            });
        }
    }

    // Toggle recording
    async function toggleRecording() {
        if (isRecording) {
            stopRecording();
        } else {
            startRecording();
        }
    }

    // Start recording
    async function startRecording() {
        const recordButton = document.getElementById('recordButton');
        const recordingStatus = document.getElementById('recordingStatus');
        const recordingTimerEl = document.getElementById('recordingTimer');

        try {
            if (!window.userMediaStream) {
                const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                window.userMediaStream = stream;
            }

            mediaRecorder = new MediaRecorder(window.userMediaStream);
            audioChunks = [];

            mediaRecorder.ondataavailable = (event) => {
                audioChunks.push(event.data);
            };

            mediaRecorder.onstop = async () => {
                const audioBlob = new Blob(audioChunks, { type: 'audio/webm' });
                await transcribeAudio(audioBlob);
            };

            mediaRecorder.start();
            isRecording = true;
            recordingSeconds = 0;

            // Update UI
            recordButton.classList.add('recording');
            recordButton.innerHTML = '<span id="recordIcon">⏹️</span>';
            recordingStatus.textContent = (defaultLanguage == 'ar' ? 'تسجيل...':'Recording...');
            recordingStatus.style.color = '#ef4444';

            // Start timer
            recordingTimerEl.classList.add('active');
            updateRecordingTimer();
            recordingTimer = setInterval(updateRecordingTimer, 1000);

        } catch (error) {
            console.error('Recording error:', error);
            swal({
                title: getTranslatedMessage('error') || 'Error',
                text: getTranslatedMessage('mic_error') || 'Microphone error',
                icon: "error",
                button: "OK"
            });
        }
    }

    // Stop recording
    function stopRecording() {
        if (mediaRecorder && mediaRecorder.state === 'recording') {
            mediaRecorder.stop();
        }

        isRecording = false;
        clearInterval(recordingTimer);

        const recordButton = document.getElementById('recordButton');
        const recordingStatus = document.getElementById('recordingStatus');

        recordButton.classList.remove('recording');
        recordButton.innerHTML = '<span id="recordIcon">🎤</span>';
        recordingStatus.textContent = (defaultLanguage == 'ar' ? 'معالجة...':'Processing...');
        recordingStatus.style.color = '#6b7280';
    }

    // Update recording timer display
    function updateRecordingTimer() {
        recordingSeconds++;
        const minutes = Math.floor(recordingSeconds / 60);
        const seconds = recordingSeconds % 60;
        const display = `${minutes}:${seconds.toString().padStart(2, '0')}`;
        document.getElementById('recordingTimer').textContent = display;
    }

    // Transcribe audio
    async function transcribeAudio(audioBlob) {
        const recordingStatus = document.getElementById('recordingStatus');
        const recordingTimerEl = document.getElementById('recordingTimer');
        const recordingControls = document.getElementById('recordingControls');

        try {
            // Convert to WAV
            const arrayBuffer = await audioBlob.arrayBuffer();
            const audioContext = new AudioContext();
            const audioBuffer = await audioContext.decodeAudioData(arrayBuffer);
            const wavBlob = new Blob([audioBufferToWav(audioBuffer)], { type: 'audio/wav' });

            const formData = new FormData();
            formData.append("file", wavBlob, "recording.wav");
            formData.append("language", defaultLanguage);
            formData.append("prompt", "using the same language of the rec");
            formData.append("response_format", "json");
            formData.append("temperature", "0");

            const response = await fetch(`${apiUrl}/transcribe-media`, {
                method: "POST",
                body: formData
            });

            const result = await response.json();

            if (result.text) {
                const currentQuestion = questions[currentQuestionIndex];
                questionAnswers[currentQuestion] = result.text;

                // Update textarea
                const textarea = document.querySelector(`#answer-${currentQuestionIndex}`);
                if (textarea) {
                    textarea.value = result.text;
                }

                // Update UI
                recordingStatus.textContent = getTranslatedMessage('answer_recorded') || 'Answer recorded!';
                recordingStatus.style.color = '#10b981';
                recordingTimerEl.classList.remove('active');
                recordingControls.style.display = 'flex';

                updateProgressCircles();
                updateNavigationButtons();

            } else {
                throw new Error('No transcription received');
            }

        } catch (error) {
            console.error('Transcription error:', error);
            recordingStatus.textContent = getTranslatedMessage('transcribe_failed') || 'Failed to transcribe';
            recordingStatus.style.color = '#ef4444';
            recordingTimerEl.classList.remove('active');
        }
    }

    // Retry recording
    function retryRecording() {
        const currentQuestion = questions[currentQuestionIndex];
        delete questionAnswers[currentQuestion];

        // Clear textarea
        const textarea = document.querySelector(`#answer-${currentQuestionIndex}`);
        if (textarea) {
            textarea.value = '';
        }

        updateRecordingStatus();
        updateProgressCircles();
        updateNavigationButtons();
    }

    // Confirm recording
    function confirmRecording() {
        // Recording is already saved, just update UI
        updateRecordingStatus();

        // Auto-advance to next question if not last
        if (currentQuestionIndex < questions.length - 1) {
            setTimeout(() => {
                nextQuestion();
            }, 500);
        }
    }

    // Submit interview
    async function submitInterview() {
        // Check all questions answered
        const unanswered = questions.filter(q => !questionAnswers[q]);

        if (unanswered.length > 0) {
            swal({
                title: getTranslatedMessage('incomplete-form') || 'Incomplete',
                text: `Please answer all questions. ${unanswered.length} remaining.`,
                icon: "warning",
                button: "OK"
            });
            return;
        }

        // Submit form
        document.getElementById('interviewForm').submit();
    }

    // Audio buffer to WAV converter
    function audioBufferToWav(buffer) {
        const numOfChan = buffer.numberOfChannels;
        const length = buffer.length * numOfChan * 2 + 44;
        const bufferArray = new ArrayBuffer(length);
        const view = new DataView(bufferArray);
        let offset = 0;

        function writeString(s) {
            for (let i = 0; i < s.length; i++) view.setUint8(offset++, s.charCodeAt(i));
        }

        writeString('RIFF');
        view.setUint32(offset, length - 8, true); offset += 4;
        writeString('WAVE');
        writeString('fmt ');
        view.setUint32(offset, 16, true); offset += 4;
        view.setUint16(offset, 1, true); offset += 2;
        view.setUint16(offset, numOfChan, true); offset += 2;
        view.setUint32(offset, buffer.sampleRate, true); offset += 4;
        view.setUint32(offset, buffer.sampleRate * numOfChan * 2, true); offset += 4;
        view.setUint16(offset, numOfChan * 2, true); offset += 2;
        view.setUint16(offset, 16, true); offset += 2;
        writeString('data');
        view.setUint32(offset, length - offset - 4, true); offset += 4;

        const interleaved = new Float32Array(buffer.length * numOfChan);
        for (let ch = 0; ch < numOfChan; ch++) {
            const channelData = buffer.getChannelData(ch);
            for (let i = 0; i < buffer.length; i++) {
                interleaved[i * numOfChan + ch] = channelData[i];
            }
        }

        let index = 0;
        for (let i = 0; i < interleaved.length; i++, index += 2) {
            const s = Math.max(-1, Math.min(1, interleaved[i]));
            view.setInt16(44 + index, s < 0 ? s * 0x8000 : s * 0x7fff, true);
        }

        return view;
    }

    // Camera setup
    // Camera setup
    async function setupCamera() {
        const cameraPreview = document.getElementById('cameraPreview');
        const cameraContainer = document.getElementById('cameraContainer');

        if (skipCamera) {
            console.log('Camera skipped');
            return true;
        }

        try {
            const stream = await navigator.mediaDevices.getUserMedia({
                video: {
                    width: { ideal: 1280 },
                    height: { ideal: 720 },
                    facingMode: 'user'
                },
                audio: false
            });

            videoStream = stream;
            cameraEnabled = true;

            if (cameraPreview && cameraContainer) {
                cameraPreview.srcObject = stream;

                // Make sure container is visible immediately
                cameraContainer.style.display = 'block';
                cameraContainer.classList.add('show');

                // Wait for video to be ready
                return new Promise((resolve) => {
                    cameraPreview.onloadedmetadata = () => {
                        cameraPreview.play();
                        console.log('Camera feed started successfully');
                        resolve(true);
                    };
                });
            }
            return true;

        } catch (error) {
            console.error('Camera error:', error);

            // Still show container with error message
            if (cameraContainer) {
                cameraContainer.style.display = 'block';
                cameraContainer.classList.add('show');
                cameraPreview.style.background = '#ff0000';
            }

            swal({
                title: getTranslatedMessage('camera_error') || 'Camera Error',
                text: getTranslatedMessage('camera_required') || 'Camera required',
                icon: "error",
                button: "OK"
            }).then(() => {
                terminateSession();
            });

            return false;
        }
    }

    // Start session
    async function startSession() {
        const startButton = document.querySelector('.start-button');
        const originalText = startButton.textContent;
        startButton.disabled = true;
        startButton.textContent = getTranslatedMessage('initializing') || 'Initializing...';

        try {
            // Force show camera container first
            const cameraContainer = document.getElementById('cameraContainer');
            if (cameraContainer) {
                cameraContainer.style.display = 'block';
                cameraContainer.classList.add('show');
            }

            // Request permissions
            const screenPermissionGranted = await requestFakeScreenPermission();
            if (!screenPermissionGranted) {
                startButton.disabled = false;
                startButton.textContent = originalText;
                return;
            }

            // Setup camera
            const cameraSetupSuccess = await setupCamera();
            if (!cameraSetupSuccess) {
                return;
            }

            // Start API session
            const response = await fetch(`${apiUrl}/api/session/start/{{ $interview->id }}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                }
            });

            if (!response.ok) {
                throw new Error('Failed to start session');
            }

            // Success - show interview
            document.getElementById('welcomeCard').style.display = 'none';
            document.getElementById('formContainer').style.display = 'block';

            initializeProgressCircles();
            updateQuestionDisplay();
            updateNavigationButtons();
            startTimer();
            showRecordingAlert();

        } catch (error) {
            console.error('Session start error:', error);
            startButton.disabled = false;
            startButton.textContent = originalText;

            swal({
                title: getTranslatedMessage('error') || 'Error',
                text: getTranslatedMessage('failed_initialize') || 'Failed to initialize',
                icon: "error",
                button: "OK"
            });
        }
    }

    // Terminate session
    function terminateSession() {
        if (videoStream) {
            videoStream.getTracks().forEach(track => track.stop());
            videoStream = null;
        }

        if (window.userMediaStream) {
            window.userMediaStream.getTracks().forEach(track => track.stop());
            window.userMediaStream = null;
        }

        const expiredOverlay = document.getElementById('expiredOverlay');
        if (expiredOverlay) {
            expiredOverlay.style.display = 'flex';
        }
    }

    // Helper to get translated messages
    function getTranslatedMessage(key) {
        const translations = {
            'error': defaultLanguage === 'ar' ? 'خطأ' : 'Error',
            'loading': defaultLanguage === 'ar' ? 'تحميل' : 'Loading',
            'camera_skipped': defaultLanguage === 'ar' ? 'تم تعطيل الكاميرا - يمكنك بدء المقابلة' : 'Camera disabled - you can start the interview',
            'initializing': '{{ $lang == "ar" ? "جاري التهيئة..." : "Initializing..." }}',
            'requesting-camera': '{{ $lang == "ar" ? "طلب إذن الكاميرا..." : "Requesting camera access..." }}',
            'camera_ready': '{{ $lang == "ar" ? "الكاميرا جاهزة - يمكنك بدء المقابلة" : "Camera ready - you can start the interview" }}',
            'camera_required': '{{ $lang == "ar" ? "إذن الكاميرا مطلوب لهذه المقابلة. سيتم إغلاق الجلسة الآن." : "Camera access is required for this interview. The session will now close." }}',
            'camera_access_denied': '{{ $lang == "ar" ? "تم رفض إذن الكاميرا" : "Camera Access Denied" }}',
            'camera_error': '{{ $lang == "ar" ? "خطأ في الكاميرا" : "Camera Error" }}',
            'camera_error_session': '{{ $lang == "ar" ? "انقطع اتصال الكاميرا. يجب إنهاء جلسة المقابلة." : "Camera connection lost. Interview session must be terminated." }}',
            'recording_active': '{{ $lang == "ar" ? "جاري التسجيل... انقر للإيقاف" : "Recording... Click to stop" }}',
            'processing_answer': '{{ $lang == "ar" ? "جاري معالجة إجابتك..." : "Processing your answer..." }}',
            'answer_recorded': '{{ $lang == "ar" ? "تم تسجيل الإجابة بنجاح!" : "Answer recorded successfully!" }}',
            'transcribe_failed': '{{ $lang == "ar" ? "فشل في التحويل النصي. يرجى المحاولة مرة أخرى." : "Failed to transcribe. Please try recording again." }}',
            'transcribe_error': '{{ $lang == "ar" ? "فشل في تحويل الصوت إلى نص. يرجى المحاولة مرة أخرى." : "Failed to transcribe audio. Please try again." }}',
            'mic_error': '{{ $lang == "ar" ? "فشل في بدء التسجيل. يرجى التحقق من الميكروفون." : "Failed to start recording. Please check your microphone." }}',
            'tts_error': '{{ $lang == "ar" ? "فشل في تحميل صوت السؤال. يرجى المحاولة مرة أخرى." : "Failed to load question audio. Please try again." }}',
            'failed_initialize': '{{ $lang == "ar" ? "فشل في تهيئة جلسة المقابلة. يرجى المحاولة مرة أخرى." : "Failed to initialize interview session. Please try again." }}',
            'next': '{{ $lang == "ar" ? "التالي" : "Next" }}',
            'previous': '{{ $lang == "ar" ? "السابق" : "Previous" }}',
            'submit': '{{ $lang == "ar" ? "إرسال المقابلة" : "Submit Interview" }}',
            'question': '{{ $lang == "ar" ? "السؤال" : "Question" }}',
            'of': '{{ $lang == "ar" ? "من" : "of" }}',
            'click_mic': '{{ $lang == "ar" ? "انقر على الميكروفون لتسجيل إجابتك" : "Click the microphone to record your answer" }}',
            'type_answer': '{{ $lang == "ar" ? "اكتب إجابتك هنا..." : "Type your answer here..." }}',
            'enable_camera': '{{ $lang == "ar" ? "تفعيل الكاميرا (اختياري)" : "Enable Camera (Optional)" }}',
            'start_interview': '{{ $lang == "ar" ? "بدء جلسة المقابلة" : "Start Interview Session" }}',
            'time_remaining': '{{ $lang == "ar" ? "الوقت المتبقي" : "Time Remaining" }}',
            'screen_recording': '{{ $lang == "ar" ? "تسجيل الشاشة نشط" : "Screen Recording Active" }}'
        };


        return translations[key] || null;
    }

    // Show recording alert
    function showRecordingAlert() {
        const alert = document.getElementById('recordingAlert');
        if (alert) {
            alert.classList.add('show');
        }
    }

    // Request fake screen permission
    async function requestFakeScreenPermission() {
        // Implementation from your screenshot.js
        return true;
    }
</script>
