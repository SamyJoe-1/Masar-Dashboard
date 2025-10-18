// translation.js

window.translations = {
    en: {
        // Notifications
        please_login: 'Please log in to save your CV',
        template_selection_required: 'Please select a template first!',
        failed_to_load_templates: 'Failed to load templates',
        failed_to_load_template: 'Failed to load template',
        cv_saved_successfully: 'CV saved successfully!',
        failed_to_save_cv: 'Failed to save CV',
        please_fill_name: 'Please fill in your first and last name',
        failed_to_finalize_cv: 'Failed to finalize CV',
        pdf_downloaded_successfully: 'PDF downloaded successfully!',
        failed_to_generate_pdf: 'Failed to generate PDF',
        image_downloaded_successfully: 'Image downloaded successfully!',
        failed_to_generate_image: 'Failed to generate image',
        content_improved_successfully: 'Content improved successfully! 🎉',
        failed_to_improve_content: 'Failed to improve content',

        // Modals
        unfinished_draft_found: 'Unfinished Draft Found',
        unfinished_draft_text: 'You have an unfinished draft for this template. Continue editing?',
        saved_draft_found: 'Saved Draft Found',
        saved_draft_text: 'You have a saved draft for this template. Continue editing?',
        continue: 'Continue',
        start_fresh: 'Start Fresh',
        your_cv_is_ready: 'Your CV is Ready!',
        what_would_you_like: 'What would you like to do?',
        download_pdf: 'Download PDF',
        view_on_profile: 'View on Profile',
        view_all_cvs: 'View All CVs',

        // Loading
        building_cv: 'Building your amazing CV...',
        improving: 'Improving...',

        // Preview
        your_name: 'Your Name',
        your_job_title: 'Your Job Title',
        position: 'Position',
        company: 'Company',
        present: 'Present',
        degree: 'Degree',
        school: 'School',
        course: 'Course',
        institution: 'Institution',
        experienced: 'Experienced',

        // Sections
        contact: 'CONTACT',
        skills: 'SKILLS',
        languages: 'LANGUAGES',
        professional_summary: 'PROFESSIONAL SUMMARY',
        experience: 'EXPERIENCE',
        education: 'EDUCATION',
        courses: 'COURSES',
        hobbies: 'HOBBIES',

        // Errors
        server_error: 'Server error',
        server_returned_html: 'Server returned HTML instead of JSON. Status',
        could_not_add_avatar: 'Could not add avatar',
        no_draft_found: 'No draft found in database',
        handlePageOverflow_warning: '⚠️ handlePageOverflow hit max iterations - possible infinite loop prevented'
    },

    ar: {
        // Notifications
        please_login: 'يرجى تسجيل الدخول لحفظ سيرتك الذاتية',
        template_selection_required: 'يرجى اختيار قالب أولاً!',
        failed_to_load_templates: 'فشل تحميل القوالب',
        failed_to_load_template: 'فشل تحميل القالب',
        cv_saved_successfully: 'تم حفظ السيرة الذاتية بنجاح!',
        failed_to_save_cv: 'فشل حفظ السيرة الذاتية',
        please_fill_name: 'يرجى ملء الاسم الأول والأخير',
        failed_to_finalize_cv: 'فشل إنهاء السيرة الذاتية',
        pdf_downloaded_successfully: 'تم تنزيل PDF بنجاح!',
        failed_to_generate_pdf: 'فشل إنشاء PDF',
        image_downloaded_successfully: 'تم تنزيل الصورة بنجاح!',
        failed_to_generate_image: 'فشل إنشاء الصورة',
        content_improved_successfully: 'تم تحسين المحتوى بنجاح! 🎉',
        failed_to_improve_content: 'فشل تحسين المحتوى',

        // Modals
        unfinished_draft_found: 'تم العثور على مسودة غير مكتملة',
        unfinished_draft_text: 'لديك مسودة غير مكتملة لهذا القالب. هل تريد متابعة التحرير؟',
        saved_draft_found: 'تم العثور على مسودة محفوظة',
        saved_draft_text: 'لديك مسودة محفوظة لهذا القالب. هل تريد متابعة التحرير؟',
        continue: 'متابعة',
        start_fresh: 'البدء من جديد',
        your_cv_is_ready: 'سيرتك الذاتية جاهزة!',
        what_would_you_like: 'ماذا تريد أن تفعل؟',
        download_pdf: 'تحميل PDF',
        view_on_profile: 'عرض في الملف الشخصي',
        view_all_cvs: 'عرض جميع السير الذاتية',

        // Loading
        building_cv: 'جاري بناء سيرتك الذاتية الرائعة...',
        improving: 'جاري التحسين...',

        // Preview
        your_name: 'اسمك',
        // your_job_title: 'مسماك الوظيفي',
        your_job_title: 'Job Title',
        position: 'المنصب',
        company: 'الشركة',
        present: 'حتى الآن',
        degree: 'الدرجة',
        school: 'المدرسة',
        course: 'الدورة',
        institution: 'المؤسسة',
        experienced: 'خبير',

        // Sections
        // contact: 'التواصل',
        // skills: 'المهارات',
        // languages: 'اللغات',
        // professional_summary: 'الملخص المهني',
        // experience: 'الخبرة',
        // education: 'التعليم',
        // courses: 'الدورات',
        // hobbies: 'الهوايات',

        // Errors
        server_error: 'خطأ في الخادم',
        server_returned_html: 'أرجع الخادم HTML بدلاً من JSON. الحالة',
        could_not_add_avatar: 'تعذر إضافة الصورة الشخصية',
        no_draft_found: 'لم يتم العثور على مسودة في قاعدة البيانات',
        handlePageOverflow_warning: '⚠️ تم الوصول إلى الحد الأقصى للتكرارات - تم منع حلقة لا نهائية محتملة'
    }
};

// Helper function to get translation
window.lang = function(key) {
    const locale = window.locale || 'en';
    return window.translations[locale][key] || key;
};

// Shorthand version
window.__ = window.lang;
