<x-eoffice::manajemen-praktikum.layout pageTitle="Pengumuman Asprak">

{{-- Header --}}
<div class="mp-page-header">
    <div>
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
            <h1 class="mp-page-title">Pengumuman</h1>
            <span class="mp-badge success sm"><span class="dot"></span>Asprak</span>
        </div>
        <p class="mp-page-sub">Buat pengumuman untuk praktikum yang Anda ampu · {{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</p>
    </div>
</div>

@if(!$asprak)
<div class="mp-alert warning flex-shrink-0">Status asprak Anda belum aktif. Hubungi koordinator untuk aktivasi akun Asprak.</div>
@else

{{-- Form Buat Pengumuman --}}
<div class="sec-head">
    <span class="sec-bar"></span>
    <span class="sec-title">Buat Pengumuman Baru</span>
    <span class="sec-rule"></span>
</div>

<div class="mp-card flex-shrink-0" style="padding:20px;">
    <form method="POST" action="{{ route('eoffice.manprak.asprak.pengumuman.store') }}"
          style="display:flex;flex-direction:column;gap:12px;">
        @csrf
        <input type="hidden" name="praktikum_id" value="{{ $asprak->praktikum_id }}">

        <div>
            <label style="display:block;font-size:12px;font-weight:600;margin-bottom:6px;color:#353849;">Judul Pengumuman</label>
            <input name="judul" required placeholder="Judul pengumuman..." class="mp-input" style="width:100%;">
        </div>

        <div>
            <label style="display:block;font-size:12px;font-weight:600;margin-bottom:6px;color:#353849;">Isi Pengumuman</label>
            <textarea name="konten" rows="4" required placeholder="Isi pengumuman..." class="mp-input" style="width:100%;"></textarea>
        </div>

        <div style="display:flex;align-items:center;justify-content:space-between;">
            <label style="display:flex;align-items:center;gap:8px;font-size:13px;font-weight:600;color:#353849;cursor:pointer;">
                <input type="hidden" name="is_published" value="0">
                <input type="checkbox" name="is_published" value="1" id="chk-publish" style="accent-color:#0B266E;width:16px;height:16px;" checked>
                Publikasikan sekarang
            </label>
            <button class="mp-btn primary md">Buat Pengumuman</button>
        </div>
    </form>
</div>

{{-- Daftar Pengumuman --}}
<div class="sec-head">
    <span class="sec-bar"></span>
    <span class="sec-title">Daftar Pengumuman</span>
    <span class="sec-rule"></span>
    <span class="mp-badge neutral sm">{{ $pengumumans->count() }} pengumuman</span>
</div>

<div class="mp-card flex-1 min-h-0">
    <div class="mp-card-header">
        <span class="mp-card-title">Semua Pengumuman</span>
        <div class="right">
            <span style="font-size:12px;color:#666D80;">{{ $pengumumans->count() }} total</span>
        </div>
    </div>

    @forelse($pengumumans as $p)
    <div class="mp-tr" style="display:flex;justify-content:space-between;gap:16px;padding:16px 20px;border-bottom:1px solid #DFE1E7;"
         onmouseover="this.style.background='#F6F8FA'" onmouseout="this.style.background=''">
        <div style="flex:1;min-width:0;">
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
                <div style="font-weight:700;font-size:14px;color:#0D0D12;">{{ $p->judul }}</div>
                @if($p->is_published ?? true)
                <span class="mp-badge success sm"><span class="dot"></span>Dipublikasikan</span>
                @else
                <span class="mp-badge neutral sm">Draft</span>
                @endif
            </div>
            <div style="font-size:11px;color:#666D80;margin-bottom:8px;">
                <span style="font-weight:500;color:#353849;">{{ $p->user?->name }}</span>
                — {{ $p->created_at?->format('d M Y, H:i') }}
            </div>
            <div style="font-size:13px;color:#353849;line-height:1.6;">{{ $p->konten }}</div>
        </div>
        <form method="POST" action="{{ route('eoffice.manprak.asprak.pengumuman.destroy', $p->id) }}"
              onsubmit="return confirm('Hapus pengumuman ini?')" style="flex-shrink:0;">
            @csrf @method('DELETE')
            <button class="mp-btn secondary sm" style="color:#DF1C41;border-color:#DF1C41;">Hapus</button>
        </form>
    </div>
    @empty
    <div style="padding:48px;text-align:center;">
        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5" stroke-linecap="round" style="margin:0 auto 12px;display:block;">
            <path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 01-3.46 0"/>
        </svg>
        <div style="font-size:13px;font-weight:500;color:#666D80;">Belum ada pengumuman.</div>
    </div>
    @endforelse
</div>

@endif

</x-eoffice::manajemen-praktikum.layout>
