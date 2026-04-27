<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CV - {{ $mhs->nama }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter+Tight:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Inter Tight', sans-serif;
            color: #1f2937;
            background: #f5f6fa;
            line-height: 1.6;
        }
        .cv-container {
            max-width: 800px;
            margin: 0 auto;
            background: #ffffff;
            min-height: 100vh;
        }

        /* Header */
        .cv-header {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: white;
            padding: 48px 48px 40px;
            position: relative;
            display: flex;
            align-items: center;
            gap: 28px;
        }
        .cv-header::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #4f46e5, #7c3aed, #a78bfa);
        }
        .cv-photo {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            border: 3px solid rgba(255,255,255,0.4);
            overflow: hidden;
            flex-shrink: 0;
            background: rgba(255,255,255,0.15);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .cv-photo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .cv-photo-initial {
            font-size: 36px;
            font-weight: 800;
            color: white;
        }
        .cv-name {
            font-size: 32px;
            font-weight: 800;
            letter-spacing: -0.02em;
            margin-bottom: 6px;
        }
        .cv-subtitle {
            font-size: 16px;
            opacity: 0.85;
            font-weight: 500;
        }
        .cv-contact {
            display: flex;
            gap: 20px;
            margin-top: 16px;
            font-size: 13px;
            opacity: 0.9;
        }
        .cv-contact span {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        /* Body */
        .cv-body {
            padding: 40px 48px;
        }
        .cv-section {
            margin-bottom: 32px;
        }
        .cv-section-title {
            font-size: 14px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #4f46e5;
            margin-bottom: 16px;
            padding-bottom: 8px;
            border-bottom: 2px solid #e0e7ff;
        }
        .cv-info-row {
            display: flex;
            margin-bottom: 8px;
            font-size: 14px;
        }
        .cv-info-label {
            width: 160px;
            font-weight: 600;
            color: #6b7280;
            flex-shrink: 0;
        }
        .cv-info-value {
            font-weight: 500;
            color: #1f2937;
        }

        /* Tables */
        .cv-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }
        .cv-table th {
            background: #f8fafc;
            padding: 10px 12px;
            text-align: left;
            font-weight: 700;
            color: #64748b;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-bottom: 2px solid #e5e7eb;
        }
        .cv-table td {
            padding: 10px 12px;
            border-bottom: 1px solid #f3f4f6;
            color: #374151;
        }
        .cv-table tbody tr:last-child td {
            border-bottom: none;
        }

        /* Print Controls */
        .print-controls {
            position: fixed;
            top: 20px;
            right: 20px;
            display: flex;
            gap: 10px;
            z-index: 100;
        }
        .btn-print {
            background: #4f46e5;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
            transition: all 0.2s;
            font-family: 'Inter Tight', sans-serif;
        }
        .btn-print:hover {
            background: #4338ca;
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(79, 70, 229, 0.4);
        }
        .btn-back {
            background: #ffffff;
            color: #6b7280;
            border: 1.5px solid #e5e7eb;
            padding: 12px 24px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s;
            font-family: 'Inter Tight', sans-serif;
        }
        .btn-back:hover {
            background: #f8fafc;
            border-color: #d1d5db;
            color: #374151;
        }

        /* Print Styles */
        @media print {
            body {
                background: white;
            }
            .print-controls {
                display: none !important;
            }
            .cv-container {
                box-shadow: none;
                margin: 0;
                max-width: 100%;
            }
            .cv-header {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            @page {
                margin: 0;
                size: A4;
            }
        }

        @media screen {
            .cv-container {
                margin: 30px auto;
                box-shadow: 0 4px 24px rgba(0,0,0,0.08);
                border-radius: 12px;
                overflow: hidden;
            }
        }
    </style>
</head>
<body>

<!-- Print Controls -->
<div class="print-controls">
    <a href="javascript:history.back()" class="btn-back">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
        Kembali
    </a>
    <button onclick="window.print()" class="btn-print">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
        Print / Download PDF
    </button>
</div>

<!-- CV Content -->
<div class="cv-container">
    <!-- Header -->
    <div class="cv-header">
        <div class="cv-photo">
            @if($mhs->user && ($mhs->user->avatar_url_format || $mhs->user->avatar_url))
                <img src="{{ $mhs->user->avatar_url_format ?? $mhs->user->avatar_url }}" alt="{{ $mhs->nama }}">
            @else
                <span class="cv-photo-initial">{{ strtoupper(substr($mhs->nama, 0, 1)) }}</span>
            @endif
        </div>
        <div>
            <div class="cv-name">{{ $mhs->nama }}</div>
            <div class="cv-subtitle">Mahasiswa · {{ $mhs->nim }}</div>
            <div class="cv-contact">
                @if($mhs->user && $mhs->user->email)
                    <span>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                        {{ $mhs->user->email }}
                    </span>
                @endif
                @if($mhs->kontak)
                    <span>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                        {{ $mhs->kontak }}
                    </span>
                @endif
            </div>
        </div>
    </div>

    <!-- Body -->
    <div class="cv-body">
        <!-- Informasi Pribadi -->
        <div class="cv-section">
            <div class="cv-section-title">Informasi Pribadi</div>
            <div class="cv-info-row">
                <div class="cv-info-label">Nama Lengkap</div>
                <div class="cv-info-value">{{ $mhs->nama }}</div>
            </div>
            <div class="cv-info-row">
                <div class="cv-info-label">NIM</div>
                <div class="cv-info-value">{{ $mhs->nim }}</div>
            </div>
            <div class="cv-info-row">
                <div class="cv-info-label">Angkatan</div>
                <div class="cv-info-value">{{ $mhs->angkatan }}</div>
            </div>
            <div class="cv-info-row">
                <div class="cv-info-label">Status</div>
                <div class="cv-info-value">
                    @switch($mhs->status)
                        @case('aktif') Aktif @break
                        @case('alumni') Lulus @break
                        @case('cuti') Cuti @break
                        @case('drop_out') Drop Out @break
                        @default {{ ucfirst($mhs->status) }}
                    @endswitch
                </div>
            </div>
            @if($mhs->tahun_lulus)
            <div class="cv-info-row">
                <div class="cv-info-label">Tahun Lulus</div>
                <div class="cv-info-value">{{ $mhs->tahun_lulus }}</div>
            </div>
            @endif
            @if($mhs->profesi)
            <div class="cv-info-row">
                <div class="cv-info-label">Profesi</div>
                <div class="cv-info-value">{{ $mhs->profesi }}</div>
            </div>
            @endif
        </div>

        <!-- Prestasi -->
        @if($mhs->prestasi->count() > 0)
        <div class="cv-section">
            <div class="cv-section-title">Prestasi & Penghargaan</div>
            <table class="cv-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Prestasi</th>
                        <th>Tingkat</th>
                        <th>Tahun</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($mhs->prestasi as $i => $p)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td style="font-weight: 600;">{{ $p->nama_prestasi }}</td>
                            <td>{{ ucfirst($p->tingkat) }}</td>
                            <td>{{ $p->tanggal ? $p->tanggal->format('Y') : '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        <!-- Riwayat Kegiatan -->
        @if($riwayatKegiatan->count() > 0)
        <div class="cv-section">
            <div class="cv-section-title">Riwayat Kegiatan & Organisasi</div>
            <table class="cv-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Kegiatan</th>
                        <th>Peran</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($riwayatKegiatan as $i => $rw)
                        @php
                            $hasKegiatan = is_object($rw->kegiatan ?? null) && ($rw->kegiatan->id ?? false);
                            $namaManual  = $rw->nama_kegiatan_manual ?? null;
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
                            <td>{{ $i + 1 }}</td>
                            <td style="font-weight: 600;">
                                @if($hasKegiatan)
                                    {{ $rw->kegiatan->judul }}
                                @elseif($namaManual)
                                    {{ $namaManual }}
                                @else
                                    Kegiatan tidak ditemukan
                                @endif
                            </td>
                            <td>{{ $peranValue }}</td>
                            <td>
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
        @endif

        <!-- Footer -->
        <div style="margin-top: 48px; padding-top: 20px; border-top: 1px solid #e5e7eb; text-align: center;">
            <p style="font-size: 11px; color: #9ca3af;">
                Dokumen ini digenerate secara otomatis oleh Sistem Manajemen Kemahasiswaan pada {{ now()->translatedFormat('d F Y, H:i') }} WIB
            </p>
        </div>
    </div>
</div>

</body>
</html>
