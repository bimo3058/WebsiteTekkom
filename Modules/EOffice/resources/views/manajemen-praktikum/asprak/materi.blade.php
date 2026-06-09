<x-eoffice::manajemen-praktikum.layout pageTitle="Materi Modul">

{{-- Header --}}
<div class="mp-page-header">
    <div>
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
            <h1 class="mp-page-title">Materi Modul</h1>
            <span class="mp-badge success sm"><span class="dot"></span>Asprak</span>
        </div>
        <p class="mp-page-sub">Unggah dan kelola materi untuk modul yang Anda ampu · {{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</p>
    </div>
</div>

{{-- Form Upload Materi --}}
<div class="sec-head">
    <span class="sec-bar"></span>
    <span class="sec-title">Upload Materi Baru</span>
    <span class="sec-rule"></span>
</div>

<div class="mp-card flex-shrink-0" style="padding:20px;">
    <form method="POST" action="{{ route('eoffice.manprak.asprak.materi.store') }}" enctype="multipart/form-data"
          style="display:grid;grid-template-columns:1fr 1fr 1fr auto;gap:12px;align-items:end;">
        @csrf
        <div>
            <label style="display:block;font-size:12px;font-weight:600;margin-bottom:6px;color:#353849;">Modul</label>
            <select name="modul_id" required class="mp-select" style="width:100%;">
                <option value="">Pilih modul</option>
                @foreach($modulsForSelect as $m)
                <option value="{{ $m->id }}">{{ $m->nama }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label style="display:block;font-size:12px;font-weight:600;margin-bottom:6px;color:#353849;">Judul</label>
            <input name="judul" required class="mp-input" style="width:100%;" placeholder="Judul materi...">
        </div>
        <div>
            <label style="display:block;font-size:12px;font-weight:600;margin-bottom:6px;color:#353849;">File</label>
            <input type="file" name="file" class="mp-input" style="width:100%;">
        </div>
        <div>
            <button class="mp-btn primary md">Upload</button>
        </div>
        <div style="grid-column:1/-1;">
            <textarea name="deskripsi" rows="2" class="mp-input" style="width:100%;" placeholder="Deskripsi singkat materi..."></textarea>
        </div>
    </form>
</div>

{{-- Daftar Materi --}}
<div class="sec-head">
    <span class="sec-bar"></span>
    <span class="sec-title">Daftar Materi</span>
    <span class="sec-rule"></span>
    <span class="mp-badge neutral sm">{{ $materis->count() }} materi</span>
</div>

<div class="mp-card flex-1 min-h-0">
    <div class="mp-card-header">
        <span class="mp-card-title">Semua Materi</span>
        <div class="right">
            <span style="font-size:12px;color:#666D80;">{{ $materis->count() }} file diunggah</span>
        </div>
    </div>

    @forelse($materis as $materi)
    <div class="mp-tr" style="display:flex;align-items:center;justify-content:space-between;gap:12px;padding:14px 20px;border-bottom:1px solid #DFE1E7;"
         onmouseover="this.style.background='#F6F8FA'" onmouseout="this.style.background=''">
        <div style="display:flex;align-items:center;gap:12px;flex:1;min-width:0;">
            <div class="mp-stat-icon navy" style="width:36px;height:36px;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                    <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/>
                </svg>
            </div>
            <div style="min-width:0;">
                <div style="font-weight:600;font-size:13px;color:#0D0D12;" class="truncate">{{ $materi->judul }}</div>
                <div style="font-size:11px;color:#666D80;margin-top:2px;">{{ $materi->modul?->nama }} — {{ $materi->created_at?->format('d M Y H:i') }}</div>
                @if($materi->deskripsi)
                <div style="font-size:12px;color:#666D80;margin-top:4px;">{{ $materi->deskripsi }}</div>
                @endif
            </div>
        </div>
        <div style="display:flex;gap:8px;flex-shrink:0;">
            @if($materi->file_path)
            <a href="{{ Storage::url($materi->file_path) }}" target="_blank" class="mp-btn primary sm" style="text-decoration:none;">Unduh</a>
            @endif
            <form method="POST" action="{{ route('eoffice.manprak.asprak.materi.destroy', $materi->id) }}" onsubmit="return confirm('Hapus materi ini?')">
                @csrf @method('DELETE')
                <button class="mp-btn secondary sm" style="color:#DF1C41;border-color:#DF1C41;">Hapus</button>
            </form>
        </div>
    </div>
    @empty
    <div style="padding:48px;text-align:center;">
        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5" stroke-linecap="round" style="margin:0 auto 12px;display:block;">
            <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/>
        </svg>
        <div style="font-size:13px;font-weight:500;color:#666D80;">Belum ada materi.</div>
    </div>
    @endforelse
</div>

</x-eoffice::manajemen-praktikum.layout>
