{{--
    Pagination bergaya navy — bentuk yang sama dengan tabel Verifikasi Prestasi
    & Verifikasi Riwayat Kegiatan.

    Dipakai halaman pengajuan mahasiswa dan halaman Klaim Reward. Klaim Reward
    sebelumnya memakai $paginator->links() bawaan Laravel, yang di aplikasi ini
    dirender dengan template Tailwind — padahal layout modul hanya memuat
    Bootstrap. Akibatnya kelas penyembunyi (hidden / sm:flex) tidak dikenali dan
    blok mobile + desktop tampil dua-duanya tanpa gaya begitu datanya lebih dari
    satu halaman.

    Variabel include:
      $paginator  LengthAwarePaginator — sudah ->withQueryString() di controller
--}}
@php
    $pFrom    = $paginator->firstItem() ?? 0;
    $pTo      = $paginator->lastItem() ?? 0;
    $pTotal   = $paginator->total();
    $pSaatIni = $paginator->currentPage();
    $pAkhir   = $paginator->lastPage();
    $pRange   = 2;
    $pMulai   = max(1, $pSaatIni - $pRange);
    $pSelesai = min($pAkhir, $pSaatIni + $pRange);
@endphp
<div style="display:flex; align-items:center; justify-content:space-between; padding:12px 16px; background:#fff; border-top:1px solid #e5e7eb; flex-wrap:wrap; gap:10px;">
    <span style="font-size:12px; color:var(--c-fg-sec);">
        Menampilkan <strong style="color:var(--c-fg); font-weight:700;">{{ $pFrom }}</strong>
        sampai <strong style="color:var(--c-fg); font-weight:700;">{{ $pTo }}</strong>
        dari <strong style="color:var(--c-fg); font-weight:700;">{{ number_format($pTotal) }}</strong> data
    </span>

    @if($pAkhir > 1)
        <div style="display:flex; align-items:center; gap:4px;">
            @if($pSaatIni > 1)
                <a href="{{ $paginator->previousPageUrl() }}" aria-label="Halaman sebelumnya"
                   style="width:28px; height:28px; display:flex; align-items:center; justify-content:center; border:1px solid var(--c-border); border-radius:6px; background:#fff; color:var(--c-fg-sec); text-decoration:none;">
                    <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><path d="M15 18l-6-6 6-6"/></svg>
                </a>
            @else
                <span style="width:28px; height:28px; display:flex; align-items:center; justify-content:center; border:1px solid #F3F4F6; border-radius:6px; background:#FAFAFA; color:#D1D5DB; cursor:not-allowed;">
                    <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><path d="M15 18l-6-6 6-6"/></svg>
                </span>
            @endif

            @if($pMulai > 1)
                <a href="{{ $paginator->url(1) }}"
                   style="width:28px; height:28px; display:flex; align-items:center; justify-content:center; border:1px solid var(--c-border); border-radius:6px; background:#fff; font-size:12px; font-weight:500; color:var(--c-fg-sec); text-decoration:none;">1</a>
                @if($pMulai > 2)
                    <span style="width:28px; height:28px; display:flex; align-items:center; justify-content:center; font-size:12px; color:var(--c-fg-muted);">…</span>
                @endif
            @endif

            @for($p = $pMulai; $p <= $pSelesai; $p++)
                <a href="{{ $paginator->url($p) }}"
                   style="width:28px; height:28px; display:flex; align-items:center; justify-content:center; border-radius:6px; font-size:12px; text-decoration:none; font-weight:{{ $p === $pSaatIni ? '700' : '500' }}; {{ $p === $pSaatIni ? 'background:var(--c-primary); color:#fff; border:1px solid var(--c-primary); box-shadow:0 2px 6px rgba(11,38,110,0.25);' : 'border:1px solid var(--c-border); background:#fff; color:var(--c-fg-sec);' }}">{{ $p }}</a>
            @endfor

            @if($pSelesai < $pAkhir)
                @if($pSelesai < $pAkhir - 1)
                    <span style="width:28px; height:28px; display:flex; align-items:center; justify-content:center; font-size:12px; color:var(--c-fg-muted);">…</span>
                @endif
                <a href="{{ $paginator->url($pAkhir) }}"
                   style="width:28px; height:28px; display:flex; align-items:center; justify-content:center; border:1px solid var(--c-border); border-radius:6px; background:#fff; font-size:12px; font-weight:500; color:var(--c-fg-sec); text-decoration:none;">{{ $pAkhir }}</a>
            @endif

            @if($pSaatIni < $pAkhir)
                <a href="{{ $paginator->nextPageUrl() }}" aria-label="Halaman berikutnya"
                   style="width:28px; height:28px; display:flex; align-items:center; justify-content:center; border:1px solid var(--c-border); border-radius:6px; background:#fff; color:var(--c-fg-sec); text-decoration:none;">
                    <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><path d="M9 18l6-6-6-6"/></svg>
                </a>
            @else
                <span style="width:28px; height:28px; display:flex; align-items:center; justify-content:center; border:1px solid #F3F4F6; border-radius:6px; background:#FAFAFA; color:#D1D5DB; cursor:not-allowed;">
                    <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><path d="M9 18l6-6-6-6"/></svg>
                </span>
            @endif
        </div>
    @endif
</div>
