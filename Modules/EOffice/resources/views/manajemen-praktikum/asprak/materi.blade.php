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

{{-- Main Scrollable Container --}}
<div style="display:flex;flex-direction:column;gap:24px;min-height:0;flex:1;overflow-y:auto;padding-right:8px;">

{{-- Upload Section --}}
<div style="flex-shrink:0;">
    <div class="sec-head">
        <span class="sec-bar"></span>
        <span class="sec-title">Upload Materi Baru</span>
        <span class="sec-rule"></span>
    </div>

    <div class="mp-card" style="padding:28px;">
        <form method="POST" action="{{ route('eoffice.manprak.asprak.materi.store') }}" enctype="multipart/form-data" class="flex flex-col gap-5">
            @csrf
            
            <div>
                <label class="block text-[12px] font-semibold text-[#353849] mb-3">Modul <span style="color:#DF1C41;">*</span></label>
                <select name="modul_id" required class="mp-input w-full" style="min-height:44px;">
                    <option value="">Pilih modul</option>
                    @foreach($modulsForSelect as $m)
                    <option value="{{ $m->id }}">{{ $m->nama }}</option>
                    @endforeach
                </select>
                @error('modul_id')
                <div style="font-size:11px;color:#DF1C41;margin-top:4px;">{{ $message }}</div>
                @enderror
            </div>

            <div>
                <label class="block text-[12px] font-semibold text-[#353849] mb-3">Judul Materi <span style="color:#DF1C41;">*</span></label>
                <input name="judul" required class="mp-input w-full" style="min-height:44px;" placeholder="Contoh: Dasar-dasar Elektronika, Praktikum Dioda, dst." value="{{ old('judul') }}">
                @error('judul')
                <div style="font-size:11px;color:#DF1C41;margin-top:4px;">{{ $message }}</div>
                @enderror
            </div>

            <div>
                <label class="block text-[12px] font-semibold text-[#353849] mb-3">Deskripsi <span style="color:#999;">(opsional)</span></label>
                <textarea name="deskripsi" rows="4" class="mp-input w-full resize-none" placeholder="Jelaskan konten materi, topik yang dibahas, atau catatan penting untuk mahasiswa...">{{ old('deskripsi') }}</textarea>
                @error('deskripsi')
                <div style="font-size:11px;color:#DF1C41;margin-top:4px;">{{ $message }}</div>
                @enderror
            </div>

            <div>
                <label class="block text-[12px] font-semibold text-[#353849] mb-3">Upload File <span style="color:#DF1C41;">*</span></label>
                <div id="dropzone-materi" style="border:2px dashed #DFE1E7;border-radius:10px;padding:56px 32px;text-align:center;cursor:pointer;transition:all .2s;background:#FAFBFC;min-height:140px;display:flex;flex-direction:column;align-items:center;justify-content:center;" 
                     onmouseover="this.style.borderColor='#6366F1';this.style.backgroundColor='#F0F4FF';"
                     onmouseout="this.style.borderColor='#DFE1E7';this.style.backgroundColor='#FAFBFC';"
                     onclick="document.getElementById('file-upload-materi').click()">
                    <svg width="52" height="52" viewBox="0 0 24 24" fill="none" stroke="#6366F1" stroke-width="1.5" stroke-linecap="round" style="margin-bottom:16px;opacity:0.8;"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                    <div style="font-size:15px;font-weight:700;color:#0D0D12;margin-bottom:6px;">Klik atau drag file ke sini</div>
                    <div style="font-size:13px;color:#666D80;">PDF, DOC, DOCX, PPT, PPTX, XLS, XLSX hingga 50MB</div>
                    <input type="file" id="file-upload-materi" name="file" style="display:none;" accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx" required>
                </div>
                <div id="file-info-materi" style="margin-top:14px;font-size:13px;color:#666D80;text-align:center;"></div>
                @error('file')
                <div style="font-size:11px;color:#DF1C41;margin-top:8px;text-align:center;">{{ $message }}</div>
                @enderror
            </div>

            <div style="padding-top:16px;border-top:1px solid #DFE1E7;display:flex;gap:12px;">
                <button type="submit" class="mp-btn primary md" style="flex:1;">Simpan Materi</button>
                <button type="reset" class="mp-btn secondary md" style="flex:1;">Batal</button>
            </div>
        </form>

        <script>
            const dropzone = document.getElementById('dropzone-materi');
            const fileInput = document.getElementById('file-upload-materi');
            const fileInfo = document.getElementById('file-info-materi');

            dropzone.addEventListener('click', () => fileInput.click());

            dropzone.addEventListener('dragover', (e) => {
                e.preventDefault();
                dropzone.style.borderColor = '#6366F1';
                dropzone.style.backgroundColor = '#F0F4FF';
            });

            dropzone.addEventListener('dragleave', (e) => {
                e.preventDefault();
                dropzone.style.borderColor = '#DFE1E7';
                dropzone.style.backgroundColor = '#FAFBFC';
            });

            dropzone.addEventListener('drop', (e) => {
                e.preventDefault();
                dropzone.style.borderColor = '#DFE1E7';
                dropzone.style.backgroundColor = '#FAFBFC';
                
                if (e.dataTransfer.files.length > 0) {
                    fileInput.files = e.dataTransfer.files;
                    updateFileInfo();
                }
            });

            fileInput.addEventListener('change', updateFileInfo);

            function updateFileInfo() {
                if (fileInput.files.length > 0) {
                    const file = fileInput.files[0];
                    const sizeMB = (file.size / 1024 / 1024).toFixed(2);
                    fileInfo.innerHTML = '<span style="color:#22c55e;font-weight:600;font-size:14px;">✓ ' + file.name + ' (' + sizeMB + 'MB)</span>';
                } else {
                    fileInfo.innerHTML = '';
                }
            }
        </script>
    </div>
</div>

{{-- Daftar Materi Section --}}
<div style="flex-shrink:0;">
    <div class="sec-head">
        <span class="sec-bar"></span>
        <span class="sec-title">Daftar Materi</span>
        <span class="sec-rule"></span>
        <span class="mp-badge neutral sm">{{ $materis->count() }} materi</span>
    </div>

    @if($materis->count() > 0)
    <div class="mp-card">
        <div class="mp-card-header">
            <span class="mp-card-title">Semua Materi Terupload</span>
            <div class="right">
                <span style="font-size:12px;color:#666D80;">{{ $materis->count() }} file</span>
            </div>
        </div>

        @foreach($materis as $materi)
        <div style="padding:18px 24px;border-bottom:1px solid #DFE1E7;transition:background .15s;" 
             onmouseover="this.style.background='#F9FAFB'" onmouseout="this.style.background=''">
            <div style="display:flex;align-items:flex-start;gap:16px;">
                {{-- File Icon --}}
                <div style="width:48px;height:48px;border-radius:10px;background:#E0E7FF;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#6366F1" stroke-width="2" stroke-linecap="round">
                        <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/>
                    </svg>
                </div>

                {{-- Content --}}
                <div style="flex:1;min-width:0;">
                    <div style="display:flex;align-items:center;gap:10px;margin-bottom:6px;flex-wrap:wrap;">
                        <div style="font-weight:700;font-size:14px;color:#0D0D12;">{{ $materi->judul }}</div>
                        <span class="mp-badge neutral sm" style="font-size:11px;">{{ $materi->modul?->nama ?? 'Uncategorized' }}</span>
                    </div>
                    
                    @if($materi->deskripsi)
                    <div style="font-size:13px;color:#666D80;margin-bottom:8px;line-height:1.5;">{{ $materi->deskripsi }}</div>
                    @endif
                    
                    <div style="font-size:12px;color:#999;display:flex;gap:20px;flex-wrap:wrap;">
                        <span>📅 Diupload: {{ $materi->created_at?->locale('id')->format('d M Y, H:i') }}</span>
                        @if($materi->file_path)
                        <span>📄 File: {{ strtoupper(pathinfo($materi->file_path, PATHINFO_EXTENSION)) }}</span>
                        @endif
                    </div>
                </div>

                {{-- Actions --}}
                <div style="display:flex;gap:8px;flex-shrink:0;margin-top:2px;">
                    @if($materi->file_path)
                    <a href="{{ app(\App\Services\SupabaseStorage::class)->publicUrl($materi->file_path, 'eoffice') }}" target="_blank" class="mp-btn primary sm" style="text-decoration:none;white-space:nowrap;display:flex;align-items:center;gap:6px;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        Unduh
                    </a>
                    @endif
                    <form method="POST" action="{{ route('eoffice.manprak.asprak.materi.destroy', $materi->id) }}" style="display:inline;" onsubmit="return confirm('Hapus materi ini? Tindakan tidak dapat dibatalkan.')">
                        @csrf @method('DELETE')
                        <button type="submit" class="mp-btn secondary sm" style="color:#DF1C41;">Hapus</button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="mp-card" style="padding:64px 28px;text-align:center;">
        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5" stroke-linecap="round" style="margin:0 auto 16px;display:block;opacity:0.6;">
            <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/>
        </svg>
        <div style="font-size:14px;font-weight:600;color:#666D80;">Belum ada materi. Upload materi pertama Anda sekarang!</div>
    </div>
    @endif
</div>

</div>
{{-- End Scrollable Container --}}

</x-eoffice::manajemen-praktikum.layout>