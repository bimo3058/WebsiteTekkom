<x-eoffice::manajemen-praktikum.layout pageTitle="Edit Praktikum — {{ $praktikum->nama }}">

{{-- Header --}}
<div class="mp-page-header" style="flex-shrink:0;">
    <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
        <a href="{{ route('eoffice.manprak.admin.praktikum.detail', $praktikum->id) }}"
           style="display:flex;align-items:center;gap:4px;font-size:12px;color:#A4ABB8;text-decoration:none;white-space:nowrap;flex-shrink:0;transition:color .15s;"
           onmouseover="this.style.color='#0B266E'" onmouseout="this.style.color='#A4ABB8'">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="15 18 9 12 15 6"/></svg>
            Detail Praktikum
        </a>
        <span style="color:#DFE1E7;">›</span>
        <h1 class="mp-page-title">Edit Praktikum</h1>
        <span class="mp-badge error sm"><span class="dot"></span>Admin</span>
    </div>
</div>

{{-- Flash messages --}}
@if(session('success'))
<div class="mp-alert success flex-shrink-0">{{ session('success') }}</div>
@endif
@if($errors->any())
<div class="mp-alert warning flex-shrink-0">
    <strong>Ada kesalahan:</strong>
    <ul style="margin:4px 0 0 16px;">
        @foreach($errors->all() as $e)<li style="font-size:12px;">{{ $e }}</li>@endforeach
    </ul>
</div>
@endif

{{-- Form card --}}
<div style="background:#fff;border:1px solid #DFE1E7;border-radius:14px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,.04);flex-shrink:0;">

    {{-- Card header --}}
    <div style="padding:14px 20px;border-bottom:1px solid #DFE1E7;background:#FAFAFA;display:flex;align-items:center;gap:10px;">
        <div style="width:34px;height:34px;border-radius:9px;background:rgba(11,38,110,0.08);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#0B266E" stroke-width="1.8" stroke-linecap="round">
                <path d="M9 21V13H5C3.895 13 3 13.895 3 15v4C3 20.105 3.895 21 5 21H9ZM9 21H15M9 21V10C9 8.895 9.895 8 11 8H15V21M15 21H19C20.105 21 21 20.105 21 19V5C21 3.895 20.105 3 19 3h-2C15.895 3 15 3.895 15 5V21Z"/>
            </svg>
        </div>
        <div>
            <div style="font-size:14px;font-weight:700;color:#0D0D12;">{{ $praktikum->nama }}</div>
            <div style="font-size:11px;color:#A4ABB8;">
                @if($praktikum->kode)<span style="font-family:monospace;color:#0B266E;">{{ $praktikum->kode }}</span> · @endif
                {{ $praktikum->semester }} {{ $praktikum->tahun_ajaran }}
            </div>
        </div>
    </div>

    {{-- Form --}}
    <form method="POST" action="{{ route('eoffice.manprak.admin.praktikum.update', $praktikum->id) }}" id="editForm">
        @csrf
        @method('PUT')

        <div style="padding:24px 20px;display:grid;grid-template-columns:1fr 1fr;gap:20px;">

            {{-- Nama Praktikum --}}
            <div style="grid-column:1/-1;">
                <label style="display:block;font-size:12px;font-weight:600;color:#353849;margin-bottom:5px;">
                    Nama Praktikum <span style="color:#EF4444;">*</span>
                </label>
                <input type="text" name="nama" required
                       value="{{ old('nama', $praktikum->nama) }}"
                       placeholder="Contoh: Praktikum Sistem Basis Data"
                       class="mp-input" style="width:100%;">
            </div>

            {{-- Mata Kuliah --}}
            <div style="grid-column:1/-1;">
                <label style="display:block;font-size:12px;font-weight:600;color:#353849;margin-bottom:5px;">Mata Kuliah Praktikum</label>
                <select name="matkul_id" class="mp-input mp-select" style="width:100%;">
                    <option value="">— Tidak dikaitkan dengan matkul —</option>
                    @foreach(($matkulList ?? collect())->groupBy('semester') as $sem => $items)
                    <optgroup label="Semester {{ $sem }}">
                        @foreach($items as $mk)
                        <option value="{{ $mk->id }}"
                            {{ old('matkul_id', $praktikum->matkul_id) == $mk->id ? 'selected' : '' }}>
                            [{{ $mk->kode }}] {{ $mk->nama }}
                        </option>
                        @endforeach
                    </optgroup>
                    @endforeach
                </select>
            </div>

            {{-- Tahun Ajaran --}}
            <div>
                <label style="display:block;font-size:12px;font-weight:600;color:#353849;margin-bottom:5px;">
                    Tahun Ajaran <span style="color:#EF4444;">*</span>
                </label>
                <input type="number" name="tahun_ajaran" required
                       value="{{ old('tahun_ajaran', $praktikum->tahun_ajaran) }}"
                       placeholder="2025" min="2000" max="2099"
                       class="mp-input" style="width:100%;">
            </div>

            {{-- Semester --}}
            <div>
                <label style="display:block;font-size:12px;font-weight:600;color:#353849;margin-bottom:5px;">
                    Semester <span style="color:#EF4444;">*</span>
                </label>
                <select name="semester" required class="mp-input mp-select" style="width:100%;">
                    <option value="Ganjil" {{ old('semester', $praktikum->semester) === 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                    <option value="Genap"  {{ old('semester', $praktikum->semester) === 'Genap'  ? 'selected' : '' }}>Genap</option>
                </select>
            </div>

            {{-- Dosen Pengampu --}}
            <div style="grid-column:1/-1;">
                <label style="display:block;font-size:12px;font-weight:600;color:#353849;margin-bottom:5px;">Dosen Pengampu</label>
                <select name="dosen_id" class="mp-input mp-select" style="width:100%;">
                    <option value="">— Pilih Dosen —</option>
                    @foreach($dosenList ?? [] as $d)
                    <option value="{{ $d->id }}"
                        {{ old('dosen_id', $praktikum->dosen_id) == $d->id ? 'selected' : '' }}>
                        {{ $d->name }}
                    </option>
                    @endforeach
                </select>
                <div style="font-size:11px;color:#A4ABB8;margin-top:4px;">Hanya user dengan role dosen yang tersedia.</div>
            </div>

            {{-- Status --}}
            <div>
                <label style="display:block;font-size:12px;font-weight:600;color:#353849;margin-bottom:5px;">Status</label>
                <select name="status" class="mp-input mp-select" style="width:100%;">
                    <option value="aktif"    {{ old('status', $praktikum->status) === 'aktif'    ? 'selected' : '' }}>Aktif</option>
                    <option value="nonaktif" {{ old('status', $praktikum->status) === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>

            {{-- Kode (readonly, untuk referensi) --}}
            <div>
                <label style="display:block;font-size:12px;font-weight:600;color:#353849;margin-bottom:5px;">Kode Praktikum</label>
                <input type="text" value="{{ $praktikum->kode ?? '—' }}" readonly
                       class="mp-input" style="width:100%;background:#F6F8FA;color:#A4ABB8;font-family:monospace;cursor:default;">
                <div style="font-size:11px;color:#A4ABB8;margin-top:4px;">Kode di-generate otomatis dan tidak dapat diubah manual.</div>
            </div>

            {{-- Deskripsi --}}
            <div style="grid-column:1/-1;">
                <label style="display:block;font-size:12px;font-weight:600;color:#353849;margin-bottom:5px;">Deskripsi</label>
                <textarea name="deskripsi" rows="3"
                          placeholder="Deskripsi singkat tentang praktikum ini..."
                          class="mp-input" style="width:100%;resize:vertical;">{{ old('deskripsi', $praktikum->deskripsi) }}</textarea>
            </div>

        </div>

        {{-- Footer actions --}}
        <div style="padding:14px 20px;border-top:1px solid #DFE1E7;background:#FAFAFA;display:flex;align-items:center;justify-content:space-between;gap:10px;">
            <a href="{{ route('eoffice.manprak.admin.praktikum.detail', $praktikum->id) }}"
               class="mp-btn secondary md" style="text-decoration:none;">
                Batal
            </a>
            <button type="submit" class="mp-btn primary md" style="display:flex;align-items:center;gap:6px;">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>

{{-- Zona Bahaya --}}
<div style="background:#fff;border:1px solid #FADAE1;border-radius:14px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,.04);flex-shrink:0;margin-top:4px;">
    <div style="padding:14px 20px;border-bottom:1px solid #FADAE1;background:#FFF5F6;">
        <div style="font-size:13px;font-weight:700;color:#DF1C41;">Zona Bahaya</div>
    </div>
    <div style="padding:16px 20px;display:flex;align-items:center;justify-content:space-between;gap:16px;">
        <div>
            <div style="font-size:13px;font-weight:600;color:#0D0D12;margin-bottom:2px;">Hapus Praktikum</div>
            <div style="font-size:12px;color:#666D80;">Menghapus praktikum akan menghapus semua data terkait termasuk daftar praktikan dan modul. Tindakan ini tidak dapat diurungkan.</div>
        </div>
        <form method="POST" action="{{ route('eoffice.manprak.admin.praktikum.destroy', $praktikum->id) }}"
              onsubmit="return confirm('PERINGATAN: Hapus praktikum \'{{ addslashes($praktikum->nama) }}\'? Semua data terkait akan ikut terhapus dan tidak bisa dikembalikan.')"
              style="flex-shrink:0;">
            @csrf @method('DELETE')
            <button type="submit" class="mp-btn destructive md" style="white-space:nowrap;">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4a1 1 0 011-1h4a1 1 0 011 1v2"/></svg>
                Hapus Praktikum
            </button>
        </form>
    </div>
</div>

</x-eoffice::manajemen-praktikum.layout>