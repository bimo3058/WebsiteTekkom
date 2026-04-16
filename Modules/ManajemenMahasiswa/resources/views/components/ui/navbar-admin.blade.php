<div class="navbar-custom">
    <div class="user-profile">
        <span>{{ Auth::user()->name ?? 'Username' }}</span>
        <img src="{{ Auth::user()->avatar ?? 'https://via.placeholder.com/40' }}" alt="User">
    </div>
</div>