<!DOCTYPE html>
<html lang="{{ $lang }}" dir="{{ $lang == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="{{ asset('styles/css/interviewForm.css') }}" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <title>{{ $lang == 'ar' ? 'جلسة المقابلة' : 'Interview Session' }}</title>


    <style>/* Add these styles for the camera preview in the avatar section */

        .avatar-container {
            width: 200px;
            height: 200px;
            border-radius: 12px;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f3f4f6;
            border: 2px solid #e5e7eb;
            margin-bottom: 20px;
        }

        .avatar-container video {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border: none;
        }

        .avatar-container.no-camera {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-size: 48px;
            font-weight: bold;
        }

        /* Camera permission styles */
        .camera-permission-popup {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }

        .camera-permission-content {
            background: white;
            padding: 30px;
            border-radius: 16px;
            text-align: center;
            max-width: 400px;
            margin: 20px;
        }

        .camera-permission-content h3 {
            color: #1a202c;
            margin-bottom: 15px;
            font-size: 20px;
        }

        .camera-permission-content p {
            color: #64748b;
            margin-bottom: 20px;
            line-height: 1.5;
        }

        .permission-button {
            background: #3464b0;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            margin: 5px;
            transition: background 0.3s ease;
        }

        .permission-button:hover {
            background: #2d5aa0;
        }

        .permission-button.cancel {
            background: #ef4444;
        }

        .permission-button.cancel:hover {
            background: #dc2626;
        }
    </style>
</head>
<body>

<!-- Language Selector -->
<div class="language-selector">
    <svg class="translate-icon" viewBox="0 0 24 24" fill="currentColor">
        <path d="M12.87 15.07l-2.54-2.51.03-.03c1.74-1.94 2.98-4.17 3.71-6.53H17V4h-7V2H8v2H1v1.99h11.17C11.5 7.92 10.44 9.75 9 11.35 8.07 10.32 7.3 9.19 6.69 8h-2c.73 1.63 1.73 3.17 2.98 4.56l-5.09 5.02L4 19l5-5 3.11 3.11.76-2.04zM18.5 10h-2L12 22h2l1.12-3h4.75L21 22h2l-4.5-12zm-2.62 7l1.62-4.33L19.12 17h-3.24z"/>
    </svg>
    <select class="language-select" id="languageSelect">
        <option value="ar" {{ $lang == 'ar' ? 'selected' : '' }}>العربية</option>
        <option value="en" {{ $lang == 'en' ? 'selected' : '' }}>English</option>
    </select>
</div>

<!-- Translation Loading Overlay -->
<div id="translateOverlay" class="translate-overlay">
    <div class="translate-loader">
        <div class="translate-spinner"></div>
        <div class="translate-text">{{ $lang == 'ar' ? 'جاري ترجمة المحتوى...' : 'Translating Content...' }}</div>
        <div class="translate-subtext">{{ $lang == 'ar' ? 'يرجى الانتظار أثناء ترجمة الصفحة' : 'Please wait while we translate the page' }}</div>
    </div>
</div>

<div class="container">
    <!-- Welcome Card -->
    <div id="welcomeCard" class="welcome-card">
        <h1 class="job-title">{{ @$jobTitle }}</h1>
        <p class="job-description" data-translate="job-description">
            {{ $lang == 'ar' ? 'انضم إلى فريق التطوير الديناميكي لدينا الذي يعمل على الأنظمة الحكومية الحيوية. نحن نبحث عن مطورين ذوي خبرة في التقنيات الحديثة لمساعدتنا في بناء تطبيقات آمنة وقابلة للتوسع تخدم ملايين المواطنين.' : 'Join our dynamic development team working on critical government systems. We\'re looking for experienced developers with expertise in modern web technologies to help build secure, scalable applications that serve millions of citizens.' }}
        </p>

        <div class="warning-box">
            <div class="warning-title" data-translate="warning-title">
                ⚠️ Important Notice
            </div>
            <div class="warning-text" data-translate="warning-text">
                Camera access is required for this interview. Once you start, you will have exactly 30 minutes to complete all questions. Please ensure you have a stable internet connection, working camera, and quiet environment before proceeding.
            </div>
        </div>

        <button class="start-button" onclick="startSession()" data-translate="start-button">{{ $lang == 'ar' ? 'بدء جلسة المقابلة' : 'Start Interview Session' }}</button>
    </div>

    <!-- Interview Form Container -->
    <div id="formContainer" class="form-container">
        <!-- Screen Recording Alert -->
        <div id="recordingAlert" class="recording-alert">
            <div class="recording-indicator">
                <div class="red-dot"></div>
                <span data-translate="recording-text">{{ $lang == 'ar' ? 'تسجيل الشاشة نشط' : 'Screen Recording Active' }}</span>
            </div>
        </div>

        <!-- Interview Card -->
        <div class="interview-card">
            <!-- Progress Circles Row -->
            <div class="progress-circles" id="progressCircles"></div>

            <!-- Question Content Row -->
            <div class="question-content-row">
                <!-- Left Column - Question & Avatar -->
                <div class="question-column">
                    <div class="question-header">
                        <div class="question-number" id="questionNumber"></div>
                        <div class="question-text" id="questionText"></div>
                        <button class="listen-button" id="listenButton" onclick="toggleAvatarAudio()">
                            <span>🔊</span>
                            <span data-translate="listen">{{ $lang == 'ar' ? 'استمع للسؤال' : 'Listen to Question' }}</span>
                        </button>
                    </div>

                    <!-- Avatar Whirlpool -->
                    <div class="avatar-whirlpool" id="avatarWhirlpool">
                        <div class="whirlpool-effect"></div>
                        <video class="avatar-video" autoplay loop muted playsinline>
                            <source src="{{ asset('assets/videos/avatar.mp4') }}" type="video/mp4">
                        </video>
                    </div>
                </div>

                <!-- Right Column - Recording & Info -->
                <div class="recording-column">
                    <!-- Timer Info -->
                    <div class="timer-info">
                        <div class="timer-display" id="timerDisplay">30:00</div>
                        <div data-translate="time-remaining">{{ $lang == 'ar' ? 'الوقت المتبقي' : 'Time Remaining' }}</div>
                        <div class="timer-bar">
                            <div class="timer-progress" id="timerProgress"></div>
                        </div>
                    </div>

                    <!-- Recording Area -->
                    <div class="recording-area">
                        <button class="recording-button" id="recordButton" onclick="toggleRecording()">
                            <span id="recordIcon">🎤</span>
                        </button>

                        <div class="recording-status" id="recordingStatus" data-translate="click-record">
                            {{ $lang == 'ar' ? 'انقر للتسجيل' : 'Click to Record' }}
                        </div>

                        <div class="recording-timer" id="recordingTimer">0:00</div>

                        <!-- ADD THIS: Answer Duration Display -->
                        <div class="answer-duration" id="answerDuration" style="display: none;">
                            <span data-translate="answer-duration">{{ $lang == 'ar' ? 'مدة الإجابة:' : 'Answer Duration:' }}</span>
                            <span id="durationValue"></span>
                        </div>

                        <div class="recording-controls" id="recordingControls" style="display: none;">
                            <button class="control-button retry" onclick="retryRecording()" data-translate="retry">
                                {{ $lang == 'ar' ? 'إعادة التسجيل' : 'Record Again' }}
                            </button>
                            <button class="control-button done" onclick="confirmRecording()" data-translate="done">
                                {{ $lang == 'ar' ? 'تم' : 'Done' }}
                            </button>
                        </div>
                    </div>

                    <!-- Camera Container - MOVED HERE -->
                    <div class="camera-container" id="cameraContainer">
                        <video class="camera-preview" id="cameraPreview" autoplay muted></video>
                    </div>
                    <hr>

                    <div class="camera-status" id="cameraStatus" style="display: none;">
                        <div class="camera-indicator">
                            <div class="camera-dot"></div>
                            <span data-translate="camera-active">{{ $lang == 'ar' ? 'الكاميرا نشطة' : 'Camera Active' }}</span>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Navigation Row -->
            <div class="navigation-row">
                <button class="nav-button prev" id="prevButton" onclick="previousQuestion()" disabled data-translate="previous">
                    {{ $lang == 'ar' ? 'السابق' : 'Previous' }}
                </button>
                <button class="nav-button next" id="nextButton" onclick="nextQuestion()" disabled data-translate="next">
                    {{ $lang == 'ar' ? 'التالي' : 'Next' }}
                </button>
                <button class="nav-button submit hidden" id="submitButton" onclick="submitInterview()" data-translate="submit">
                    {{ $lang == 'ar' ? 'إرسال المقابلة' : 'Submit Interview' }}
                </button>
            </div>
        </div>

        <!-- Hidden Form Fields -->
        <form id="interviewForm" style="display: none;">
            <input type="hidden" name="interview_slug" value="{{ $interview->id }}">
            @foreach($interview->questions as $key => $question)
                <textarea class="answer-textarea" name="{{ urlencode($question) }}" id="answer-{{ $loop->index }}"></textarea>
            @endforeach
        </form>
    </div>

    <!-- Camera Container (Fixed Position) -->
    <div class="camera-container" id="cameraContainer">
        <video class="camera-preview" id="cameraPreview" autoplay muted></video>
    </div>
</div>

<!-- Expired Overlay -->
<div id="expiredOverlay" class="expired-overlay">
    <div class="expired-message">
        <h2 data-translate="expired-title">{{ $lang == 'ar' ? 'انتهت الجلسة' : 'Session Expired' }}</h2>
        <p data-translate="expired-text">{{ $lang == 'ar' ? 'انتهت مهلة جلسة المقابلة. يرجى الاتصال بالموارد البشرية لإعادة الجدولة.' : 'Your interview session has timed out. Please contact HR to reschedule.' }}</p>
    </div>
</div>

@include('components.script.mainInterview')
<script src="{{ asset('styles/js/translation.js') }}" async></script>
<script src="{{ asset('styles/js/timer.js') }}"></script>
<script src="{{ asset('styles/js/validation.js') }}"></script>
<script src="{{ asset('styles/js/main.js') }}"></script>
<script src="{{ asset('styles/js/screenshot.js') }}"></script>

</body>
</html>
