<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="sidebar-logo">
            <a href="/home">
                <img src="{{ asset('assets/images/logo.png') }}" width="100%">
            </a>
        </div>
    </div>

    <div class="sidebar-section">
        <div class="sidebar-title">لوحة التحكم</div>
        <a href="#" class="sidebar-item {{ request()->routeIs('dashboard.home') ? "active":"" }}">
            <i class="fas fa-users"></i>
            الإحصائيات
        </a>
    </div>

    <div class="sidebar-section">
        <div class="sidebar-title">الوظائف</div>
        <a href="#" class="sidebar-item">
            <i class="fas fa-briefcase"></i>
            جميع الوظائف
        </a>
        <a href="{{ route('upload.form') }}" class="sidebar-item {{ request()->routeIs('upload.form') ? "active":"" }}">
            <i class="fas fa-plus-circle"></i>
            إنشاء وظيفة
        </a>
    </div>

    <div class="sidebar-section">
        <div class="sidebar-title">المتقدمين</div>
        <a href="#" class="sidebar-item">
            <i class="fas fa-user-tie"></i>
            جميع المتقدمين
        </a>
        <a href="#" class="sidebar-item">
            <i class="fa-solid fa-circle-check"></i>
            المقبولين
        </a>
        <a href="#" class="sidebar-item">
            <i class="fa-solid fa-circle-xmark"></i>
            المرفوضين
        </a>
    </div>

    <div class="sidebar-section">
        <div class="sidebar-title">النظام</div>
        <a href="#" class="sidebar-item">
            <i class="fa-solid fa-address-card"></i>
            الملف الشخصي
        </a>
        <a href="#" class="sidebar-item">
            <i class="fa-solid fa-building"></i>
            المزيد عنا
        </a>
        <a href="#" class="sidebar-item" style="color: #ff3c3c;font-weight: bolder">
            <i class="fa-solid fa-arrow-right-from-bracket"></i>
            تسجيل الخروج
        </a>
    </div>
</div>
