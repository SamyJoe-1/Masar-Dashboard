<!DOCTYPE html>
<html lang="{{ $lang }}" dir="{{ $lang == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="{{ asset('styles/css/interviewForm.css') }}" rel="stylesheet">
    <link href="{{ asset('styles/css/recordAlert.css') }}" rel="stylesheet">
    <link href="{{ asset('styles/css/formValidation.css') }}" rel="stylesheet">
    <link href="{{ asset('styles/css/translation.css') }}" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <title>{{ $lang == 'ar' ? 'جلسة المقابلة' : 'Interview Session' }}</title>
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

    <div id="welcomeCard" class="welcome-card">
        <h1 class="job-title">{{ @$jobTitle }}</h1>
        <p class="job-description" data-translate="job-description">
            {{ $lang == 'ar' ? 'انضم إلى فريق التطوير الديناميكي لدينا الذي يعمل على الأنظمة الحكومية الحيوية. نحن نبحث عن مطورين ذوي خبرة في التقنيات الحديثة لمساعدتنا في بناء تطبيقات آمنة وقابلة للتوسع تخدم ملايين المواطنين.' : 'Join our dynamic development team working on critical government systems. We\'re looking for experienced developers with expertise in modern web technologies to help build secure, scalable applications that serve millions of citizens.' }}
        </p>

        <div class="warning-box">
            <div class="warning-title" data-translate="warning-title">
                Important Notice
            </div>
            <div class="warning-text" data-translate="warning-text">
                Camera access is required for this interview. Once you start, you will have exactly 30 minutes to complete all questions. Please ensure you have a stable internet connection, working camera, and quiet environment before proceeding.
            </div>
        </div>

        <div class="camera-toggle">
            <label>
                <input type="checkbox" id="cameraToggle" class="checkbox">
                <span data-translate="enable-camera">{{ $lang == 'ar' ? 'تفعيل الكاميرا (اختياري)' : 'Enable Camera (Optional)' }}</span>
            </label>
        </div>

        <button class="start-button" onclick="startSession()" data-translate="start-button">{{ $lang == 'ar' ? 'بدء جلسة المقابلة' : 'Start Interview Session' }}</button>
    </div>

    <div id="formContainer" class="form-container">
        <div class="timer-bar">
            <div id="timerProgress" class="timer-progress"></div>
        </div>
        <div id="timerText" class="timer-text" data-translate="timer-text">{{ $lang == 'ar' ? 'الوقت المتبقي: 30:00' : 'Time Remaining: 30:00' }}</div>

        <!-- Screen Recording Alert -->
        <div id="recordingAlert" class="recording-alert">
            <div class="recording-indicator">
                <div class="red-dot"></div>
                <span data-translate="recording-text">{{ $lang == 'ar' ? 'تسجيل الشاشة نشط' : 'Screen Recording Active' }}</span>
            </div>
        </div>

        <!-- Step Indicators -->
        <div class="step-indicators" id="stepIndicators"></div>

        <form id="interviewForm" action="#" method="POST">
            <input type="hidden" name="interview_slug" value="{{ $interview->id }}">

            @foreach($interview->questions as $key => $question)
                <div class="step-container" id="step-{{ $loop->index }}">
                    <div class="question-group">
                        <div class="question-number" >{{ $lang == 'ar' ? 'السؤال' : 'Question' }} {{ $loop->iteration }} {{ $lang == 'ar' ? 'من' : 'of' }} {{ count($interview->questions) }}</div>

                        <!-- Question with voice icon -->
                        <div class="question-label" data-translate="question-{{ $key }}">
                            {{ $question }} <span style="color: red">*</span>
                            <button type="button" class="voice-icon" id="playButton-{{ $loop->index }}" onclick="playQuestion({{ $loop->index }}, '{{ addslashes($question) }}')">
                                🔊
                            </button>
                        </div>

                        <!-- Avatar Container -->
                        <div class="avatar-container" id="avatar-{{ $loop->index }}" align="center">
                            <video class="avatar" autoplay loop muted playsinline>
                                <source src="{{ asset('assets/videos/avatar.mp4') }}" type="video/mp4">
                            </video>
                        </div>

                        <!-- Camera Container -->
                        <div class="camera-container" id="camera-{{ $loop->index }}">
                            <video class="camera-preview" id="video-{{ $loop->index }}" autoplay muted></video>
                        </div>

                        <!-- Voice Controls -->
                        <div class="voice-controls">
                            <button type="button" class="mic-button" id="micButton-{{ $loop->index }}" onclick="toggleRecording({{ $loop->index }})">
                                🎤
                            </button>
                        </div>

                        <div class="recording-status" id="status-{{ $loop->index }}">
                            {{ $lang == 'ar' ? 'انقر على الميكروفون لتسجيل إجابتك' : 'Click the microphone to record your answer' }}
                        </div>

                        <div class="answer-container">
                            <textarea class="answer-textarea" name="{{ urlencode($question) }}" data-translate-placeholder="placeholder1" placeholder="{{ $lang == 'ar' ? 'اكتب إجابتك هنا...' : 'Type your answer here...' }}" required readonly></textarea>
                            <div class="validation-error" id="error-{{ $key }}"></div>
                        </div>
                    </div>
                </div>
            @endforeach

            <!-- Navigation -->
            <div class="navigation">
                <button type="button" class="nav-button prev" id="prevButton" onclick="previousStep()" disabled>
                    {{ $lang == 'ar' ? 'السابق' : 'Previous' }}
                </button>
                <button type="button" class="nav-button next" id="nextButton" onclick="nextStep()" disabled>
                    {{ $lang == 'ar' ? 'التالي' : 'Next' }}
                </button>
                <button type="submit" class="nav-button submit hidden" id="submitBtn" data-translate="submit-button" disabled>
                    {{ $lang == 'ar' ? 'إرسال المقابلة' : 'Submit Interview' }}
                </button>
            </div>
        </form>
    </div>
</div>

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
