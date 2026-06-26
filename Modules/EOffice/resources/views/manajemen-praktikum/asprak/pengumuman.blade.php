<x-eoffice::manajemen-praktikum.layout pageTitle="Pengumuman Asprak">

{{-- Flash Messages --}}
@if(session('success'))
<div class="mp-flash mp-flash-success flex-shrink-0">
    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>
    {{ session('success') }}
</div>
@endif
@if(session('error'))
<div class="mp-flash mp-flash-error flex-shrink-0">
    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
    {{ session('error') }}
</div>
@endif

{{-- Page Header --}}
<div class="mp-page-header">
    <div>
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
            <h1 class="mp-page-title">Pengumuman</h1>
            <span class="mp-badge success sm"><span class="dot"></span>Asprak</span>
        </div>
        <p class="mp-page-sub">Kelola pengumuman untuk praktikum yang Anda ampu · {{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</p>
    </div>
</div>

@if($praktikumList->isEmpty())
<div class="mp-alert warning flex-shrink-0">Anda belum terdaftar sebagai asprak di praktikum manapun. Hubungi koordinator untuk aktivasi.</div>
@else

{{-- ── Pilih Praktikum ─────────────────────────────────────────────── --}}
<div class="sec-head flex-shrink-0">
    <span class="sec-bar" style="background:#40C4AA;"></span>
    <span class="sec-title">Pilih Praktikum</span>
    <span class="sec-rule"></span>
</div>

<div class="mp-card flex-shrink-0">
    <div style="padding:14px 18px;">
        <form method="GET" style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#808897" stroke-width="2" stroke-linecap="round" style="flex-shrink:0;">
                <path d="M2 3h6a4 4 0 014 4v14a3 3 0 00-3-3H2zM22 3h-6a4 4 0 00-4 4v14a3 3 0 013-3h7z"/>
            </svg>
            <select name="praktikum_id" class="mp-input mp-select" style="max-width:360px;"
                    onchange="this.form.submit()">
                @foreach($praktikumList as $p)
                <option value="{{ $p->id }}" {{ ($praktikum?->id == $p->id) ? 'selected' : '' }}>
                    {{ $p->nama }}
                    @if($p->kode) [{{ $p->kode }}] @endif
                    · {{ $p->semester }} {{ $p->tahun_ajaran }}
                </option>
                @endforeach
            </select>
            @if($praktikum)
            <span class="mp-badge success sm"><span class="dot"></span>{{ $pengumumans->count() }} pengumuman</span>
            @endif
        </form>
    </div>
</div>

@if($asprak)

{{-- ── Buat Pengumuman ─────────────────────────────────────────────── --}}
<div class="sec-head flex-shrink-0">
    <span class="sec-bar" style="background:#40C4AA;"></span>
    <span class="sec-title">Buat Pengumuman Baru</span>
    <span class="sec-rule"></span>
</div>

<div class="mp-card flex-shrink-0">
    <div class="mp-card-header">
        <div style="width:32px;height:32px;border-radius:9px;background:rgba(64,196,170,0.12);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#40C4AA" stroke-width="2" stroke-linecap="round">
                <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
            </svg>
        </div>
        <span class="mp-card-title">Tulis Pengumuman</span>
        @if($praktikum)
        <span style="margin-left:auto;font-size:11px;color:#666D80;">untuk: <strong style="color:#353849;">{{ $praktikum->nama }}</strong></span>
        @endif
    </div>
    <div style="padding:20px;">
        <form method="POST" action="{{ route('eoffice.manprak.asprak.pengumuman.store') }}" enctype="multipart/form-data"
              style="display:flex;flex-direction:column;gap:14px;">
            @csrf
            <input type="hidden" name="praktikum_id" value="{{ $asprak->praktikum_id }}">

            <div>
                <label for="judul-pengumuman" style="display:block;font-size:12px;font-weight:600;margin-bottom:6px;color:#353849;">
                    Judul Pengumuman <span style="color:#DF1C41;">*</span>
                </label>
                <input id="judul-pengumuman" name="judul" required
                       placeholder="Masukkan judul pengumuman..."
                       class="mp-input" style="width:100%;"
                       value="{{ old('judul') }}">
            </div>

            <div>
                <label for="konten-pengumuman" style="display:block;font-size:12px;font-weight:600;margin-bottom:6px;color:#353849;">
                    Isi Pengumuman <span style="color:#DF1C41;">*</span>
                </label>
                <textarea id="konten-pengumuman" name="konten" rows="5" required
                          placeholder="Tulis isi pengumuman di sini..."
                          class="mp-input" style="width:100%;resize:vertical;">{{ old('konten') }}</textarea>
            </div>

            <div x-data="fileUploader()">
                <label style="display:block;font-size:12px;font-weight:600;margin-bottom:6px;color:#353849;">
                    Lampiran (Opsional, Max 3 File)
                </label>
                
                <div style="display:flex;gap:10px;align-items:center;margin-bottom:8px;">
                    <button type="button" @click="$refs.fileInput.click()" 
                            style="background:#F8FAFC;border:1px solid #DFE1E7;border-radius:6px;padding:6px 12px;font-size:12px;font-weight:600;color:#353849;cursor:pointer;transition:background .2s;"
                            onmouseover="this.style.background='#F0F4FA'" onmouseout="this.style.background='#F8FAFC'">
                        + Tambah File
                    </button>
                    <span x-text="files.length + '/3 file terpilih'" style="font-size:12px;color:#808897;"></span>
                </div>
                
                <input type="file" x-ref="fileInput" style="display:none" multiple accept="*/*" @change="addFiles($event)">
                <!-- Hidden input that actually gets submitted -->
                <input type="file" id="hidden-lampiran" name="lampiran[]" multiple style="display:none">

                <div style="font-size:11px;color:#808897;">Ukuran maksimal per file: 5MB</div>

                <div style="margin-top:10px;display:flex;flex-direction:column;gap:6px;">
                    <template x-for="(file, index) in files" :key="index">
                        <div style="display:flex;align-items:center;justify-content:space-between;padding:8px 12px;background:#F8FAFC;border:1px solid #EDF0F4;border-radius:6px;">
                            <div style="display:flex;align-items:center;gap:8px;overflow:hidden;">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#666D80" stroke-width="2" stroke-linecap="round"><path d="M21.44 11.05l-9.19 9.19a6 6 0 01-8.49-8.49l9.19-9.19a4 4 0 015.66 5.66l-9.2 9.19a2 2 0 01-2.83-2.83l8.49-8.48"/></svg>
                                <span x-text="file.name" style="font-size:12px;color:#353849;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:250px;"></span>
                            </div>
                            <button type="button" @click="removeFile(index)" title="Hapus file"
                                    style="background:none;border:none;cursor:pointer;color:#DF1C41;padding:4px;display:flex;align-items:center;justify-content:center;border-radius:4px;"
                                    onmouseover="this.style.background='#FFF0F2'" onmouseout="this.style.background='none'">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                            </button>
                        </div>
                    </template>
                </div>
            </div>

            <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;">
                <label style="display:flex;align-items:center;gap:8px;font-size:13px;font-weight:500;color:#353849;cursor:pointer;user-select:none;">
                    <input type="hidden" name="is_published" value="0">
                    <input type="checkbox" name="is_published" value="1" id="chk-publish"
                           style="accent-color:#40C4AA;width:16px;height:16px;cursor:pointer;" checked>
                    Publikasikan sekarang
                    <span style="font-size:11px;color:#808897;">(mahasiswa dapat melihat)</span>
                </label>
                <button type="submit" class="mp-btn primary md"
                        style="background:#40C4AA;box-shadow:0 2px 6px rgba(64,196,170,.3);"
                        onmouseover="this.style.background='#2DA898'" onmouseout="this.style.background='#40C4AA'">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round">
                        <line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/>
                    </svg>
                    Kirim Pengumuman
                </button>
            </div>
        </form>
    </div>
</div>

@else
<div class="mp-alert warning flex-shrink-0">Anda tidak terdaftar sebagai asprak di praktikum yang dipilih.</div>
@endif

{{-- ── Daftar Pengumuman ─────────────────────────────────────────────── --}}
<div class="sec-head flex-shrink-0">
    <span class="sec-bar" style="background:#40C4AA;"></span>
    <span class="sec-title">Pengumuman Terkirim</span>
    <span class="sec-rule"></span>
    <span class="mp-badge neutral sm">{{ $pengumumans->count() }} pengumuman</span>
</div>

@forelse($pengumumans as $p)
@php
    $nameParts = explode(' ', $p->user?->name ?? 'SY');
    $initials  = strtoupper(substr($nameParts[0] ?? 'S', 0, 1) . substr($nameParts[1] ?? $nameParts[0] ?? 'Y', 0, 1));
    $isMine    = $p->user_id === auth()->id();
    $isNew     = $p->created_at && $p->created_at->gt(now()->subHours(12));
@endphp
<div class="mp-card flex-shrink-0"
     style="transition:border-color .2s, box-shadow .2s;"
     onmouseover="this.style.borderColor='#95E3D5';this.style.boxShadow='0 4px 14px rgba(64,196,170,.1)'"
     onmouseout="this.style.borderColor='#DFE1E7';this.style.boxShadow=''">

    <div style="height:3px;background:{{ $isMine ? 'linear-gradient(90deg,#40C4AA,#5EDAC8)' : 'linear-gradient(90deg,#DFE1E7,#ECEFF3)' }};border-radius:14px 14px 0 0;"></div>

    <div style="padding:18px 20px;">
        <div style="display:flex;align-items:flex-start;gap:14px;">

            {{-- Avatar --}}
            <div style="width:36px;height:36px;border-radius:10px;background:{{ $isMine ? '#40C4AA' : '#ECEFF3' }};display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;color:{{ $isMine ? '#fff' : '#353849' }};flex-shrink:0;margin-top:2px;">
                {{ $initials }}
            </div>

            <div style="flex:1;min-width:0;">
                <div style="display:flex;align-items:flex-start;gap:8px;flex-wrap:wrap;margin-bottom:5px;">
                    <div style="font-size:15px;font-weight:700;color:#0D0D12;flex:1;min-width:0;line-height:1.3;">
                        {{ $p->judul }}
                    </div>
                    @if($p->is_published ?? true)
                    <span class="mp-badge success sm"><span class="dot"></span>Dipublikasikan</span>
                    @else
                    <span class="mp-badge neutral sm">Draft</span>
                    @endif
                    @if($isNew)
                    <span class="mp-badge sky sm" style="animation:pulse-badge 2s infinite;"><span class="dot"></span>Baru</span>
                    @endif
                    @if($isMine)
                    <span class="mp-badge neutral sm" style="background:rgba(64,196,170,0.1);color:#176155;">Anda</span>
                    @endif
                </div>

                <div style="font-size:11px;color:#808897;margin-bottom:12px;display:flex;gap:12px;flex-wrap:wrap;align-items:center;">
                    <span style="display:flex;align-items:center;gap:4px;">
                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                            <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2M12 11a4 4 0 100-8 4 4 0 000 8z"/>
                        </svg>
                        <strong style="color:#666D80;">{{ $p->user?->name ?? '—' }}</strong>
                    </span>
                    <span style="display:flex;align-items:center;gap:4px;">
                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                            <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                        </svg>
                        {{ $p->created_at?->locale('id')->isoFormat('D MMM YYYY, HH:mm') }}
                    </span>
                </div>

                <div style="font-size:13px;color:#353849;line-height:1.7;white-space:pre-line;background:#F8FAFC;border:1px solid #EDF0F4;border-radius:8px;padding:14px;">
                    {{ $p->konten }}
                </div>

                @if(!empty($p->lampiran))
                <div style="margin-top:12px;display:flex;flex-wrap:wrap;gap:8px;">
                    @foreach($p->lampiran as $lamp)
                    <a href="{{ app(\App\Services\SupabaseStorage::class)->getPublicUrl($lamp['path'], 'eoffice') }}" target="_blank"
                       style="display:inline-flex;align-items:center;gap:6px;padding:6px 12px;background:#fff;border:1px solid #DFE1E7;border-radius:6px;font-size:12px;font-weight:500;color:#0B266E;text-decoration:none;transition:all .2s;"
                       onmouseover="this.style.borderColor='#0B266E';this.style.background='#F0F4FA'" onmouseout="this.style.borderColor='#DFE1E7';this.style.background='#fff'">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                            <path d="M21.44 11.05l-9.19 9.19a6 6 0 01-8.49-8.49l9.19-9.19a4 4 0 015.66 5.66l-9.2 9.19a2 2 0 01-2.83-2.83l8.49-8.48"/>
                        </svg>
                        <span style="max-width:150px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;" title="{{ $lamp['name'] }}">
                            {{ $lamp['name'] }}
                        </span>
                    </a>
                    @endforeach
                </div>
                @endif
            </div>

            {{-- Delete button --}}
            <form method="POST" action="{{ route('eoffice.manprak.asprak.pengumuman.destroy', $p->id) }}"
                  onsubmit="return confirm('Hapus pengumuman ini?')" style="flex-shrink:0;">
                @csrf @method('DELETE')
                <button type="submit" title="Hapus pengumuman"
                        style="width:32px;height:32px;border-radius:8px;background:#FFF0F2;border:1px solid #DF1C41;color:#DF1C41;display:flex;align-items:center;justify-content:center;cursor:pointer;transition:background .15s;"
                        onmouseover="this.style.background='#FADAE1'" onmouseout="this.style.background='#FFF0F2'">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                        <polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6M10 11v6M14 11v6M9 6V4h6v2"/>
                    </svg>
                </button>
            </form>
        </div>
    </div>
</div>
@empty
<div class="mp-card flex-shrink-0" style="min-height:180px;display:flex;align-items:center;justify-content:center;">
    <div style="padding:36px;text-align:center;">
        <div style="width:48px;height:48px;border-radius:12px;background:#F4F6F8;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5" stroke-linecap="round">
                <path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9M13.73 21a2 2 0 01-3.46 0"/>
            </svg>
        </div>
        <div style="font-size:14px;font-weight:600;color:#0D0D12;margin-bottom:4px;">Belum Ada Pengumuman</div>
        <div style="font-size:12px;color:#666D80;">Tulis pengumuman pertama untuk praktikum ini.</div>
    </div>
</div>
@endforelse

@endif {{-- end if praktikumList not empty --}}

<script>
function fileUploader() {
    return {
        files: [],
        addFiles(e) {
            let selectedFiles = Array.from(e.target.files);
            let totalFiles = this.files.length + selectedFiles.length;
            if (totalFiles > 3) {
                alert('Maksimal 3 file yang dapat diunggah!');
                selectedFiles = selectedFiles.slice(0, 3 - this.files.length);
            }
            this.files = [...this.files, ...selectedFiles];
            this.syncInput();
            e.target.value = ''; // reset so same file can be picked again
        },
        removeFile(index) {
            this.files.splice(index, 1);
            this.syncInput();
        },
        syncInput() {
            let dt = new DataTransfer();
            this.files.forEach(file => dt.items.add(file));
            document.getElementById('hidden-lampiran').files = dt.files;
        }
    }
}
</script>

<style>
@keyframes pulse-badge { 0%,100%{opacity:1} 50%{opacity:.6} }
</style>

</x-eoffice::manajemen-praktikum.layout>
