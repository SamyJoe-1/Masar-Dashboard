@extends('layouts.app')

@section('header')
    <link href="{{ asset('styles/css/static_pages.css') }}" rel="stylesheet">
@endsection

@section('content')
    <section class="static-hero">
        <div class="container">
            <div class="static-hero-content">
                <h1 class="static-title">المزيد عنا</h1>
                <p class="static-subtitle">نحن فريق من المتخصصين في الذكاء الاصطناعي والتوظيف، نسعى لثورة حقيقية في عالم اختيار المواهب</p>
            </div>
        </div>
    </section>

    <section class="about-intro">
        <div class="container">
            <div class="intro-content">
                <div class="intro-text">
                    <h2>قصة مسار</h2>
                    <p>في عالم يتسارع فيه النمو وتتزايد فيه احتياجات الشركات للمواهب المناسبة، وُلدت فكرة "مسار" من رؤية بسيطة وطموحة: جعل عملية التوظيف أكثر ذكاءً وكفاءة ودقة.</p>

                    <p>بدأت رحلتنا عندما لاحظنا أن الشركات تواجه تحديات كبيرة في فحص آلاف السير الذاتية يدوياً، مما يستغرق وقتاً طويلاً ويحمل مخاطر فقدان المرشحين المناسبين وسط هذا الكم الهائل من البيانات.</p>

                    <p>اليوم، تقف "مسار" كحل متطور يجمع بين قوة الذكاء الاصطناعي وفهم عميق لاحتياجات سوق العمل المحلي، لتقدم تجربة توظيف لا مثيل لها.</p>
                </div>
                <div class="intro-image">
                    <div class="image-placeholder">
                        <div class="placeholder-icon">🚀</div>
                        <p>رحلة التطوير والابتكار</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="mission-vision">
        <div class="container">
            <div class="mission-vision-grid">
                <div class="mission-card">
                    <div class="card-icon">🎯</div>
                    <h3>رسالتنا</h3>
                    <p>تمكين الشركات والمؤسسات من اتخاذ قرارات توظيف أذكى وأسرع من خلال تقنيات الذكاء الاصطناعي المتطورة، مع ضمان العدالة والشفافية في عمليات الاختيار.</p>
                </div>
                <div class="vision-card">
                    <div class="card-icon">🌟</div>
                    <h3>رؤيتنا</h3>
                    <p>أن نصبح الخيار الأول والأكثر موثوقية لجميع الشركات في المنطقة لفحص وتحليل السير الذاتية، وأن نساهم في بناء سوق عمل أكثر كفاءة ونزاهة.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="our-values">
        <div class="container">
            <h2 class="section-title">قيمنا الأساسية</h2>
            <div class="values-grid">
                <div class="value-item">
                    <div class="value-icon">🔍</div>
                    <h3>الدقة والجودة</h3>
                    <p>نلتزم بأعلى معايير الدقة في التحليل ونسعى باستمرار لتحسين جودة نتائجنا</p>
                </div>
                <div class="value-item">
                    <div class="value-icon">🤝</div>
                    <h3>الثقة والشفافية</h3>
                    <p>نبني علاقات قائمة على الثقة المتبادلة والوضوح الكامل في جميع تعاملاتنا</p>
                </div>
                <div class="value-item">
                    <div class="value-icon">⚡</div>
                    <h3>الابتكار المستمر</h3>
                    <p>نواكب أحدث التطورات التقنية ونطور حلولنا باستمرار لتلبية احتياجات عملائنا</p>
                </div>
                <div class="value-item">
                    <div class="value-icon">🔒</div>
                    <h3>الأمان والخصوصية</h3>
                    <p>نضع حماية بيانات عملائنا والمرشحين في المقدمة ونطبق أعلى معايير الأمان</p>
                </div>
                <div class="value-item">
                    <div class="value-icon">🌍</div>
                    <h3>التأثير الإيجابي</h3>
                    <p>نسعى لخلق تأثير إيجابي على سوق العمل وتحسين تجربة التوظيف للجميع</p>
                </div>
                <div class="value-item">
                    <div class="value-icon">📈</div>
                    <h3>التميز في الخدمة</h3>
                    <p>نقدم خدمة عملاء استثنائية ونضع رضا عملائنا في قمة أولوياتنا</p>
                </div>
            </div>
        </div>
    </section>

    <section class="our-team">
        <div class="container">
            <h2 class="section-title">فريق العمل</h2>
            <p class="section-subtitle">مجموعة من الخبراء والمتخصصين في مجالات متنوعة</p>

            <div class="team-categories">
                <div class="team-category">
                    <h3>فريق التطوير والتقنية</h3>
                    <p>مهندسون متخصصون في الذكاء الاصطناعي ومعالجة اللغات الطبيعية وتطوير الأنظمة</p>
                    <div class="team-stats">
                        <span class="stat">10+ مطورين</span>
                        <span class="stat">15+ سنة خبرة متوسطة</span>
                        <span class="stat">5+ خبراء AI</span>
                    </div>
                </div>

                <div class="team-category">
                    <h3>فريق الموارد البشرية</h3>
                    <p>خبراء في التوظيف وإدارة المواهب يفهمون تحديات السوق المحلي والعالمي</p>
                    <div class="team-stats">
                        <span class="stat">8+ خبراء HR</span>
                        <span class="stat">20+ سنة خبرة مجتمعة</span>
                        <span class="stat">500+ شركة تم التعامل معها</span>
                    </div>
                </div>

                <div class="team-category">
                    <h3>فريق خدمة العملاء</h3>
                    <p>متخصصون في تقديم الدعم وضمان أفضل تجربة للعملاء على مدار الساعة</p>
                    <div class="team-stats">
                        <span class="stat">6+ مستشارين</span>
                        <span class="stat">دعم 24/7</span>
                        <span class="stat">98% رضا العملاء</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="our-achievements">
        <div class="container">
            <h2 class="section-title">إنجازاتنا</h2>
            <div class="achievements-timeline">
                <div class="achievement-item">
                    <div class="achievement-year">2023</div>
                    <div class="achievement-content">
                        <h3>إطلاق مسار</h3>
                        <p>انطلاق المنصة رسمياً بعد عامين من البحث والتطوير</p>
                    </div>
                </div>
                <div class="achievement-item">
                    <div class="achievement-year">2024</div>
                    <div class="achievement-content">
                        <h3>10,000 سيرة ذاتية</h3>
                        <p>وصلنا لمعالجة أول 10,000 سيرة ذاتية بنجاح</p>
                    </div>
                </div>
                <div class="achievement-item">
                    <div class="achievement-year">2024</div>
                    <div class="achievement-content">
                        <h3>100+ شركة</h3>
                        <p>انضمام أكثر من 100 شركة لقائمة عملائنا المميزين</p>
                    </div>
                </div>
                <div class="achievement-item">
                    <div class="achievement-year">2025</div>
                    <div class="achievement-content">
                        <h3>التوسع الإقليمي</h3>
                        <p>بداية التوسع لخدمة الشركات في دول الخليج</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="technology-stack">
        <div class="container">
            <h2 class="section-title">التقنيات المستخدمة</h2>
            <div class="tech-categories">
                <div class="tech-category">
                    <h3>الذكاء الاصطناعي</h3>
                    <div class="tech-items">
                        <span class="tech-item">معالجة اللغات الطبيعية</span>
                        <span class="tech-item">التعلم الآلي</span>
                        <span class="tech-item">الشبكات العصبية</span>
                        <span class="tech-item">التحليل الدلالي</span>
                    </div>
                </div>
                <div class="tech-category">
                    <h3>الأمان والحماية</h3>
                    <div class="tech-items">
                        <span class="tech-item">تشفير AES-256</span>
                        <span class="tech-item">حماية الخوادم</span>
                        <span class="tech-item">النسخ الاحتياطي الآمن</span>
                        <span class="tech-item">مراقبة مستمرة</span>
                    </div>
                </div>
                <div class="tech-category">
                    <h3>البنية التحتية</h3>
                    <div class="tech-items">
                        <span class="tech-item">حوسبة سحابية</span>
                        <span class="tech-item">معالجة متوازية</span>
                        <span class="tech-item">قواعد بيانات متقدمة</span>
                        <span class="tech-item">واجهات برمجة RESTful</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="join-us">
        <div class="container">
            <div class="join-content">
                <h2>انضم إلى رحلة النجاح</h2>
                <p>هل تبحث عن حلول ذكية لتحسين عملية التوظيف في شركتك؟ نحن هنا لمساعدتك في تحقيق أهدافك</p>
                <div class="join-stats">
                    <div class="join-stat">
                        <div class="stat-number">500+</div>
                        <div class="stat-label">شركة سعيدة</div>
                    </div>
                    <div class="join-stat">
                        <div class="stat-number">50,000+</div>
                        <div class="stat-label">سيرة تم فحصها</div>
                    </div>
                    <div class="join-stat">
                        <div class="stat-number">95%</div>
                        <div class="stat-label">دقة النتائج</div>
                    </div>
                    <div class="join-stat">
                        <div class="stat-number">24/7</div>
                        <div class="stat-label">دعم فني</div>
                    </div>
                </div>
                <div class="join-buttons">
                    <a href="{{ route('upload.form') }}" class="btn btn-primary">جرب مجاناً</a>
                    <a href="{{ route('contact') }}" class="btn btn-secondary">تواصل معنا</a>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
    <script src="{{ asset('styles/js/static_pages.js') }}"></script>
@endsection
