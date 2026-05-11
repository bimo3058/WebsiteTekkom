<x-manajemenmahasiswa::layouts.mahasiswa>
<style>
    .btn-back{width:40px;height:40px;border-radius:50%;background:#fff;border:1px solid #e5e7eb;display:flex;align-items:center;justify-content:center;text-decoration:none;color:#374151;font-size:18px;transition:all .2s;flex-shrink:0}
    .btn-back:hover{background:#f3f4f6}
    .detail-card{background:#fff;border-radius:12px;padding:24px;box-shadow:0 4px 6px -1px rgba(0,0,0,.05);margin-bottom:20px}
    .section-title{font-weight:700;font-size:16px;color:#1f2937;margin-bottom:16px;display:flex;align-items:center;gap:8px;padding-bottom:12px;border-bottom:1px solid #f3f4f6}
    .meta-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:14px}
    .meta-item{background:#f9fafb;border-radius:10px;padding:14px 16px;border:1px solid #f3f4f6}
    .meta-label{font-size:11px;font-weight:700;color:#9ca3af;text-transform:uppercase;margin-bottom:4px}
    .meta-value{font-size:14px;font-weight:600;color:#1f2937}
    .badge-bidang{font-size:11px;font-weight:700;padding:4px 12px;border-radius:20px;background:#eef2ff;color:#4f46e5}
    /* Compare panel */
    .compare-panel{display:grid;grid-template-columns:1fr 1fr;gap:16px}
    @media(max-width:768px){.compare-panel{grid-template-columns:1fr}}
    .compare-col{border-radius:12px;padding:20px}
    .compare-col.rencana{background:#f9fafb;border:1.5px solid #e5e7eb}
    .compare-col.realisasi{background:#f0fdf4;border:1.5px solid #bbf7d0}
    .compare-col-title{font-weight:700;font-size:13px;margin-bottom:14px;display:flex;align-items:center;gap:6px}
    .compare-col.rencana .compare-col-title{color:#6b7280}
    .compare-col.realisasi .compare-col-title{color:#16a34a}
    .compare-item{margin-bottom:10px}
    .compare-item-label{font-size:11px;color:#9ca3af;font-weight:600;text-transform:uppercase;margin-bottom:2px}
    .compare-item-value{font-size:14px;font-weight:600;color:#1f2937}
    /* Form realisasi */
    .form-card{background:#fff;border-radius:12px;padding:24px;box-shadow:0 4px 6px -1px rgba(0,0,0,.05);margin-bottom:20px;border:1.5px solid #bbf7d0}
    .form-label-custom{font-weight:600;font-size:13px;color:#374151;margin-bottom:6px;display:block}
    .form-label-custom .req{color:#dc2626}
    .form-control-custom{border:1.5px solid #e5e7eb;border-radius:10px;padding:10px 14px;font-size:14px;font-weight:500;color:#1f2937;transition:all .2s;background:#fff;width:100%}
    .form-control-custom:focus{border-color:#818cf8;box-shadow:0 0 0 3px rgba(99,102,241,.1);outline:none}
    textarea.form-control-custom{min-height:120px;resize:vertical}
    .btn-save{background:#16a34a;color:#fff;font-weight:600;padding:12px 28px;border-radius:10px;border:none;cursor:pointer;transition:all .2s;font-size:14px}
    .btn-save:hover{background:#15803d;transform:translateY(-1px)}
    /* Photo gallery */
    .photo-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:12px;margin-top:14px}
    .photo-item{border-radius:10px;overflow:hidden;aspect-ratio:4/3;background:#f3f4f6;cursor:pointer;transition:all .3s}
    .photo-item:hover{transform:translateY(-3px);box-shadow:0 8px 20px rgba(79,70,229,.15)}
    .photo-item img{width:100%;height:100%;object-fit:cover}
    /* File upload */
    .file-upload-area{border:2px dashed #d1d5db;border-radius:12px;padding:24px;text-align:center;cursor:pointer;transition:all .2s;background:#fafafa}
    .file-upload-area:hover{border-color:#818cf8;background:#f5f3ff}
    /* Status pill */
    .status-pill{display:inline-flex;align-items:center;gap:6px;padding:6px 16px;border-radius:20px;font-size:12px;font-weight:700}
    .s-disetujui{background:#dcfce7;color:#166534}
    .s-berlangsung{background:#dbeafe;color:#1d4ed8}
    .s-selesai{background:#f3f4f6;color:#374151}
</style>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" style="border-radius:10px;border:none;background:#dcfce7;color:#166534;font-weight:500;font-size:14px;">
        {{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- Header --}}
<div class="d-flex justify-content-between align-items-start mb-4">
    <div class="d-flex align-items-center gap-3">
        <a href="{{ route('manajemenmahasiswa.pelaksanaan.index') }}" class="btn-back">&larr;</a>
        <div>
            <h3 class="fw-bold mb-0 text-dark">Pelaksanaan Kegiatan</h3>
            <p class="text-muted mb-0" style="font-size:14px;font-weight:500;">{{ $proker->judul }}</p>
        </div>
    </div>
    <div class="d-flex align-items-center gap-2">
        <span class="status-pill s-{{ $proker->status }}">{{ $proker->status_label }}</span>
        @if($proker->status === 'selesai')
            <a href="{{ route('manajemenmahasiswa.kegiatan.show', $proker->id) }}"
               class="btn" style="background:#4f46e5;color:#fff;font-weight:600;font-size:13px;padding:8px 18px;border-radius:10px;">
                Lihat di Arsip &rarr;
            </a>
        @endif
    </div>
</div>

{{-- Banner --}}
@if($proker->banner)
    <div style="width:100%;aspect-ratio:16/9;border-radius:12px;overflow:hidden;margin-bottom:24px;">
        <img src="{{ $proker->banner_url }}" alt="{{ $proker->judul }}" style="width:100%;height:100%;object-fit:cover;">
    </div>
@endif

{{-- Info Proker --}}
<div class="detail-card">
    <div class="d-flex flex-wrap gap-2 mb-3">
        @foreach(($proker->bidangs ?? collect()) as $b)
            <span class="badge-bidang">{{ $b->nama_bidang }}</span>
        @endforeach
        @if(!$proker->bidangs || $proker->bidangs->isEmpty())
            <span class="badge-bidang" style="background:#f3e8ff;color:#7c3aed;">Prodi</span>
        @endif
        @foreach(($proker->kategoris ?? collect()) as $k)
            <span class="badge-bidang" style="background:#fef3c7;color:#92400e;">{{ $k->nama_kategori }}</span>
        @endforeach
    </div>
    <h4 class="fw-bold text-dark mb-3">{{ $proker->judul }}</h4>
    <div class="meta-grid">
        @if($proker->ketuaPelaksana)
            <div class="meta-item"><div class="meta-label">Ketua Pelaksana</div><div class="meta-value">{{ $proker->ketuaPelaksana->user->name ?? '-' }}</div></div>
        @endif
        @if($proker->dosenPendamping)
            <div class="meta-item"><div class="meta-label">Dosen Pendamping</div><div class="meta-value">{{ $proker->dosenPendamping->user->name ?? '-' }}</div></div>
        @endif
        @if($proker->tahun)
            <div class="meta-item"><div class="meta-label">Tahun</div><div class="meta-value">{{ $proker->tahun }}</div></div>
        @endif
        @if($proker->disetujuiOleh)
            <div class="meta-item"><div class="meta-label">Disetujui Oleh</div><div class="meta-value">{{ $proker->disetujuiOleh->name }}</div></div>
        @endif
    </div>
</div>

{{-- Perbandingan Rencana vs Realisasi --}}
<div class="detail-card">
    <div class="section-title">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 20V10M12 20V4M6 20v-6"/></svg>
        Rencana vs Realisasi
    </div>
    <div class="compare-panel">
        <div class="compare-col rencana">
            <div class="compare-col-title">&#128221; Rencana (Subbab 1)</div>
            <div class="compare-item"><div class="compare-item-label">Tanggal Mulai</div><div class="compare-item-value">{{ $proker->tanggal_mulai->translatedFormat('d M Y') }}@if($proker->jam_mulai) pukul {{ $proker->jam_mulai_formatted }}@endif</div></div>
            @if($proker->tanggal_selesai)
                <div class="compare-item"><div class="compare-item-label">Tanggal Selesai</div><div class="compare-item-value">{{ $proker->tanggal_selesai->translatedFormat('d M Y') }}</div></div>
            @endif
            @if($proker->lokasi)
                <div class="compare-item"><div class="compare-item-label">Lokasi</div><div class="compare-item-value">{{ $proker->lokasi }}</div></div>
            @endif
            @if($proker->target_peserta)
                <div class="compare-item"><div class="compare-item-label">Target Peserta</div><div class="compare-item-value">{{ number_format($proker->target_peserta) }} orang</div></div>
            @endif
            @if($proker->anggaran && $canViewRestricted)
                <div class="compare-item"><div class="compare-item-label">Est. Anggaran</div><div class="compare-item-value">Rp {{ number_format($proker->anggaran,0,',','.') }}</div></div>
            @endif
        </div>
        <div class="compare-col realisasi">
            <div class="compare-col-title">&#10003; Realisasi (Subbab 2)</div>
            <div class="compare-item">
                <div class="compare-item-label">Tanggal Aktual</div>
                <div class="compare-item-value" style="{{ !$proker->realisasi_tanggal_mulai ? 'color:#f59e0b;font-style:italic' : '' }}">
                    {{ $proker->realisasi_tanggal_mulai ? $proker->realisasi_tanggal_mulai->translatedFormat('d M Y') : 'Belum diisi' }}
                </div>
            </div>
            @if($proker->realisasi_tanggal_selesai)
                <div class="compare-item"><div class="compare-item-label">Selesai Aktual</div><div class="compare-item-value">{{ $proker->realisasi_tanggal_selesai->translatedFormat('d M Y') }}</div></div>
            @endif
            <div class="compare-item">
                <div class="compare-item-label">Lokasi Aktual</div>
                <div class="compare-item-value" style="{{ !$proker->realisasi_lokasi ? 'color:#f59e0b;font-style:italic' : '' }}">
                    {{ $proker->realisasi_lokasi ?? 'Belum diisi' }}
                </div>
            </div>
            <div class="compare-item">
                <div class="compare-item-label">Peserta Hadir</div>
                <div class="compare-item-value" style="{{ $proker->realisasi_peserta === null ? 'color:#f59e0b;font-style:italic' : '' }}">
                    {{ $proker->realisasi_peserta !== null ? number_format($proker->realisasi_peserta).' orang' : 'Belum diisi' }}
                </div>
            </div>
            @if($canViewRestricted)
                <div class="compare-item">
                    <div class="compare-item-label">Pengeluaran Aktual</div>
                    <div class="compare-item-value" style="{{ $proker->realisasi_anggaran === null ? 'color:#f59e0b;font-style:italic' : '' }}">
                        {{ $proker->realisasi_anggaran !== null ? 'Rp '.number_format($proker->realisasi_anggaran,0,',','.') : 'Belum diisi' }}
                    </div>
                </div>
            @endif
        </div>
    </div>
    @if($proker->catatan_pelaksanaan)
        <div style="margin-top:20px;padding-top:16px;border-top:1px solid #f3f4f6;">
            <div class="meta-label" style="margin-bottom:8px;">CATATAN PELAKSANAAN</div>
            <div style="font-size:14px;color:#374151;line-height:1.75;white-space:pre-line;">{{ $proker->catatan_pelaksanaan }}</div>
        </div>
    @endif
</div>

{{-- Form Input / Update Realisasi --}}
@if($canManage && in_array($proker->status, ['disetujui','berlangsung']))
    @php $isUpdate = $proker->has_realisasi; @endphp
    <div class="form-card">
        <div class="section-title" style="color:#16a34a;border-bottom-color:#bbf7d0;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
            {{ $isUpdate ? 'Update Data Realisasi' : 'Input Data Realisasi Hari H' }}
        </div>
        <form action="{{ $isUpdate ? route('manajemenmahasiswa.pelaksanaan.realisasi.update',$proker->id) : route('manajemenmahasiswa.pelaksanaan.realisasi.store',$proker->id) }}"
              method="POST" enctype="multipart/form-data">
            @csrf
            @if($isUpdate) @method('PUT') @endif

            <div class="row g-3 mb-3">
                <div class="col-md-3">
                    <label class="form-label-custom">Tanggal Mulai Aktual <span class="req">*</span></label>
                    <input type="date" name="realisasi_tanggal_mulai" class="form-control form-control-custom"
                           value="{{ old('realisasi_tanggal_mulai', $proker->realisasi_tanggal_mulai?->format('Y-m-d')) }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label-custom">Tanggal Selesai Aktual</label>
                    <input type="date" name="realisasi_tanggal_selesai" class="form-control form-control-custom"
                           value="{{ old('realisasi_tanggal_selesai', $proker->realisasi_tanggal_selesai?->format('Y-m-d')) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label-custom">Lokasi Aktual</label>
                    <input type="text" name="realisasi_lokasi" class="form-control form-control-custom"
                           placeholder="Lokasi saat pelaksanaan berlangsung"
                           value="{{ old('realisasi_lokasi', $proker->realisasi_lokasi) }}">
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-3">
                    <label class="form-label-custom">Jumlah Peserta Hadir</label>
                    <input type="number" name="realisasi_peserta" class="form-control form-control-custom"
                           placeholder="0" min="0"
                           value="{{ old('realisasi_peserta', $proker->realisasi_peserta) }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label-custom">Pengeluaran Aktual (Rp)</label>
                    <input type="number" name="realisasi_anggaran" class="form-control form-control-custom"
                           placeholder="0" min="0"
                           value="{{ old('realisasi_anggaran', $proker->realisasi_anggaran) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label-custom">Status Kegiatan <span class="req">*</span></label>
                    <select name="status_realisasi" class="form-select form-control-custom" required>
                        <option value="berlangsung" {{ old('status_realisasi',$proker->status)==='berlangsung'?'selected':'' }}>&#9654; Sedang Berlangsung</option>
                        <option value="selesai" {{ old('status_realisasi',$proker->status)==='selesai'?'selected':'' }}>&#10003; Selesai (Pindah ke Arsip)</option>
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label-custom">Catatan Pelaksanaan / Laporan Singkat</label>
                <textarea name="catatan_pelaksanaan" class="form-control form-control-custom"
                          placeholder="Tuliskan ringkasan pelaksanaan, hal-hal yang perlu dicatat, kendala, dll...">{{ old('catatan_pelaksanaan', $proker->catatan_pelaksanaan) }}</textarea>
            </div>

            {{-- Upload Foto --}}
            <div class="mb-3">
                <label class="form-label-custom">Upload Foto Dokumentasi</label>
                <div class="file-upload-area" onclick="document.getElementById('fotoInput').click()">
                    <div style="font-size:28px;margin-bottom:6px;opacity:0.5;">&#128247;</div>
                    <p style="color:#6b7280;font-size:13px;font-weight:500;margin:0;">Klik atau drop foto di sini</p>
                    <small style="color:#9ca3af;">JPG, PNG, WEBP • Maks 10MB • Hingga 20 foto</small>
                </div>
                <input type="file" id="fotoInput" name="foto_kegiatan[]" multiple accept="image/*" style="display:none;">
            </div>

            {{-- Upload Dokumen --}}
            <div class="mb-4">
                <label class="form-label-custom">Upload Dokumen (Absensi, LPJ, dll)</label>
                <div class="file-upload-area" onclick="document.getElementById('dokInput').click()">
                    <div style="font-size:28px;margin-bottom:6px;opacity:0.5;">&#128196;</div>
                    <p style="color:#6b7280;font-size:13px;font-weight:500;margin:0;">Klik atau drop dokumen di sini</p>
                    <small style="color:#9ca3af;">PDF, DOC, XLS, PPT • Maks 10MB • Hingga 10 file</small>
                </div>
                <input type="file" id="dokInput" name="dokumen_kegiatan[]" multiple
                       accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx" style="display:none;">
            </div>

            <div class="d-flex justify-content-end">
                <button type="submit" class="btn-save">
                    {{ $isUpdate ? '&#128190; Perbarui Realisasi' : '&#128640; Simpan Data Realisasi' }}
                </button>
            </div>
        </form>
    </div>
@endif

{{-- Foto Dokumentasi yang sudah ada --}}
@if($images && $images->count() > 0)
    <div class="detail-card">
        <div class="section-title">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="18" height="18" x="3" y="3" rx="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
            Galeri Foto Dokumentasi
            <span style="font-size:12px;font-weight:600;color:#6b7280;background:#f3f4f6;padding:3px 10px;border-radius:20px;margin-left:4px;">{{ $images->count() }} foto</span>
        </div>
        <div class="photo-grid">
            @foreach($images->values() as $img)
                <div class="photo-item">
                    <img src="{{ $img->url }}" alt="{{ $img->judul_file }}" loading="lazy">
                </div>
            @endforeach
        </div>
    </div>
@endif

{{-- Dokumen --}}
@if($documents && $documents->count() > 0 && $canViewRestricted)
    <div class="detail-card">
        <div class="section-title">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/></svg>
            Dokumen
        </div>
        <div style="display:flex;flex-direction:column;gap:8px;">
            @foreach($documents as $doc)
                <a href="{{ $doc->url }}" target="_blank" download
                   style="display:flex;align-items:center;gap:12px;padding:14px 16px;background:#f9fafb;border:1px solid #e5e7eb;border-radius:10px;text-decoration:none;transition:all .2s;color:#1f2937;">
                    <span style="font-size:24px;">&#128196;</span>
                    <span style="flex:1;font-size:14px;font-weight:600;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $doc->judul_file ?: $doc->nama_file }}</span>
                    <span style="font-size:12px;color:#6b7280;">Unduh</span>
                </a>
            @endforeach
        </div>
    </div>
@endif

</x-manajemenmahasiswa::layouts.mahasiswa>
