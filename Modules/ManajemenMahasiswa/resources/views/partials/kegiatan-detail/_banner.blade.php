{{--
    Banner di halaman detail kegiatan (klik = perbesar lewat _lightbox).
    Dipakai _body, dan oleh detail Laporan & Arsip di atas deretan tab Rencana vs
    Pelaksanaan (S-70) supaya banner tetap terlihat di kedua tab.

    Variabel wajib: $proker.
--}}
@if($proker->banner)
<div style="position:relative;width:100%;max-height:340px;flex-shrink:0;border-radius:18px;overflow:hidden;margin-bottom:28px;box-shadow:0 10px 30px -10px rgba(0,0,0,0.15);cursor:pointer;transition:transform 0.2s;"
     onclick="openBannerLightbox()"
     onmouseover="this.style.transform='scale(1.005)'"
     onmouseout="this.style.transform='scale(1)'">
    <div style="position:absolute;inset:0;background:linear-gradient(to top, rgba(0,0,0,0.65) 0%, transparent 45%);z-index:1;transition:background 0.2s;" onmouseover="this.style.background='linear-gradient(to top, rgba(0,0,0,0.75) 0%, transparent 50%)'" onmouseout="this.style.background='linear-gradient(to top, rgba(0,0,0,0.65) 0%, transparent 45%)'"></div>
    <img src="{{ $proker->banner_url }}" alt="{{ $proker->judul }}" style="width:100%;height:340px;object-fit:cover;display:block;">
    <div style="position:absolute;bottom:24px;left:28px;z-index:2;display:flex;align-items:center;gap:12px;">
        <span style="background:rgba(255,255,255,0.25);backdrop-filter:blur(10px);-webkit-backdrop-filter:blur(10px);color:var(--c-surface);padding:6px 14px;border-radius:20px;font-size:12px;font-weight:700;letter-spacing:0.5px;border:1px solid rgba(255,255,255,0.4);text-shadow:0 1px 2px rgba(0,0,0,0.2);">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:-2px;margin-right:5px;"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>Banner Kegiatan &bull; Klik untuk memperbesar
        </span>
    </div>
</div>
@endif
