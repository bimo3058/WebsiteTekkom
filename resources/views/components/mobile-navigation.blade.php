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
    $mobileRoles = auth()->user()->roles->pluck('name')->map(fn ($role) => ucwords(str_replace(['_', '-'], ' ', $role)));
@endphp
<nav id="mobile-navigation" class="mobile-navigation" aria-label="Navigasi utama HP">
    <div class="mobile-navigation-context"><span>SITKOM</span><strong>{{ $mobileLabel }}</strong></div>
    <button type="button" data-mobile-menu aria-haspopup="dialog" aria-expanded="false" aria-controls="mobile-account-menu">
        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="{{ $mobileIcons['menu'] }}" /></svg><span>Menu</span>
    </button>
</nav>
<div class="mobile-navigation-backdrop" data-mobile-close aria-hidden="true" hidden></div>
<section id="mobile-account-menu" class="mobile-account-menu" role="dialog" aria-modal="true" aria-labelledby="mobile-menu-title" tabindex="-1" hidden>
    <div class="mobile-menu-handle" aria-hidden="true"></div>
    <header class="mobile-menu-header">
        <div><p>{{ $mobileLabel }}</p><h2 id="mobile-menu-title">Menu navigasi</h2></div>
        <button type="button" class="mobile-navigation-close" data-mobile-close aria-label="Tutup menu"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="m6 6 12 12M18 6 6 18" /></svg></button>
    </header>
    <div class="mobile-menu-scroll">
        <div class="mobile-menu-user"><strong>{{ auth()->user()->name }}</strong><div class="mobile-menu-roles">@foreach($mobileRoles as $role)<span>{{ $role }}</span>@endforeach</div></div>
        <section data-mobile-module-section hidden>
            <h3>Menu {{ $mobileLabel }}</h3>
            <nav data-mobile-module-links class="mobile-menu-grid" aria-label="Menu modul sesuai role"></nav>
        </section>
        <section>
            <h3>Aplikasi &amp; akun</h3>
            <nav class="mobile-menu-grid" aria-label="Aplikasi dan akun">
                <a href="{{ route($mobileHome) }}"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="{{ $mobileIcons['home'] }}" /></svg><span>{{ $mobileLabel === 'Beranda' ? 'Beranda' : 'Beranda '.$mobileLabel }}</span></a>
                @if($mobileHome !== 'dashboard')<a href="{{ route('dashboard') }}"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="{{ $mobileIcons['apps'] }}" /></svg><span>Daftar aplikasi</span></a>@endif
                @if(auth()->user()->hasRole('superadmin'))<a href="{{ route('superadmin.dashboard') }}"><span>Dashboard superadmin</span></a>@endif
                <a href="{{ route('profile.edit') }}"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="{{ $mobileIcons['user'] }}" /></svg><span>Pengaturan profil</span></a>
            </nav>
        </section>
        <form class="mobile-menu-logout" method="POST" action="{{ str_starts_with($mobileRoute, 'capstone.') ? route('capstone.logout') : route('logout') }}">@csrf<button type="submit">Keluar dari akun</button></form>
    </div>
</section>
@endonce
@endauth
