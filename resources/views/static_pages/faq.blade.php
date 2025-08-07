@extends('layouts.app')

@section('header')
    <link href="{{ asset('styles/css/static_pages.css') }}" rel="stylesheet">
@endsection

@section('content')
    <section class="static-hero">
        <div class="container">
            <div class="static-hero-content">
                <h1 class="static-title">الأسئلة الشائعة</h1>
                <p class="static-subtitle">إجابات على أكثر الأسئلة شيوعاً حول منصة مسار لفحص السير الذاتية</p>
            </div>
        </div>
    </section>

    <section class="faq-content">
        <div class="container">
            <div class="faq-search">
                <input type="text" placeholder="ابحث في الأسئلة الشائعة..." class="search-input">
                <button class="search-btn">🔍</button>
            </div>

            <div class="faq-categories">
                <button class="category-btn active" data-category="all">جميع الأسئلة</button>
                <button class="category-btn" data-category="general">عام</button>
                <button class="category-btn" data-category="technical">تقني</button>
                <button class="category-btn" data-category="pricing">الأسعار</button>
                <button class="category-btn" data-category="security">الأمان</button>
            </div>

            <div class="faq-sections">
                <!-- General Questions -->
                <div class="faq-section" data-category="general">
                    <h2 class="faq-section-title">أسئلة عامة</h2>

                    <div class="faq-item">
                        <div class="faq-question">
                            <h3>ما هي منصة مسار وكيف تعمل؟</h3>
                            <span class="faq-toggle">+</span>
                        </div>
                        <div class="faq-answer">
                            <p>مسار هي منصة ذكية تستخدم تقنيات الذكاء الاصطناعي لفحص وتحليل السير الذاتية. تقوم المنصة برفع مئات السير الذاتية، تحليلها تلقائياً، ومطابقتها مع متطلبات الوظيفة لتقديم تقرير شامل بأفضل المرشحين مرتبين حسب الملاءمة.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            <h3>كم من الوقت تستغرق عملية فحص السير الذاتية؟</h3>
                            <span class="faq-toggle">+</span>
                        </div>
                        <div class="faq-answer">
                            <p>عادة ما تستغرق عملية فحص مئات السير الذاتية من 5-10 دقائق فقط، اعتماداً على عدد الملفات المرفوعة وحجمها. المنصة مصممة للسرعة والكفاءة العالية.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            <h3>ما هي أنواع الملفات المدعومة؟</h3>
                            <span class="faq-toggle">+</span>
                        </div>
                        <div class="faq-answer">
                            <p>المنصة تدعم الصيغ التالية: PDF، DOC، DOCX. هذه هي أكثر صيغ السير الذاتية شيوعاً واستخداماً في سوق العمل.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            <h3>هل يمكن فحص السير الذاتية باللغة الإنجليزية؟</h3>
                            <span class="faq-toggle">+</span>
                        </div>
                        <div class="faq-answer">
                            <p>نعم، المنصة تدعم فحص السير الذاتية باللغتين العربية والإنجليزية بنفس الدقة والكفاءة. كما تتعامل مع المصطلحات المحلية والعالمية في كلا اللغتين.</p>
                        </div>
                    </div>
                </div>

                <!-- Technical Questions -->
                <div class="faq-section" data-category="technical">
                    <h2 class="faq-section-title">الأسئلة التقنية</h2>

                    <div class="faq-item">
                        <div class="faq-question">
                            <h3>ما هي دقة نتائج التحليل؟</h3>
                            <span class="faq-toggle">+</span>
                        </div>
                        <div class="faq-answer">
                            <p>تصل دقة نظام التحليل إلى 95% بفضل استخدام خوارزميات التعلم الآلي المتطورة. النظام يتحسن باستمرار من خلال التعلم من البيانات والتحديثات المنتظمة.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            <h3>كيف يتم تحليل المهارات والخبرات؟</h3>
                            <span class="faq-toggle">+</span>
                        </div>
                        <div class="faq-answer">
                            <p>يستخدم النظام تقنيات معالجة اللغات الطبيعية (NLP) لاستخراج وتحليل المهارات والخبرات من النصوص، ثم يقارنها بمتطلبات الوظيفة ويحسب درجة المطابقة لكل مرشح.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            <h3>هل يمكن تخصيص معايير التقييم؟</h3>
                            <span class="faq-toggle">+</span>
                        </div>
                        <div class="faq-answer">
                            <p>نعم، يمكن تخصيص معايير التقييم حسب متطلبات كل وظيفة. يمكن تحديد المهارات المطلوبة، سنوات الخبرة، المؤهلات التعليمية، وأي معايير أخرى خاصة بالوظيفة.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            <h3>ماذا لو كانت السيرة الذاتية غير واضحة أو معقدة التنسيق؟</h3>
                            <span class="faq-toggle">+</span>
                        </div>
                        <div class="faq-answer">
                            <p>النظام مصمم للتعامل مع تنسيقات مختلفة من السير الذاتية. في حالة وجود صعوبة في قراءة ملف معين، سيتم تمييزه في التقرير مع توضيح نوع المشكلة.</p>
                        </div>
                    </div>
                </div>

                <!-- Pricing Questions -->
                <div class="faq-section" data-category="pricing">
                    <h2 class="faq-section-title">الأسعار والباقات</h2>

                    <div class="faq-item">
                        <div class="faq-question">
                            <h3>هل تتوفر نسخة تجريبية مجانية؟</h3>
                            <span class="faq-toggle">+</span>
                        </div>
                        <div class="faq-answer">
                            <p>نعم، نوفر فحص مجاني لعدد محدود من السير الذاتية حتى تتمكن من تجربة المنصة وتقييم جودة النتائج قبل الاشتراك في الباقات المدفوعة.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            <h3>ما هي الباقات المتاحة؟</h3>
                            <span class="faq-toggle">+</span>
                        </div>
                        <div class="faq-answer">
                            <p>نوفر عدة باقات تناسب احتياجات مختلفة: الباقة الأساسية للشركات الصغيرة، الباقة المتقدمة للشركات المتوسطة، والباقة المؤسسية للشركات الكبيرة والمؤسسات الحكومية.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            <h3>هل توجد خصومات للاستخدام المكثف؟</h3>
                            <span class="faq-toggle">+</span>
                        </div>
                        <div class="faq-answer">
                            <p>نعم، نقدم خصومات تدريجية للشركات التي تستخدم المنصة بشكل مكثف. كلما زاد عدد السير الذاتية المفحوصة شهرياً، كلما قل السعر لكل فحص.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            <h3>هل يمكن إلغاء الاشتراك في أي وقت؟</h3>
                            <span class="faq-toggle">+</span>
                        </div>
                        <div class="faq-answer">
                            <p>نعم، يمكن إلغاء الاشتراك في أي وقت دون أي رسوم إضافية. سيظل الاشتراك فعالاً حتى نهاية الفترة المدفوعة ثم سيتم إيقافه تلقائياً.</p>
                        </div>
                    </div>
                </div>

                <!-- Security Questions -->
                <div class="faq-section" data-category="security">
                    <h2 class="faq-section-title">الأمان والخصوصية</h2>

                    <div class="faq-item">
                        <div class="faq-question">
                            <h3>هل البيانات آمنة ومحمية؟</h3>
                            <span class="faq-toggle">+</span>
                        </div>
                        <div class="faq-answer">
                            <p>نعم، نستخدم أحدث تقنيات التشفير (AES-256) لحماية جميع البيانات. كما أن جميع الخوادم محمية ومعتمدة مع تطبيق أعلى معايير الأمان العالمية.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            <h3>هل تحتفظون بنسخ من السير الذاتية؟</h3>
                            <span class="faq-toggle">+</span>
                        </div>
                        <div class="faq-answer">
                            <p>لا، نحن لا نحتفظ بأي نسخ من السير الذاتية بعد انتهاء عملية التحليل. جميع الملفات يتم حذفها فوراً من الخوادم بعد إنتاج التقرير النهائي.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            <h3>هل تشاركون البيانات مع أطراف ثالثة؟</h3>
                            <span class="faq-toggle">+</span>
                        </div>
                        <div class="faq-answer">
                            <p>لا، نحن لا نشارك أي بيانات مع أطراف ثالثة لأغراض تجارية أو تسويقية. البيانات تستخدم فقط لتقديم الخدمة المطلوبة وتبقى سرية تماماً.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            <h3>ما هي الضمانات الأمنية المطبقة؟</h3>
                            <span class="faq-toggle">+</span>
                        </div>
                        <div class="faq-answer">
                            <p>نطبق عدة طبقات أمنية: تشفير البيانات، جدران حماية متقدمة، مراقبة مستمرة للأنظمة، نسخ احتياطي آمن، والتحكم الصارم في الوصول للبيانات.</p>
                        </div>
                    </div>
                </div>

                <!-- Additional Questions -->
                <div class="faq-section" data-category="general">
                    <h2 class="faq-section-title">أسئلة إضافية</h2>

                    <div class="faq-item">
                        <div class="faq-question">
                            <h3>كيف يمكن تصدير النتائج؟</h3>
                            <span class="faq-toggle">+</span>
                        </div>
                        <div class="faq-answer">
                            <p>يمكن تصدير التقارير بصيغ مختلفة: PDF للعرض والطباعة، Excel للتحليل الإضافي، أو JSON للتكامل مع الأنظمة الأخرى.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            <h3>هل يمكن دمج المنصة مع أنظمة إدارة الموارد البشرية؟</h3>
                            <span class="faq-toggle">+</span>
                        </div>
                        <div class="faq-answer">
                            <p>نعم، نوفر واجهات برمجة تطبيقات (APIs) للتكامل مع أنظمة إدارة الموارد البشرية الشائعة. يمكن للشركات ربط المنصة بأنظمتها الحالية.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            <h3>ما نوع الدعم التقني المقدم؟</h3>
                            <span class="faq-toggle">+</span>
                        </div>
                        <div class="faq-answer">
                            <p>نقدم دعم تقني شامل عبر البريد الإلكتروني والهاتف والدردشة المباشرة. فريق الدعم متاح خلال ساعات العمل الرسمية ويتم الرد على الاستفسارات خلال 24 ساعة.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="faq-cta">
                <h3>لم تجد إجابة سؤالك؟</h3>
                <p>تواصل معنا وسنكون سعداء بالمساعدة</p>
                <a href="{{ route('contact') }}" class="btn btn-primary">تواصل معنا</a>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
    <script src="{{ asset('styles/js/static_pages.js') }}"></script>
@endsection
