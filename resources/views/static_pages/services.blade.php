@extends('layouts.app')

@section('header')
    <link href="{{ asset('styles/css/static_pages.css') }}" rel="stylesheet">
@endsection

@section('content')
    <section class="static-hero">
        <div class="container">
            <div class="static-hero-content">
                <h1 class="static-title">خدماتنا</h1>
                <p class="static-subtitle">حلول ذكية ومتطورة لجميع احتياجات التوظيف وفحص السير الذاتية</p>
            </div>
        </div>
    </section>

    <section class="services-overview">
        <div class="container">
            <div class="services-stats">
                <div class="stat-item">
                    <div class="stat-number">50,000+</div>
                    <div class="stat-label">سيرة ذاتية تم فحصها</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">95%</div>
                    <div class="stat-label">دقة التحليل</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">500+</div>
                    <div class="stat-label">شركة تثق بنا</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">10x</div>
                    <div class="stat-label">تسريع عملية التوظيف</div>
                </div>
            </div>
        </div>
    </section>

    <section class="main-services">
        <div class="container">
            <h2 class="section-title">خدماتنا الرئيسية</h2>
            <div class="services-grid">
                <div class="service-card featured">
                    <div class="service-icon">🤖</div>
                    <h3>فحص ذكي للسير الذاتية</h3>
                    <p>تحليل متقدم للسير الذاتية باستخدام الذكاء الاصطناعي مع تقارير مفصلة ودرجات تقييم دقيقة</p>
                    <ul class="service-features">
                        <li>فحص مئات السير في دقائق</li>
                        <li>تحليل المهارات والخبرات</li>
                        <li>ترتيب المرشحين حسب الملاءمة</li>
                        <li>تقارير مفصلة قابلة للتصدير</li>
                    </ul>
                    <div class="service-pricing">ابتداء من 0.5 ريال لكل سيرة</div>
                    <a href="{{ route('upload.form') }}" class="btn btn-primary">ابدأ الآن</a>
                </div>

                <div class="service-card">
                    <div class="service-icon">📊</div>
                    <h3>تحليلات متقدمة</h3>
                    <p>تحليلات عميقة لسوق التوظيف ومطابقة احتياجات الوظيفة مع المهارات المتاحة في السوق</p>
                    <ul class="service-features">
                        <li>تحليل اتجاهات السوق</li>
                        <li>معدلات الرواتب المتوقعة</li>
                        <li>تحليل المهارات النادرة</li>
                        <li>تقارير مقارنة بالسوق</li>
                    </ul>
                    <div class="service-pricing">باقات مخصصة</div>
                    <a href="{{ route('contact') }}" class="btn btn-secondary">استفسر الآن</a>
                </div>

                <div class="service-card">
                    <div class="service-icon">🎯</div>
                    <h3>فلترة ذكية متخصصة</h3>
                    <p>فلترة دقيقة للمرشحين باستخدام معايير مخصصة ومتطلبات الوظيفة الخاصة بشركتك</p>
                    <ul class="service-features">
                        <li>معايير تقييم مخصصة</li>
                        <li>فلترة متعددة المستويات</li>
                        <li>استبعاد تلقائي للغير مناسبين</li>
                        <li>ترشيح أفضل المرشحين</li>
                    </ul>
                    <div class="service-pricing">حسب المتطلبات</div>
                    <a href="{{ route('contact') }}" class="btn btn-secondary">تواصل معنا</a>
                </div>
            </div>
        </div>
    </section>

    <section class="specialized-services">
        <div class="container">
            <h2 class="section-title">خدمات متخصصة</h2>
            <div class="specialized-grid">
                <div class="specialized-item">
                    <div class="specialized-icon">🏢</div>
                    <h3>للشركات الكبيرة</h3>
                    <p>حلول مؤسسية مع تكامل كامل مع أنظمة إدارة الموارد البشرية</p>
                    <ul>
                        <li>تكامل مع أنظمة HR</li>
                        <li>واجهات برمجة مخصصة</li>
                        <li>دعم تقني مخصص</li>
                        <li>تدريب للفرق</li>
                    </ul>
                </div>

                <div class="specialized-item">
                    <div class="specialized-icon">🎓</div>
                    <h3>للجامعات ومراكز التدريب</h3>
                    <p>تقييم شامل لخريجي الجامعات والمتدربين لمساعدتهم في سوق العمل</p>
                    <ul>
                        <li>تحليل نقاط القوة والضعف</li>
                        <li>توجيهات للتطوير</li>
                        <li>مقارنة مع متطلبات السوق</li>
                        <li>تقارير للمؤسسات التعليمية</li>
                    </ul>
                </div>

                <div class="specialized-item">
                    <div class="specialized-icon">👔</div>
                    <h3>لشركات التوظيف</h3>
                    <p>أدوات متطورة لشركات التوظيف لتسريع عمليات البحث والاختيار</p>
                    <ul>
                        <li>معالجة مجمعة للسير</li>
                        <li>قوالب تقييم متخصصة</li>
                        <li>تصنيف حسب القطاعات</li>
                        <li>أسعار تفضيلية للكميات</li>
                    </ul>
                </div>

                <div class="specialized-item">
                    <div class="specialized-icon">🏛️</div>
                    <h3>للقطاع الحكومي</h3>
                    <p>حلول متوافقة مع متطلبات القطاع الحكومي ومعايير الأمان العالية</p>
                    <ul>
                        <li>امتثال كامل للأنظمة</li>
                        <li>أمان وخصوصية محسنة</li>
                        <li>تقارير مفصلة للجان التوظيف</li>
                        <li>دعم اللغة العربية الكامل</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <section class="service-process">
        <div class="container">
            <h2 class="section-title">كيف نعمل</h2>
            <div class="process-timeline">
                <div class="timeline-item">
                    <div class="timeline-number">1</div>
                    <div class="timeline-content">
                        <h3>تحديد المتطلبات</h3>
                        <p>نفهم احتياجاتك ومتطلبات الوظيفة المحددة</p>
                    </div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-number">2</div>
                    <div class="timeline-content">
                        <h3>رفع السير الذاتية</h3>
                        <p>رفع مجموعة السير الذاتية بصيغ مختلفة</p>
                    </div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-number">3</div>
                    <div class="timeline-content">
                        <h3>التحليل الذكي</h3>
                        <p>معالجة متقدمة باستخدام الذكاء الاصطناعي</p>
                    </div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-number">4</div>
                    <div class="timeline-content">
                        <h3>النتائج والتقارير</h3>
                        <p>تقارير شاملة مع ترتيب المرشحين والتوصيات</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="service-benefits">
        <div class="container">
            <h2 class="section-title">لماذا تختار مسار؟</h2>
            <div class="benefits-grid">
                <div class="benefit-item">
                    <div class="benefit-icon">⚡</div>
                    <h3>سرعة فائقة</h3>
                    <p>توفير 80% من الوقت المستغرق في فحص السير الذاتية يدوياً</p>
                </div>
                <div class="benefit-item">
                    <div class="benefit-icon">🎯</div>
                    <h3>دقة عالية</h3>
                    <p>خوارزميات متطورة تضمن دقة في التحليل تصل إلى 95%</p>
                </div>
                <div class="benefit-item">
                    <div class="benefit-icon">💰</div>
                    <h3>توفير في التكاليف</h3>
                    <p>تقليل تكاليف التوظيف بنسبة تصل إلى 60%</p>
                </div>
                <div class="benefit-item">
                    <div class="benefit-icon">🔒</div>
                    <h3>أمان متقدم</h3>
                    <p>حماية شاملة للبيانات مع عدم الاحتفاظ بالملفات</p>
                </div>
                <div class="benefit-item">
                    <div class="benefit-icon">🌐</div>
                    <h3>دعم متعدد اللغات</h3>
                    <p>معالجة السير الذاتية باللغتين العربية والإنجليزية</p>
                </div>
                <div class="benefit-item">
                    <div class="benefit-icon">📱</div>
                    <h3>سهولة الاستخدام</h3>
                    <p>واجهة بسيطة وسهلة تناسب جميع مستويات المستخدمين</p>
                </div>
            </div>
        </div>
    </section>

    <section class="pricing-preview" id="pricing-preview">
        <div class="container">
            <h2 class="section-title">باقات الأسعار</h2>
            <div class="pricing-cards">
                <div class="pricing-card">
                    <h3>الباقة الأساسية</h3>
                    <div class="price">
                        <span class="currency">ريال</span>
                        <span class="amount">199</span>
                        <span class="period">/شهرياً</span>
                    </div>
                    <ul class="features">
                        <li>حتى 500 سيرة شهرياً</li>
                        <li>تقارير أساسية</li>
                        <li>دعم بالبريد الإلكتروني</li>
                        <li>تصدير PDF</li>
                    </ul>
                    <a href="{{ route('contact') }}" class="btn btn-outline">اختر الباقة</a>
                </div>

                <div class="pricing-card featured">
                    <div class="popular-badge">الأكثر شيوعاً</div>
                    <h3>الباقة المتقدمة</h3>
                    <div class="price">
                        <span class="currency">ريال</span>
                        <span class="amount">499</span>
                        <span class="period">/شهرياً</span>
                    </div>
                    <ul class="features">
                        <li>حتى 2000 سيرة شهرياً</li>
                        <li>تقارير مفصلة</li>
                        <li>دعم هاتفي وبالبريد</li>
                        <li>تصدير متعدد الصيغ</li>
                        <li>تحليلات متقدمة</li>
                    </ul>
                    <a href="{{ route('contact') }}" class="btn btn-primary">اختر الباقة</a>
                </div>

                <div class="pricing-card">
                    <h3>الباقة المؤسسية</h3>
                    <div class="price">
                        <span class="currency">ريال</span>
                        <span class="amount">1299</span>
                        <span class="period">/شهرياً</span>
                    </div>
                    <ul class="features">
                        <li>سير ذاتية غير محدودة</li>
                        <li>تكامل مع أنظمة HR</li>
                        <li>دعم مخصص 24/7</li>
                        <li>واجهات برمجة مخصصة</li>
                        <li>تدريب للفريق</li>
                    </ul>
                    <a href="{{ route('contact') }}" class="btn btn-outline">تواصل معنا</a>
                </div>
            </div>
            <p class="pricing-note">جميع الباقات تشمل نسخة تجريبية مجانية لمدة 14 يوم</p>
        </div>
    </section>

    <section class="service-cta">
        <div class="container">
            <div class="cta-content">
                <h2>جاهز لتجربة مسار؟</h2>
                <p>ابدأ الآن واكتشف كيف يمكن لمسار تحسين عملية التوظيف في شركتك</p>
                <div class="cta-buttons">
                    <a href="{{ route('upload.form') }}" class="btn btn-primary">جرب مجاناً</a>
                    <a href="{{ route('contact') }}" class="btn btn-secondary">تحدث مع خبير</a>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
    <script src="{{ asset('styles/js/static_pages.js') }}"></script>
@endsection
