@extends('layouts.app')

@section('header')
    <link href="{{ asset('styles/css/static_pages.css') }}" rel="stylesheet">
@endsection

@section('content')
    <section class="static-hero">
        <div class="container">
            <div class="static-hero-content">
                <h1 class="static-title">تواصل معنا</h1>
                <p class="static-subtitle">نحن هنا للمساعدة والإجابة على جميع استفساراتك حول منصة مسار</p>
            </div>
        </div>
    </section>

    <section class="contact-section">
        <div class="container">
            <div class="contact-content">
                <div class="contact-info">
                    <h2>معلومات التواصل</h2>
                    <div class="contact-cards">
                        <div class="contact-card">
                            <div class="contact-icon">📧</div>
                            <h3>البريد الإلكتروني</h3>
                            <p>info@masar.com</p>
                            <p>support@masar.com</p>
                        </div>
                        <div class="contact-card">
                            <div class="contact-icon">📞</div>
                            <h3>الهاتف</h3>
                            <p>+966 11 234 5678</p>
                            <p>+966 50 123 4567</p>
                        </div>
                        <div class="contact-card">
                            <div class="contact-icon">📍</div>
                            <h3>العنوان</h3>
                            <p>الرياض، المملكة العربية السعودية</p>
                            <p>حي الملك فهد، شارع الأمير محمد بن عبدالعزيز</p>
                        </div>
                        <div class="contact-card">
                            <div class="contact-icon">🕒</div>
                            <h3>ساعات العمل</h3>
                            <p>الأحد - الخميس: 9:00 ص - 6:00 م</p>
                            <p>الجمعة: مغلق</p>
                        </div>
                    </div>
                </div>

                <div class="contact-form-section">
                    <h2>أرسل لنا رسالة</h2>
                    <form class="contact-form">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="name">الاسم الكامل</label>
                                <input type="text" id="name" name="name" required>
                            </div>
                            <div class="form-group">
                                <label for="email">البريد الإلكتروني</label>
                                <input type="email" id="email" name="email" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="phone">رقم الهاتف</label>
                                <input type="tel" id="phone" name="phone">
                            </div>
                            <div class="form-group">
                                <label for="subject">الموضوع</label>
                                <select id="subject" name="subject" required>
                                    <option value="">اختر الموضوع</option>
                                    <option value="general">استفسار عام</option>
                                    <option value="technical">دعم تقني</option>
                                    <option value="billing">الفوترة والدفع</option>
                                    <option value="partnership">الشراكات</option>
                                    <option value="other">أخرى</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="message">الرسالة</label>
                            <textarea id="message" name="message" rows="6" required placeholder="اكتب رسالتك هنا..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">إرسال الرسالة</button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <section class="faq-preview">
        <div class="container">
            <h2 class="section-title">الأسئلة الشائعة</h2>
            <p class="section-subtitle">قد تجد إجابة سؤالك هنا قبل التواصل معنا</p>
            <div class="faq-items">
                <div class="faq-item">
                    <h3>كم من الوقت تستغرق عملية فحص السير الذاتية؟</h3>
                    <p>عادة ما تستغرق عملية فحص مئات السير الذاتية من 5-10 دقائق فقط</p>
                </div>
                <div class="faq-item">
                    <h3>هل البيانات آمنة ومحمية؟</h3>
                    <p>نعم، نستخدم أحدث تقنيات التشفير لضمان حماية بيانات المرشحين بالكامل</p>
                </div>
                <div class="faq-item">
                    <h3>هل يمكن فحص السير الذاتية باللغة الإنجليزية؟</h3>
                    <p>بالتأكيد، المنصة تدعم فحص السير الذاتية باللغتين العربية والإنجليزية</p>
                </div>
            </div>
            <a href="{{ route('faq') }}" class="btn btn-secondary">المزيد من الأسئلة</a>
        </div>
    </section>
@endsection

@section('scripts')
    <script src="{{ asset('styles/js/static_pages.js') }}"></script>
@endsection
