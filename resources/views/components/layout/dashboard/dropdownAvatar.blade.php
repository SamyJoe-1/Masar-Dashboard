@php(@$user = Auth()->user())
<div class="user-dropdown">
    <div class="user-info" onclick="toggleDropdown()">
        <div class="user-avatar">{{ getInitials(@$user->name) }}</div>
        <span class="username">{{ substr($user->name, 0, 15) }}</span>
        <i class="fas fa-chevron-down"></i>
    </div>

    <div class="dropdown-menu" id="userDropdown">
        <a href="" class="dropdown-item">
            <i class="fas fa-user"></i>
            الملف الشخصي
        </a>
        <a href="#" class="dropdown-item">
            <i class="fas fa-question-circle"></i>
            المساعدة
        </a>
        <a href="#" class="dropdown-item">
            <i class="fas fa-sign-out-alt"></i>
            تسجيل الخروج
        </a>
    </div>
</div>
