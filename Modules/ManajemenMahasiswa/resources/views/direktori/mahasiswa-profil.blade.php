<x-manajemenmahasiswa::layouts.mahasiswa>

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
    .peran-badge.ketua { background: #FFFBEB; color: #92400e; }
    .peran-badge.anggota { background: #eef2ff; color: #0B266E; }
    .peran-badge.panitia { background: #f3e8ff; color: #7c3aed; }
    .peran-badge.peserta { background: #ECFDF5; color: #059669; }

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

    .btn-primary-custom {
        background: #0B266E;
        color: #fff;
        border: none;
        padding: 10px 20px;
        border-radius: 10px;
        font-size: 14px;
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
</style>

<!-- Flash Messages -->
@if(session('error'))
    <div class="alert alert-danger" style="border-radius: 10px; border: none; background: #fef2f2; color: #991b1b; font-weight: 500; font-size: 14px;">
        {{ session('error') }}
    </div>
@endif

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
            <div class="mt-2">
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
    <div style="position: absolute; top: 24px; right: 24px;">
        <a href="{{ route('manajemenmahasiswa.direktori.mahasiswa.profil.cv') }}" target="_blank"
           class="btn-primary-custom" style="background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.3);">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
            Download CV
        </a>
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

        @php
            $emailPribadi = $mhs->user->personal_email ?? null;
            $kontakDisplay = $mhs->kontak ?? $mhs->user->whatsapp ?? null;
        @endphp

        @if($emailPribadi)
        <div>
            <div class="info-item-label">Email Pribadi</div>
            <div class="info-item-value" style="word-break: break-all;">{{ $emailPribadi }}</div>
        </div>
        @endif

        @if($kontakDisplay)
        <div>
            <div class="info-item-label">Nomor Telepon</div>
            <div class="info-item-value">{{ $kontakDisplay }}</div>
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
                        <div style="font-size: 12px; color: #666D80;">Tahun {{ $p->tanggal ? $p->tanggal->format('Y') : '-' }}</div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="tingkat-badge {{ $p->tingkat }}">{{ ucfirst($p->tingkat) }}</span>
                        @if(isset($p->verification_status))
                            @if($p->verification_status === 'pending')
                                <span style="font-size: 10px; font-weight: 700; padding: 2px 8px; border-radius: 8px; background: #FFFBEB; color: #d97706;"><span style="display:inline-block;width:6px;height:6px;border-radius:50%;background:currentColor;margin-right:4px;vertical-align:1px;"></span>Pending</span>
                            @elseif($p->verification_status === 'approved')
                                <span style="font-size: 10px; font-weight: 700; padding: 2px 8px; border-radius: 8px; background: #ECFDF5; color: #059669;"><svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:-1px;margin-right:3px;"><polyline points="20 6 9 17 4 12"/></svg>Verified</span>
                            @elseif($p->verification_status === 'rejected')
                                <span style="font-size: 10px; font-weight: 700; padding: 2px 8px; border-radius: 8px; background: #fef2f2; color: #dc2626;" title="{{ $p->verification_note }}"><svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:-1px;margin-right:3px;"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>Ditolak</span>
                            @endif
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <p style="color: #666D80; font-size: 14px; text-align: center; padding: 20px 0;">Belum ada data prestasi.</p>
    @endif
</div>

<!-- Riwayat Kegiatan -->
<div class="section-card">
    <div class="section-title">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#0B266E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"></rect><line x1="16" x2="16" y1="2" y2="6"></line><line x1="8" x2="8" y1="2" y2="6"></line><line x1="3" x2="21" y1="10" y2="10"></line></svg>
        Riwayat Keikutsertaan Kegiatan
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
                        <th>Verifikasi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($riwayatKegiatan as $i => $rw)
                        @php
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
                            <td style="color: #666D80;">{{ $i + 1 }}</td>
                            <td>
                                @if($hasKegiatan)
                                    <a href="{{ route('manajemenmahasiswa.kegiatan.show', $rw->kegiatan->id) }}"
                                       style="color: #0B266E; font-weight: 600; text-decoration: none;">
                                        {{ $rw->kegiatan->judul }}
                                    </a>
                                @elseif($namaManual)
                                    <span style="font-weight: 600; color: #0D0D12;">{{ $namaManual }}</span>
                                    <span style="font-size: 9px; font-weight: 700; padding: 1px 6px; border-radius: 6px; background: #FFFBEB; color: #d97706; margin-left: 6px; vertical-align: 1px;">Eksternal</span>
                                @else
                                    <span style="color: #666D80;">Kegiatan tidak ditemukan</span>
                                @endif
                            </td>
                            <td><span class="peran-badge {{ $rw->peran ?? '' }}">{{ $peranValue }}</span></td>
                            <td>
                                @if($isAutoEntry)
                                    <span style="font-size: 10px; font-weight: 700; padding: 2px 8px; border-radius: 8px; background: #ECFDF5; color: #059669;">
                                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -1px;"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                        Auto
                                    </span>
                                @elseif($isManualEntry)
                                    <span style="font-size: 10px; font-weight: 700; padding: 2px 8px; border-radius: 8px; background: #FFFBEB; color: #d97706;">
                                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -1px;"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                        Input Manual
                                    </span>
                                @else
                                    <span style="font-size: 10px; font-weight: 700; padding: 2px 8px; border-radius: 8px; background: #eef2ff; color: #0B266E;">Sistem</span>
                                @endif
                            </td>
                            <td style="font-size: 13px; color: #666D80;">
                                @if($tanggalDisplay)
                                    {{ \Carbon\Carbon::parse($tanggalDisplay)->translatedFormat('d M Y') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if($isAutoEntry)
                                    <span style="font-size: 10px; font-weight: 700; padding: 2px 8px; border-radius: 8px; background: #ECFDF5; color: #059669;"><svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:-1px;margin-right:3px;"><polyline points="20 6 9 17 4 12"/></svg>Auto</span>
                                @elseif(isset($rw->verification_status))
                                    @if($rw->verification_status === 'pending')
                                        <span style="font-size: 10px; font-weight: 700; padding: 2px 8px; border-radius: 8px; background: #FFFBEB; color: #d97706;"><span style="display:inline-block;width:6px;height:6px;border-radius:50%;background:currentColor;margin-right:4px;vertical-align:1px;"></span>Pending</span>
                                    @elseif($rw->verification_status === 'approved')
                                        <span style="font-size: 10px; font-weight: 700; padding: 2px 8px; border-radius: 8px; background: #ECFDF5; color: #059669;"><svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:-1px;margin-right:3px;"><polyline points="20 6 9 17 4 12"/></svg>Verified</span>
                                    @elseif($rw->verification_status === 'rejected')
                                        <span style="font-size: 10px; font-weight: 700; padding: 2px 8px; border-radius: 8px; background: #fef2f2; color: #dc2626;" title="{{ $rw->verification_note ?? '' }}"><svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:-1px;margin-right:3px;"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>Ditolak</span>
                                    @endif
                                @else
                                    <span style="font-size: 10px; color: #666D80;">—</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <p style="color: #666D80; font-size: 14px; text-align: center; padding: 20px 0;">Belum ada riwayat kegiatan.</p>
    @endif
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</x-manajemenmahasiswa::layouts.mahasiswa>
