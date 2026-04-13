{{--
    Shared Dosen Sidebar
    Usage: @include('banksoal::partials.dosen.sidebar', ['active' => 'home|rps|bank-soal|arsip'])
--}}
@php
    $active = $active ?? 'home';
@endphp

<aside class="sidebar">
    <div class="sidebar-brand">
        <div class="brand-icon"><i class="fas fa-university"></i></div>
        <div class="brand-text">
            <strong>Departemen Teknik Komputer</strong>
            <span>Universitas Wakamsi</span>
        </div>
    </div>
    <nav class="sidebar-nav">
        <a href="{{ route('banksoal.dashboard') }}" class="nav-item {{ $active === 'home' ? 'active' : '' }}">
            <span class="nav-icon"><i class="fas fa-th-large"></i></span> Home
        </a>
        <a href="{{ route('banksoal.rps.dosen.index') }}" class="nav-item {{ $active === 'rps' ? 'active' : '' }}">
            <span class="nav-icon"><i class="fas fa-file-alt"></i></span> Manajemen RPS
        </a>
        <a href="{{ route('banksoal.soal.dosen.index') }}" class="nav-item {{ $active === 'bank-soal' ? 'active' : '' }}">
            <span class="nav-icon"><i class="fas fa-database"></i></span> Bank Soal
        </a>
        <a href="{{ route('banksoal.arsip.dosen.index') }}" class="nav-item {{ $active === 'arsip' ? 'active' : '' }}">
            <span class="nav-icon"><i class="fas fa-archive"></i></span> Arsip Soal
        </a>
    </nav>
</aside>
