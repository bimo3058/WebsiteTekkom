<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Riwayat Peminjaman Ruangan</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10pt;
            line-height: 1.4;
            color: #000;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 15px;
        }
        .header h1 {
            margin: 0;
            font-size: 16pt;
            text-transform: uppercase;
        }
        .header p {
            margin: 5px 0 0 0;
            font-size: 10pt;
            color: #555;
        }
        .filter-info {
            margin-bottom: 15px;
            padding: 10px;
            background: #f5f5f5;
            border-radius: 4px;
        }
        .filter-info strong {
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table th {
            background: #0B266E;
            color: #fff;
            padding: 8px 6px;
            text-align: left;
            font-size: 9pt;
            font-weight: bold;
            border: 1px solid #0B266E;
        }
        table td {
            padding: 6px;
            border: 1px solid #ddd;
            font-size: 9pt;
            vertical-align: top;
        }
        table tbody tr:nth-child(even) {
            background: #f9f9f9;
        }
        table tbody tr:nth-child(odd) {
            background: #fff;
        }
        .status-disetujui {
            color: #047857;
            font-weight: bold;
        }
        .status-ditolak {
            color: #9D174D;
            font-weight: bold;
        }
        .status-dibatalkan {
            color: #DC2626;
            font-weight: bold;
        }
        .text-center {
            text-align: center;
        }
        .footer {
            margin-top: 20px;
            text-align: right;
            font-size: 9pt;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Riwayat Peminjaman Ruangan</h1>
        <p>E-Office Manajemen Ruangan - Teknik Komputer</p>
    </div>

    <div class="filter-info">
        <strong>Periode Laporan:</strong>
        @if($request->filled('start_date') && $request->filled('end_date'))
            {{ \Carbon\Carbon::parse($request->start_date)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($request->end_date)->format('d/m/Y') }}
        @else
            Seluruh Periode
        @endif
        &nbsp;|&nbsp;
        <strong>Status:</strong>
        @if($request->filled('status'))
            {{ ucfirst($request->status) }}
        @else
            Semua
        @endif
        &nbsp;|&nbsp;
        <strong>Ruangan:</strong>
        @if($request->filled('ruangan_id'))
            {{ $ruangans->where('id', $request->ruangan_id)->first()->nama ?? '-' }}
        @else
            Semua Ruangan
        @endif
        &nbsp;|&nbsp;
        <strong>Total Data:</strong> {{ $peminjamans->count() }} peminjaman
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 25px;">No</th>
                <th style="width: 100px;">Peminjam</th>
                <th style="width: 70px;">NIM/NIP</th>
                <th style="width: 50px;">Role</th>
                <th style="width: 80px;">Ruangan</th>
                <th style="width: 70px;">Tanggal</th>
                <th style="width: 50px;">Jam</th>
                <th style="width: 100px;">Kegiatan</th>
                <th style="width: 50px;">Status</th>
                <th style="width: 70px;">Waktu Pengajuan</th>
                <th style="width: 70px;">Waktu Diproses</th>
            </tr>
        </thead>
        <tbody>
            @forelse($peminjamans as $i => $pinjam)
                @php
                    $fullName = $pinjam->user->name ?? 'User Tidak Diketahui';
                    $isDosen = $pinjam->user && $pinjam->user->hasRole('dosen');
                    $identityNumber = $pinjam->user ? ($pinjam->user->student->student_number ?? $pinjam->user->lecturer->employee_number ?? $pinjam->user->external_id) : '-';
                    $roleName = $isDosen ? 'Dosen' : 'Mahasiswa';
                @endphp
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>{{ $fullName }}</td>
                    <td>{{ $identityNumber }}</td>
                    <td>{{ $roleName }}</td>
                    <td>{{ $pinjam->ruangan->nama ?? '-' }}</td>
                    <td>{{ \Carbon\Carbon::parse($pinjam->tanggal_pinjam)->format('d/m/Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($pinjam->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($pinjam->jam_selesai)->format('H:i') }}</td>
                    <td>{{ Str::limit($pinjam->tujuan, 40) }}</td>
                    <td class="status-{{ $pinjam->status }}">{{ ucfirst($pinjam->status) }}</td>
                    <td>{{ $pinjam->created_at ? \Carbon\Carbon::parse($pinjam->created_at)->format('d/m/Y H:i') : '-' }}</td>
                    <td>{{ $pinjam->waktu_approval ? \Carbon\Carbon::parse($pinjam->waktu_approval)->format('d/m/Y H:i') : '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" class="text-center" style="padding: 20px;">Tidak ada data peminjaman.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Dicetak pada: {{ now()->translatedFormat('d F Y, H:i') }} WIB</p>
    </div>
</body>
</html>
