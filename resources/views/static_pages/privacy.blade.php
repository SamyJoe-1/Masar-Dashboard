@extends('layouts.app')

@section('header')
    <link href="{{ asset('styles/css/static_pages.css') }}" rel="stylesheet">
@endsection

@section('content')
    <section class="static-hero">
        <div class="container">
            <div class="static-hero-content">
                <h1 class="static-title">سياسة الخصوصية</h1>
                <p class="static-subtitle">نحن ملتزمون بحماية خصوصيتك وأمان بياناتك على منصة مسار</p>
            </div>
        </div>
    </section>

    <section class="privacy-content">
        <div class="container">
            <div class="privacy-document">
                <div class="privacy-header">
                    <p class="last-updated">آخر تحديث: يناير 2025</p>
                    <div class="privacy-highlights">
                        <h3>الالتزامات الأساسية</h3>
                        <div class="highlight-cards">
                            <div class="highlight-card">
                                <div class="highlight-icon">🔒</div>
                                <h4>حماية البيانات</h4>
                                <p>تشفير متقدم لجميع البيانات</p>
                            </div>
                            <div class="highlight-card">
                                <div class="highlight-icon">🚫</div>
                                <h4>عدم التخزين</h4>
                                <p>لا نحتفظ بالسير الذاتية</p>
                            </div>
                            <div class="highlight-card">
                                <div class="highlight-icon">🤝</div>
                                <h4>عدم المشاركة</h4>
                                <p>لا نشارك البيانات مع الغير</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="privacy-section">
                    <h2>1. المعلومات التي نجمعها</h2>
                    <h3>1.1 البيانات المرفوعة</h3>
                    <ul>
                        <li>ملفات السير الذاتية (PDF, DOC, DOCX)</li>
                        <li>المعلومات المستخرجة من السير الذاتية للتحليل</li>
                        <li>معايير الوظيفة المحددة من قبل المستخدم</li>
                    </ul>

                    <h3>1.2 معلومات الاستخدام</h3>
                    <ul>
                        <li>بيانات تسجيل الدخول (في حالة إنشاء حساب)</li>
                        <li>معلومات تقنية عن الجهاز والمتصفح</li>
                        <li>إحصائيات الاستخدام لتحسين الخدمة</li>
                        <li>عنوان IP (مؤقتاً لأغراض الأمان)</li>
                    </ul>
                </div>

                <div class="privacy-section">
                    <h2>2. كيفية استخدام المعلومات</h2>
                    <div class="usage-grid">
                        <div class="usage-item">
                            <h4>التحليل الذكي</h4>
                            <p>معالجة السير الذاتية باستخدام الذكاء الاصطناعي لتقديم تحليل دقيق ومفصل</p>
                        </div>
                        <div class="usage-item">
                            <h4>إنتاج التقارير</h4>
                            <p>إنشاء تقارير شاملة تتضمن تقييم المرشحين وترتيبهم حسب الملاءمة</p>
                        </div>
                        <div class="usage-item">
                            <h4>تحسين الخدمة</h4>
                            <p>استخدام بيانات الاستخدام المجهولة لتطوير وتحسين خوارزميات التحليل</p>
                        </div>
                        <div class="usage-item">
                            <h4>الدعم التقني</h4>
                            <p>تقديم المساعدة والدعم الفني عند الحاجة وحل المشاكل التقنية</p>
                        </div>
                    </div>
                </div>

                <div class="privacy-section">
                    <h2>3. حماية البيانات</h2>
                    <div class="security-measures">
                        <div class="security-item">
                            <h4>🔐 التشفير المتقدم</h4>
                            <p>جميع البيانات محمية بتشفير AES-256 أثناء النقل والمعالجة</p>
                        </div>
                        <div class="security-item">
                            <h4>🏢 خوادم آمنة</h4>
                            <p>استضافة البيانات على خوادم محمية ومعتمدة مع أحدث تقنيات الأمان</p>
                        </div>
                        <div class="security-item">
                            <h4>🚪 التحكم في الوصول</h4>
                            <p>وصول محدود للبيانات فقط للموظفين المخولين وفقاً لمبدأ "الحاجة للمعرفة"</p>
                        </div>
                        <div class="security-item">
                            <h4>🔍 المراقبة المستمرة</h4>
                            <p>مراقبة مستمرة للأنظمة والشبكات للكشف عن أي محاولات اختراق</p>
                        </div>
                    </div>
                </div>

                <div class="privacy-section">
                    <h2>4. الاحتفاظ بالبيانات</h2>
                    <div class="retention-timeline">
                        <div class="timeline-item">
                            <div class="timeline-marker">⚡</div>
                            <h4>أثناء المعالجة</h4>
                            <p>البيانات محمية في الذاكرة المؤقتة فقط أثناء عملية التحليل</p>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-marker">🗑️</div>
                            <h4>بعد التحليل</h4>
                            <p>حذف فوري لجميع ملفات السير الذاتية بعد إنتاج التقرير</p>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-marker">📊</div>
                            <h4>التقارير</h4>
                            <p>تتوفر التقارير للمستخدم لفترة محدودة ثم يتم حذفها نهائياً</p>
                        </div>
                    </div>
                </div>

                <div class="privacy-section">
                    <h2>5. مشاركة المعلومات</h2>
                    <div class="sharing-policy">
                        <div class="no-sharing">
                            <h3>❌ لا نشارك البيانات مع:</h3>
                            <ul>
                                <li>شركات التسويق أو الإعلان</li>
                                <li>وسطاء بيانات أو شركات بيع المعلومات</li>
                                <li>شبكات التواصل الاجتماعي</li>
                                <li>أي طرف ثالث لأغراض تجارية</li>
                            </ul>
                        </div>
                        <div class="limited-sharing">
                            <h3>⚖️ مشاركة محدودة فقط في حالة:</h3>
                            <ul>
                                <li>الالتزام القانوني أو الأمر القضائي</li>
                                <li>حماية حقوقنا أو حقوق المستخدمين</li>
                                <li>التعامل مع مقدمي الخدمات التقنية (مع اتفاقيات سرية صارمة)</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="privacy-section">
                    <h2>6. حقوق المستخدمين</h2>
                    <div class="user-rights">
                        <div class="right-item">
                            <h4>🔍 الوصول للمعلومات</h4>
                            <p>الحق في معرفة البيانات المجمعة عنك وكيفية استخدامها</p>
                        </div>
                        <div class="right-item">
                            <h4>✏️ التعديل والتصحيح</h4>
                            <p>الحق في تعديل أو تصحيح أي معلومات غير صحيحة</p>
                        </div>
                        <div class="right-item">
                            <h4>🗑️ الحذف</h4>
                            <p>الحق في طلب حذف جميع بياناتك من أنظمتنا</p>
                        </div>
                        <div class="right-item">
                            <h4>📤 النقل</h4>
                            <p>الحق في نقل بياناتك إلى خدمة أخرى بصيغة قابلة للقراءة</p>
                        </div>
                    </div>
                </div>

                <div class="privacy-section">
                    <h2>7. ملفات الارتباط (Cookies)</h2>
                    <p>نستخدم ملفات الارتباط لتحسين تجربة المستخدم وضمان الأمان:</p>
                    <ul>
                        <li><strong>ملفات ضرورية:</strong> لضمان عمل المنصة بشكل صحيح</li>
                        <li><strong>ملفات تحليلية:</strong> لفهم كيفية استخدام المنصة وتحسينها</li>
                        <li><strong>ملفات الأمان:</strong> لحماية المنصة من التهديدات الأمنية</li>
                    </ul>
                </div>

                <div class="privacy-section">
                    <h2>8. التحديثات على السياسة</h2>
                    <p>قد نقوم بتحديث هذه السياسة من وقت لآخر. سنقوم بإشعار المستخدمين بأي تغييرات مهمة عبر:</p>
                    <ul>
                        <li>إشعار على المنصة عند التسجيل</li>
                        <li>رسالة بريد إلكتروني (للمستخدمين المسجلين)</li>
                        <li>إعلان على الموقع الإلكتروني</li>
                    </ul>
                </div>

                <div class="privacy-section">
                    <h2>9. التواصل بشأن الخصوصية</h2>
                    <p>لأي أسئلة أو مخاوف بخصوص سياسة الخصوصية، يرجى التواصل معنا:</p>
                    <div class="contact-privacy">
                        <div class="contact-method">
                            <strong>📧 البريد الإلكتروني:</strong> privacy@masar.com
                        </div>
                        <div class="contact-method">
                            <strong>📞 الهاتف:</strong> +966 11 234 5678
                        </div>
                        <div class="contact-method">
                            <strong>⏰ وقت الاستجابة:</strong> 24-48 ساعة
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
    <script src="{{ asset('styles/js/static_pages.js') }}"></script>
@endsection
