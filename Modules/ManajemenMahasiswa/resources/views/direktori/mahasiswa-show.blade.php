<x-dynamic-component :component="$layout">

<style>
    .profile-header {
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
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
        color: #4f46e5;
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
        color: #1f2937;
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
        color: #9ca3af;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 4px;
    }
    .info-item-value {
        font-size: 15px;
        color: #1f2937;
        font-weight: 600;
    }
    .status-badge {
        font-size: 11px;
        font-weight: 700;
        padding: 3px 10px;
        border-radius: 20px;
        display: inline-block;
    }
    .status-badge.aktif { background: #dcfce7; color: #166534; }
    .status-badge.alumni { background: #dbeafe; color: #1e40af; }
    .status-badge.cuti { background: #fef3c7; color: #92400e; }
    .status-badge.drop_out { background: #fef2f2; color: #991b1b; }

    .riwayat-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }
    .riwayat-table thead th {
        background: #f8fafc;
        padding: 10px 14px;
        font-size: 12px;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        border-bottom: 2px solid #e5e7eb;
    }
    .riwayat-table tbody td {
        padding: 12px 14px;
        font-size: 14px;
        color: #374151;
        border-bottom: 1px solid #f3f4f6;
        vertical-align: middle;
    }
    .riwayat-table tbody tr:hover {
        background: #f8fafc;
    }
    .peran-badge {
        font-size: 11px;
        font-weight: 700;
        padding: 3px 10px;
        border-radius: 20px;
        display: inline-block;
    }
    .peran-badge.ketua { background: #fef3c7; color: #92400e; }
    .peran-badge.anggota { background: #eef2ff; color: #4f46e5; }
    .peran-badge.panitia { background: #f3e8ff; color: #7c3aed; }
    .peran-badge.peserta { background: #dcfce7; color: #166534; }

    .btn-primary-custom {
        background: #4f46e5;
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
        background: #4338ca;
        color: #fff;
    }
    .btn-outline-custom {
        background: transparent;
        color: #4f46e5;
        border: 1.5px solid #c7d2fe;
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
        border-color: #818cf8;
        color: #4338ca;
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
        background: #f8fafc;
        border: 1px solid #e5e7eb;
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
    .tingkat-badge.internasional { background: #fef3c7; color: #92400e; }
    .tingkat-badge.nasional { background: #dbeafe; color: #1e40af; }
    .tingkat-badge.regional { background: #f3e8ff; color: #7c3aed; }
    .tingkat-badge.universitas { background: #dcfce7; color: #166534; }
    .tingkat-badge.prodi { background: #eef2ff; color: #4f46e5; }

    /* Modal */
    .modal-content { border-radius: 16px; border: none; }
    .modal-header { border-bottom: 1px solid #f3f4f6; padding: 20px 24px; }
    .modal-body { padding: 24px; }
    .modal-footer { border-top: 1px solid #f3f4f6; padding: 16px 24px; }
</style>

<!-- Flash Messages -->
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert"
         style="border-radius: 10px; border: none; background: #dcfce7; color: #166534; font-weight: 500; font-size: 14px;">
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
                <span class="status-badge {{ $mhs->status }}" style="background: rgba(255,255,255,0.2); color: white;">
                    @switch($mhs->status)
                        @case('aktif') ● Aktif @break
                        @case('alumni') ● Lulus @break
                        @case('cuti') ● Cuti @break
                        @case('drop_out') ● Drop Out @break
                        @default ● {{ ucfirst($mhs->status) }}
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
        @if($isPengurus || $isAdmin)
            <a href="{{ route('manajemenmahasiswa.direktori.mahasiswa.cv', $mhs->id) }}" target="_blank" class="btn-outline-custom" style="background: rgba(255,255,255,0.15); border-color: rgba(255,255,255,0.3); color: white;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                Generate CV
            </a>
        @endif
    </div>
</div>

<!-- Biodata -->
<div class="section-card">
    <div class="section-title">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#4f46e5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
        Biodata
    </div>
    <div class="info-grid">
        <div>
            <div class="info-item-label">Nama Lengkap <span class="sso-tag">SSO</span></div>
            <div class="info-item-value">{{ $mhs->nama }}</div>
        </div>
        <div>
            <div class="info-item-label">NIM <span class="sso-tag">SSO</span></div>
            <div class="info-item-value" style="font-family: monospace; color: #4f46e5;">{{ $mhs->nim }}</div>
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

        @if($mhs->profesi)
        <div>
            <div class="info-item-label">Profesi</div>
            <div class="info-item-value">{{ $mhs->profesi }}</div>
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
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#4f46e5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z" /><path d="M6 12v5c3 3 9 3 12 0v-5" /></svg>
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
                    @default {{ ucfirst($mhs->status) }}
                @endswitch
            </span></div>
        </div>
        @if($mhs->tahun_lulus)
        <div>
            <div class="info-item-label">Tahun Lulus</div>
            <div class="info-item-value">{{ $mhs->tahun_lulus }}</div>
        </div>
        @endif
    </div>
</div>

<!-- Prestasi -->
@if(!$isMahasiswa)
<div class="section-card">
    <div class="section-title">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#4f46e5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="7"></circle><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline></svg>
        Prestasi / Lomba
    </div>
    @if($mhs->prestasi->count() > 0)
        <div class="d-flex flex-column gap-2">
            @foreach($mhs->prestasi as $p)
                <div class="prestasi-item">
                    <div>
                        <div style="font-weight: 600; font-size: 14px; color: #1f2937;">{{ $p->nama_prestasi }}</div>
                        <div style="font-size: 12px; color: #9ca3af;">Tahun {{ $p->tahun }}</div>
                    </div>
                    <span class="tingkat-badge {{ $p->tingkat }}">{{ ucfirst($p->tingkat) }}</span>
                </div>
            @endforeach
        </div>
    @else
        <p style="color: #9ca3af; font-size: 14px; text-align: center; padding: 20px 0;">Belum ada data prestasi.</p>
    @endif
</div>
@endif

@if(!$isMahasiswa)
<!-- Riwayat Kegiatan -->
<div class="section-card">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="section-title mb-0">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#4f46e5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"></rect><line x1="16" x2="16" y1="2" y2="6"></line><line x1="8" x2="8" y1="2" y2="6"></line><line x1="3" x2="21" y1="10" y2="10"></line></svg>
            Riwayat Keikutsertaan Kegiatan
        </div>
        @if($isPengurus || $isAdmin)
            <button class="btn-primary-custom" data-bs-toggle="modal" data-bs-target="#addRiwayatModal">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                Tambah Riwayat
            </button>
        @endif
    </div>

    @if($riwayatKegiatan->count() > 0)
        <div style="overflow-x: auto; border-radius: 10px; border: 1px solid #f3f4f6;">
            <table class="riwayat-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama Kegiatan</th>
                        <th>Peran</th>
                        <th>Sumber</th>
                        <th>Tanggal</th>
                        @if($isPengurus || $isAdmin)
                            <th style="width: 120px;">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach($riwayatKegiatan as $i => $rw)
                        @php
                            // Resolve display values (works for both Eloquent models and stdClass auto-entries)
                            $hasKegiatan = is_object($rw->kegiatan ?? null) && ($rw->kegiatan->id ?? false);
                            $namaManual  = $rw->nama_kegiatan_manual ?? null;
                            $peranManual = $rw->peran_manual ?? null;
                            $peranValue  = $peranManual ?: ucfirst($rw->peran ?? '');
                            $isAutoEntry = !empty($rw->is_auto);
                            $isManualEntry = !$hasKegiatan && !$isAutoEntry;
                            $tanggalDisplay = null;
                            if ($hasKegiatan && $rw->kegiatan->tanggal_mulai) {
                                $tanggalDisplay = $rw->kegiatan->tanggal_mulai;
                            } elseif (isset($rw->tanggal_kegiatan) && $rw->tanggal_kegiatan) {
                                $tanggalDisplay = $rw->tanggal_kegiatan;
                            }
                        @endphp
                        <tr>
                            <td style="color: #9ca3af;">{{ $i + 1 }}</td>
                            <td>
                                @if($hasKegiatan)
                                    <a href="{{ route('manajemenmahasiswa.kegiatan.show', $rw->kegiatan->id) }}"
                                       style="color: #4f46e5; font-weight: 600; text-decoration: none;">
                                        {{ $rw->kegiatan->judul }}
                                    </a>
                                @elseif($namaManual)
                                    <span style="font-weight: 600; color: #1f2937;">{{ $namaManual }}</span>
                                    <span style="font-size: 9px; font-weight: 700; padding: 1px 6px; border-radius: 6px; background: #fef3c7; color: #d97706; margin-left: 6px; vertical-align: 1px;">Eksternal</span>
                                @else
                                    <span style="color: #9ca3af;">Kegiatan tidak ditemukan</span>
                                @endif
                            </td>
                            <td>
                                <span class="peran-badge {{ $rw->peran ?? '' }}">{{ $peranValue }}</span>
                            </td>
                            <td>
                                @if($isAutoEntry)
                                    <span style="font-size: 10px; font-weight: 700; padding: 2px 8px; border-radius: 8px; background: #dcfce7; color: #166534;">
                                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -1px;"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                        Auto
                                    </span>
                                @elseif($isManualEntry)
                                    <span style="font-size: 10px; font-weight: 700; padding: 2px 8px; border-radius: 8px; background: #fef3c7; color: #d97706;">
                                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -1px;"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                        Input Manual
                                    </span>
                                @else
                                    <span style="font-size: 10px; font-weight: 700; padding: 2px 8px; border-radius: 8px; background: #eef2ff; color: #4f46e5;">Sistem</span>
                                @endif
                            </td>
                            <td style="font-size: 13px; color: #6b7280;">
                                @if($tanggalDisplay)
                                    {{ \Carbon\Carbon::parse($tanggalDisplay)->translatedFormat('d M Y') }}
                                @else
                                    -
                                @endif
                            </td>
                            @if($isPengurus || $isAdmin)
                                <td>
                                    @if(!$isAutoEntry && $rw->id)
                                        <div class="d-flex gap-1">
                                            <button class="btn-danger-sm"
                                                    onclick="if(confirm('Hapus riwayat ini?')) document.getElementById('deleteRiwayat{{ $rw->id }}').submit();">
                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                            </button>
                                            <form id="deleteRiwayat{{ $rw->id }}" method="POST"
                                                  action="{{ route('manajemenmahasiswa.direktori.mahasiswa.riwayat.destroy', $rw->id) }}" style="display:none;">
                                                @csrf @method('DELETE')
                                            </form>
                                        </div>
                                    @else
                                        <span style="font-size: 11px; color: #9ca3af;" title="Data otomatis dari Manajemen Kegiatan">—</span>
                                    @endif
                                </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <p style="color: #9ca3af; font-size: 14px; text-align: center; padding: 20px 0;">Belum ada riwayat kegiatan.</p>
    @endif
</div>
@endif

<!-- Modal Tambah Riwayat -->
@if($isPengurus || $isAdmin)
<div class="modal fade" id="addRiwayatModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('manajemenmahasiswa.direktori.mahasiswa.riwayat.store', $mhs->id) }}">
                @csrf
                <input type="hidden" name="input_mode" id="inputMode" value="dropdown">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Tambah Riwayat Kegiatan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- Mode Toggle -->
                    <div class="d-flex gap-2 mb-4">
                        <button type="button" id="btnModeDropdown" class="mode-toggle-btn active"
                                onclick="switchMode('dropdown')">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                            Pilih dari Sistem
                        </button>
                        <button type="button" id="btnModeManual" class="mode-toggle-btn"
                                onclick="switchMode('manual')">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                            Input Manual
                        </button>
                    </div>

                    <!-- DROPDOWN MODE -->
                    <div id="dropdownFields">
                        <div class="mb-3">
                            <label class="form-label fw-bold" style="font-size: 13px;">Kegiatan</label>
                            <select name="kegiatan_id" id="kegiatanSelect" class="form-select" style="border-radius: 10px;">
                                <option value="">Pilih kegiatan...</option>
                                @foreach($semuaKegiatan as $kg)
                                    <option value="{{ $kg->id }}">{{ $kg->judul }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold" style="font-size: 13px;">Peran</label>
                            <select name="peran" id="peranSelect" class="form-select" style="border-radius: 10px;">
                                <option value="">Pilih peran...</option>
                                <option value="ketua">Ketua</option>
                                <option value="anggota">Anggota</option>
                                <option value="panitia">Panitia</option>
                                <option value="peserta">Peserta</option>
                            </select>
                        </div>
                    </div>

                    <!-- MANUAL MODE -->
                    <div id="manualFields" style="display: none;">
                        <div class="mb-3">
                            <label class="form-label fw-bold" style="font-size: 13px;">Nama Kegiatan</label>
                            <input type="text" name="nama_kegiatan_manual" id="namaManualInput"
                                   class="form-control" placeholder="Contoh: Lomba Debat Nasional 2025"
                                   style="border-radius: 10px;">
                            <small class="text-muted" style="font-size: 11px;">Ketik nama kegiatan yang tidak ada di sistem</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold" style="font-size: 13px;">Peran</label>
                            <input type="text" name="peran_manual" id="peranManualInput"
                                   class="form-control" placeholder="Contoh: Delegasi, Juri, Koordinator Acara"
                                   style="border-radius: 10px;">
                            <small class="text-muted" style="font-size: 11px;">Ketik peran bebas sesuai konteks kegiatan</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold" style="font-size: 13px;">Tanggal Kegiatan <span style="color: #9ca3af; font-weight: 400;">(opsional)</span></label>
                            <input type="date" name="tanggal_kegiatan" id="tanggalManualInput"
                                   class="form-control" style="border-radius: 10px;">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border-radius: 10px;">Batal</button>
                    <button type="submit" class="btn-primary-custom">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .mode-toggle-btn {
        flex: 1;
        padding: 10px 16px;
        border: 2px solid #e5e7eb;
        border-radius: 10px;
        background: #fff;
        color: #6b7280;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
    }
    .mode-toggle-btn:hover {
        border-color: #c7d2fe;
        color: #4f46e5;
        background: #f5f3ff;
    }
    .mode-toggle-btn.active {
        border-color: #4f46e5;
        color: #4f46e5;
        background: #eef2ff;
    }
</style>

<script>
    function switchMode(mode) {
        document.getElementById('inputMode').value = mode;

        const dropdownFields = document.getElementById('dropdownFields');
        const manualFields   = document.getElementById('manualFields');
        const btnDropdown    = document.getElementById('btnModeDropdown');
        const btnManual      = document.getElementById('btnModeManual');

        if (mode === 'manual') {
            dropdownFields.style.display = 'none';
            manualFields.style.display   = 'block';
            btnDropdown.classList.remove('active');
            btnManual.classList.add('active');

            // Disable dropdown fields, enable manual
            document.getElementById('kegiatanSelect').removeAttribute('required');
            document.getElementById('peranSelect').removeAttribute('required');
            document.getElementById('namaManualInput').setAttribute('required', 'required');
            document.getElementById('peranManualInput').setAttribute('required', 'required');
        } else {
            dropdownFields.style.display = 'block';
            manualFields.style.display   = 'none';
            btnDropdown.classList.add('active');
            btnManual.classList.remove('active');

            // Enable dropdown fields, disable manual
            document.getElementById('kegiatanSelect').setAttribute('required', 'required');
            document.getElementById('peranSelect').setAttribute('required', 'required');
            document.getElementById('namaManualInput').removeAttribute('required');
            document.getElementById('peranManualInput').removeAttribute('required');
        }
    }
</script>
@endif

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</x-dynamic-component>
