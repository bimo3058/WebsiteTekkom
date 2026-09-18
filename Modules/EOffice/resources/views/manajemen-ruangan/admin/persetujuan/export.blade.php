<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Peminjaman Ruangan</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; text-align: center; }
        h2, p { text-align: center; margin: 5px 0; }
        .text-center { text-align: center; }
        .status-disetujui { color: #047857; }
        .status-ditolak { color: #9D174D; }
        .status-selesai { color: #5E53F4; }
    </style>
</head>
<body>
    <h2>Laporan Peminjaman Ruangan</h2>
    <p>Dicetak pada: {{ now()->translatedFormat('d F Y H:i') }}</p>
    
    @if(isset($startDate) && isset($endDate))
        <p>Periode: {{ \Carbon\Carbon::parse($startDate)->translatedFormat('d M Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d M Y') }}</p>
    @elseif(isset($startDate))
        <p>Sejak: {{ \Carbon\Carbon::parse($startDate)->translatedFormat('d M Y') }}</p>
    @elseif(isset($endDate))
        <p>Hingga: {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d M Y') }}</p>
    @endif

    <table>
        <thead>
            <tr>
                <th style="width: 30px">No</th>
                <th style="width: 150px">Nama Peminjam</th>
                <th style="width: 100px">Identitas (NIM/NIP)</th>
                <th style="width: 80px">Role</th>
                <th style="width: 100px">No Telepon</th>
                <th style="width: 120px">Ruangan</th>
                <th style="width: 90px">Tanggal</th>
                <th style="width: 60px">Mulai</th>
                <th style="width: 60px">Selesai</th>
                <th style="width: 150px">Kegiatan</th>
                <th style="width: 80px">Status</th>
                <th style="width: 120px">Alasan Penolakan</th>
                <th style="width: 100px">Waktu Diajukan</th>
                <th style="width: 100px">Waktu Proses</th>
            </tr>
        </thead>
        <tbody>
            @foreach($peminjamans as $index => $pinjam)
                @php
                    $fullName = $pinjam->user->name ?? 'Tidak Diketahui';
                    $roleName = 'Mahasiswa';
                    $identityNumber = '-';
                    
                    if ($pinjam->user) {
                        if ($pinjam->user->hasRole('dosen')) {
                            $roleName = 'Dosen';
                            $identityNumber = $pinjam->user->lecturer->employee_number ?? '-';
                        } elseif ($pinjam->user->hasRole('mahasiswa') || $pinjam->user->hasRole('praktikan')) {
                            $identityNumber = $pinjam->user->student->student_number ?? '-';
                        } else {
                            $roleName = 'Eksternal / Lainnya';
                            $identityNumber = $pinjam->user->external_id ?? '-';
                        }
                    }
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $fullName }}</td>
                    <td class="text-center">{{ $identityNumber }}</td>
                    <td class="text-center">{{ $roleName }}</td>
                    <td class="text-center">{{ $pinjam->nomor_telepon ?? '-' }}</td>
                    <td>{{ $pinjam->ruangan->nama ?? 'Dihapus' }}</td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($pinjam->tanggal_pinjam)->translatedFormat('d M Y') }}</td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($pinjam->jam_mulai)->format('H:i') }}</td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($pinjam->jam_selesai)->format('H:i') }}</td>
                    <td>{{ $pinjam->tujuan }}</td>
                    <td class="text-center status-{{ strtolower($pinjam->status) }}">{{ strtoupper($pinjam->status) }}</td>
                    <td>{{ $pinjam->alasan_penolakan ?? '-' }}</td>
                    <td class="text-center">{{ $pinjam->created_at ? $pinjam->created_at->format('d/m/Y H:i') : '-' }}</td>
                    <td class="text-center">{{ $pinjam->waktu_approval ? \Carbon\Carbon::parse($pinjam->waktu_approval)->format('d/m/Y H:i') : '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @if(isset($isPdf) && $isPdf)
    <script>
        window.onload = function() {
            window.print();
        };
    </script>
    @endif
</body>
</html>
