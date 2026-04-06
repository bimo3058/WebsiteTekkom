<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>GPM Dashboard - Bank Soal</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8f9fa; overflow-x: hidden; }
        .sidebar { width: 260px; height: 100vh; position: fixed; top: 0; left: 0; background-color: #ffffff; border-right: 1px solid #f1f1f1; z-index: 1000; }
        .main-content { margin-left: 260px; min-height: 100vh; }
        .topbar { height: 80px; background-color: #ffffff; border-bottom: 1px solid #f1f1f1; display: flex; align-items: center; justify-content: space-between; padding: 0 2.5rem; }
        .nav-link-custom { color: #6c757d; font-weight: 500; padding: 0.8rem 1.5rem; border-radius: 0.5rem; margin: 0.2rem 1rem; transition: all 0.2s; font-size: 0.95rem; }
        .nav-link-custom:hover { background-color: #f8f9fa; color: #2563eb; }
        .nav-link-custom.active { background-color: #2563eb; color: white; }
        .icon-width { width: 25px; text-align: center; }
    </style>
</head>
<body>

    <div class="sidebar d-flex flex-column py-4">
        <div class="px-4 mb-5 d-flex align-items-center">
            <div class="bg-primary text-white rounded-3 d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; background-color: #2563eb !important;">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <div>
                <h6 class="mb-0 fw-bold text-dark">GPM Home</h6>
                <small class="text-muted" style="font-size: 0.65rem; letter-spacing: 0.5px;">Dashboard Dosen GPM</small>
            </div>
        </div>
        
        <nav class="nav flex-column mb-auto">
            <a class="nav-link nav-link-custom {{ request()->routeIs('banksoal.dashboard') ? 'active' : '' }}" 
            href="{{ route('banksoal.dashboard') }}">
                <i class="fas fa-home icon-width me-2"></i> Home
            </a>
            <a class="nav-link nav-link-custom {{ request()->routeIs('banksoal.rps.gpm.validasi-rps*') ? 'active' : '' }}" href="{{ route('banksoal.rps.gpm.validasi-rps') ?? '#' }}">
                <i class="fas fa-check-circle icon-width me-2"></i> Validasi RPS
            </a>
            <a class="nav-link nav-link-custom {{ request()->routeIs('banksoal.soal.gpm.validasi-bank-soal*') ? 'active' : '' }}" href="{{ route('banksoal.soal.gpm.validasi-bank-soal') ?? '#' }}">
                <i class="fas fa-question-circle icon-width me-2"></i> Validasi Bank Soal
            </a>
            <a class="nav-link nav-link-custom {{ request()->routeIs('banksoal.riwayat-validasi*') ? 'active' : '' }}" href="{{ route('banksoal.riwayat-validasi') ?? '#' }}">
                <i class="fas fa-history icon-width me-2"></i> Riwayat Validasi
            </a>
        </nav>

        <div class="mt-auto px-4 border-top pt-4">
            <div class="d-flex align-items-center">
                <img src="https://ui-avatars.com/api/?name=Prof+Aris&background=f1f5f9&color=334155" class="rounded-circle me-3" width="40" height="40" alt="Profile">
                <div>
                    <h6 class="mb-0 fw-bold text-dark" style="font-size: 0.9rem;">Prof. Aris S.</h6>
                    <small class="text-muted" style="font-size: 0.75rem;">Administrator</small>
                </div>
            </div>
        </div>
    </div>

    <div class="main-content">
        <div class="topbar">
            <div>
                <h5 class="mb-1 fw-bold text-dark">@yield('page-title', 'Dashboard GPM')</h5>
                <small class="text-muted">@yield('page-subtitle', 'Ringkasan aktivitas penjaminan mutu akademik')</small>
            </div>
            <div class="dropdown">
                <button class="btn text-white rounded-3 px-4 py-2 fw-semibold" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="background-color: #2563eb; font-size: 0.9rem;">
                    {{ Auth::check() ? Auth::user()->name : 'Prof. Dr. Aris S. (GPM)' }} 
                    <i class="fas fa-chevron-down ms-2" style="font-size: 0.75rem;"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2" style="border-radius: 0.5rem; min-width: 200px;">
                    <li>
                        <div class="px-3 py-2">
                            <span class="d-block fw-bold text-dark" style="font-size: 0.9rem;">{{ Auth::check() ? Auth::user()->name : 'Prof. Dr. Aris S.' }}</span>
                            <span class="d-block text-muted" style="font-size: 0.8rem;">Administrator / GPM</span>
                        </div>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger d-flex align-items-center py-2 fw-medium" style="font-size: 0.9rem;">
                                <i class="fas fa-sign-out-alt me-2"></i> Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>

        <div class="content-wrapper" style="background-color: #f8f9fa;">
            {{ $slot }}
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>