<x-dynamic-component :component="$layout">

@include('manajemenmahasiswa::direktori.partials.palette')
@include('manajemenmahasiswa::partials.card-frame')

<style>
    /* ── Card profil: susunan disamakan dengan halaman Detail User di User Management
       global (superadmin/users/show) — toolbar di atas, avatar kiri, grid label:nilai. ── */
    .detail-box {
        background: #fff;
        border: 1px solid var(--c-border);
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06);
        overflow: hidden;
        margin-bottom: 20px;
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
    /* Badge outline berdot; warnanya tetap dari palette status, hanya latar dibuat transparan */
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
    .badge-outline .dot { width: 5px; height: 5px; border-radius: 50%; background: currentColor; }
    .detail-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px 40px;
        max-width: 900px;
    }
    .detail-row { display: flex; align-items: center; min-width: 0; }
    .detail-label { width: 170px; font-size: 13px; color: #94A3B8; flex-shrink: 0; font-weight: 500; }
    .detail-value { font-size: 13px; font-weight: 600; color: #334155; min-width: 0; overflow-wrap: anywhere; }
    .detail-value.empty { color: var(--c-fg-placeholder); font-style: italic; font-weight: 400; }
    @media (max-width: 768px) {
        .detail-profile { flex-direction: column; }
        .detail-grid { grid-template-columns: 1fr; }
        .detail-label { width: 130px; }
    }
    .section-card {
        background: #ffffff;
        border: 1px solid var(--c-border);
        border-radius: 12px;
        padding: 24px;
        margin-bottom: 20px;
        box-shadow: var(--shadow-card);
    }
    .section-title {
        font-size: 16px;
        font-weight: 700;
        color: var(--c-fg);
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    /* Warna tiap status ada di partials/palette */
    .status-badge {
        font-size: 11px;
        font-weight: 700;
        padding: 3px 10px;
        border-radius: 20px;
        display: inline-block;
    }

    .riwayat-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }
    .riwayat-table thead th {
        background: #FAFAFA;
        padding: 10px 14px;
        font-size: 12px;
        font-weight: 700;
        color: var(--c-fg-muted);
        text-transform: uppercase;
        letter-spacing: 0.05em;
        border-bottom: 1px solid var(--c-border);
    }
    .riwayat-table tbody td {
        padding: 12px 14px;
        font-size: 14px;
        color: var(--c-fg);
        border-bottom: 1px solid #F3F4F6;
        vertical-align: middle;
    }
    .riwayat-table tbody tr:hover {
        background: #FAFAFA;
    }
    .peran-badge {
        font-size: 11px;
        font-weight: 700;
        padding: 3px 10px;
        border-radius: 20px;
        display: inline-block;
    }
    .peran-badge.ketua { background: var(--c-primary-subtle); color: var(--c-primary); }
    .peran-badge.anggota { background: var(--c-primary-subtle); color: var(--c-primary); }
    .peran-badge.panitia { background: var(--c-sky-subtle); color: var(--c-sky); }
    .peran-badge.peserta { background: var(--c-success-subtle); color: var(--c-success); }

    .prestasi-item {
        background: var(--c-grey-0);
        border: 1px solid var(--c-border);
        border-radius: 10px;
        padding: 14px 18px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    /* Warna tiap tingkat ada di partials/palette */
    .tingkat-badge {
        font-size: 10px;
        font-weight: 700;
        padding: 2px 8px;
        border-radius: 12px;
        text-transform: uppercase;
    }

    /* Modal */
    .modal-content { border-radius: 16px; border: none; }
    .modal-header { border-bottom: 1px solid var(--c-border); padding: 20px 24px; }
    .modal-body { padding: 24px; }
    .modal-footer { border-top: 1px solid var(--c-border); padding: 16px 24px; }
</style>

<!-- Flash Messages -->
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert"
         style="border-radius: 10px; border: none; background: var(--c-success-subtle); color: var(--c-success); font-weight: 500; font-size: 14px;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@php
    $statusLabel = match ($mhs->status) {
        'aktif'        => 'Aktif',
        'alumni'       => 'Lulus',
        'cuti'         => 'Cuti',
        'drop_out'     => 'Drop Out',
        'pindah_studi' => 'Pindah Studi',
        'wafat'        => 'Wafat',
        'mangkir'      => 'Mangkir',
        default        => ucfirst($mhs->status),
    };
@endphp

<!-- Card Profil (susunan mengikuti Detail User di User Management global) -->
<div class="detail-box">
    {{-- ── Toolbar ── --}}
    <div class="detail-toolbar">
        <div class="detail-toolbar-left">
            <a href="{{ route('manajemenmahasiswa.direktori.mahasiswa.index') }}" class="mk-btn mk-btn--secondary mk-btn--icon mk-btn--sm" title="Kembali" aria-label="Kembali">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M15 19l-7-7 7-7"/></svg>
            </a>
            <h1 class="detail-title">Detail Mahasiswa</h1>
        </div>
        <div class="detail-toolbar-right">
            {{-- Pemilik profil memakai route /profil/cv yang tanpa {id} — CV-nya sendiri tidak
                 perlu (dan tidak boleh) lewat gerbang pengelola yang menerima id bebas. --}}
            @if(($canDownloadCv ?? false) || ($isSelf ?? false))
                <a href="{{ ($canDownloadCv ?? false)
                        ? route('manajemenmahasiswa.direktori.mahasiswa.cv', $mhs->id)
                        : route('manajemenmahasiswa.direktori.mahasiswa.profil.cv') }}" target="_blank"
                   class="mk-btn mk-btn--secondary mk-btn--sm">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                    Download CV
                </a>
            @endif
            {{-- Admin boleh mengedit siapa pun; mahasiswa hanya barisnya sendiri. Dua-duanya
                 membuka form yang sama, bedanya field Status tidak dirender untuk pemilik. --}}
            @if($isAdmin || ($isSelf ?? false))
                <a href="{{ route('manajemenmahasiswa.direktori.mahasiswa.edit', $mhs->id) }}" class="mk-btn mk-btn--primary mk-btn--sm">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                    Edit
                </a>
            @endif
        </div>
    </div>

    {{-- ── Profil ── --}}
    <div class="detail-profile">
        <div class="detail-avatar">
            @if($mhs->user && $mhs->user->avatar_url)
                <img src="{{ $mhs->user->avatar_url }}" alt="{{ $mhs->nama }}">
            @else
                {{ strtoupper(substr($mhs->nama, 0, 1)) }}
            @endif
        </div>

        <div style="flex: 1; min-width: 0;">
            <div class="detail-name-row">
                <h2 class="detail-name">{{ $mhs->nama }}</h2>
                <span class="status-badge {{ $mhs->status }} badge-outline">
                    <span class="dot"></span> {{ $statusLabel }}
                </span>
            </div>
            <p class="detail-sub">NIM: {{ $mhs->nim }}</p>

            <div class="detail-grid">
                @if($mhs->user && $mhs->user->email)
                <div class="detail-row">
                    <span class="detail-label">Email UNDIP</span>
                    <span class="detail-value">{{ $mhs->user->email }}</span>
                </div>
                @endif
                @if($mhs->user && $mhs->user->personal_email)
                <div class="detail-row">
                    <span class="detail-label">Email Pribadi</span>
                    <span class="detail-value">{{ $mhs->user->personal_email }}</span>
                </div>
                @endif
                @if($mhs->kontak)
                <div class="detail-row">
                    <span class="detail-label">Nomor WhatsApp/Telepon</span>
                    <span class="detail-value">{{ $mhs->kontak }}</span>
                </div>
                @endif
                <div class="detail-row">
                    <span class="detail-label">Angkatan</span>
                    <span class="detail-value">{{ $mhs->angkatan }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Status Saat Ini</span>
                    <span class="status-badge {{ $mhs->status }}">{{ $statusLabel }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Prestasi -->
<div class="section-card">
    <div class="section-title">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" style="color: var(--c-primary);" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="7"></circle><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline></svg>
        Prestasi / Lomba
    </div>
    @if($mhs->prestasi->count() > 0)
        <div class="d-flex flex-column gap-2">
            @foreach($mhs->prestasi as $p)
                <div class="prestasi-item">
                    <div>
                        <div style="font-weight: 600; font-size: 14px; color: var(--c-fg);">{{ $p->nama_prestasi }}</div>
                        <div style="font-size: 12px; color: var(--c-fg-muted);">
                            {{ $p->tanggal ? \Carbon\Carbon::parse($p->tanggal)->translatedFormat('d M Y') : '' }}
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="tingkat-badge {{ $p->tingkat }}">{{ ucfirst($p->tingkat) }}</span>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <p style="color: var(--c-fg-muted); font-size: 14px; text-align: center; padding: 20px 0;">Belum ada data prestasi.</p>
    @endif
</div>


<!-- Riwayat Kegiatan -->
@php
    // Pisahkan kegiatan internal (punya kegiatan_id / auto entry) dan eksternal (manual tanpa kegiatan_id)
    $kegiatanInternal = $riwayatKegiatan->filter(function ($rw) {
        $hasKegiatan = is_object($rw->kegiatan ?? null) && ($rw->kegiatan->id ?? false);
        $isAutoEntry = !empty($rw->is_auto);
        return $hasKegiatan || $isAutoEntry;
    })->values();

    $kegiatanEksternal = $riwayatKegiatan->filter(function ($rw) {
        $hasKegiatan = is_object($rw->kegiatan ?? null) && ($rw->kegiatan->id ?? false);
        $isAutoEntry = !empty($rw->is_auto);
        return !$hasKegiatan && !$isAutoEntry;
    })->values();
@endphp

<!-- Kegiatan Internal -->
<div class="section-card">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="section-title mb-0">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" style="color: var(--c-primary);" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"></rect><line x1="16" x2="16" y1="2" y2="6"></line><line x1="8" x2="8" y1="2" y2="6"></line><line x1="3" x2="21" y1="10" y2="10"></line></svg>
            Kegiatan Internal
        </div>
    </div>
    <p style="font-size: 12px; color: var(--c-fg-muted); margin: -8px 0 14px 0;">Kegiatan himpunan &amp; prodi yang tercatat di sistem (sebagai ketua pelaksana atau panitia)</p>

    @if($kegiatanInternal->count() > 0)
        <div style="overflow-x: auto; border-radius: 10px; border: 1px solid var(--c-border);">
            <table class="riwayat-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama Kegiatan</th>
                        <th>Peran</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($kegiatanInternal as $i => $rw)
                        @php
                            $hasKegiatan = is_object($rw->kegiatan ?? null) && ($rw->kegiatan->id ?? false);
                            $peranManual = $rw->peran_manual ?? null;
                            $peranValue  = $peranManual ?: ucfirst($rw->peran ?? '');
                            $tanggalDisplay = null;
                            if ($hasKegiatan && $rw->kegiatan->tanggal_mulai) {
                                $tanggalDisplay = $rw->kegiatan->tanggal_mulai;
                            } elseif (isset($rw->tanggal_kegiatan) && $rw->tanggal_kegiatan) {
                                $tanggalDisplay = $rw->tanggal_kegiatan;
                            }
                        @endphp
                        <tr>
                            <td style="color: var(--c-fg-muted);">{{ $i + 1 }}</td>
                            <td>
                                @if($hasKegiatan)
                                    <span style="color: var(--c-fg); font-weight: 600;">
                                        {{ $rw->kegiatan->judul }}
                                    </span>
                                @else
                                    <span style="color: var(--c-fg-muted);">Kegiatan tidak ditemukan</span>
                                @endif
                            </td>
                            <td>
                                <span style="font-size: 14px; color: var(--c-fg);">{{ $peranValue }}</span>
                            </td>
                            <td style="font-size: 13px; color: var(--c-fg-muted);">
                                @if($tanggalDisplay)
                                    {{ \Carbon\Carbon::parse($tanggalDisplay)->translatedFormat('d M Y') }}
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <p style="color: var(--c-fg-muted); font-size: 14px; text-align: center; padding: 20px 0; margin: 0;">
            Belum ada kegiatan internal.<br>
            <span style="font-size: 12px;">Kegiatan yang masih berjalan atau belum ditandai <strong>Selesai</strong> belum muncul di sini.</span>
        </p>
    @endif
</div>

<!-- Kegiatan Eksternal -->
<div class="section-card">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="section-title mb-0">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" style="color: var(--c-warning);" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
            Kegiatan Eksternal
        </div>
    </div>
    <p style="font-size: 12px; color: var(--c-fg-muted); margin: -8px 0 14px 0;">Kegiatan di luar sistem yang diajukan mahasiswa melalui verifikasi data</p>

    @if($kegiatanEksternal->count() > 0)
        <div style="overflow-x: auto; border-radius: 10px; border: 1px solid var(--c-border);">
            <table class="riwayat-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama Kegiatan</th>
                        <th>Peran</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($kegiatanEksternal as $i => $rw)
                        @php
                            $peranManual = $rw->peran_manual ?? null;
                            $peranValue  = $peranManual ?: ucfirst($rw->peran ?? '');
                            $tanggalDisplay = isset($rw->tanggal_kegiatan) && $rw->tanggal_kegiatan ? $rw->tanggal_kegiatan : null;
                        @endphp
                        <tr>
                            <td style="color: var(--c-fg-muted);">{{ $i + 1 }}</td>
                            <td>
                                <span style="font-weight: 600; color: var(--c-fg);">{{ $rw->nama_kegiatan_manual ?? 'Kegiatan tidak ditemukan' }}</span>
                            </td>
                            <td>
                                <span style="font-size: 14px; color: var(--c-fg);">{{ $peranValue }}</span>
                            </td>
                            <td style="font-size: 13px; color: var(--c-fg-muted);">
                                @if($tanggalDisplay)
                                    {{ \Carbon\Carbon::parse($tanggalDisplay)->translatedFormat('d M Y') }}
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <p style="color: var(--c-fg-muted); font-size: 14px; text-align: center; padding: 20px 0;">Belum ada kegiatan eksternal.</p>
    @endif
</div>




<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</x-dynamic-component>
