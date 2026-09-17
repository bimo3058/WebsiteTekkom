@auth
@once
@php
    $mobileRoute = request()->route()?->getName() ?? '';
    $mobileHome = match (true) {
        str_starts_with($mobileRoute, 'capstone.') => 'capstone.dashboard',
        str_starts_with($mobileRoute, 'eoffice.manprak.') => 'eoffice.manprak.dashboard',
        str_starts_with($mobileRoute, 'eoffice.') => 'eoffice.dashboard',
        str_starts_with($mobileRoute, 'banksoal.') || str_starts_with($mobileRoute, 'komprehensif.') => 'banksoal.dashboard',
        str_starts_with($mobileRoute, 'manajemenmahasiswa.') => 'manajemenmahasiswa.dashboard',
        str_starts_with($mobileRoute, 'superadmin.') => 'superadmin.dashboard',
        default => 'dashboard',
    };
    $mobileLabel = match ($mobileHome) {
        'capstone.dashboard' => 'Capstone', 'eoffice.manprak.dashboard' => 'Praktikum',
        'eoffice.dashboard' => 'E-Office', 'banksoal.dashboard' => 'Bank Soal',
        'manajemenmahasiswa.dashboard' => 'Mahasiswa', 'superadmin.dashboard' => 'Admin',
        default => 'Beranda',
    };
    $mobileIcons = [
        'apps' => 'M3 3h7v7H3zM14 3h7v7h-7zM3 14h7v7H3zM14 14h7v7h-7z',
        'home' => 'm3 10 9-7 9 7M5 9v12h5v-7h4v7h5V9',
        'menu' => 'M4 6h16M4 12h16M4 18h16',
        'user' => 'M20 21v-2a7 7 0 0 0-14 0v2M17 7a4 4 0 1 1-8 0 4 4 0 0 1 8 0',
    ];
@endphp
<nav id="mobile-navigation" class="mobile-navigation" aria-label="Navigasi utama HP">
    @if($mobileHome !== 'dashboard')
        <a href="{{ route('dashboard') }}"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="{{ $mobileIcons['apps'] }}" /></svg><span>Aplikasi</span></a>
    @endif
    <a href="{{ route($mobileHome) }}" @if($mobileRoute === $mobileHome) aria-current="page" @endif>
        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="{{ $mobileIcons['home'] }}" /></svg><span>{{ $mobileLabel }}</span>
    </a>
    <button type="button" data-mobile-menu aria-expanded="false" aria-controls="mobile-account-menu">
        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="{{ $mobileIcons['menu'] }}" /></svg><span>Menu</span>
    </button>
    <a href="{{ route('profile.edit') }}" @if(str_starts_with($mobileRoute, 'profile.')) aria-current="page" @endif>
        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="{{ $mobileIcons['user'] }}" /></svg><span>Profil</span>
    </a>
</nav>
<button type="button" class="mobile-navigation-backdrop" data-mobile-close aria-label="Tutup menu" hidden></button>
<section id="mobile-account-menu" class="mobile-account-menu" aria-label="Menu akun" tabindex="-1" hidden>
    <h2>Menu akun</h2>
    <p>{{ auth()->user()->name }}</p>
    <a href="{{ route('dashboard') }}">Daftar aplikasi</a>
    @if(auth()->user()->hasRole('superadmin'))
        <a href="{{ route('superadmin.dashboard') }}">Dashboard superadmin</a>
    @endif
    <a href="{{ route('profile.edit') }}">Pengaturan profil</a>
    <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit">Keluar</button></form>
</section>
@endonce
@endauth
