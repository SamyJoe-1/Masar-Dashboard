@php(@$user = Auth()->user())
<div class="user-dropdown">
    <div class="user-info" onclick="toggleDropdown()">
        <div class="user-avatar">{{ getInitials(@$user->name) }}</div>
        <span class="username">{{ substr($user->name, 0, 15) }}</span>
        <i class="fas fa-chevron-down"></i>
    </div>

    <div class="dropdown-menu" id="userDropdown">
        <a href="{{ route('profile') }}" class="dropdown-item">
            <i class="fas fa-user"></i>
            {{ __("words.Profile") }}
        </a>
        <a href="{{ route('contact') }}" target="_blank" class="dropdown-item">
            <i class="fas fa-question-circle"></i>
            {{ __("words.support") }}
        </a>
        <a href="{{ route('logout') }}" class="dropdown-item">
            <i class="fas fa-sign-out-alt"></i>
            {{ __("words.logout") }}
        </a>
    </div>
</div>
