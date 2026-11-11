<x-eoffice::manajemen-praktikum.layout :breadcrumbPraktikum="$praktikum ?? null" pageTitle="Materi Modul">

{{-- Header --}}
<div class="mp-page-header">
    <div>
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
            <h1 class="mp-page-title">Materi Modul</h1>
            <span class="mp-badge success sm"><span class="dot"></span>Asisten Praktikum</span>
        </div>
        <p class="mp-page-sub">Unggah dan kelola materi untuk modul yang Anda ampu · {{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</p>
    </div>
</div>

{{-- Main Scrollable Container --}}
<div style="display:flex;flex-direction:column;gap:24px;min-height:0;flex:1;overflow-y:auto;padding-right:8px;">

@php
    $modulDenganMateriLama = $modulsForSelect->filter(fn ($modul) => $modul->modulSumber?->materi?->isNotEmpty());
@endphp

@if($modulDenganMateriLama->isNotEmpty())
<div style="flex-shrink:0;">
    <div class="sec-head">
        <span class="sec-bar"></span>
        <span class="sec-title">Materi Periode Sebelumnya</span>
        <span class="sec-rule"></span>
    </div>
    <div class="mp-card">
        <div class="mp-card-header">
            <div>
                <span class="mp-card-title">Gunakan Materi Sebelumnya</span>
                <div style="font-size:12px;color:#666D80;margin-top:4px;">Pilih secara sadar materi yang masih relevan. Materi lama tidak digunakan otomatis.</div>
            </div>
        </div>
        @foreach($modulDenganMateriLama as $modulPilihan)
            <div style="padding:18px 22px;border-top:1px solid #DFE1E7;">
                <div style="font-size:13px;font-weight:700;color:#0D0D12;margin-bottom:10px;">{{ $modulPilihan->urutan }}. {{ $modulPilihan->nama }}</div>
                <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:10px;">
                    @foreach($modulPilihan->modulSumber->materi as $materiLama)
                        @php $sudahDipakai = $materis->contains(fn ($materi) => (int) $materi->materi_sumber_id === (int) $materiLama->id); @endphp
                        <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;padding:12px 14px;border:1px solid #DFE1E7;border-radius:9px;background:#fff;">
                            <div style="min-width:0;">
                                <div style="font-size:12px;font-weight:650;color:#353849;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $materiLama->judul }}</div>
                                <div style="font-size:11px;color:#808897;margin-top:3px;">{{ $modulPilihan->modulSumber->praktikum?->semester ?? '' }} {{ $modulPilihan->modulSumber->praktikum?->tahun_ajaran ?? '' }}</div>
                            </div>
                            @if($sudahDipakai)
                                <span style="font-size:11px;font-weight:700;color:#047857;background:#ECFDF5;padding:5px 8px;border-radius:999px;white-space:nowrap;">Sudah digunakan</span>
                            @else
                                <form method="POST" action="{{ route('eoffice.manprak.asprak.materi.reuse') }}">
                                    @csrf
                                    <input type="hidden" name="modul_id" value="{{ $modulPilihan->id }}">
                                    <input type="hidden" name="materi_sumber_id" value="{{ $materiLama->id }}">
                                    <button type="submit" class="mp-btn secondary sm" style="white-space:nowrap;">Gunakan Materi</button>
                                </form>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</div>
@endif

{{-- Upload Section --}}
@if(isset($praktikum) && $praktikum->is_active)
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
                @php
                $modulOptions = [];
                if(isset($modulsForSelect)) {
                    foreach($modulsForSelect as $m) {
                        $modulOptions[] = ['value' => (string)$m->id, 'label' => $m->urutan . '. ' . $m->nama];
                    }
                }
            @endphp
            <x-eoffice::manajemen-praktikum.ui.select 
                name="modul_id"
                :options="$modulOptions"
                :selected="(string)request('modul_id', (isset($modul) ? $modul?->id : ''))"
                placeholder="Pilih Modul..."
                minWidth="200px"
            />
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

            <div x-data="fileUploaderMateri()">
                <label class="block text-[12px] font-semibold text-[#353849] mb-3">Upload File <span style="color:#DF1C41;">*</span></label>
                
                <div style="display:flex;gap:10px;align-items:center;margin-bottom:8px;">
                    <button type="button" @click="$refs.fileInput.click()" 
                            style="background:#F8FAFC;border:1px solid #DFE1E7;border-radius:6px;padding:8px 16px;font-size:13px;font-weight:600;color:#353849;cursor:pointer;transition:background .2s;"
                            onmouseover="this.style.background='#F0F4FA'" onmouseout="this.style.background='#F8FAFC'">
                        + Tambah File
                    </button>
                    <span x-text="files.length + '/10 file terpilih'" style="font-size:12px;color:#808897;"></span>
                </div>

                <input type="file" x-ref="fileInput" style="display:none" multiple accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx" @change="addFiles($event)">
                <input type="file" id="hidden-materi" name="file[]" multiple style="display:none" required>

                <div style="font-size:11px;color:#808897;">PDF, DOC, DOCX, PPT, PPTX, XLS, XLSX. Ukuran maksimal per file: 50MB</div>

                <div style="margin-top:12px;display:flex;flex-direction:column;gap:8px;">
                    <template x-for="(file, index) in files" :key="index">
                        <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 14px;background:#FAFBFC;border:1px solid #EDF0F4;border-radius:8px;">
                            <div style="display:flex;align-items:center;gap:10px;overflow:hidden;">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#5D6DA2" stroke-width="2" stroke-linecap="round"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                                <div>
                                    <div x-text="file.name" style="font-size:13px;font-weight:500;color:#353849;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:280px;"></div>
                                    <div x-text="(file.size / 1024 / 1024).toFixed(2) + ' MB'" style="font-size:11px;color:#808897;"></div>
                                </div>
                            </div>
                            <button type="button" @click="removeFile(index)" title="Hapus file"
                                    style="background:none;border:none;cursor:pointer;color:#DF1C41;padding:6px;display:flex;align-items:center;justify-content:center;border-radius:6px;"
                                    onmouseover="this.style.background='#FFF0F2'" onmouseout="this.style.background='none'" class="mp-delete-button" aria-label="Hapus"><x-eoffice::manajemen-praktikum.ui.icon name="trash" :size="14" /></button>
                        </div>
                    </template>
                </div>
                
                @error('file')
                <div style="font-size:11px;color:#DF1C41;margin-top:8px;">{{ $message }}</div>
                @enderror
                @error('file.*')
                <div style="font-size:11px;color:#DF1C41;margin-top:8px;">{{ $message }}</div>
                @enderror
            </div>

            <div style="padding-top:16px;border-top:1px solid #DFE1E7;display:flex;gap:12px;">
                <button type="submit" class="mp-btn primary md" style="flex:1;">Simpan Materi</button>
                <button type="reset" class="mp-btn secondary md" style="flex:1;">Batal</button>
            </div>
        </form>
    </div>
</div>
@endif

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
                <div style="width:48px;height:48px;border-radius:10px;background:#D0D6E9;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#5D6DA2" stroke-width="2" stroke-linecap="round">
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
                        <span><x-eoffice::manajemen-praktikum.ui.icon name="calendar" /> Diupload: {{ $materi->created_at?->locale('id')->format('d M Y, H:i') }}</span>
                        @if($materi->file_path)
                        <span><x-eoffice::manajemen-praktikum.ui.icon name="file" /> File: {{ strtoupper(pathinfo($materi->file_path, PATHINFO_EXTENSION)) }}</span>
                        @endif
                    </div>
                </div>

                {{-- Actions --}}
                <div style="display:flex;gap:8px;flex-shrink:0;margin-top:2px;">
                    @if($materi->file_path)
                    <a rel="noopener noreferrer" href="{{ app(\Modules\EOffice\Services\ManajemenPraktikum\PracticumAttachments::class)->publicUrl($materi->file_path, 'eoffice', $materi->judul ?? basename($materi->file_path)) }}" target="_blank" class="mp-btn primary sm" style="text-decoration:none;white-space:nowrap;display:flex;align-items:center;gap:6px;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        Lihat
                    </a>
                    @endif
                    @if(isset($praktikum) && $praktikum->is_active)
                    @if(isset($praktikum) && $praktikum->is_active)
                    <form method="POST" action="{{ route('eoffice.manprak.asprak.materi.destroy', $materi->id) }}" style="display:inline;" onsubmit="return confirm('Hapus materi ini? Tindakan tidak dapat dibatalkan.')">
                        @csrf @method('DELETE')
                        <button type="submit" class="mp-delete-button mp-btn secondary sm" style="color:#DF1C41;" aria-label="Hapus" title="Hapus"><x-eoffice::manajemen-praktikum.ui.icon name="trash" :size="14" /></button>
                    </form>
                    @endif
                    @endif
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

<script>
function fileUploaderMateri() {
    return {
        files: [],
        addFiles(e) {
            let selectedFiles = Array.from(e.target.files);
            let totalFiles = this.files.length + selectedFiles.length;
            if (totalFiles > 10) {
                alert('Maksimal 10 file yang dapat diunggah sekaligus!');
                selectedFiles = selectedFiles.slice(0, 10 - this.files.length);
            }
            this.files = [...this.files, ...selectedFiles];
            this.syncInput();
            e.target.value = ''; // reset so same file can be picked again
            this.updateRequired();
        },
        removeFile(index) {
            this.files.splice(index, 1);
            this.syncInput();
            this.updateRequired();
        },
        syncInput() {
            let dt = new DataTransfer();
            this.files.forEach(file => dt.items.add(file));
            document.getElementById('hidden-materi').files = dt.files;
        },
        updateRequired() {
            document.getElementById('hidden-materi').required = this.files.length === 0;
        }
    }
}
</script>

</x-eoffice::manajemen-praktikum.layout>
