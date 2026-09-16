<x-dynamic-component :component="$layout">

@include('manajemenmahasiswa::direktori.partials.palette')

@push('styles')
<style>
    /* Kotak putih pembungkus halaman sengaja tidak lagi dibuat transparan, supaya latar
       halaman ini sama dengan Direktori Mahasiswa: konten di dalam kotak putih di atas
       latar abu, seperti dashboard Super Admin. */

    .back-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }
    .back-bar a, .back-bar .btn {
        font-weight: 600;
        font-size: 13px;
        text-decoration: none;
        border-radius: 8px;
        padding: 8px 16px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s;
    }
    /* Tombol sekunder: sama dengan tombol outline "Audit Logs"/"Users" di dashboard global */
    .btn-back {
        background: #fff;
        border: 1px solid var(--c-border);
        color: var(--c-fg-sec);
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
    }
    .btn-back:hover { background: var(--c-bg); border-color: var(--c-border-strong); color: var(--c-fg); }
    .btn-edit-top {
        background: var(--c-warning-subtle);
        border: 1px solid var(--c-warning-subtle);
        color: var(--c-warning);
    }
    .btn-edit-top:hover { border-color: var(--c-warning); }

    /* ── Profile Card ── */
    .profile-card {
        background: #fff;
        border-radius: 16px;
        border: 1px solid var(--c-border);
        box-shadow: var(--shadow-card);
        overflow: hidden;
    }
    .profile-banner {
        background: linear-gradient(135deg, var(--c-primary) 0%, var(--c-primary-hover) 100%);
        height: 120px;
        position: relative;
    }
    .profile-actions {
        position: absolute;
        top: 20px;
        right: 20px;
        display: flex;
        gap: 8px;
    }
    .btn-banner {
        font-weight: 600;
        font-size: 13px;
        text-decoration: none;
        border-radius: 8px;
        padding: 8px 16px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s;
    }
    .btn-banner-edit {
        background: rgba(255, 255, 255, 0.15);
        border: 1px solid rgba(255, 255, 255, 0.3);
        color: white;
        backdrop-filter: blur(4px);
    }
    .btn-banner-edit:hover {
        background: rgba(255, 255, 255, 0.25);
        color: white;
    }
    .btn-banner-cv {
        background: white;
        border: 1px solid white;
        color: var(--c-primary);
    }
    .btn-banner-cv:hover {
        background: var(--c-grey-0);
        color: var(--c-primary-hover);
    }
    .profile-avatar-wrap {
        position: absolute;
        bottom: -44px;
        left: 32px;
    }
    /* Avatar inisial netral, sama dengan komponen user-avatar global */
    .profile-avatar {
        width: 88px;
        height: 88px;
        border-radius: 50%;
        background: var(--c-grey-50);
        border: 4px solid #fff;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 32px;
        font-weight: bold;
        color: var(--c-fg-muted);
        overflow: hidden;
    }
    .profile-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .profile-body {
        padding: 56px 32px 32px;
    }
    .profile-name {
        font-size: 20px;
        font-weight: 700;
        color: var(--c-fg);
        margin-bottom: 4px;
    }
    .profile-meta {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 13px;
        color: var(--c-fg-muted);
        font-weight: 500;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }
    .profile-meta .nim {
        font-family: monospace;
        color: var(--c-primary);
        font-weight: 700;
    }
    .profile-meta .dot {
        color: var(--c-border-strong);
    }

    /* Warna tiap status karir ada di partials/palette */
    .status-badge-lg {
        font-size: 13px;
        font-weight: 700;
        padding: 6px 16px;
        border-radius: 20px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    /* ── Info Section ── */
    /* Warna judul & label disamakan dengan halaman Detail Mahasiswa */
    .section-title {
        font-size: 14px;
        font-weight: 700;
        color: var(--c-fg);
        text-transform: uppercase;
        letter-spacing: 0.04em;
        padding-bottom: 10px;
        margin-bottom: 16px;
        margin-top: 24px;
    }
    .info-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 14px;
    }
    @media (max-width: 768px) { .info-grid { grid-template-columns: 1fr; } }

    .info-item {
        background: var(--c-grey-0);
        padding: 14px 18px;
        border-radius: 10px;
        border: 1px solid var(--c-bg);
    }
    .info-label {
        font-size: 11px;
        text-transform: uppercase;
        color: var(--c-fg-muted);
        font-weight: 700;
        letter-spacing: 0.5px;
        margin-bottom: 5px;
    }
    .info-value {
        font-size: 14px;
        color: var(--c-fg);
        font-weight: 600;
    }
    .info-value.empty {
        color: var(--c-fg-placeholder);
        font-style: italic;
        font-weight: 400;
    }
    .info-value a {
        color: #0077b5; /* warna brand LinkedIn, sengaja tidak memakai token */
        text-decoration: none;
        font-weight: 600;
    }
    .info-value a:hover { text-decoration: underline; }
</style>
@endpush

<div class="back-bar">
    <a href="{{ route('manajemenmahasiswa.direktori.alumni.index') }}" class="btn-back">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
        Kembali
    </a>
</div>

<div class="profile-card">
    <div class="profile-banner">
        <div class="profile-actions">
            @if($canDownloadCv ?? false)
                {{-- Sebelumnya hanya `btn-banner`, sehingga tombol tampil sebagai teks link biru
                     Bootstrap yang nyaris tak terlihat di atas banner navy. --}}
                <a href="{{ route('manajemenmahasiswa.direktori.alumni.cv', $alumni->id) }}" target="_blank" class="btn-banner btn-banner-cv">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                    Download CV
                </a>
            @endif

            @if($isAdmin)
                <a href="{{ route('manajemenmahasiswa.direktori.alumni.edit', $alumni->id) }}" class="btn-banner btn-banner-edit">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    Edit Data
                </a>
            @endif

        </div>
        <div class="profile-avatar-wrap">
            <div class="profile-avatar">
                @if($alumni->user && $alumni->user->avatar_url)
                    <img src="{{ $alumni->user->avatar_url }}" alt="{{ $alumni->user->name }}">
                @else
                    {{ strtoupper(substr($alumni->user->name ?? 'A', 0, 1)) }}
                @endif
            </div>
        </div>
    </div>

    <div class="profile-body">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
            <div>
                <div class="profile-name">{{ $alumni->user->name ?? 'Tanpa Nama' }}</div>
                <div class="profile-meta">
                    <span class="nim">{{ $alumni->nim }}</span>
                    <span class="dot">•</span>
                    <span>Angkatan {{ $alumni->angkatan }}</span>
                    <span class="dot">•</span>
                    <span>Lulus {{ $alumni->tahun_lulus }}</span>
                    @if($alumni->program_studi)
                        <span class="dot">•</span>
                        <span>{{ $alumni->program_studi }}</span>
                    @endif
                    @if($isCanSeeIpk)
                        @if($alumni->ipk !== null)
                            <span class="dot">•</span>
                            <span>IPK {{ number_format($alumni->ipk, 2) }}</span>
                        @endif
                    @endif
                </div>
            </div>
            <span class="status-badge-lg {{ $alumni->status_karir ?? 'belum_terdata' }}">
                @if(in_array($alumni->status_karir, ['bekerja', 'wirausaha'])) <span class="material-symbols-outlined" style="font-size: 14px; vertical-align: middle;">work</span>
                @elseif($alumni->status_karir == 'studi_lanjut') <span class="material-symbols-outlined" style="font-size: 14px; vertical-align: middle;">school</span>
                @else <span class="material-symbols-outlined" style="font-size: 14px; vertical-align: middle;">hourglass_empty</span>
                @endif
                {{ $alumni->status_karir_label }}
            </span>
        </div>

        <div class="section-title">Informasi Karir & Pekerjaan</div>

        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">WhatsApp / Telepon</div>
                <div class="info-value {{ empty($alumni->user->whatsapp) ? 'empty' : '' }}">
                    {{ $alumni->user->whatsapp ?? 'Belum diisi' }}
                </div>
            </div>
            <div class="info-item">
                <div class="info-label">Email Pribadi</div>
                <div class="info-value {{ empty($alumni->user->personal_email) ? 'empty' : '' }}">
                    {{ $alumni->user->personal_email ?? 'Belum diisi' }}
                </div>
            </div>
            <div class="info-item">
                <div class="info-label">Perusahaan / Instansi</div>
                <div class="info-value {{ !$alumni->perusahaan ? 'empty' : '' }}">
                    {{ $alumni->perusahaan ?: 'Belum diisi' }}
                </div>
            </div>
            <div class="info-item">
                <div class="info-label">Posisi / Jabatan</div>
                <div class="info-value {{ !$alumni->jabatan ? 'empty' : '' }}">
                    {{ $alumni->jabatan ?: 'Belum diisi' }}
                </div>
            </div>
            <div class="info-item">
                <div class="info-label">Bidang Industri</div>
                <div class="info-value {{ !$alumni->bidang_industri ? 'empty' : '' }}">
                    {{ $alumni->bidang_industri ? $alumni->bidang_industri_label : 'Belum diisi' }}
                </div>
            </div>
            <div class="info-item">
                <div class="info-label">Tahun Mulai Bekerja</div>
                <div class="info-value {{ !$alumni->tahun_mulai_bekerja ? 'empty' : '' }}">
                    {{ $alumni->tahun_mulai_bekerja ?: 'Belum diisi' }}
                    @if($alumni->waktu_tunggu !== null)
                        <span style="font-size: 11px; color: var(--c-fg-muted); font-weight: 400;">({{ $alumni->waktu_tunggu }} tahun setelah lulus)</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="info-grid" style="margin-top: 14px;">
            <div class="info-item" style="grid-column: 1 / -1;">
                <div class="info-label">LinkedIn</div>
                @if($alumni->linkedin)
                    <div class="info-value">
                        <a href="{{ $alumni->linkedin }}" target="_blank">🔗 {{ $alumni->linkedin }}</a>
                    </div>
                @else
                    <div class="info-value empty">Belum ada tautan LinkedIn</div>
                @endif
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
                              onsubmit="return confirm('Hapus prestasi ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-del-sm">Hapus</button>
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
                        <select name="tingkat" class="form-select" style="border-radius:8px;font-size:14px;" required>
                            <option value="">-- Pilih Tingkat --</option>
                            <option value="internasional">Internasional</option>
                            <option value="nasional">Nasional</option>
                            <option value="regional">Regional</option>
                            <option value="universitas">Universitas</option>
                            <option value="prodi">Program Studi</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label style="font-size:13px;font-weight:600;color:var(--c-fg-sec);" class="mb-1">Tanggal</label>
                        <input type="date" name="tanggal" class="form-control"
                               style="border-radius:8px;font-size:14px;">
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid var(--c-border);padding:16px 24px;">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal"
                            style="border-radius:8px;font-size:13px;font-weight:600;">Batal</button>
                    <button type="submit" class="btn-add-riwayat">Simpan Prestasi</button>
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
            <span style="font-size:11px;font-weight:600;padding:2px 10px;border-radius:20px;background:var(--c-primary-subtle);color:var(--c-primary);margin-left:4px;">{{ $kegiatanInternal->count() }}</span>
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
                                    <a href="{{ route('manajemenmahasiswa.kegiatan.show', $rw->kegiatan->id) }}"
                                       style="color:var(--c-primary);font-weight:600;text-decoration:none;">
                                        {{ $rw->kegiatan->judul }}
                                    </a>
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
                                          onsubmit="return confirm('Hapus riwayat ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn-del-sm">Hapus</button>
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
        <p class="empty-state">Belum ada kegiatan internal.</p>
    @endif
</div>

{{-- Kegiatan Eksternal --}}
<div class="history-section">
    <div class="history-section-title">
        <span>
            <span class="material-symbols-outlined" style="font-size:18px;color:var(--c-warning);">location_on</span>
            Kegiatan Eksternal
            <span style="font-size:11px;font-weight:600;padding:2px 10px;border-radius:20px;background:var(--c-warning-subtle);color:var(--c-warning);margin-left:4px;">{{ $kegiatanEksternal->count() }}</span>
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
                                          onsubmit="return confirm('Hapus riwayat ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn-del-sm">Hapus</button>
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
                            <select name="kegiatan_id" class="form-select" style="border-radius:8px;font-size:14px;">
                                <option value="">-- Pilih Kegiatan --</option>
                                @foreach($semuaKegiatan as $kg)
                                    <option value="{{ $kg->id }}">{{ $kg->judul }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label style="font-size:13px;font-weight:600;color:var(--c-fg-sec);" class="mb-1">Peran</label>
                            <select name="peran" class="form-select" style="border-radius:8px;font-size:14px;">
                                <option value="ketua">Ketua</option>
                                <option value="anggota">Anggota</option>
                                <option value="panitia">Panitia</option>
                                <option value="peserta">Peserta</option>
                            </select>
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
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal"
                            style="border-radius:8px;font-size:13px;font-weight:600;">Batal</button>
                    <button type="submit" class="btn-add-riwayat">Simpan Riwayat</button>
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
