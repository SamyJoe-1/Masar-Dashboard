<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="{{ asset('styles/css/interviewForm.css') }}" rel="stylesheet">
    <link href="{{ asset('styles/css/recordAlert.css') }}" rel="stylesheet">
    <link href="{{ asset('styles/css/formValidation.css') }}" rel="stylesheet">
    <link href="{{ asset('styles/css/translation.css') }}" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <title>جلسة المقابلة</title>
</head>
<body>

<!-- Language Selector -->
<div class="language-selector">
    <svg class="translate-icon" viewBox="0 0 24 24" fill="currentColor">
        <path d="M12.87 15.07l-2.54-2.51.03-.03c1.74-1.94 2.98-4.17 3.71-6.53H17V4h-7V2H8v2H1v1.99h11.17C11.5 7.92 10.44 9.75 9 11.35 8.07 10.32 7.3 9.19 6.69 8h-2c.73 1.63 1.73 3.17 2.98 4.56l-5.09 5.02L4 19l5-5 3.11 3.11.76-2.04zM18.5 10h-2L12 22h2l1.12-3h4.75L21 22h2l-4.5-12zm-2.62 7l1.62-4.33L19.12 17h-3.24z"/>
    </svg>
    <select class="language-select" id="languageSelect">
        <option value="ar">العربية</option>
        <option value="en">English</option>
    </select>
</div>

<!-- Translation Loading Overlay -->
<div id="translateOverlay" class="translate-overlay">
    <div class="translate-loader">
        <div class="translate-spinner"></div>
        <div class="translate-text">جاري ترجمة المحتوى...</div>
        <div class="translate-subtext">يرجى الانتظار أثناء ترجمة الصفحة</div>
    </div>
</div>

<div class="container">

    <div id="welcomeCard" class="welcome-card">
        <h1 class="job-title" data-translate="job-title">مطور برمجيات أول</h1>
        <p class="job-description" data-translate="job-description">
            انضم إلى فريق التطوير الديناميكي لدينا الذي يعمل على الأنظمة الحكومية الحيوية.
            نحن نبحث عن مطورين ذوي خبرة في التقنيات الحديثة لمساعدتنا في بناء تطبيقات آمنة وقابلة للتوسع تخدم ملايين المواطنين.
        </p>

        <div class="warning-box">
            <div class="warning-title" data-translate="warning-title">
                ⚠️ إشعار مهم
            </div>
            <div class="warning-text" data-translate="warning-text">
                بمجرد بدء جلسة المقابلة، ستحصل على 30 دقيقة بالضبط لإكمال جميع الأسئلة.
                سينتهي النموذج تلقائياً بعد هذا الوقت. يرجى التأكد من وجود اتصال إنترنت مستقر وبيئة هادئة قبل المتابعة.
            </div>
        </div>

        <button class="start-button" onclick="startSession()" data-translate="start-button">بدء جلسة المقابلة</button>
    </div>

    <div id="formContainer" class="form-container">
        <div class="timer-bar">
            <div id="timerProgress" class="timer-progress"></div>
        </div>
        <div id="timerText" class="timer-text" data-translate="timer-text">الوقت المتبقي: 30:00</div>

        <!-- Screen Recording Alert -->
        <div id="recordingAlert" class="recording-alert">
            <div class="recording-indicator">
                <div class="red-dot"></div>
                <span data-translate="recording-text">تسجيل الشاشة نشط</span>
            </div>
        </div>

        <form id="interviewForm" action="#" method="POST">
            <input type="hidden" name="interview_slug" value="{{ $interview->id }}">

            @foreach($interview->questions as $key => $question)
                <div class="question-group">
                    <!-- Use a consistent pattern for dynamic question translation keys -->
                    <div class="question-label" data-translate="question-{{ $key }}">{{ $question }} <span style="color: red">*</span></div>
                    <div class="answer-container">
                        <textarea class="answer-textarea" name="{{ $key }}" data-translate-placeholder="placeholder1" placeholder="اكتب إجابتك هنا..." required></textarea>
                        <div class="validation-error" id="error-{{ $key }}"></div>
                    </div>
                </div>
            @endforeach
            <button type="submit" class="submit-button" id="submitBtn" data-translate="submit-button" disabled>إرسال المقابلة</button>
        </form>
    </div>
</div>

<div id="expiredOverlay" class="expired-overlay">
    <div class="expired-message">
        <h2 data-translate="expired-title">انتهت الجلسة</h2>
        <p data-translate="expired-text">انتهت مهلة جلسة المقابلة. يرجى الاتصال بالموارد البشرية لإعادة الجدولة.</p>
    </div>
</div>

<script>
    const questions = [
        @foreach(array_keys($interview->questions) as $q)
            '{{ $q }}',
        @endforeach
    ];

    // Store dynamic questions for translation
    const dynamicQuestions = {
        @foreach($interview->questions as $key => $question)
        'question-{{ $key }}': {!! json_encode($question) !!},
        @endforeach
    };
</script>
<script src="{{ asset('styles/js/translation.js') }}" async></script>
<script src="{{ asset('styles/js/timer.js') }}"></script>
<script src="{{ asset('styles/js/validation.js') }}"></script>
<script src="{{ asset('styles/js/main.js') }}"></script>
<script src="{{ asset('styles/js/screenshot.js') }}"></script>

</body>
</html>
