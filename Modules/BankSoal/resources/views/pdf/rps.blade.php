<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
@php
function parseCpmkDeskripsiForPdf(string $deskripsi): string {
    $deskripsi = trim($deskripsi);
    if (preg_match('/^\((.+?)\)\s*\((.+?)\)(?:\s*\((.+?)\))?$/', $deskripsi, $m)) {
        $kko = trim($m[1]);
        $objek = trim($m[2]);
        $konteks = isset($m[3]) ? trim($m[3]) : '';
        return 'Mahasiswa Mampu ' . $kko . ' ' . $objek . ($konteks !== '' ? ' ' . $konteks : '');
    }
    // Already human-readable or unknown format — return as-is
    return $deskripsi;
}
@endphp
    <title>RPS & Kontrak Kuliah - {{ $mata_kuliah['nama'] }}</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11pt;
            line-height: 1.4;
            color: #000;
            margin: 0;
            padding: 0;
        }
        .page-break {
            page-break-before: always;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .font-bold {
            font-weight: bold;
        }
        h2, h3 {
            margin: 5px 0;
        }
        .header-section {
            margin-bottom: 20px;
        }
        .meta-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .meta-table td {
            padding: 4px 0;
            vertical-align: top;
        }
        .meta-table td.label {
            width: 20%;
        }
        .meta-table td.colon {
            width: 2%;
        }
        .main-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 10pt;
        }
        .main-table th, .main-table td {
            border: 1.5px solid #000;
            padding: 6px 8px;
            vertical-align: top;
        }
        .main-table th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: center;
        }

        /* RPS Header Table Layout */
        .rps-header-table {
            width: 100%;
            border-collapse: collapse;
            border: 3px solid #000;
            margin-bottom: 15px;
        }
        .rps-header-table td {
            border: 1px solid #000;
            padding: 8px;
            vertical-align: middle;
        }
        .logo-cell {
            width: 15%;
            text-align: center;
            border-right: 2px solid #000;
        }
        .logo-img {
            max-width: 60px;
            height: auto;
        }
        .title-cell {
            width: 45%;
            text-align: center;
            font-weight: bold;
            font-size: 12pt;
            border-right: 2px solid #000;
        }
        .prodi-cell {
            width: 40%;
            font-size: 10pt;
            font-weight: bold;
        }

        /* Detailed RPS Table layout */
        .rps-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9pt;
            margin-top: 10px;
        }
        .rps-table th, .rps-table td {
            border: 1px solid #000;
            padding: 5px;
            vertical-align: top;
        }
        .rps-table th {
            background-color: #f2f2f2;
            text-align: center;
            font-weight: bold;
        }
        .section-row {
            background-color: #e6e6e6;
            font-weight: bold;
            text-align: center;
        }
    </style>
</head>
<body>

    <!-- ==================== HALAMAN 1: KONTRAK KULIAH ==================== -->
    <div class="header-section text-center">
        <h2 class="font-bold" style="text-decoration: underline; letter-spacing: 1px; margin-bottom: 20px;">KONTRAK KULIAH</h2>
    </div>

    <table class="meta-table">
        <tr>
            <td class="label">Fakultas / Prodi</td>
            <td class="colon">:</td>
            <td>Teknik / Teknik Komputer</td>
        </tr>
        <tr>
            <td class="label">Tahun Akademik</td>
            <td class="colon">:</td>
            <td>{{ $tahun_akademik }}</td>
        </tr>
        <tr>
            <td class="label">Mata Kuliah</td>
            <td class="colon">:</td>
            <td>{{ $mata_kuliah['nama'] }}</td>
        </tr>
        <tr>
            <td class="label">Dosen</td>
            <td class="colon">:</td>
            <td>
                @foreach($dosens as $index => $dosen)
                    {{ $index + 1 }}. {{ $dosen['name'] }}<br>
                @endforeach
            </td>
        </tr>
    </table>

    <h3 class="font-bold" style="font-size: 11pt; margin-top: 25px; margin-bottom: 10px;">Tabel Penilaian Mata Kuliah</h3>
    <table class="main-table">
        <thead>
            <tr>
                <th style="width: 5%;">No.</th>
                <th style="width: 25%;">Poin Evaluasi</th>
                <th style="width: 40%;">Komponen Evaluasi</th>
                <th style="width: 15%;">Bobot (%)</th>
                <th style="width: 15%;">Komponen CPMK yang Dinilai</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @foreach($penilaian as $item)
                @if(isset($item['sub_rows']) && count($item['sub_rows']) > 0)
                    @foreach($item['sub_rows'] as $subIdx => $subRow)
                        <tr>
                            @if($subIdx === 0)
                                <td class="text-center" rowspan="{{ count($item['sub_rows']) }}">{{ $no++ }}.</td>
                                <td rowspan="{{ count($item['sub_rows']) }}">{{ $item['poin'] }}</td>
                            @endif
                            <td>{{ $subRow['komponen'] }}</td>
                            <td class="text-center">{{ $subRow['bobot'] }}</td>
                            <td class="text-center">{{ $subRow['cpmk'] }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td class="text-center">{{ $no++ }}.</td>
                        <td>{{ $item['poin'] }}</td>
                        <td>{!! nl2br(e($item['komponen'] ?? '')) !!}</td>
                        <td class="text-center">{{ $item['bobot'] }}</td>
                        <td class="text-center">{{ $item['cpmk'] ?? '' }}</td>
                    </tr>
                @endif
            @endforeach
            <tr class="font-bold" style="background-color: #f2f2f2;">
                <td colspan="3" class="text-center">Jumlah</td>
                <td class="text-center">100</td>
                <td></td>
            </tr>
        </tbody>
    </table>

    <!-- ==================== HALAMAN 2: RENCANA PEMBELAJARAN SEMESTER ==================== -->
    <div class="page-break"></div>

    <table class="rps-header-table">
        <tr>
            <td class="logo-cell" rowspan="2">
                @if(file_exists(public_path('images/logo-undip.png')))
                    <img src="{{ public_path('images/logo-undip.png') }}" class="logo-img" alt="Logo UNDIP">
                @else
                    <div style="font-size: 8px; font-weight: bold;">LOGO UNDIP</div>
                @endif
            </td>
            <td class="title-cell" rowspan="2">
                RENCANA PEMBELAJARAN SEMESTER
            </td>
            <td class="prodi-cell" style="border-bottom: 1px solid #000;">
                Program Studi: TEKNIK KOMPUTER
            </td>
        </tr>
        <tr>
            <td class="prodi-cell">
                Fakultas: TEKNIK
            </td>
        </tr>
    </table>

    <table class="main-table" style="margin-top: 5px;">
        <tr>
            <td class="font-bold" style="width: 20%; background-color: #f2f2f2;">Mata Kuliah:</td>
            <td style="width: 35%;">{{ $mata_kuliah['nama'] }}</td>
            <td class="font-bold" style="width: 10%; background-color: #f2f2f2;">Kode:</td>
            <td style="width: 15%;">{{ $mata_kuliah['kode'] }}</td>
            <td class="font-bold" style="width: 10%; background-color: #f2f2f2;">SKS:</td>
            <td style="width: 10%;">{{ $mata_kuliah['sks'] }} SKS</td>
            <td class="font-bold" style="width: 10%; background-color: #f2f2f2;">Sem:</td>
            <td style="width: 10%;">{{ $semester }}</td>
        </tr>
        <tr>
            <td class="font-bold" style="background-color: #f2f2f2;">Dosen Pengampu:</td>
            <td colspan="7">
                {{ implode(', ', collect($dosens)->pluck('name')->toArray()) }}
            </td>
        </tr>
        <tr>
            <td class="font-bold" style="background-color: #f2f2f2;">Capaian Pembelajaran Lulusan (CPL):</td>
            <td colspan="7">
                <ul style="margin: 0; padding-left: 15px;">
                    @foreach($cpl as $item)
                        <li><strong>{{ $item['kode'] }}</strong>: {{ $item['deskripsi'] }}</li>
                    @endforeach
                </ul>
            </td>
        </tr>
        <tr>
            <td class="font-bold" style="background-color: #f2f2f2;">Capaian Pembelajaran Mata Kuliah (CPMK):</td>
            <td colspan="7">
                <ul style="margin: 0; padding-left: 15px;">
                    @foreach($cpmk as $item)
                        <li><strong>{{ $item['kode'] }}</strong>: {{ parseCpmkDeskripsiForPdf($item['deskripsi']) }}</li>
                    @endforeach
                </ul>
            </td>
        </tr>
        <tr>
            <td class="font-bold" style="background-color: #f2f2f2;">Deskripsi Singkat Mata Kuliah:</td>
            <td colspan="7">
                {{ $deskripsi_mk }}
            </td>
        </tr>
    </table>

    <table class="rps-table">
        <thead>
            <tr>
                <th style="width: 5%;">1</th>
                <th style="width: 15%;">2</th>
                <th style="width: 15%;">3</th>
                <th style="width: 12%;">4</th>
                <th style="width: 8%;">5</th>
                <th style="width: 15%;">6</th>
                <th style="width: 8%;">7</th>
                <th colspan="2" style="width: 22%;">8</th>
            </tr>
            <tr>
                <th>Pertemuan</th>
                <th>Kemampuan Akhir Tiap Tahapan Pembelajaran</th>
                <th>Bahan Kajian/ Pokok Bahasan</th>
                <th>Metode Pembelajaran</th>
                <th>Waktu (menit)</th>
                <th>Pengalaman Belajar Mahasiswa</th>
                <th>Target CPMK</th>
                <th>Kriteria & Indikator Penilaian</th>
                <th>Bobot (%)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pertemuan as $p)
                @if($p['pertemuan'] == 8)
                    <tr class="section-row">
                        <td class="text-center font-bold">8</td>
                        <td colspan="8" class="font-bold">UJIAN TENGAH SEMESTER</td>
                    </tr>
                @elseif($p['pertemuan'] == 16)
                    <tr class="section-row">
                        <td class="text-center font-bold">16</td>
                        <td colspan="8" class="font-bold">UJIAN AKHIR SEMESTER</td>
                    </tr>
                @else
                    <tr>
                        <td class="text-center">{{ $p['pertemuan'] }}</td>
                        <td>{!! nl2br(e($p['kemampuan_akhir'] ?? '-')) !!}</td>
                        <td>{!! nl2br(e($p['pokok_bahasan'] ?? '-')) !!}</td>
                        <td>{!! nl2br(e($p['metode'] ?? '-')) !!}</td>
                        <td class="text-center">{{ $p['waktu'] ?? '-' }}</td>
                        <td>{!! nl2br(e($p['pengalaman_belajar'] ?? '-')) !!}</td>
                        <td class="text-center">{{ $p['target_cpmk'] ?? '-' }}</td>
                        <td>{!! nl2br(e($p['kriteria_penilaian'] ?? '-')) !!}</td>
                        <td class="text-center">{{ $p['bobot'] ?? '-' }}</td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>

    <table class="main-table" style="margin-top: 15px;">
        <tr>
            <td class="font-bold" style="width: 20%; background-color: #f2f2f2; border: 1px solid #000;">Daftar Referensi:</td>
            <td style="border: 1px solid #000;">
                <ol style="margin: 0; padding-left: 15px;">
                    @foreach($referensi as $ref)
                        <li>{{ $ref }}</li>
                    @endforeach
                </ol>
            </td>
        </tr>
    </table>

</body>
</html>
