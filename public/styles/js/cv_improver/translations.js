// translations.js - Multi-language support
const translations = {
    en: {
        // File upload errors
        'error.invalid_file_type': 'Invalid file type. Please upload PDF only.',
        'error.file_size_limit': 'File size exceeds 5MB limit.',
        'error.improvement_failed': 'An error occurred during improvement. Please try again.',
        'error.no_improvement_data': 'No improvement data received from the API',
        'error.no_cv_data': 'No CV data available from previous step',
        'error.cv_data_empty': 'CV data is empty or invalid',
        'error.pdf_rendering_failed': 'PDF rendering failed: ',
        'error.invalid_response': 'Invalid response from PDF API - no CV data found',
        'error.cv_improvement_failed': 'CV improvement failed: ',
        'error.failed_to_render': 'Failed to render PDF: ',
        'error.failed_to_improve': 'Failed to improve CV: ',
        'error.cv_content_not_found': 'CV content not found',
        'error.pdf_generation_failed': 'PDF generation failed: ',
        'error.download_failed': 'Failed to download CV: ',

        // Success messages
        'success.cv_downloaded': 'Your improved CV has been downloaded',

        // Progress messages
        'progress.generating_pdf': 'Generating PDF...',
        'progress.please_wait': 'Please wait while we create your PDF',

        // UI Labels
        'label.your_name': 'Your Name',
        'label.email': 'Email',
        'label.phone': 'Phone',
        'label.location': 'Location',
        'label.linkedin': 'LinkedIn',

        // Sections
        'section.summary': 'Summary',
        'section.experience': 'Experience',
        'section.education': 'Education',
        'section.skills': 'Skills',
        'section.projects': 'Projects',
        'section.certifications': 'Certifications',
        'section.languages': 'Languages',
        'section.achievements': 'Achievements',

        'section_key.summary': 'Summary',
        'section_key.experience': 'Experience',
        'section_key.education': 'Education',
        'section_key.skills': 'Skills',
        'section_key.projects': 'Projects',
        'section_key.certifications': 'Certifications',
        'section_key.achievements': 'Achievements',
        'section_key.languages': 'Languages',

        // Improvements
        'improvement.missing_skills': 'Missing Skills',
        'improvement.consider_adding': 'Consider adding: ',
        'improvement.recommended_certifications': 'Recommended Certifications',
        'improvement.recommended_projects': 'Recommended Projects',
        'improvement.experience_highlights': 'Experience Highlights',
        'improvement.ats_tips': 'ATS Optimization Tips',
        'improvement.career_advice': 'Career Advice',
        'improvement.no_improvements': 'No specific improvements suggested',
        'improvement.no_data_available': 'No improvements data available',

        // Preview
        'preview.error_no_data': 'Error: No CV data to display',

        // Modal
        'modal.error': 'Error',
        'modal.success': 'Success!'
    },
    ar: {
        // File upload errors
        'error.invalid_file_type': 'نوع ملف غير صالح. يرجى تحميل ملف PDF فقط.',
        'error.file_size_limit': 'حجم الملف يتجاوز حد 5 ميجابايت.',
        'error.improvement_failed': 'حدث خطأ أثناء التحسين. يرجى المحاولة مرة أخرى.',
        'error.no_improvement_data': 'لم يتم استلام بيانات التحسين من الخادم',
        'error.no_cv_data': 'لا توجد بيانات سيرة ذاتية متاحة من الخطوة السابقة',
        'error.cv_data_empty': 'بيانات السيرة الذاتية فارغة أو غير صالحة',
        'error.pdf_rendering_failed': 'فشل عرض ملف PDF: ',
        'error.invalid_response': 'استجابة غير صالحة من خادم PDF - لم يتم العثور على بيانات السيرة الذاتية',
        'error.cv_improvement_failed': 'فشل تحسين السيرة الذاتية: ',
        'error.failed_to_render': 'فشل في عرض PDF: ',
        'error.failed_to_improve': 'فشل في تحسين السيرة الذاتية: ',
        'error.cv_content_not_found': 'لم يتم العثور على محتوى السيرة الذاتية',
        'error.pdf_generation_failed': 'فشل إنشاء ملف PDF: ',
        'error.download_failed': 'فشل تنزيل السيرة الذاتية: ',

        // Success messages
        'success.cv_downloaded': 'تم تنزيل سيرتك الذاتية المحسنة',

        // Progress messages
        'progress.generating_pdf': 'جاري إنشاء ملف PDF...',
        'progress.please_wait': 'يرجى الانتظار بينما نقوم بإنشاء ملف PDF الخاص بك',

        // UI Labels
        'label.your_name': 'اسمك',
        'label.email': 'البريد الإلكتروني',
        'label.phone': 'الهاتف',
        'label.location': 'الموقع',
        'label.linkedin': 'لينكد إن',

        // Sections
        'section.summary': 'الملخص',
        'section.experience': 'الخبرة',
        'section.education': 'التعليم',
        'section.skills': 'المهارات',
        'section.projects': 'المشاريع',
        'section.certifications': 'الشهادات',
        'section.languages': 'اللغات',
        'section.achievements': 'الإنجازات',
        'section.Achievements': 'الإنجازات',

        'section_key.summary': 'الملخص',
        'section_key.experience': 'الخبرة',
        'section_key.education': 'التعليم',
        'section_key.skills': 'المهارات',
        'section_key.projects': 'المشاريع',
        'section_key.certifications': 'الشهادات',
        'section_key.achievements': 'الإنجازات',
        'section_key.languages': 'اللغات',

        // Improvements
        'improvement.missing_skills': 'المهارات المفقودة',
        'improvement.consider_adding': 'فكر في إضافة: ',
        'improvement.recommended_certifications': 'الشهادات الموصى بها',
        'improvement.recommended_projects': 'المشاريع الموصى بها',
        'improvement.experience_highlights': 'أبرز نقاط الخبرة',
        'improvement.ats_tips': 'نصائح تحسين ATS',
        'improvement.career_advice': 'نصائح مهنية',
        'improvement.no_improvements': 'لا توجد تحسينات محددة مقترحة',
        'improvement.no_data_available': 'لا توجد بيانات تحسينات متاحة',

        // Preview
        'preview.error_no_data': 'خطأ: لا توجد بيانات سيرة ذاتية للعرض',

        // Modal
        'modal.error': 'خطأ',
        'modal.success': 'نجح!'
    }
};

// Get current language from meta tag
const currentLang = document.querySelector('meta[name="locale"]')?.content || 'en';

// Translation helper function
function t(key) {
    return translations[currentLang][key] || translations['en'][key] || key;
}

// Export for use in other files
window.t = t;
window.translations = translations;
window.currentLang = currentLang;
