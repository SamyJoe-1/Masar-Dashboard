<script>
    const questions = [
        @foreach($interview->questions as $q)
            {!! json_encode($q) !!},
        @endforeach
    ];

    // Store dynamic questions for translation with proper indexing
    const dynamicQuestions = {
        @foreach($interview->questions as $key => $question)
        'question-{{ $loop->index }}': {!! json_encode($question) !!},
        @endforeach
    };

    console.log('Dynamic questions:', dynamicQuestions);

    // Pass the controller language to JavaScript
    const defaultLanguage = '{{ $lang }}';

    // Voice and step functionality
    let currentStep = 0;
    let voiceAnswers = {};
    let isRecording = false;
    let mediaRecorder = null;
    let audioChunks = [];
    let currentAudio = null;
    let videoStream = null;
    let cameraEnabled = false;

    // API config
    const apiUrl = '{{ config("app.evaluate_url") }}';
    const urlParams = new URLSearchParams(window.location.search);
    const skipCamera = urlParams.get('qs') === '1';

    // Add this to your existing DOMContentLoaded event listener
    document.addEventListener('DOMContentLoaded', function() {
        // Hide the camera toggle checkbox since camera is now mandatory
        const cameraToggle = document.querySelector('.camera-toggle');
        if (cameraToggle) {
            cameraToggle.style.display = 'none';
        }

        // Update warning text to mention mandatory camera
        const warningText = document.querySelector('[data-translate="warning-text"]');
        if (warningText) {
            const mandatoryCameraText = '{{ $lang == "ar" ? "Ø§Ù„ÙƒØ§Ù…ÙŠØ±Ø§ Ù…Ø·Ù„ÙˆØ¨Ø© Ù„Ù‡Ø°Ù‡ Ø§Ù„Ù…Ù‚Ø§Ø¨Ù„Ø©. " : "Camera access is required for this interview. " }}';
            warningText.textContent = mandatoryCameraText + warningText.textContent;
        }
    });

    // Clean up resources when page unloads
    window.addEventListener('beforeunload', function() {
        if (videoStream) {
            videoStream.getTracks().forEach(track => track.stop());
        }
        if (window.userMediaStream) {
            window.userMediaStream.getTracks().forEach(track => track.stop());
        }
    });

    // Wait for DOM to be fully loaded
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize all components after DOM is ready
        console.log('DOM loaded, initializing components...');

        // Verify questions are properly loaded
        console.log('Questions count:', questions.length);
        console.log('Step containers found:', document.querySelectorAll('.step-container').length);

        // Make sure all question elements exist before validation
        setTimeout(() => {
            initializeSteps();
            console.log('Steps initialized');
        }, 100);
    });

    // Initialize steps on form container show
    function initializeSteps() {
        console.log('Initializing steps...');

        // Hide all steps except first
        document.querySelectorAll('.step-container').forEach((step, index) => {
            step.classList.toggle('active', index === 0);
        });

        // Create step indicators
        const stepIndicators = document.getElementById('stepIndicators');
        if (stepIndicators) {
            stepIndicators.innerHTML = '';
            questions.forEach((_, index) => {
                const dot = document.createElement('div');
                dot.className = 'step-dot';
                if (index === 0) dot.classList.add('active');
                stepIndicators.appendChild(dot);
            });
        }

        updateNavigation();
        setupCamera();

        console.log('Steps initialization complete');
    }

    function showStep(stepIndex) {
        console.log('Showing step:', stepIndex);

        // Hide all steps
        document.querySelectorAll('.step-container').forEach(step => {
            step.classList.remove('active');
        });

        // Hide all cameras (only if camera is enabled)
        if (!skipCamera) {
            document.querySelectorAll('.camera-container').forEach(camera => {
                camera.classList.remove('show');
            });
        }

        // Show current step
        const currentStepElement = document.getElementById(`step-${stepIndex}`);
        if (currentStepElement) {
            currentStepElement.classList.add('active');
        }

        // Show current camera only if camera is enabled and not skipped
        if (!skipCamera && cameraEnabled) {
            const currentCamera = document.getElementById(`camera-${stepIndex}`);
            if (currentCamera) {
                currentCamera.classList.add('show');
            }
        }

        // Update step indicators
        document.querySelectorAll('.step-dot').forEach((dot, index) => {
            dot.classList.remove('active');
            if (index < stepIndex) {
                dot.classList.add('completed');
            } else if (index === stepIndex) {
                dot.classList.add('active');
            } else {
                dot.classList.remove('completed');
            }
        });

        updateNavigation();
    }

    function updateNavigation() {
        const prevButton = document.getElementById('prevButton');
        const nextButton = document.getElementById('nextButton');
        const submitButton = document.getElementById('submitBtn');

        if (prevButton) prevButton.disabled = currentStep === 0;

        const currentQuestionText = questions[currentStep];

        if (currentStep === questions.length - 1) {
            if (nextButton) nextButton.classList.add('hidden');
            if (submitButton) {
                submitButton.classList.remove('hidden');
                submitButton.disabled = !voiceAnswers[currentQuestionText];
            }
        } else {
            if (nextButton) {
                nextButton.classList.remove('hidden');
                nextButton.disabled = !voiceAnswers[currentQuestionText];
            }
            if (submitButton) submitButton.classList.add('hidden');
        }
    }

    function previousStep() {
        if (currentStep > 0) {
            currentStep--;
            showStep(currentStep);
        }
    }

    function nextStep() {
        const currentQuestionText = questions[currentStep];
        if (currentStep < questions.length - 1 && voiceAnswers[currentQuestionText]) {
            currentStep++;
            showStep(currentStep);
        }
    }

    // Update your setupCamera function:
    async function setupCamera() {
        const cameraToggle = document.getElementById('cameraToggle');
        const status = document.getElementById('status-0');

        // Skip camera if qs=1 parameter is present
        if (skipCamera) {
            console.log('Skipping camera due to qs=1 parameter');
            cameraEnabled = false;

            // Hide all camera containers
            document.querySelectorAll('.camera-container').forEach(container => {
                container.style.display = 'none';
            });

            // Update status message
            if (status) {
                status.textContent = getTranslatedMessage('camera_skipped') || 'Camera disabled - you can start the interview';
            }

            return true; // Allow session to continue without camera
        }

        // Original camera setup logic for normal cases
        cameraEnabled = true;

        if (status) {
            status.textContent = getTranslatedMessage('requesting-camera') || 'Requesting camera access...';
        }

        try {
            const stream = await navigator.mediaDevices.getUserMedia({
                video: {
                    width: { ideal: 640 },
                    height: { ideal: 480 },
                    facingMode: 'user'
                },
                audio: false
            });

            videoStream = stream;
            cameraEnabled = true;
            setupCameraForAllSteps();

            if (status) {
                status.textContent = getTranslatedMessage('camera_ready') || 'Camera ready - you can start the interview';
            }

            console.log('Camera initialized successfully');
            return true;

        } catch (error) {
            console.error('Camera access denied:', error);
            cameraEnabled = false;

            const errorMessage = getTranslatedMessage('camera_required') || 'Camera access is required for this interview. The session will now close.';

            swal({
                title: getTranslatedMessage('camera_access_denied') || 'Camera Access Denied',
                text: errorMessage,
                icon: "error",
                button: "OK",
                closeOnClickOutside: false,
                closeOnEsc: false
            }).then(() => {
                terminateSession();
            });

            return false;
        }
    }

    // Setup camera for all steps at once
    function setupCameraForAllSteps() {
        if (!cameraEnabled || !videoStream) return;

        // Setup video stream for all video elements but keep them hidden
        for (let i = 0; i < questions.length; i++) {
            const video = document.getElementById(`video-${i}`);
            const container = document.getElementById(`camera-${i}`);

            if (video && container) {
                video.srcObject = videoStream;
                // Remove this line: container.classList.add('show');
                // Camera visibility will be controlled by showStep function

                video.onerror = function() {
                    console.error(`Video error on step ${i}`);
                    handleCameraError();
                };
            }
        }
    }

    function setupCameraForStep(stepIndex) {
        // Since we're setting up all cameras at once, this function just ensures visibility
        if (cameraEnabled && videoStream) {
            const container = document.getElementById(`camera-${stepIndex}`);
            if (container) {
                container.classList.add('show');
            }
        }
    }

    function handleCameraError() {
        const errorMessage = getTranslatedMessage('camera_error_session') || 'Camera connection lost. Interview session must be terminated.';

        swal({
            title: getTranslatedMessage('camera_error') || 'Camera Error',
            text: errorMessage,
            icon: "error",
            button: "OK",
            closeOnClickOutside: false,
            closeOnEsc: false
        }).then(() => {
            terminateSession();
        });
    }

    // Terminate session function
    function terminateSession() {
        // Stop all media streams
        if (videoStream) {
            videoStream.getTracks().forEach(track => track.stop());
            videoStream = null;
        }

        if (window.userMediaStream) {
            window.userMediaStream.getTracks().forEach(track => track.stop());
            window.userMediaStream = null;
        }

        // Show expired overlay or redirect
        const expiredOverlay = document.getElementById('expiredOverlay');
        if (expiredOverlay) {
            expiredOverlay.style.display = 'flex';
        } else {
            // Fallback: redirect to home or interview list
            window.location.href = '/'; // Adjust this URL as needed
        }
    }


    async function playQuestion(stepIndex, questionText) {
        const playButton = document.getElementById(`playButton-${stepIndex}`);
        const avatarContainer = document.getElementById(`avatar-${stepIndex}`);

        if (!playButton || !avatarContainer) {
            console.error('Required elements not found for step:', stepIndex);
            return;
        }

        // Stop any currently playing audio
        if (currentAudio) {
            currentAudio.pause();
            currentAudio = null;
            document.querySelectorAll('.voice-icon').forEach(btn => {
                btn.className = 'voice-icon';
                btn.innerHTML = '🔊';
            });
            document.querySelectorAll('.avatar-container').forEach(container => {
                container.classList.remove('show');
            });
        }

        // If this button is already playing, stop it
        if (playButton.classList.contains('playing')) {
            playButton.className = 'voice-icon';
            playButton.innerHTML = '🔊';
            avatarContainer.classList.remove('show');
            return;
        }

        // Set loading state
        playButton.className = 'voice-icon loading';
        playButton.innerHTML = '🔊';

        try {
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
                    text: questionText
                })
            });

            // console.log(response.text())
            const result = await response.json();
            console.log(result);

            audioURL = `${apiUrl}${result.url}`;
            // audioURL = `https://api5.massar.biz/static/audio/7bbe46a7-a32d-47ea-9f15-5a5eef9c24c2.mp3`;

            console.log(audioURL)
            if (audioURL) {

                currentAudio = new Audio(audioURL);

                currentAudio.onloadeddata = () => {
                    playButton.className = 'voice-icon playing';
                    playButton.innerHTML = '🔊';
                    avatarContainer.classList.add('show');
                    currentAudio.play();
                };

                currentAudio.onended = () => {
                    playButton.className = 'voice-icon';
                    playButton.innerHTML = '🔊';
                    avatarContainer.classList.remove('show');
                    currentAudio = null;
                };

                currentAudio.onerror = () => {
                    console.error('Error playing audio');
                    playButton.className = 'voice-icon';
                    playButton.innerHTML = '🔊';
                    avatarContainer.classList.remove('show');
                };
            } else {
                throw new Error('No audio URL received');
            }

        } catch (error) {
            console.error('Error with TTS:', error);
            playButton.className = 'voice-icon';
            playButton.innerHTML = '🔊';

            // Use fallback message if translation not available
            const errorMessage = getTranslatedMessage('tts_error') || 'Failed to load question audio. Please try again.';
            swal({
                title: getTranslatedMessage('error') || 'Error',
                text: errorMessage,
                icon: "error",
                button: "OK"
            });
        }
    }

    async function toggleRecording(stepIndex) {
        const micButton = document.getElementById(`micButton-${stepIndex}`);
        const status = document.getElementById(`status-${stepIndex}`);
        const textarea = document.querySelector(`#step-${stepIndex} .answer-textarea`);

        if (!micButton || !status || !textarea) {
            console.error('Required elements not found for recording step:', stepIndex);
            return;
        }

        if (!isRecording) {
            // Start recording
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
                    await transcribeAudio(audioBlob, stepIndex);
                };

                mediaRecorder.start();
                isRecording = true;

                micButton.classList.add('recording');
                status.textContent = getTranslatedMessage('recording_active') || 'Recording... Click to stop';
                status.classList.add('recording');

            } catch (error) {
                console.error('Error starting recording:', error);
                swal({
                    title: getTranslatedMessage('error') || 'Error',
                    text: getTranslatedMessage('mic_error') || 'Failed to start recording. Please check your microphone.',
                    icon: "error",
                    button: "OK"
                });
            }
        } else {
            // Stop recording
            if (mediaRecorder && mediaRecorder.state === 'recording') {
                mediaRecorder.stop();
            }
            isRecording = false;

            micButton.classList.remove('recording');
            micButton.classList.add('processing');
            status.textContent = getTranslatedMessage('processing_answer') || 'Processing your answer...';
            status.classList.remove('recording');
        }
    }

    async function transcribeAudio(audioBlob, stepIndex) {
        const micButton = document.getElementById(`micButton-${stepIndex}`);
        const status = document.getElementById(`status-${stepIndex}`);
        const textarea = document.querySelector(`#step-${stepIndex} .answer-textarea`);

        try {
            // Convert .webm to .wav
            const arrayBuffer = await audioBlob.arrayBuffer();
            const audioContext = new AudioContext();
            const audioBuffer = await audioContext.decodeAudioData(arrayBuffer);
            const wavBlob = new Blob([audioBufferToWav(audioBuffer)], { type: 'audio/wav' });

            const formData = new FormData();
            formData.append("file", wavBlob, "recording.wav");
            formData.append("language", "{{ $lang }}");
            formData.append("prompt", "using the same language of the rec");
            formData.append("response_format", "json");
            formData.append("temperature", 0);

            const response = await fetch(`${apiUrl}/transcribe-media`, {
                method: "POST",
                body: formData,
            });

            const result = await response.json();
            console.log(result);

            if (result.text) {
                const questionText = questions[stepIndex];
                voiceAnswers[questionText] = result.text;
                textarea.value = result.text;
                textarea.classList.add('has-content');
                status.textContent = getTranslatedMessage('answer_recorded') || 'Answer recorded successfully!';
                updateNavigation();
                console.log('Answer recorded for step:', stepIndex, result.text);
            } else {
                throw new Error('No transcription received');
            }

        } catch (error) {
            console.error('Error transcribing audio:', error);
            status.textContent = getTranslatedMessage('transcribe_failed') || 'Failed to transcribe. Please try recording again.';
            swal({
                title: getTranslatedMessage('error') || 'Error',
                text: getTranslatedMessage('transcribe_error') || 'Failed to transcribe audio. Please try again.',
                icon: "error",
                button: "OK"
            });
        } finally {
            micButton.classList.remove('processing');
        }
    }

    // Helper: convert AudioBuffer to WAV
    function audioBufferToWav(buffer) {
        const numOfChan = buffer.numberOfChannels;
        const length = buffer.length * numOfChan * 2 + 44;
        const bufferArray = new ArrayBuffer(length);
        const view = new DataView(bufferArray);
        let offset = 0;

        function writeString(s) {
            for (let i = 0; i < s.length; i++) view.setUint8(offset++, s.charCodeAt(i));
        }

        // WAV header
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

        // Write interleaved PCM samples
        const interleaved = new Float32Array(buffer.length * numOfChan);
        for (let ch = 0; ch < numOfChan; ch++) {
            const channelData = buffer.getChannelData(ch);
            for (let i = 0; i < buffer.length; i++) {
                interleaved[i * numOfChan + ch] = channelData[i];
            }
        }

        let index = 0;
        const volume = 1;
        for (let i = 0; i < interleaved.length; i++, index += 2) {
            const s = Math.max(-1, Math.min(1, interleaved[i] * volume));
            view.setInt16(44 + index, s < 0 ? s * 0x8000 : s * 0x7fff, true);
        }

        return view;
    }


    // Monitor camera stream throughout the interview
    function monitorCameraStream() {
        if (!videoStream) return;

        // Check if any track has ended
        videoStream.getTracks().forEach(track => {
            track.onended = function() {
                console.error('Camera track ended unexpectedly');
                handleCameraError();
            };
        });
    }

    // Enhanced session start function
    // Enhanced startSession function with mandatory camera check
    async function startSession() {
        console.log('Starting session with mandatory camera...');

        // Show loading state
        const startButton = document.querySelector('.start-button');
        const originalText = startButton.textContent;
        startButton.disabled = true;
        startButton.textContent = getTranslatedMessage('initializing') || 'Initializing...';

        try {
            // First, request screen permission (your existing function)
            const screenPermissionGranted = await requestFakeScreenPermission();
            if (!screenPermissionGranted) {
                startButton.disabled = false;
                startButton.textContent = originalText;
                return;
            }

            // MANDATORY: Setup camera - session fails if this fails
            const cameraSetupSuccess = await setupCamera();
            if (!cameraSetupSuccess) {
                // setupCamera() already handles the error and termination
                return;
            }

            // Start the API session
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

            console.log('Interview session started successfully');

            // Success - hide welcome card and show form
            document.getElementById('welcomeCard').style.display = 'none';
            document.getElementById('formContainer').style.display = 'block';

            // Initialize steps and other components
            initializeSteps();
            startTimer();

            // Initialize validation with delay to ensure DOM is ready
            setTimeout(() => {
                if (typeof initializeValidation === 'function') {
                    initializeValidation();
                    console.log('Validation initialized');
                }
            }, 500);

            showRecordingAlert();

        } catch (error) {
            console.error('Error starting session:', error);
            startButton.disabled = false;
            startButton.textContent = originalText;

            swal({
                title: getTranslatedMessage('error') || 'Error',
                text: getTranslatedMessage('failed_initialize') || 'Failed to initialize interview session. Please try again.',
                icon: "error",
                button: "OK"
            });
        }
    }

    // Helper function to get translated messages
    // Helper function to get translated messages
    function getTranslatedMessage(key) {
        const translations = {
            'error': defaultLanguage === 'ar' ? 'خطأ' : 'Error',
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
    // Request fake screen permission (placeholder function)
    async function requestFakeScreenPermission() {
        // This should be implemented based on your screenshot.js
        return true; // For now, always return true
    }

    // Placeholder for timer function
    function startTimer() {
        // This should be implemented in your timer.js
        console.log('Timer started');
    }

    // Placeholder for recording alert
    function showRecordingAlert() {
        const alert = document.getElementById('recordingAlert');
        if (alert) {
            alert.style.display = 'block';
        }
    }
</script>
