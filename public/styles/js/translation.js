function showError(message) {
    swal("Translation Error", message, "error");
}

function delay(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}

class LiveTranslator {
    constructor() {
        this.currentLang = 'ar'; // Arabic as default
        this.isTranslating = false;
        this.originalTexts = new Map();
        this.translations = new Map();
        this.translationQueue = [];
        this.isProcessingQueue = false;
        this.initializeTranslator();
    }

    initializeTranslator() {
        // Wait for DOM to be fully loaded
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.setup());
        } else {
            this.setup();
        }
    }

    setup() {
        this.storeOriginalTexts();

        // Set initial language selector to Arabic
        const languageSelect = document.getElementById('languageSelect');
        if (languageSelect) {
            languageSelect.value = 'ar';
            languageSelect.addEventListener('change', (e) => {
                this.translatePage(e.target.value);
            });
        }

        // Apply RTL direction for Arabic by default
        document.documentElement.dir = 'rtl';
        document.documentElement.lang = 'ar';

        // Initialize with Arabic content
        this.initializeArabicContent();
    }

    storeOriginalTexts() {
        // Store all elements with data-translate or data-translate-placeholder
        const elements = document.querySelectorAll('[data-translate], [data-translate-placeholder]');
        elements.forEach(element => {
            const key = element.getAttribute('data-translate') || element.getAttribute('data-translate-placeholder');

            if (element.hasAttribute('data-translate-placeholder')) {
                // Store current placeholder (should be Arabic by default)
                this.originalTexts.set(key + '_placeholder', element.placeholder || '');
            } else {
                // Store current text content (should be Arabic by default)
                this.originalTexts.set(key, element.textContent.trim() || '');
            }
        });

        console.log('Stored original texts:', this.originalTexts);
    }

    async translatePage(targetLang) {
        if (this.isTranslating || targetLang === this.currentLang) return;

        this.isTranslating = true;
        this.showLoader();

        try {
            // Handle RTL/LTR direction change
            if (targetLang === 'ar') {
                document.documentElement.dir = 'rtl';
                document.documentElement.lang = 'ar';
            } else {
                document.documentElement.dir = 'ltr';
                document.documentElement.lang = 'en';
            }

            const elementsToTranslate = document.querySelectorAll('[data-translate], [data-translate-placeholder]');
            const translationPromises = [];

            // Process each element
            elementsToTranslate.forEach(element => {
                const key = element.getAttribute('data-translate') || element.getAttribute('data-translate-placeholder');
                const isPlaceholder = element.hasAttribute('data-translate-placeholder');

                // First check if we have predefined translations
                if (jsMessages[targetLang] && jsMessages[targetLang][key]) {
                    this.applyTranslation(element, jsMessages[targetLang][key], isPlaceholder);
                } else {
                    // Use dynamic translation for questions and other content
                    let sourceText;
                    if (targetLang === 'ar') {
                        // Going back to Arabic - use stored original or predefined Arabic text
                        if (isPlaceholder) {
                            sourceText = this.originalTexts.get(key + '_placeholder') || jsMessages['ar'][key] || element.placeholder;
                        } else {
                            sourceText = this.originalTexts.get(key) || jsMessages['ar'][key] || element.textContent.trim();
                        }
                        this.applyTranslation(element, sourceText, isPlaceholder);
                    } else {
                        // Going to English - translate from Arabic
                        if (isPlaceholder) {
                            sourceText = this.originalTexts.get(key + '_placeholder') || jsMessages['ar'][key] || element.placeholder;
                        } else {
                            sourceText = this.originalTexts.get(key) || jsMessages['ar'][key] || element.textContent.trim();
                        }

                        if (sourceText && sourceText.trim()) {
                            const translationPromise = this.translateText(sourceText, targetLang)
                                .then(translatedText => {
                                    this.applyTranslation(element, translatedText, isPlaceholder);
                                })
                                .catch(error => {
                                    console.warn('Translation failed for:', sourceText, error);
                                    this.applyTranslation(element, sourceText, isPlaceholder);
                                });
                            translationPromises.push(translationPromise);
                        }
                    }
                }
            });

            // Wait for all translations to complete
            if (translationPromises.length > 0) {
                await Promise.allSettled(translationPromises);
            }

            this.currentLang = targetLang;

        } catch (error) {
            console.error('Translation failed:', error);
            showError('Translation failed. Please try again.');
        } finally {
            this.isTranslating = false;
            this.hideLoader();
        }
    }

    async translateText(text, targetLang) {
        // Clean the text first
        const cleanText = text.trim();
        if (!cleanText) return text;

        // Check cache first
        const cacheKey = `${cleanText}-${targetLang}`;
        if (this.translations.has(cacheKey)) {
            return this.translations.get(cacheKey);
        }

        // Determine source language
        const sourceLang = this.currentLang === 'ar' ? 'ar' : 'en';

        // If translating to the same language, return original
        if (sourceLang === targetLang) {
            return cleanText;
        }

        // Simple fallback translation for common terms if APIs fail
        const fallbackTranslations = {
            'ar-en': {
                'مطور برمجيات أول': 'Senior Software Developer',
                'بدء جلسة المقابلة': 'Start Interview Session',
                'إرسال المقابلة': 'Submit Interview',
                'الوقت المتبقي': 'Time Remaining',
                'تسجيل الشاشة نشط': 'Screen Recording Active',
                'انتهت الجلسة': 'Session Expired',
                'اكتب إجابتك هنا...': 'Type your answer here...'
            },
            'en-ar': {
                'Senior Software Developer': 'مطور برمجيات أول',
                'Start Interview Session': 'بدء جلسة المقابلة',
                'Submit Interview': 'إرسال المقابلة',
                'Time Remaining': 'الوقت المتبقي',
                'Screen Recording Active': 'تسجيل الشاشة نشط',
                'Session Expired': 'انتهت الجلسة',
                'Type your answer here...': 'اكتب إجابتك هنا...'
            }
        };

        const fallbackKey = `${sourceLang}-${targetLang}`;
        if (fallbackTranslations[fallbackKey] && fallbackTranslations[fallbackKey][cleanText]) {
            const fallbackResult = fallbackTranslations[fallbackKey][cleanText];
            this.translations.set(cacheKey, fallbackResult);
            return fallbackResult;
        }

        // Try translation APIs with better error handling
        const apis = [
            {
                name: 'mymemory',
                translate: async () => {
                    const response = await fetch(`https://api.mymemory.translated.net/get?q=${encodeURIComponent(cleanText)}&langpair=${sourceLang}|${targetLang}`);
                    if (!response.ok) throw new Error('API request failed');
                    const data = await response.json();
                    if (data.responseStatus === 200 && data.responseData && data.responseData.translatedText) {
                        return data.responseData.translatedText;
                    }
                    throw new Error('Invalid response format');
                }
            }
        ];

        for (const api of apis) {
            try {
                const translatedText = await api.translate();
                if (translatedText && translatedText !== cleanText && translatedText.toLowerCase() !== cleanText.toLowerCase()) {
                    this.translations.set(cacheKey, translatedText);
                    return translatedText;
                }
            } catch (error) {
                console.warn(`${api.name} translation failed:`, error);
                continue;
            }
        }

        console.warn('All translation methods failed for:', cleanText);
        return cleanText; // Return original text if all methods fail
    }

    applyTranslation(element, translatedText, isPlaceholder) {
        if (!element || !translatedText) return;

        // Add visual feedback
        element.classList.add('translating');

        setTimeout(() => {
            if (isPlaceholder) {
                element.placeholder = translatedText;
            } else {
                element.textContent = translatedText;
            }

            element.classList.remove('translating');
            element.classList.add('translated');

            setTimeout(() => {
                element.classList.remove('translated');
            }, 300);
        }, 100);
    }

    showLoader() {
        const overlay = document.getElementById('translateOverlay');
        if (!overlay) return;

        const loaderText = overlay.querySelector('.translate-text');
        const loaderSubtext = overlay.querySelector('.translate-subtext');

        if (loaderText && loaderSubtext) {
            if (this.currentLang === 'en') {
                loaderText.textContent = 'Translating Content...';
                loaderSubtext.textContent = 'Please wait while we translate the page';
            } else {
                loaderText.textContent = 'جاري ترجمة المحتوى...';
                loaderSubtext.textContent = 'يرجى الانتظار أثناء ترجمة الصفحة';
            }
        }

        overlay.style.display = 'flex';
        setTimeout(() => overlay.style.opacity = '1', 10);
    }

    hideLoader() {
        const overlay = document.getElementById('translateOverlay');
        if (!overlay) return;

        overlay.style.opacity = '0';
        setTimeout(() => overlay.style.display = 'none', 300);
    }

    // Initialize page with Arabic content on first load
    initializeArabicContent() {
        const elementsToTranslate = document.querySelectorAll('[data-translate], [data-translate-placeholder]');

        elementsToTranslate.forEach(element => {
            const key = element.getAttribute('data-translate') || element.getAttribute('data-translate-placeholder');

            if (jsMessages['ar'][key]) {
                if (element.hasAttribute('data-translate-placeholder')) {
                    element.placeholder = jsMessages['ar'][key];
                } else {
                    element.textContent = jsMessages['ar'][key];
                }
            }
        });
    }

    // Method to manually refresh original texts
    refreshOriginalTexts() {
        this.storeOriginalTexts();
    }
}

// Message translations with properly encoded Arabic
const jsMessages = {
    'ar': {
        'job-title': 'مطور برمجيات أول',
        'job-description': 'انضم إلى فريق التطوير الديناميكي لدينا الذي يعمل على الأنظمة الحكومية الحيوية. نحن نبحث عن مطورين ذوي خبرة في التقنيات الحديثة لمساعدتنا في بناء تطبيقات آمنة وقابلة للتوسع تخدم ملايين المواطنين.',
        'warning-title': '⚠️ إشعار مهم',
        'warning-text': 'بمجرد بدء جلسة المقابلة، ستحصل على 30 دقيقة بالضبط لإكمال جميع الأسئلة. سينتهي النموذج تلقائياً بعد هذا الوقت. يرجى التأكد من وجود اتصال إنترنت مستقر وبيئة هادئة قبل المتابعة.',
        'start-button': 'بدء جلسة المقابلة',
        'timer-text': 'الوقت المتبقي: 30:00',
        'recording-text': 'تسجيل الشاشة نشط',
        'submit-button': 'إرسال المقابلة',
        'expired-title': 'انتهت الجلسة',
        'expired-text': 'انتهت مهلة جلسة المقابلة. يرجى الاتصال بالموارد البشرية لإعادة الجدولة.',
        'placeholder1': 'اكتب إجابتك هنا...',
        'screen-recording-required': 'تسجيل الشاشة مطلوب',
        'screen-recording-text': 'نظام المقابلة يحتاج لتسجيل شاشتك لأغراض الأمان. هذا إجباري للمتابعة.',
        'cancel': 'إلغاء',
        'allow-screen-recording': 'السماح بتسجيل الشاشة',
        'screen-recording-started': 'بدأ تسجيل الشاشة',
        'screen-recording-active': 'شاشتك يتم تسجيلها الآن لأمان المقابلة.',
        'permission-denied': 'تم رفض الإذن',
        'permission-required': 'إذن تسجيل الشاشة مطلوب للمتابعة مع المقابلة.',
        'error': 'خطأ',
        'failed-initialize': 'فشل في تهيئة تسجيل الشاشة. يرجى المحاولة مرة أخرى.',
        'success': 'نجح!',
        'incomplete-form': 'نموذج غير مكتمل',
        'complete-required-fields': 'يرجى إكمال جميع الحقول المطلوبة بشكل صحيح.',
        'submitting-interview': 'جاري إرسال المقابلة...',
        'processing-responses': 'يرجى الانتظار أثناء معالجة إجاباتك.',
        'interview-submitted': 'تم إرسال المقابلة بنجاح!',
        'field-required': 'هذا الحقل مطلوب',
        'detailed-answer': 'يرجى تقديم إجابة أكثر تفصيلاً (5 أحرف كحد أدنى)',
        'time-remaining': 'الوقت المتبقي:',
        'session-expired': 'انتهت الجلسة',
        'session-timeout': 'انتهت مهلة جلسة المقابلة. يرجى الاتصال بالموارد البشرية لإعادة الجدولة.',
        'leave-warning': 'ستفقد تقدم المقابلة إذا غادرت هذه الصفحة.'
    },
    'en': {
        'job-title': 'Senior Software Developer',
        'job-description': 'Join our dynamic development team working on critical government systems. We\'re looking for experienced developers with expertise in modern web technologies to help build secure, scalable applications that serve millions of citizens.',
        'warning-title': '⚠️ Important Notice',
        'warning-text': 'Once you start the interview session, you will have exactly 30 minutes to complete all questions. The form will automatically expire after this time. Please ensure you have a stable internet connection and a quiet environment before proceeding.',
        'start-button': 'Start Interview Session',
        'timer-text': 'Time Remaining: 30:00',
        'recording-text': 'Screen Recording Active',
        'submit-button': 'Submit Interview',
        'expired-title': 'Session Expired',
        'expired-text': 'Your interview session has timed out. Please contact HR to reschedule.',
        'placeholder1': 'Type your answer here...',
        'screen-recording-required': 'Screen Recording Required',
        'screen-recording-text': 'Interview System needs to record your screen for security purposes. This is mandatory to continue.',
        'cancel': 'Cancel',
        'allow-screen-recording': 'Allow Screen Recording',
        'screen-recording-started': 'Screen Recording Started',
        'screen-recording-active': 'Your screen is now being recorded for interview security.',
        'permission-denied': 'Permission Denied',
        'permission-required': 'Screen recording permission is required to continue with the interview.',
        'error': 'Error',
        'failed-initialize': 'Failed to initialize screen recording. Please try again.',
        'success': 'Success',
        'incomplete-form': 'Incomplete Form',
        'complete-required-fields': 'Please complete all required fields correctly.',
        'submitting-interview': 'Submitting Interview...',
        'processing-responses': 'Please wait while we process your responses.',
        'interview-submitted': 'Interview submitted successfully!',
        'field-required': 'This field is required',
        'detailed-answer': 'Please provide a more detailed answer (minimum 5 characters)',
        'time-remaining': 'Time Remaining:',
        'session-expired': 'Session Expired',
        'session-timeout': 'Your interview session has timed out. Please contact HR to reschedule.',
        'leave-warning': 'You will lose your interview progress if you leave this page.'
    }
};

// Global function to get translated messages
function getTranslatedMessage(key) {
    const currentLang = translator ? translator.currentLang : 'ar';
    return jsMessages[currentLang][key] || jsMessages['ar'][key] || key;
}

// Initialize translator
let translator;

// Make sure this runs after DOM is loaded
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initTranslator);
} else {
    initTranslator();
}

function initTranslator() {
    if (!translator) {
        translator = new LiveTranslator();
    }
}
