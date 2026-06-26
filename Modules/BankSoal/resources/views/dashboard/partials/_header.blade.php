{{-- Modules/BankSoal/resources/views/dashboard/partials/_header.blade.php --}}
<div style="display:flex; align-items:center; justify-content:space-between; gap:16px; flex-wrap:wrap;">
    {{-- Left: Title + welcome --}}
    <div style="display:flex; align-items:center; gap:14px; flex-wrap:wrap;">
        <div>
            <div style="display:flex; align-items:center; gap:8px; margin-bottom:3px;">
                <h1 style="font-size:22px; font-weight:700; color:var(--c-fg); letter-spacing:-0.02em; line-height:1.2;">Dashboard Admin Bank Soal</h1>
            </div>
            <p style="font-size:12px; color:var(--c-fg-muted);">
                Selamat datang kembali, <span style="color:var(--c-fg); font-weight:600;">{{ auth()->user()->name }}</span>
                <span style="margin-left:4px; color:var(--c-fg-placeholder);">·</span>
                <span style="margin-left:4px; color:var(--c-fg-muted);">{{ now()->translatedFormat('l, d F Y') }}</span>
            </p>
        </div>
    </div>

    {{-- Right: action buttons --}}
    <div style="display:flex; gap:8px; align-items:center; flex-wrap:wrap;">
        @if($sesiOngoing > 0)
            <a href="{{ route('banksoal.admin.cbt.live-proctoring') }}"
               style="display:inline-flex; align-items:center; gap:6px; padding:8px 16px; background:var(--c-success); border:1px solid var(--c-success); border-radius:8px; font-size:12px; font-weight:600; color:#fff; text-decoration:none; transition:all .15s; white-space:nowrap; box-shadow:0 2px 6px rgba(16,185,129,0.3);"
               onmouseover="this.style.background='#059669'; this.style.boxShadow='0 4px 12px rgba(16,185,129,0.4)'"
               onmouseout="this.style.background='var(--c-success)'; this.style.boxShadow='0 2px 6px rgba(16,185,129,0.3)'">
                <span class="w-2 h-2 rounded-full bg-white animate-pulse"></span>
                <span>Live Sekarang ({{ $sesiOngoing }})</span>
            </a>
        @endif
    </div>
</div>
