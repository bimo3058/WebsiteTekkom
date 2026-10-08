{{--
    Pesan flash bab Pengaduan, meniru resources/views/superadmin/users/_alerts.blade.php:
    kartu putih bergaris kiri 3px, lencana ikon kecil, teks 12px, tombol tutup.
    Menampilkan session('success'), session('error'), session('info'), dan $errors.
    Butuh partials/palette.
--}}
@once
    <style>
        .pgd-alert {
            display: flex; align-items: flex-start; justify-content: space-between; gap: 10px;
            background: #ffffff; border: 1px solid; border-left-width: 3px; border-radius: 10px;
            padding: 10px 14px; margin-bottom: 16px;
        }
        .pgd-alert-main { display: flex; align-items: flex-start; gap: 10px; min-width: 0; }
        .pgd-alert-icon {
            width: 26px; height: 26px; border-radius: 7px; flex-shrink: 0;
            display: flex; align-items: center; justify-content: center;
        }
        .pgd-alert-text { font-size: 12px; font-weight: 600; line-height: 1.5; padding-top: 4px; }
        .pgd-alert-title {
            font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .08em;
            padding-top: 6px; margin-bottom: 4px;
        }
        .pgd-alert-list { margin: 0; padding-left: 16px; font-size: 12px; font-weight: 500; line-height: 1.6; }
        .pgd-alert-close {
            background: none; border: none; padding: 2px; cursor: pointer; flex-shrink: 0;
            color: var(--c-fg-muted); line-height: 0;
        }
        .pgd-alert-close:hover { color: var(--c-fg); }

        .pgd-alert.success { border-color: #9DE0D3; border-left-color: var(--c-success); }
        .pgd-alert.success .pgd-alert-icon { background: var(--c-success-subtle); color: var(--c-success); }
        .pgd-alert.success .pgd-alert-text { color: var(--c-success); }

        .pgd-alert.error { border-color: var(--c-error-subtle); border-left-color: var(--c-error); }
        .pgd-alert.error .pgd-alert-icon { background: var(--c-error-subtle); color: var(--c-error); }
        .pgd-alert.error .pgd-alert-text,
        .pgd-alert.error .pgd-alert-title,
        .pgd-alert.error .pgd-alert-list { color: var(--c-error-200); }

        .pgd-alert.info { border-color: var(--c-sky-subtle); border-left-color: var(--c-sky); }
        .pgd-alert.info .pgd-alert-icon { background: var(--c-sky-subtle); color: var(--c-sky); }
        .pgd-alert.info .pgd-alert-text { color: var(--c-sky); }
    </style>
@endonce

@php
    $pgdCloseBtn = '<button type="button" class="pgd-alert-close" onclick="this.closest(\'.pgd-alert\').remove()" aria-label="Tutup">'
        . '<svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><path d="M18 6L6 18M6 6l12 12"/></svg>'
        . '</button>';
@endphp

@if (session('success'))
    <div class="pgd-alert success" role="status">
        <div class="pgd-alert-main">
            <span class="pgd-alert-icon">
                <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>
            </span>
            <span class="pgd-alert-text">{{ session('success') }}</span>
        </div>
        {!! $pgdCloseBtn !!}
    </div>
@endif

@if (session('info'))
    <div class="pgd-alert info" role="status">
        <div class="pgd-alert-main">
            <span class="pgd-alert-icon">
                <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
            </span>
            <span class="pgd-alert-text">{{ session('info') }}</span>
        </div>
        {!! $pgdCloseBtn !!}
    </div>
@endif

@if (session('error'))
    <div class="pgd-alert error" role="alert">
        <div class="pgd-alert-main">
            <span class="pgd-alert-icon">
                <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            </span>
            <span class="pgd-alert-text">{{ session('error') }}</span>
        </div>
        {!! $pgdCloseBtn !!}
    </div>
@endif

@if ($errors->any())
    <div class="pgd-alert error" role="alert">
        <div class="pgd-alert-main">
            <span class="pgd-alert-icon">
                <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            </span>
            <div>
                <div class="pgd-alert-title">Periksa kembali isian</div>
                <ul class="pgd-alert-list">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
@endif
