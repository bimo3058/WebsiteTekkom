<x-dynamic-component :component="$layout">

@include('manajemenmahasiswa::direktori.partials.palette')
@include('manajemenmahasiswa::partials.card-frame')

@push('styles')
<style>
    /* Kotak putih pembungkus halaman sengaja tidak lagi dibuat transparan, supaya latar
       halaman ini sama dengan Direktori Mahasiswa: konten di dalam kotak putih di atas
       latar abu, seperti dashboard Super Admin. */

    /* ── Card profil: susunan disamakan dengan halaman Detail User di User Management
       global (superadmin/users/show) — toolbar di atas, avatar kiri, grid label:nilai.
       Kelasnya sama dengan Detail Mahasiswa. ── */
    .detail-box {
        background: #fff;
        border: 1px solid var(--c-border);
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06);
        overflow: hidden;
    }
    .detail-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
        padding: 12px 16px;
        border-bottom: 1px solid var(--c-border);
    }
    .detail-toolbar-left { display: flex; align-items: center; gap: 16px; }
    .detail-toolbar-right { display: flex; gap: 10px; flex-wrap: wrap; }
    .detail-title {
        font-size: 14px;
        font-weight: 800;
        color: var(--c-fg);
        margin: 0;
        text-transform: uppercase;
        letter-spacing: 0.02em;
    }
    .detail-profile { padding: 20px; display: flex; gap: 20px; align-items: flex-start; }
    /* Avatar inisial netral, sama dengan komponen user-avatar global ukuran xl */
    .detail-avatar {
        width: 84px;
        height: 84px;
        border-radius: 50%;
        background: #F3F4F6;
        border: 1.5px solid #E5E7EB;
        color: #6B7280;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        font-weight: 700;
        flex-shrink: 0;
        overflow: hidden;
    }
    .detail-avatar img { width: 100%; height: 100%; object-fit: cover; }
    .detail-name-row { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; margin-bottom: 4px; }
    .detail-name { font-size: 20px; font-weight: 800; color: var(--c-fg); margin: 0; letter-spacing: -0.02em; }
    .detail-sub { font-size: 14px; font-weight: 500; color: var(--c-fg-muted); margin: 0 0 16px 0; }
    /* Badge outline; warnanya tetap dari palette status karir, hanya latar dibuat transparan */
    .badge-outline {
        display: inline-flex !important;
        align-items: center;
        gap: 5px;
        padding: 2px 10px !important;
        border-radius: 99px !important;
        font-size: 10px !important;
        font-weight: 700;
        background: transparent !important;
        border: 1px solid currentColor;
        box-shadow: none !important;
    }
    .detail-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px 40px;
        max-width: 900px;
    }
    .detail-row { display: flex; align-items: center; min-width: 0; }
    .detail-row.full { grid-column: 1 / -1; }
    .detail-label { width: 170px; font-size: 13px; color: #94A3B8; flex-shrink: 0; font-weight: 500; }
    .detail-value { font-size: 13px; font-weight: 600; color: #334155; min-width: 0; overflow-wrap: anywhere; }
    .detail-value.empty { color: var(--c-fg-placeholder); font-style: italic; font-weight: 400; }
    .detail-value a {
        color: #0077b5; /* warna brand LinkedIn, sengaja tidak memakai token */
        text-decoration: none;
        font-weight: 600;
    }
    .detail-value a:hover { text-decoration: underline; }
    .detail-divider { border-top: 1px solid var(--c-border); margin: 20px 0 16px; }
    .detail-subtitle {
        font-size: 12px;
        font-weight: 800;
        color: var(--c-fg);
        text-transform: uppercase;
        letter-spacing: 0.04em;
        margin: 0 0 12px;
    }
    @media (max-width: 768px) {
        .detail-profile { flex-direction: column; }
        .detail-grid { grid-template-columns: 1fr; }
        .detail-label { width: 130px; }
    }
</style>
@endpush

<!-- Card Profil (susunan mengikuti Detail User di User Management global) -->
<div class="detail-box">
    {{-- ── Toolbar ── --}}
    <div class="detail-toolbar">
        <div class="detail-toolbar-left">
            <a href="{{ route('manajemenmahasiswa.direktori.alumni.index') }}" class="mk-btn mk-btn--secondary mk-btn--icon mk-btn--sm" title="Kembali" aria-label="Kembali">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M15 19l-7-7 7-7"/></svg>
            </a>
            <h1 class="detail-title">Detail Alumni</h1>
        </div>
        <div class="detail-toolbar-right">
            @if($canDownloadCv ?? false)
                <a href="{{ route('manajemenmahasiswa.direktori.alumni.cv', $alumni->id) }}" target="_blank" class="mk-btn mk-btn--secondary mk-btn--sm">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                    Download CV
                </a>
            @endif
            @if($isAdmin)
                <a href="{{ route('manajemenmahasiswa.direktori.alumni.edit', $alumni->id) }}" class="mk-btn mk-btn--primary mk-btn--sm">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    Edit Data
                </a>
            @endif
        </div>
    </div>

    {{-- ── Profil ── --}}
    <div class="detail-profile">
        <div class="detail-avatar">
            @if($alumni->user && $alumni->user->avatar_url)
                <img src="{{ $alumni->user->avatar_url }}" alt="{{ $alumni->user->name }}">
            @else
                {{ strtoupper(substr($alumni->user->name ?? 'A', 0, 1)) }}
            @endif
        </div>

        <div style="flex: 1; min-width: 0;">
            <div class="detail-name-row">
                <h2 class="detail-name">{{ $alumni->user->name ?? 'Tanpa Nama' }}</h2>
                <span class="status-badge-lg {{ $alumni->status_karir ?? 'belum_terdata' }} badge-outline">
                    @if(in_array($alumni->status_karir, ['bekerja', 'wirausaha'])) <span class="material-symbols-outlined" style="font-size: 12px;">work</span>
                    @elseif($alumni->status_karir == 'studi_lanjut') <span class="material-symbols-outlined" style="font-size: 12px;">school</span>
                    @else <span class="material-symbols-outlined" style="font-size: 12px;">hourglass_empty</span>
                    @endif
                    {{ $alumni->status_karir_label }}
                </span>
            </div>
            <p class="detail-sub">NIM: <span style="font-family: monospace; color: var(--c-primary); font-weight: 700;">{{ $alumni->nim }}</span></p>

            <div class="detail-grid">
                <div class="detail-row">
                    <span class="detail-label">Angkatan</span>
                    <span class="detail-value">{{ $alumni->angkatan }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Tahun Lulus</span>
                    <span class="detail-value">{{ $alumni->tahun_lulus }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">WhatsApp / Telepon</span>
                    <span class="detail-value {{ empty($alumni->user->whatsapp) ? 'empty' : '' }}">{{ $alumni->user->whatsapp ?? 'Belum diisi' }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Email Pribadi</span>
                    <span class="detail-value {{ empty($alumni->user->personal_email) ? 'empty' : '' }}">{{ $alumni->user->personal_email ?? 'Belum diisi' }}</span>
                </div>
            </div>

            <div class="detail-divider"></div>
            <div class="detail-subtitle">Informasi Karir &amp; Pekerjaan</div>

            <div class="detail-grid">
                <div class="detail-row">
                    <span class="detail-label">Perusahaan / Instansi</span>
                    <span class="detail-value {{ !$alumni->perusahaan ? 'empty' : '' }}">{{ $alumni->perusahaan ?: 'Belum diisi' }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Posisi / Jabatan</span>
                    <span class="detail-value {{ !$alumni->jabatan ? 'empty' : '' }}">{{ $alumni->jabatan ?: 'Belum diisi' }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Bidang Industri</span>
                    <span class="detail-value {{ !$alumni->bidang_industri ? 'empty' : '' }}">{{ $alumni->bidang_industri ? $alumni->bidang_industri_label : 'Belum diisi' }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Tahun Mulai Bekerja</span>
                    <span class="detail-value {{ !$alumni->tahun_mulai_bekerja ? 'empty' : '' }}">
                        {{ $alumni->tahun_mulai_bekerja ?: 'Belum diisi' }}
                    </span>
                </div>
                <div class="detail-row full">
                    <span class="detail-label">LinkedIn</span>
                    @if($alumni->linkedin)
                        <span class="detail-value"><a href="{{ $alumni->linkedin }}" target="_blank">🔗 {{ $alumni->linkedin }}</a></span>
                    @else
                        <span class="detail-value empty">Belum ada tautan LinkedIn</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════════════ --}}
{{-- SECTION: Prestasi & Riwayat Kegiatan (visible: admin, gpm, dosen, pengurus) --}}
{{-- ═══════════════════════════════════════════════════════════════════════ --}}
@if($canSeeHistory)

@push('styles')
<style>
    .history-section {
        background: #fff;
        border: 1px solid var(--c-border);
        border-radius: 16px;
        padding: 24px;
        margin-top: 20px;
        box-shadow: var(--shadow-card);
    }
    .history-section-title {
        font-size: 13px;
        font-weight: 700;
        color: var(--c-fg);
        text-transform: uppercase;
        letter-spacing: 0.04em;
        padding-bottom: 10px;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .history-section-title span { display: flex; align-items: center; gap: 8px; }
    .prestasi-item {
        background: var(--c-grey-0);
        border: 1px solid var(--c-border);
        border-radius: 10px;
        padding: 14px 18px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 8px;
    }
    /* Warna tiap tingkat ada di partials/palette */
    .tingkat-badge {
        font-size: 10px; font-weight: 700;
        padding: 2px 8px; border-radius: 12px;
        text-transform: uppercase;
    }

    .riwayat-table { width: 100%; border-collapse: separate; border-spacing: 0; }
    .riwayat-table thead th {
        background: #FAFAFA; padding: 10px 14px;
        font-size: 12px; font-weight: 700; color: var(--c-fg-muted);
        text-transform: uppercase; letter-spacing: 0.05em;
        border-bottom: 1px solid var(--c-border);
    }
    .riwayat-table tbody td {
        padding: 12px 14px; font-size: 14px; color: var(--c-fg);
        border-bottom: 1px solid #F3F4F6; vertical-align: middle;
    }
    .riwayat-table tbody tr:hover { background: #FAFAFA; }
    .peran-badge {
        font-size: 11px; font-weight: 700;
        padding: 3px 10px; border-radius: 20px; display: inline-block;
    }
    .peran-badge.ketua   { background: var(--c-warning-subtle); color: var(--c-warning); }
    .peran-badge.anggota { background: var(--c-primary-subtle); color: var(--c-primary); }
    .peran-badge.panitia { background: var(--c-sky-subtle);     color: var(--c-sky); }
    .peran-badge.peserta { background: var(--c-success-subtle); color: var(--c-success); }
    .btn-add-riwayat {
        background: var(--c-primary); color: #fff;
        border: none; padding: 6px 14px; border-radius: 8px;
        font-size: 12px; font-weight: 600; cursor: pointer;
        display: inline-flex; align-items: center; gap: 5px;
        transition: background 0.2s;
    }
    .btn-add-riwayat:hover { background: var(--c-primary-hover); color: #fff; }
    .btn-del-sm {
        background: var(--c-error-0); color: var(--c-error);
        border: 1px solid var(--c-error-subtle); padding: 3px 9px;
        border-radius: 6px; font-size: 11px; font-weight: 600; cursor: pointer;
        transition: background 0.2s;
    }
    .btn-del-sm:hover { background: var(--c-error-subtle); }
    .empty-state { color: var(--c-fg-muted); font-size: 14px; text-align: center; padding: 20px 0; }
</style>
@endpush

{{-- ── Prestasi ─────────────────────────────────────────────────────────── --}}
<div class="history-section">
    <div class="history-section-title">
        <span>
            <span class="material-symbols-outlined" style="font-size:18px;color:var(--c-primary);">emoji_events</span>
            Prestasi / Lomba
        </span>
    </div>

    @php $prestasi = $kemahasiswaan?->prestasi ?? collect(); @endphp

    @if($prestasi->count() > 0)
        @foreach($prestasi as $p)
            <div class="prestasi-item">
                <div>
                    <div style="font-weight:600;font-size:14px;color:var(--c-fg);">{{ $p->nama_prestasi }}</div>
                    <div style="font-size:12px;color:var(--c-fg-muted);">
                        {{ $p->tanggal ? \Carbon\Carbon::parse($p->tanggal)->translatedFormat('d M Y') : '—' }}
                    </div>
                </div>
                <div style="display:flex;align-items:center;gap:10px;">
                    <span class="tingkat-badge {{ $p->tingkat }}">{{ ucfirst($p->tingkat) }}</span>
                    @if($canManageHistory)
                        <form method="POST"
                              action="{{ route('manajemenmahasiswa.direktori.alumni.prestasi.destroy', $p->id) }}"
                              onsubmit="return mkConfirmSubmit(this, 'Hapus prestasi ini?', { title: 'Hapus Prestasi', confirmText: 'Ya, Hapus' })">
                            @csrf @method('DELETE')
                            <button type="submit" class="mk-btn mk-btn--primary mk-btn--sm">Hapus</button>
                        </form>
                    @endif
                </div>
            </div>
        @endforeach
    @else
        <p class="empty-state">Belum ada data prestasi yang terverifikasi.</p>
    @endif
</div>

{{-- Modal Tambah Prestasi --}}
@if($canManageHistory)
<div class="modal fade" id="modalTambahPrestasi" tabindex="-1" aria-labelledby="modalTambahPrestasiLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px;border:none;">
            <div class="modal-header" style="border-bottom:1px solid var(--c-border);padding:20px 24px;">
                <h5 class="modal-title" id="modalTambahPrestasiLabel" style="font-weight:700;font-size:16px;">
                    Tambah Prestasi / Lomba
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('manajemenmahasiswa.direktori.alumni.prestasi.store', $alumni->id) }}">
                @csrf
                <div class="modal-body" style="padding:24px;">
                    <div class="mb-3">
                        <label style="font-size:13px;font-weight:600;color:var(--c-fg-sec);" class="mb-1">Nama Prestasi</label>
                        <input type="text" name="nama_prestasi" class="form-control"
                               placeholder="Cth: Juara 1 Hackathon Nasional 2024"
                               style="border-radius:8px;font-size:14px;" required>
                    </div>
                    <div class="mb-3">
                        <label style="font-size:13px;font-weight:600;color:var(--c-fg-sec);" class="mb-1">Tingkat</label>
                        <x-manajemenmahasiswa::ui.select name="tingkat" size="md" required>
                            <option value="">-- Pilih Tingkat --</option>
                            <option value="internasional">Internasional</option>
                            <option value="nasional">Nasional</option>
                            <option value="regional">Regional</option>
                            <option value="universitas">Universitas</option>
                            <option value="prodi">Program Studi</option>
                        </x-manajemenmahasiswa::ui.select>
                    </div>
                    <div class="mb-3">
                        <label style="font-size:13px;font-weight:600;color:var(--c-fg-sec);" class="mb-1">Tanggal</label>
                        <input type="date" name="tanggal" class="form-control"
                               style="border-radius:8px;font-size:14px;">
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid var(--c-border);padding:16px 24px;">
                    <button type="button" class="mk-btn mk-btn--secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="mk-btn mk-btn--primary mk-btn--sm">Simpan Prestasi</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif



{{-- ── Riwayat Kegiatan (dipisah Internal & Eksternal, mengikuti direktori mahasiswa) ── --}}
@php
    // Internal: kegiatan yang tercatat di sistem (punya objek kegiatan) atau entry otomatis (ketua/panitia)
    $kegiatanInternal = $riwayatKegiatan->filter(function ($rw) {
        $hasKegiatan = is_object($rw->kegiatan ?? null) && ($rw->kegiatan->id ?? false);
        $isAutoEntry = !empty($rw->is_auto);
        return $hasKegiatan || $isAutoEntry;
    })->values();

    // Eksternal: kegiatan manual di luar sistem (tanpa objek kegiatan & bukan entry otomatis)
    $kegiatanEksternal = $riwayatKegiatan->filter(function ($rw) {
        $hasKegiatan = is_object($rw->kegiatan ?? null) && ($rw->kegiatan->id ?? false);
        $isAutoEntry = !empty($rw->is_auto);
        return !$hasKegiatan && !$isAutoEntry;
    })->values();
@endphp

{{-- Kegiatan Internal --}}
<div class="history-section">
    <div class="history-section-title">
        <span>
            <span class="material-symbols-outlined" style="font-size:18px;color:var(--c-primary);">calendar_month</span>
            Kegiatan Internal
        </span>
    </div>
    <p style="font-size:12px;color:var(--c-fg-muted);margin:-8px 0 14px 0;">Kegiatan himpunan & prodi yang tercatat di sistem (sebagai ketua pelaksana atau panitia)</p>

    @if($kegiatanInternal->count() > 0)
        <div style="overflow-x:auto;border-radius:10px;border:1px solid var(--c-border);">
            <table class="riwayat-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama Kegiatan</th>
                        <th>Peran</th>
                        <th>Tanggal</th>
                        @if($canManageHistory)<th></th>@endif
                    </tr>
                </thead>
                <tbody>
                    @foreach($kegiatanInternal as $i => $rw)
                        @php
                            $hasKegiatan    = is_object($rw->kegiatan ?? null) && ($rw->kegiatan->id ?? false);
                            $peranManual    = $rw->peran_manual ?? null;
                            $peranValue     = $peranManual ?: ucfirst($rw->peran ?? '');
                            $isAutoEntry    = !empty($rw->is_auto);
                            $tanggalDisplay = null;
                            if ($hasKegiatan && $rw->kegiatan->tanggal_mulai) {
                                $tanggalDisplay = $rw->kegiatan->tanggal_mulai;
                            } elseif (isset($rw->tanggal_kegiatan) && $rw->tanggal_kegiatan) {
                                $tanggalDisplay = $rw->tanggal_kegiatan;
                            }
                        @endphp
                        <tr>
                            <td style="color:var(--c-fg-muted);">{{ $i + 1 }}</td>
                            <td>
                                @if($hasKegiatan)
                                    <span style="color:var(--c-fg);font-weight:600;">
                                        {{ $rw->kegiatan->judul }}
                                    </span>
                                @else
                                    <span style="color:var(--c-fg-muted);">Kegiatan tidak ditemukan</span>
                                @endif
                            </td>
                            <td><span style="font-size:14px;color:var(--c-fg);">{{ $peranValue }}</span></td>
                            <td style="font-size:13px;color:var(--c-fg-muted);">
                                {{ $tanggalDisplay ? \Carbon\Carbon::parse($tanggalDisplay)->translatedFormat('d M Y') : '—' }}
                            </td>
                            @if($canManageHistory)
                            <td>
                                @if(!$isAutoEntry && $rw->id)
                                    <form method="POST"
                                          action="{{ route('manajemenmahasiswa.direktori.alumni.riwayat.destroy', $rw->id) }}"
                                          onsubmit="return mkConfirmSubmit(this, 'Hapus riwayat ini?', { title: 'Hapus Riwayat', confirmText: 'Ya, Hapus' })">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="mk-btn mk-btn--primary mk-btn--sm">Hapus</button>
                                    </form>
                                @endif
                            </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <p class="empty-state">Belum ada kegiatan internal.</p>
    @endif
</div>

{{-- Kegiatan Eksternal --}}
<div class="history-section">
    <div class="history-section-title">
        <span>
            <span class="material-symbols-outlined" style="font-size:18px;color:var(--c-warning);">location_on</span>
            Kegiatan Eksternal
        </span>
    </div>
    <p style="font-size:12px;color:var(--c-fg-muted);margin:-8px 0 14px 0;">Kegiatan di luar sistem yang diajukan mahasiswa melalui verifikasi data</p>

    @if($kegiatanEksternal->count() > 0)
        <div style="overflow-x:auto;border-radius:10px;border:1px solid var(--c-border);">
            <table class="riwayat-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama Kegiatan</th>
                        <th>Peran</th>
                        <th>Tanggal</th>
                        @if($canManageHistory)<th></th>@endif
                    </tr>
                </thead>
                <tbody>
                    @foreach($kegiatanEksternal as $i => $rw)
                        @php
                            $peranManual    = $rw->peran_manual ?? null;
                            $peranValue     = $peranManual ?: ucfirst($rw->peran ?? '');
                            $tanggalDisplay = isset($rw->tanggal_kegiatan) && $rw->tanggal_kegiatan ? $rw->tanggal_kegiatan : null;
                        @endphp
                        <tr>
                            <td style="color:var(--c-fg-muted);">{{ $i + 1 }}</td>
                            <td>
                                <span style="font-weight:600;color:var(--c-fg);">{{ $rw->nama_kegiatan_manual ?? 'Kegiatan tidak ditemukan' }}</span>
                            </td>
                            <td><span style="font-size:14px;color:var(--c-fg);">{{ $peranValue }}</span></td>
                            <td style="font-size:13px;color:var(--c-fg-muted);">
                                {{ $tanggalDisplay ? \Carbon\Carbon::parse($tanggalDisplay)->translatedFormat('d M Y') : '—' }}
                            </td>
                            @if($canManageHistory)
                            <td>
                                @if($rw->id)
                                    <form method="POST"
                                          action="{{ route('manajemenmahasiswa.direktori.alumni.riwayat.destroy', $rw->id) }}"
                                          onsubmit="return mkConfirmSubmit(this, 'Hapus riwayat ini?', { title: 'Hapus Riwayat', confirmText: 'Ya, Hapus' })">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="mk-btn mk-btn--primary mk-btn--sm">Hapus</button>
                                    </form>
                                @else
                                    <span style="font-size:11px;color:var(--c-border-strong);">Auto</span>
                                @endif
                            </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <p class="empty-state">Belum ada kegiatan eksternal.</p>
    @endif
</div>

{{-- ── Modal Tambah Riwayat (hanya untuk canManageHistory) ─────────────── --}}
@if($canManageHistory)
<div class="modal fade" id="modalTambahRiwayat" tabindex="-1" aria-labelledby="modalTambahRiwayatLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px;border:none;">
            <div class="modal-header" style="border-bottom:1px solid var(--c-border);padding:20px 24px;">
                <h5 class="modal-title" id="modalTambahRiwayatLabel" style="font-weight:700;font-size:16px;">
                    Tambah Riwayat Kegiatan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form method="POST" action="{{ route('manajemenmahasiswa.direktori.alumni.riwayat.store', $alumni->id) }}">
                @csrf
                <input type="hidden" name="input_mode" id="input_mode_alumni" value="dropdown">

                <div class="modal-body" style="padding:24px;">
                    {{-- Toggle mode --}}
                    <div style="display:flex;gap:8px;margin-bottom:20px;">
                        <button type="button" id="btn-mode-dropdown-alumni"
                                onclick="setModeAlumni('dropdown')"
                                style="flex:1;padding:8px;border-radius:8px;border:1.5px solid var(--c-primary);background:var(--c-primary);color:#fff;font-size:13px;font-weight:600;cursor:pointer;">
                            Pilih dari Daftar
                        </button>
                        <button type="button" id="btn-mode-manual-alumni"
                                onclick="setModeAlumni('manual')"
                                style="flex:1;padding:8px;border-radius:8px;border:1.5px solid var(--c-border);background:#fff;color:var(--c-fg-muted);font-size:13px;font-weight:600;cursor:pointer;">
                            Input Manual
                        </button>
                    </div>

                    {{-- Mode: dropdown --}}
                    <div id="section-dropdown-alumni">
                        <div class="mb-3">
                            <label style="font-size:13px;font-weight:600;color:var(--c-fg-sec);" class="mb-1">Kegiatan</label>
                            <x-manajemenmahasiswa::ui.select name="kegiatan_id" size="md">
                                <option value="">-- Pilih Kegiatan --</option>
                                @foreach($semuaKegiatan as $kg)
                                    <option value="{{ $kg->id }}">{{ $kg->judul }}</option>
                                @endforeach
                            </x-manajemenmahasiswa::ui.select>
                        </div>
                        <div class="mb-3">
                            <label style="font-size:13px;font-weight:600;color:var(--c-fg-sec);" class="mb-1">Peran</label>
                            <x-manajemenmahasiswa::ui.select name="peran" size="md">
                                <option value="ketua">Ketua</option>
                                <option value="anggota">Anggota</option>
                                <option value="panitia">Panitia</option>
                                <option value="peserta">Peserta</option>
                            </x-manajemenmahasiswa::ui.select>
                        </div>
                    </div>

                    {{-- Mode: manual --}}
                    <div id="section-manual-alumni" style="display:none;">
                        <div class="mb-3">
                            <label style="font-size:13px;font-weight:600;color:var(--c-fg-sec);" class="mb-1">Nama Kegiatan</label>
                            {{-- Batasnya sama dengan form pengajuan mahasiswa (modul Verifikasi
                                 Data), supaya nama yang ditolak di satu pintu tidak diterima
                                 di pintu lain. --}}
                            <input type="text" name="nama_kegiatan_manual" class="form-control"
                                   maxlength="{{ \Modules\ManajemenMahasiswa\Http\Controllers\VerifikasiController::MAKS_NAMA }}"
                                   placeholder="Cth: Kompetisi Robotika Nasional 2024"
                                   style="border-radius:8px;font-size:14px;">
                        </div>
                        <div class="mb-3">
                            <label style="font-size:13px;font-weight:600;color:var(--c-fg-sec);" class="mb-1">Peran</label>
                            <input type="text" name="peran_manual" class="form-control"
                                   maxlength="{{ \Modules\ManajemenMahasiswa\Http\Controllers\VerifikasiController::MAKS_PERAN }}"
                                   placeholder="Cth: Peserta, Juri, Koordinator"
                                   style="border-radius:8px;font-size:14px;">
                        </div>
                        <div class="mb-3">
                            <label style="font-size:13px;font-weight:600;color:var(--c-fg-sec);" class="mb-1">Tanggal Kegiatan</label>
                            <input type="date" name="tanggal_kegiatan" class="form-control"
                                   max="{{ date('Y-m-d') }}"
                                   style="border-radius:8px;font-size:14px;">
                        </div>
                    </div>
                </div>

                <div class="modal-footer" style="border-top:1px solid var(--c-border);padding:16px 24px;">
                    <button type="button" class="mk-btn mk-btn--secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="mk-btn mk-btn--primary mk-btn--sm">Simpan Riwayat</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function setModeAlumni(mode) {
    document.getElementById('input_mode_alumni').value = mode;
    const isDropdown = mode === 'dropdown';
    document.getElementById('section-dropdown-alumni').style.display = isDropdown ? '' : 'none';
    document.getElementById('section-manual-alumni').style.display   = isDropdown ? 'none' : '';
    document.getElementById('btn-mode-dropdown-alumni').style.background  = isDropdown ? 'var(--c-primary)'  : '#fff';
    document.getElementById('btn-mode-dropdown-alumni').style.color       = isDropdown ? '#fff'              : 'var(--c-fg-muted)';
    document.getElementById('btn-mode-dropdown-alumni').style.borderColor = isDropdown ? 'var(--c-primary)'  : 'var(--c-border)';
    document.getElementById('btn-mode-manual-alumni').style.background    = isDropdown ? '#fff'              : 'var(--c-primary)';
    document.getElementById('btn-mode-manual-alumni').style.color         = isDropdown ? 'var(--c-fg-muted)' : '#fff';
    document.getElementById('btn-mode-manual-alumni').style.borderColor   = isDropdown ? 'var(--c-border)'   : 'var(--c-primary)';
}
</script>
@endif

@endif {{-- end canSeeHistory --}}

</x-dynamic-component>
