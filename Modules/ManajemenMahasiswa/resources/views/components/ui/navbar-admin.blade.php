<div class="navbar-custom">
    <div class="user-profile">
        <span>{{ Auth::user()->name ?? 'Username' }}</span>
        <img src="{{ Auth::user()->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name ?? 'U') . '&background=4f46e5&color=fff' }}" alt="User Avatar">
    </div>
</div>