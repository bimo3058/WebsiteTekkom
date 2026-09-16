<x-eoffice::manajemen-praktikum.layout pageTitle="Buat Tugas">

{{-- Header --}}
<div class="mp-page-header">
    <div>
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
            <h1 class="mp-page-title">Buat Tugas Baru</h1>
            <span class="mp-badge success sm"><span class="dot"></span>Asisten Praktikum</span>
        </div>
        <p class="mp-page-sub">Pilih modul yang Anda ampu lalu isi detail tugas · {{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</p>
    </div>
    <div class="mp-page-actions">
        <a href="{{ route('eoffice.manprak.asprak.tugas.index') }}" class="mp-btn secondary md" style="text-decoration:none;">Kembali</a>
    </div>
</div>

{{-- Section header --}}
<div class="sec-head">
    <span class="sec-bar"></span>
    <span class="sec-title">Detail Tugas</span>
    <span class="sec-rule"></span>
</div>

<div class="mp-card" style="padding:28px;flex-shrink:0;">
    <form method="POST"
          action="{{ route('eoffice.manprak.asprak.tugas.store') }}"
          enctype="multipart/form-data"
          style="display:flex;flex-direction:column;gap:20px;">
        @csrf

        {{-- Modul --}}
        <div>
            <label style="display:block;font-size:12px;font-weight:600;color:#353849;margin-bottom:6px;">
                Modul <span style="color:#DF1C41;">*</span>
            </label>
            <select name="modul_id" required class="mp-input mp-select" style="min-height:44px;">
                <option value="">Pilih modul</option>
                @foreach($moduls as $m)
                <option value="{{ $m->id }}" @selected(old('modul_id') == $m->id)>
                    {{ $m->nama }} — {{ $m->praktikum?->nama }}
                </option>
                @endforeach
            </select>
            @error('modul_id')
            <div style="font-size:11px;color:#DF1C41;margin-top:4px;">{{ $message }}</div>
            @enderror
        </div>

        {{-- Jenis Tugas --}}
        <div>
            <label style="display:block;font-size:12px;font-weight:600;color:#353849;margin-bottom:8px;">
                Jenis Tugas <span style="color:#DF1C41;">*</span>
            </label>
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:10px;">
                @foreach(['tugas_pendahuluan' => 'Tugas Pendahuluan', 'praktikum' => 'Praktikum', 'laporan' => 'Laporan', 'responsi' => 'Responsi'] as $val => $label)
                <label style="display:flex;align-items:center;gap:10px;padding:12px 14px;border:2px solid {{ old('jenis_tugas') === $val ? '#5D6DA2' : '#DFE1E7' }};border-radius:10px;cursor:pointer;background:{{ old('jenis_tugas') === $val ? '#F0F1FE' : '#FAFBFC' }};transition:border-color .15s,background .15s;"
                       x-data
                       :style="$refs.jenis_{{ $val }}.checked ? 'border-color:#5D6DA2;background:#F0F1FE;' : 'border-color:#DFE1E7;background:#FAFBFC;'">
                    <input type="radio" name="jenis_tugas" value="{{ $val }}"
                           id="jenis_{{ $val }}" x-ref="jenis_{{ $val }}"
                           @change="$el.closest('[x-data]').querySelectorAll('label').forEach(l=>l.style.cssText=''); $el.parentElement.style.borderColor='#5D6DA2'; $el.parentElement.style.background='#F0F1FE';"
                           @if(old('jenis_tugas') === $val) checked @endif
                           style="accent-color:#5D6DA2;width:16px;height:16px;cursor:pointer;">
                    <span style="font-size:13px;font-weight:600;color:#353849;">{{ $label }}</span>
                </label>
                @endforeach
            </div>
            @error('jenis_tugas')
            <div style="font-size:11px;color:#DF1C41;margin-top:6px;">{{ $message }}</div>
            @enderror
        </div>

        {{-- Judul --}}
        <div>
            <label style="display:block;font-size:12px;font-weight:600;color:#353849;margin-bottom:6px;">
                Judul Tugas <span style="color:#DF1C41;">*</span>
            </label>
            <input name="judul" required class="mp-input" style="min-height:44px;"
                   placeholder="Contoh: Percobaan Seri RLC, Implementasi Flip-flop, dst."
                   value="{{ old('judul') }}">
            @error('judul')
            <div style="font-size:11px;color:#DF1C41;margin-top:4px;">{{ $message }}</div>
            @enderror
        </div>

        {{-- Deskripsi --}}
        <div>
            <label style="display:block;font-size:12px;font-weight:600;color:#353849;margin-bottom:6px;">
                Deskripsi <span style="color:#999;">(opsional)</span>
            </label>
            <textarea name="deskripsi" rows="5" class="mp-input" style="resize:vertical;"
                      placeholder="Jelaskan detail tugas, instruksi pengerjaan, kriteria penilaian, atau catatan penting lainnya...">{{ old('deskripsi') }}</textarea>
            @error('deskripsi')
            <div style="font-size:11px;color:#DF1C41;margin-top:4px;">{{ $message }}</div>
            @enderror
        </div>

        {{-- Upload PDF (Alpine.js, no <script> tag) --}}
        <div x-data="{
                fileName: '',
                fileSize: '',
                hasFile: false,
                fmt(b) { return b < 1048576 ? (b/1024).toFixed(1)+' KB' : (b/1048576).toFixed(1)+' MB'; },
                pick(files) {
                    const f = files[0];
                    if (!f) return;
                    this.fileName = f.name;
                    this.fileSize = this.fmt(f.size);
                    this.hasFile  = true;
                    const dt = new DataTransfer(); dt.items.add(f);
                    document.getElementById('file-pdf').files = dt.files;
                },
                clear() { this.hasFile=false; this.fileName=''; this.fileSize=''; document.getElementById('file-pdf').value=''; },
                drop(e) { e.preventDefault(); if(e.dataTransfer.files[0]?.type==='application/pdf') this.pick(e.dataTransfer.files); }
             }">
            <label style="display:block;font-size:12px;font-weight:600;color:#353849;margin-bottom:6px;">
                Lampiran PDF <span style="color:#999;">(opsional · maks. 10 MB)</span>
            </label>

            {{-- Drop zone --}}
            <div @click="$refs.filePdf.click()"
                 @dragover.prevent="$el.style.borderColor='#5D6DA2';$el.style.background='#F0F1FE'"
                 @dragleave="$el.style.borderColor=hasFile?'#40C4AA':'#DFE1E7';$el.style.background=hasFile?'#F0FBF9':'#FAFBFC'"
                 @drop="drop($event);$el.style.borderColor='#40C4AA';$el.style.background='#F0FBF9'"
                 :style="hasFile ? 'border-color:#40C4AA;background:#F0FBF9;border-style:solid;' : 'border-color:#DFE1E7;background:#FAFBFC;'"
                 style="border:2px dashed #DFE1E7;border-radius:12px;padding:28px 20px;text-align:center;cursor:pointer;transition:border-color .2s,background .2s;display:flex;align-items:center;justify-content:center;min-height:110px;">

                {{-- Placeholder --}}
                <div x-show="!hasFile" style="pointer-events:none;">
                    <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5" stroke-linecap="round" style="margin:0 auto 10px;display:block;">
                        <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                        <line x1="12" y1="18" x2="12" y2="12"/>
                        <line x1="9" y1="15" x2="15" y2="15"/>
                    </svg>
                    <div style="font-size:13px;font-weight:600;color:#353849;">Klik atau drag & drop file PDF</div>
                    <div style="font-size:11px;color:#A4ABB8;margin-top:4px;">Format: PDF · Maksimal 10 MB</div>
                </div>

                {{-- Preview file terpilih --}}
                <div x-show="hasFile" style="display:flex;align-items:center;gap:12px;width:100%;" @click.stop>
                    <div style="width:38px;height:38px;border-radius:8px;background:#FADAE1;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#DF1C41" stroke-width="2" stroke-linecap="round">
                            <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                            <polyline points="14 2 14 8 20 8"/>
                        </svg>
                    </div>
                    <div style="flex:1;min-width:0;text-align:left;">
                        <div x-text="fileName" style="font-size:13px;font-weight:600;color:#353849;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"></div>
                        <div x-text="fileSize" style="font-size:11px;color:#666D80;margin-top:2px;"></div>
                    </div>
                    <button type="button" @click.stop="clear()"
                            style="padding:6px;background:rgba(0,0,0,.04);border:none;border-radius:6px;cursor:pointer;color:#666D80;display:flex;align-items:center;justify-content:center;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                            <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                        </svg>
                    </button>
                </div>
            </div>

            <input type="file" id="file-pdf" name="file" accept=".pdf" x-ref="filePdf"
                   style="display:none;" @change="pick($event.target.files)">
            @error('file')
            <div style="font-size:11px;color:#DF1C41;margin-top:4px;">{{ $message }}</div>
            @enderror
        </div>

        {{-- Deadline & Publikasi --}}
        <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(240px, 1fr));gap:16px;">
            <div>
                <label style="display:block;font-size:12px;font-weight:600;color:#353849;margin-bottom:6px;">
                    Deadline AC <span style="color:#999;">(Tenggat Pengumpulan Awal)</span>
                </label>
                <input type="datetime-local" name="deadline" class="mp-input" style="min-height:44px;" value="{{ old('deadline') }}">
                @error('deadline')
                <div style="font-size:11px;color:#DF1C41;margin-top:4px;">{{ $message }}</div>
                @enderror
            </div>

            <div>
                <label style="display:block;font-size:12px;font-weight:600;color:#353849;margin-bottom:6px;">
                    Deadline ACC <span style="color:#999;">(Tenggat Revisi / Batas Akhir)</span>
                </label>
                <input type="datetime-local" name="deadline_acc" class="mp-input" style="min-height:44px;" value="{{ old('deadline_acc') }}">
                @error('deadline_acc')
                <div style="font-size:11px;color:#DF1C41;margin-top:4px;">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div>
            <label style="display:flex;align-items:center;gap:10px;cursor:pointer;user-select:none;margin-top:4px;">
                <input type="hidden" name="is_published" value="0">
                <input type="checkbox" name="is_published" value="1" id="chk-published"
                       style="accent-color:#5D6DA2;width:18px;height:18px;cursor:pointer;"
                       @if(old('is_published', true)) checked @endif>
                <div>
                    <span style="font-size:13px;font-weight:600;color:#353849;display:block;">Publikasikan Sekarang</span>
                    <span style="font-size:11px;color:#666D80;display:block;margin-top:2px;">Langsung terlihat oleh mahasiswa</span>
                </div>
            </label>
        </div>

        {{-- Actions --}}
        <div style="padding-top:16px;border-top:1px solid #DFE1E7;display:flex;gap:12px;">
            <button type="submit" class="mp-btn primary md" style="flex:1;justify-content:center;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                    <polyline points="20 6 9 17 4 12"/>
                </svg>
                Simpan Tugas
            </button>
            <a href="{{ route('eoffice.manprak.asprak.tugas.index') }}"
               class="mp-btn secondary md"
               style="text-decoration:none;flex:1;text-align:center;justify-content:center;">
               Batal
            </a>
        </div>
    </form>
</div>

</x-eoffice::manajemen-praktikum.layout>