{{--
    PDF rekap realisasi Laporan & Arsip (S-78). Dirender dompdf, bukan browser:
    hanya CSS sederhana (tanpa flex/grid/variabel CSS), font DejaVu Sans supaya
    "Rp", "–", dan "−" tercetak. Data dari RekapRealisasiService::susun(), sama
    dengan berkas Excel.
--}}
@php
    $rp = fn ($n) => $n === null ? '—' : ($n < 0 ? '-' : '') . 'Rp ' . number_format(abs($n), 0, ',', '.');
    $angka = fn ($n) => $n === null ? '—' : number_format($n, 0, ',', '.');
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<title>Rekap Realisasi Kegiatan</title>
<style>
    @page { margin: 22px 26px 30px 26px; }
    body { font-family: "DejaVu Sans", sans-serif; font-size: 8px; color: #1D2939; }
    h1 { font-size: 14px; color: #0B266E; margin: 0 0 3px 0; }
    h2 { font-size: 10px; color: #0B266E; margin: 14px 0 5px 0; }
    .meta { color: #475467; font-size: 8px; margin: 0 0 1px 0; }
    table { width: 100%; border-collapse: collapse; }
    th { background: #0B266E; color: #FFFFFF; font-weight: bold; padding: 4px 4px; border: 0.5px solid #0B266E; text-align: center; vertical-align: middle; }
    td { padding: 3px 4px; border: 0.5px solid #D0D5DD; vertical-align: top; }
    tr.zebra td { background: #F8F9FB; }
    tr.total td { background: #EEF2F8; font-weight: bold; border-top: 1.2px solid #0B266E; }
    .r { text-align: right; white-space: nowrap; }
    .c { text-align: center; }
    .minus { color: #B42318; }
    .hemat { color: #067647; }
    .catatan { margin-top: 8px; color: #667085; font-size: 7px; }
    .catatan p { margin: 0 0 2px 0; }
    .ringkas { width: 85%; }
    /* Nomor halaman: penghitung CSS dompdf (tanpa opsi PHP dompdf yang berisiko). */
    .kaki { position: fixed; bottom: -18px; right: 0; font-size: 7px; color: #667085; }
    .kaki:after { content: "Halaman " counter(page); }
</style>
</head>
<body>
<div class="kaki"></div>

<h1>Rekap Realisasi Kegiatan</h1>
<p class="meta">Laporan &amp; Arsip — Manajemen Kegiatan</p>
<p class="meta">Filter: {{ $filter }}</p>
<p class="meta">{{ $diunduh }}</p>

<h2>Ringkasan per Bidang/Unit</h2>
@php
    $barisBidang = fn ($b) => [
        $angka($b['jumlah']), $angka($b['punya_rencana']),
        $rp($b['banding_rencana']), $rp($b['banding_terpakai']), $rp($b['banding_selisih']),
        $rp($b['semua_terpakai']),
    ];
@endphp
<table class="ringkas">
    <thead>
        <tr>
            <th style="text-align:left;" rowspan="2">Bidang/Unit</th>
            <th rowspan="2">Kegiatan</th>
            <th colspan="4">Kegiatan ber-Rencana Proker</th>
            <th rowspan="2">Total Terpakai<br>(semua kegiatan)</th>
        </tr>
        <tr>
            <th>Jumlah</th>
            <th>Anggaran Rencana</th>
            <th>Terpakai</th>
            <th>Selisih</th>
        </tr>
    </thead>
    <tbody>
        @foreach($perBidang as $i => $b)
            <tr class="{{ $i % 2 ? 'zebra' : '' }}">
                <td>{{ $b['bidang'] }}</td>
                @foreach($barisBidang($b) as $k => $sel)
                    <td class="r {{ $k === 4 && $b['banding_selisih'] < 0 ? 'minus' : '' }}">{{ $sel }}</td>
                @endforeach
            </tr>
        @endforeach
        <tr class="total">
            <td>TOTAL</td>
            @foreach($barisBidang($total) as $k => $sel)
                <td class="r {{ $k === 4 && $total['banding_selisih'] < 0 ? 'minus' : '' }}">{{ $sel }}</td>
            @endforeach
        </tr>
    </tbody>
</table>

<h2>Ringkasan per Sumber Dana</h2>
<table style="width:60%;">
    <thead>
        <tr>
            <th style="text-align:left;">Sumber Dana</th>
            <th>Anggaran Rencana</th>
            <th>Anggaran Terpakai</th>
            <th>Selisih</th>
        </tr>
    </thead>
    <tbody>
        @foreach($perSumber['baris'] as $i => $s)
            <tr class="{{ $i % 2 ? 'zebra' : '' }}">
                <td>{{ $s['sumber'] }}</td>
                <td class="r">{{ $rp($s['rencana']) }}</td>
                <td class="r">{{ $rp($s['terpakai']) }}</td>
                <td class="r {{ $s['selisih'] < 0 ? 'minus' : '' }}">{{ $rp($s['selisih']) }}</td>
            </tr>
        @endforeach
        <tr class="total">
            <td>TOTAL KEGIATAN BERENCANA ({{ $perSumber['banding']['jumlah'] }})</td>
            <td class="r">{{ $rp($perSumber['banding']['rencana']) }}</td>
            <td class="r">{{ $rp($perSumber['banding']['terpakai']) }}</td>
            <td class="r {{ $perSumber['banding']['selisih'] < 0 ? 'minus' : '' }}">{{ $rp($perSumber['banding']['selisih']) }}</td>
        </tr>
        <tr class="total">
            <td>TOTAL SEMUA KEGIATAN ({{ $perSumber['semua_jumlah'] }})</td>
            <td class="r">—</td>
            <td class="r">{{ $rp($perSumber['semua_terpakai']) }}</td>
            <td class="r">—</td>
        </tr>
    </tbody>
</table>

<h2>Rincian per Kegiatan</h2>
<table>
    <thead>
        <tr>
            <th style="width:3%;">No</th>
            <th style="width:20%; text-align:left;">Judul Kegiatan</th>
            <th style="width:7%;">Bidang/Unit</th>
            <th style="width:6%;">Periode</th>
            <th style="width:9%;">Tanggal</th>
            <th style="width:13%;">Lokasi</th>
            <th style="width:6%;">Target Peserta</th>
            <th style="width:6%;">Peserta Hadir</th>
            <th style="width:8.5%;">Anggaran Rencana</th>
            <th style="width:8.5%;">Anggaran Terpakai</th>
            <th style="width:13%;">Keterangan</th>
        </tr>
    </thead>
    <tbody>
        @forelse($baris as $i => $b)
            <tr class="{{ $i % 2 ? 'zebra' : '' }}">
                <td class="c">{{ $b['no'] }}</td>
                <td>{{ $b['judul'] }}<br><span style="color:#667085;">Ketua: {{ $b['ketua'] }}</span></td>
                <td>{{ $b['bidang'] }}</td>
                <td class="c">{{ $b['periode'] }}</td>
                <td>{{ $b['tanggal'] }}</td>
                <td>{{ $b['lokasi'] }}</td>
                <td class="r">{{ $angka($b['peserta_target']) }}</td>
                <td class="r">{{ $angka($b['peserta_hadir']) }}</td>
                <td class="r">{{ $rp($b['anggaran_rencana']) }}</td>
                <td class="r">{{ $rp($b['anggaran_terpakai']) }}</td>
                <td class="{{ $b['selisih'] !== null && $b['selisih'] < 0 ? 'minus' : ($b['selisih'] > 0 ? 'hemat' : '') }}">{{ $b['keterangan'] }}</td>
            </tr>
        @empty
            <tr><td colspan="11" class="c" style="padding:14px;">Tidak ada kegiatan yang cocok dengan filter.</td></tr>
        @endforelse
        @if(count($baris))
            <tr class="total">
                <td></td>
                <td colspan="5">TOTAL KEGIATAN BERENCANA ({{ $total['punya_rencana'] }} kegiatan)</td>
                <td class="r">{{ $angka($total['banding_peserta_target']) }}</td>
                <td class="r">{{ $angka($total['banding_peserta_hadir']) }}</td>
                <td class="r">{{ $rp($total['banding_rencana']) }}</td>
                <td class="r">{{ $rp($total['banding_terpakai']) }}</td>
                <td class="{{ $total['banding_selisih'] < 0 ? 'minus' : ($total['banding_selisih'] > 0 ? 'hemat' : '') }}">Selisih {{ $rp($total['banding_selisih']) }}</td>
            </tr>
            <tr class="total">
                <td></td>
                <td colspan="5">TOTAL SEMUA KEGIATAN ({{ $total['jumlah'] }} kegiatan)</td>
                <td class="r">—</td>
                <td class="r">{{ $angka($total['semua_peserta_hadir']) }}</td>
                <td class="r">—</td>
                <td class="r">{{ $rp($total['semua_terpakai']) }}</td>
                <td>Termasuk tanpa Rencana Proker</td>
            </tr>
        @endif
    </tbody>
</table>

<div class="catatan">
    @foreach(\Modules\ManajemenMahasiswa\Exports\RekapRealisasiSheet::catatanKaki() as $catatan)
        <p>{{ $catatan }}</p>
    @endforeach
</div>

</body>
</html>
