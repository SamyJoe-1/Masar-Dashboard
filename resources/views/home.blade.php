@extends('layouts.app')

@section('content')
    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <div class="hero-text">
                    <h1 class="hero-title">فحص ذكي للسير الذاتية بتقنية الذكاء الاصطناعي</h1>
                    <p class="hero-subtitle">ارفع مئات السير الذاتية واحصل على تقرير شامل بالمرشحين المقبولين والمرفوضين مع تحليل مفصل لكل ملف</p>
                    <div class="cta-buttons">
                        <a href="{{ route('upload.form') }}" class="btn btn-primary">ابدأ المسح المجاني</a>
                        <a href="{{ route('register') }}" class="btn btn-secondary">انضم الينا</a>
                    </div>
                </div>
                <div class="hero-dashboard">
                    <div class="dashboard-header">
                        <h3>لوحة المسح الذكي</h3>
                        <span>🚀</span>
                    </div>
                    <div class="upload-zone" onclick="window.location.href = '{{ route('upload.form') }}'">
                        <div class="upload-icon">📄</div>
                        <h4>اسحب وأفلت السير الذاتية هنا</h4>
                        <p>أو انقر للتصفح (PDF, DOC, DOCX)</p>
                    </div>
                    <div class="stats-grid">
                        <div class="stat-card">
                            <span class="stat-number">156</span>
                            <span class="stat-label">تم المسح</span>
                        </div>
                        <div class="stat-card">
                            <span class="stat-number">89</span>
                            <span class="stat-label">مقبول</span>
                        </div>
                        <div class="stat-card">
                            <span class="stat-number">67</span>
                            <span class="stat-label">مرفوض</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="features" id="features">
        <div class="container">
            <h2 class="section-title">مميزات استثنائية</h2>
            <p class="section-subtitle">تقنيات متطورة لفحص وتحليل السير الذاتية بدقة عالية</p>

            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">🤖</div>
                    <h3 class="feature-title">ذكاء اصطناعي متقدم</h3>
                    <p class="feature-description">خوارزميات تعلم آلة متطورة تحلل السير الذاتية بدقة تصل إلى 95% مع فهم السياق والمهارات المطلوبة</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">⚡</div>
                    <h3 class="feature-title">معالجة سريعة</h3>
                    <p class="feature-description">فحص مئات السير الذاتية في دقائق معدودة مع تقارير مفصلة فورية وترتيب تلقائي للمرشحين</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">📊</div>
                    <h3 class="feature-title">تقارير شاملة</h3>
                    <p class="feature-description">تحليلات عميقة تشمل نقاط القوة والضعف، مطابقة المهارات، والتوصيات المخصصة لكل مرشح</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🌐</div>
                    <h3 class="feature-title">دعم متعدد اللغات</h3>
                    <p class="feature-description">يتعامل مع السير الذاتية باللغة العربية والإنجليزية بنفس الدقة مع فهم المصطلحات المحلية</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🔒</div>
                    <h3 class="feature-title">أمان وخصوصية</h3>
                    <p class="feature-description">تشفير متقدم لحماية بيانات المرشحين مع ضمان الخصوصية التامة وعدم تخزين المعلومات الحساسة</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🎯</div>
                    <h3 class="feature-title">مطابقة دقيقة</h3>
                    <p class="feature-description">مطابقة ذكية للمهارات والخبرات مع متطلبات الوظيفة وترشيح أفضل المرشحين تلقائياً</p>
                </div>
            </div>
        </div>
    </section>

    <section class="process" id="process">
        <div class="container">
            <h2 class="section-title">كيف يعمل النظام؟</h2>
            <p class="section-subtitle">ثلاث خطوات بسيطة للحصول على أفضل المرشحين</p>

            <div class="process-steps">
                <div class="process-step">
                    <div class="step-number">1</div>
                    <h3 class="step-title">ارفع السير الذاتية</h3>
                    <p class="step-description">قم برفع مجموعة السير الذاتية بصيغ PDF أو Word، يمكن رفع مئات الملفات دفعة واحدة</p>
                </div>
                <div class="process-step">
                    <div class="step-number">2</div>
                    <h3 class="step-title">المعالجة الذكية</h3>
                    <p class="step-description">النظام يحلل كل سيرة ذاتية باستخدام الذكاء الاصطناعي ويقارنها بمعايير الوظيفة المحددة</p>
                </div>
                <div class="process-step">
                    <div class="step-number">3</div>
                    <h3 class="step-title">احصل على التقرير</h3>
                    <p class="step-description">تقرير شامل بالمرشحين مرتبين حسب الأولوية مع تحليل مفصل لكل مرشح ونقاط القوة والضعف</p>
                </div>
            </div>
        </div>
    </section>
@endsection
