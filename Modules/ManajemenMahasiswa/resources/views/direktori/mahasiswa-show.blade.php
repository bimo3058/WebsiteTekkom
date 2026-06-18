<x-dynamic-component :component="$layout">

<style>
    .profile-header {
        background: linear-gradient(135deg, #0B266E 0%, #091958 100%);
        border-radius: 16px;
        padding: 32px;
        color: white;
        position: relative;
        overflow: hidden;
        margin-bottom: 24px;
    }
    .profile-header::after {
        content: '';
        position: absolute;
        top: -50%;
        right: -20%;
        width: 300px;
        height: 300px;
        background: rgba(255,255,255,0.05);
        border-radius: 50%;
    }
    .profile-avatar {
        width: 72px;
        height: 72px;
        border-radius: 50%;
        background: rgba(255,255,255,0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 28px;
        color: white;
        border: 3px solid rgba(255,255,255,0.3);
        flex-shrink: 0;
        overflow: hidden;
    }
    .profile-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .sso-source {
        font-size: 10px;
        font-weight: 700;
        padding: 2px 8px;
        border-radius: 6px;
        background: rgba(255,255,255,0.2);
        color: rgba(255,255,255,0.9);
        letter-spacing: 0.05em;
    }
    .sso-tag {
        font-size: 9px;
        font-weight: 700;
        padding: 1px 5px;
        border-radius: 4px;
        background: #eef2ff;
        color: #0B266E;
        letter-spacing: 0.03em;
        margin-left: 6px;
        vertical-align: middle;
    }
    .section-card {
        background: #ffffff;
        border: 1px solid #f3f4f6;
        border-radius: 12px;
        padding: 24px;
        margin-bottom: 20px;
    }
    .section-title {
        font-size: 16px;
        font-weight: 700;
        color: #0D0D12;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 16px;
    }
    .info-item-label {
        font-size: 12px;
        color: #666D80;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 4px;
    }
    .info-item-value {
        font-size: 15px;
        color: #0D0D12;
        font-weight: 600;
    }
    .status-badge {
        font-size: 11px;
        font-weight: 700;
        padding: 3px 10px;
        border-radius: 20px;
        display: inline-block;
    }
    .status-badge.aktif { background: #ECFDF5; color: #059669; }
    .status-badge.alumni { background: #dbeafe; color: #1e40af; }
    .status-badge.cuti { background: #FFFBEB; color: #92400e; }
    .status-badge.drop_out { background: #fef2f2; color: #991b1b; }
    .status-badge.pindah_studi { background: #f3f4f6; color: #374151; }

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
        color: #666D80;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        border-bottom: 2px solid #DFE1E7;
    }
    .riwayat-table tbody td {
        padding: 12px 14px;
        font-size: 14px;
        color: #374151;
        border-bottom: 1px solid #f3f4f6;
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
    .peran-badge.ketua { background: #eef2ff; color: #0B266E; }
    .peran-badge.anggota { background: #eef2ff; color: #0B266E; }
    .peran-badge.panitia { background: #f3e8ff; color: #7c3aed; }
    .peran-badge.peserta { background: #ECFDF5; color: #059669; }

    .btn-primary-custom {
        background: #0B266E;
        color: #fff;
        border: none;
        padding: 8px 18px;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        text-decoration: none !important;
    }
    .btn-primary-custom:hover {
        background: #091958;
        color: #fff;
    }
    .btn-outline-custom {
        background: transparent;
        color: #0B266E;
        border: 1.5px solid #5C78B8;
        padding: 8px 18px;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        text-decoration: none !important;
    }
    .btn-outline-custom:hover {
        background: #eef2ff;
        border-color: #0B266E;
        color: #091958;
    }
    .btn-danger-sm {
        background: #fef2f2;
        color: #dc2626;
        border: 1px solid #fecaca;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
    }
    .btn-danger-sm:hover {
        background: #fee2e2;
    }
    .prestasi-item {
        background: #FAFAFA;
        border: 1px solid #DFE1E7;
        border-radius: 10px;
        padding: 14px 18px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .tingkat-badge {
        font-size: 10px;
        font-weight: 700;
        padding: 2px 8px;
        border-radius: 12px;
        text-transform: uppercase;
    }
    .tingkat-badge.internasional { background: #FFFBEB; color: #92400e; }
    .tingkat-badge.nasional { background: #dbeafe; color: #1e40af; }
    .tingkat-badge.regional { background: #f3e8ff; color: #7c3aed; }
    .tingkat-badge.universitas { background: #ECFDF5; color: #059669; }
    .tingkat-badge.prodi { background: #eef2ff; color: #0B266E; }

    /* Modal */
    .modal-content { border-radius: 16px; border: none; }
    .modal-header { border-bottom: 1px solid #f3f4f6; padding: 20px 24px; }
    .modal-body { padding: 24px; }
    .modal-footer { border-top: 1px solid #f3f4f6; padding: 16px 24px; }
</style>

<!-- Flash Messages -->
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert"
         style="border-radius: 10px; border: none; background: #ECFDF5; color: #059669; font-weight: 500; font-size: 14px;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Back Button -->
<div class="mb-3">
    <a href="{{ route('manajemenmahasiswa.direktori.mahasiswa.index') }}" class="btn-outline-custom" style="font-size: 12px; padding: 6px 14px;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
        Kembali
    </a>
</div>

<!-- Profile Header -->
<div class="profile-header">
    <div class="d-flex align-items-center gap-4">
        <div class="profile-avatar">
            @if($mhs->user && $mhs->user->avatar_url)
                <img src="{{ $mhs->user->avatar_url }}" alt="{{ $mhs->nama }}">
            @else
                {{ strtoupper(substr($mhs->nama, 0, 1)) }}
            @endif
        </div>
        <div>
            <h2 style="font-weight: 800; margin-bottom: 4px; font-size: 24px;">{{ $mhs->nama }}</h2>
            <p style="margin: 0; opacity: 0.85; font-size: 15px;">{{ $mhs->nim }} · Angkatan {{ $mhs->angkatan }}</p>
            <div class="mt-2 d-flex gap-2">
                <span class="status-badge {{ $mhs->status }}" style="background: rgba(255,255,255,0.2); color: white; display: inline-flex; align-items: center; gap: 6px;">
                    <span style="width:6px;height:6px;border-radius:50%;background:currentColor;display:inline-block;flex-shrink:0;"></span>
                    @switch($mhs->status)
                        @case('aktif') Aktif @break
                        @case('alumni') Lulus @break
                        @case('cuti') Cuti @break
                        @case('drop_out') Drop Out @break
                        @case('pindah_studi') Pindah Studi @break
                        @default {{ ucfirst($mhs->status) }}
                    @endswitch
                </span>
            </div>
        </div>
    </div>
    <div style="position: absolute; top: 20px; right: 24px;" class="d-flex gap-2">
        @if($isAdmin)
            <a href="{{ route('manajemenmahasiswa.direktori.mahasiswa.edit', $mhs->id) }}" class="btn-outline-custom" style="background: rgba(255,255,255,0.15); border-color: rgba(255,255,255,0.3); color: white;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                Edit
            </a>
        @endif
        @if($isCanCv)
            <a href="{{ route('manajemenmahasiswa.direktori.mahasiswa.cv', $mhs->id) }}" target="_blank" class="btn-outline-custom" style="background: rgba(255,255,255,0.15); border-color: rgba(255,255,255,0.3); color: white;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
                Generate CV
            </a>
        @endif
    </div>
</div>

<!-- Biodata -->
<div class="section-card">
    <div class="section-title">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#0B266E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
        Biodata
    </div>
    <div class="info-grid">
        <div>
            <div class="info-item-label">Nama Lengkap <span class="sso-tag">SSO</span></div>
            <div class="info-item-value">{{ $mhs->nama }}</div>
        </div>
        <div>
            <div class="info-item-label">NIM <span class="sso-tag">SSO</span></div>
            <div class="info-item-value" style="font-family: monospace; color: #0B266E;">{{ $mhs->nim }}</div>
        </div>

        @if($mhs->user && $mhs->user->email)
        <div>
            <div class="info-item-label">Email UNDIP <span class="sso-tag">SSO</span></div>
            <div class="info-item-value" style="word-break: break-all;">{{ $mhs->user->email }}</div>
        </div>
        @endif
        @if($mhs->user && $mhs->user->personal_email)
        <div>
            <div class="info-item-label">Email Pribadi</div>
            <div class="info-item-value" style="word-break: break-all;">{{ $mhs->user->personal_email }}</div>
        </div>
        @endif

        @if($mhs->kontak)
        <div>
            <div class="info-item-label">Kontak</div>
            <div class="info-item-value">{{ $mhs->kontak }}</div>
        </div>
        @endif


        @if($mhs->user && $mhs->user->last_login)
        <div>
            <div class="info-item-label">Terakhir Aktif <span class="sso-tag">SSO</span></div>
            <div class="info-item-value" style="font-size: 13px;">{{ $mhs->user->last_login->translatedFormat('d M Y, H:i') }}</div>
        </div>
        @endif
    </div>
</div>

<!-- Riwayat Akademik -->
<div class="section-card">
    <div class="section-title">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#0B266E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z" /><path d="M6 12v5c3 3 9 3 12 0v-5" /></svg>
        Riwayat Akademik
    </div>
    <div class="info-grid">
        <div>
            <div class="info-item-label">Angkatan Masuk <span class="sso-tag">SSO</span></div>
            <div class="info-item-value">{{ $mhs->angkatan }}</div>
        </div>
        <div>
            <div class="info-item-label">Status Saat Ini</div>
            <div><span class="status-badge {{ $mhs->status }}">
                @switch($mhs->status)
                    @case('aktif') Aktif @break
                    @case('alumni') Lulus @break
                    @case('cuti') Cuti @break
                    @case('drop_out') Drop Out @break
                    @case('pindah_studi') Pindah Studi @break
                    @default {{ ucfirst($mhs->status) }}
                @endswitch
            </span></div>
        </div>
    </div>
</div>

<!-- Prestasi -->
<div class="section-card">
    <div class="section-title">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#0B266E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="7"></circle><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline></svg>
        Prestasi / Lomba
    </div>
    @if($mhs->prestasi->count() > 0)
        <div class="d-flex flex-column gap-2">
            @foreach($mhs->prestasi as $p)
                <div class="prestasi-item">
                    <div>
                        <div style="font-weight: 600; font-size: 14px; color: #0D0D12;">{{ $p->nama_prestasi }}</div>
                        <div style="font-size: 12px; color: #666D80;">
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
        <p style="color: #666D80; font-size: 14px; text-align: center; padding: 20px 0;">Belum ada data prestasi.</p>
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
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#0B266E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"></rect><line x1="16" x2="16" y1="2" y2="6"></line><line x1="8" x2="8" y1="2" y2="6"></line><line x1="3" x2="21" y1="10" y2="10"></line></svg>
            Kegiatan Internal
            <span style="font-size: 11px; font-weight: 600; padding: 2px 10px; border-radius: 20px; background: #eef2ff; color: #0B266E; margin-left: 4px;">{{ $kegiatanInternal->count() }}</span>
        </div>
    </div>
    <p style="font-size: 12px; color: #666D80; margin: -8px 0 14px 0;">Kegiatan himpunan & prodi yang tercatat di sistem (sebagai ketua pelaksana atau panitia)</p>

    @if($kegiatanInternal->count() > 0)
        <div style="overflow-x: auto; border-radius: 10px; border: 1px solid #f3f4f6;">
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
                            <td style="color: #666D80;">{{ $i + 1 }}</td>
                            <td>
                                @if($hasKegiatan)
                                    <a href="{{ route('manajemenmahasiswa.kegiatan.show', $rw->kegiatan->id) }}"
                                       style="color: #0B266E; font-weight: 600; text-decoration: none;">
                                        {{ $rw->kegiatan->judul }}
                                    </a>
                                @else
                                    <span style="color: #666D80;">Kegiatan tidak ditemukan</span>
                                @endif
                            </td>
                            <td>
                                <span style="font-size: 14px; color: #374151;">{{ $peranValue }}</span>
                            </td>
                            <td style="font-size: 13px; color: #666D80;">
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
        <p style="color: #666D80; font-size: 14px; text-align: center; padding: 20px 0;">Belum ada kegiatan internal.</p>
    @endif
</div>

<!-- Kegiatan Eksternal -->
<div class="section-card">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="section-title mb-0">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
            Kegiatan Eksternal
            <span style="font-size: 11px; font-weight: 600; padding: 2px 10px; border-radius: 20px; background: #FFFBEB; color: #d97706; margin-left: 4px;">{{ $kegiatanEksternal->count() }}</span>
        </div>
    </div>
    <p style="font-size: 12px; color: #666D80; margin: -8px 0 14px 0;">Kegiatan di luar sistem yang diajukan mahasiswa melalui verifikasi data</p>

    @if($kegiatanEksternal->count() > 0)
        <div style="overflow-x: auto; border-radius: 10px; border: 1px solid #f3f4f6;">
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
                            <td style="color: #666D80;">{{ $i + 1 }}</td>
                            <td>
                                <span style="font-weight: 600; color: #0D0D12;">{{ $rw->nama_kegiatan_manual ?? 'Kegiatan tidak ditemukan' }}</span>
                            </td>
                            <td>
                                <span style="font-size: 14px; color: #374151;">{{ $peranValue }}</span>
                            </td>
                            <td style="font-size: 13px; color: #666D80;">
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
        <p style="color: #666D80; font-size: 14px; text-align: center; padding: 20px 0;">Belum ada kegiatan eksternal.</p>
    @endif
</div>




<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</x-dynamic-component>
