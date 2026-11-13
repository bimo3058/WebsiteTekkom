<?php

namespace Modules\ManajemenMahasiswa\Services;

use Illuminate\Support\Collection;
use Modules\ManajemenMahasiswa\Models\Kegiatan;
use Modules\ManajemenMahasiswa\Support\PeriodeKepengurusan;
use Modules\ManajemenMahasiswa\Support\SumberDana;

/**
 * Rekap realisasi kegiatan Laporan & Arsip untuk ekspor Excel/PDF (S-78).
 *
 * Usulan stakeholder Pak Maman (10 Nov 2026): siklus anggaran himpunan punya
 * bagian realisasi, jadi rekapnya perlu bisa diunduh supaya evaluasi lebih mudah.
 * Satu penyusun data dipakai kedua format supaya isi Excel dan PDF tidak pernah
 * berbeda.
 *
 * Angka rencana diambil dari baris Rencana Proker yang terkunci sejak Ajukan
 * (S-49), angka terpakai dari baris Arsip itu sendiri — sumber yang sama dengan
 * tab Rencana vs Pelaksanaan (S-70, RencanaProkerService::bandingkan). Kegiatan
 * yang dicatat langsung tanpa Rencana Proker (data sebelum S-49 dan Prodi murni)
 * tetap ikut, tetapi kolom rencana & selisihnya kosong.
 *
 * Kolom `realisasi_*` di mk_kegiatan sengaja tidak dipakai: tidak ada form yang
 * mengisinya (lihat catatan S-70).
 */
class RekapRealisasiService
{
    /**
     * @param  Collection<int, Kegiatan>  $kegiatan  muat relasi rencana, bidangs, bidang, kategoris, ketuaPelaksana.user
     * @return array{baris: list<array>, perBidang: list<array>, total: array, filter: string}
     */
    public function susun(Collection $kegiatan, string $filter): array
    {
        $baris = $kegiatan->values()->map(fn (Kegiatan $k, int $i) => $this->baris($k, $i + 1))->all();

        $perBidang = collect($baris)
            ->groupBy('bidang')
            ->map(fn (Collection $grup, string $bidang) => ['bidang' => $bidang] + $this->jumlahkan($grup))
            ->sortBy('bidang', SORT_NATURAL | SORT_FLAG_CASE)
            ->values()
            ->all();

        return [
            'baris'     => $baris,
            'perBidang' => $perBidang,
            'perSumber' => $this->perSumber(collect($baris)),
            'total'     => $this->jumlahkan(collect($baris)),
            'filter'    => $filter,
        ];
    }

    /**
     * Ringkasan per sumber dana (S-80) — inilah yang dikontrol himpunan (L-41).
     * Rencana dijumlah dari Rencana Proker, terpakai dari Arsip. Kegiatan yang
     * anggarannya belum dirinci (data sebelum S-80) dikumpulkan di "Belum dirinci".
     */
    /**
     * Baris = kegiatan BERENCANA saja, supaya rencana − terpakai = selisih (pelajaran
     * S-78: kegiatan tanpa rencana yang ikut di kolom terpakai membuat himpunan tampak
     * boros). Kegiatan tanpa Rencana Proker hanya masuk total "semua kegiatan".
     *
     * @return array{baris: list<array>, banding: array, semua_terpakai: float, tanpa_rencana: int}
     */
    private function perSumber(Collection $baris): array
    {
        $berencana = $baris->where('punya_rencana', true);
        $hasil = [];
        foreach (SumberDana::KOLOM as $kolom => $label) {
            $r = (float) $berencana->sum(fn ($b) => $b['dana_rencana'][$kolom] ?? 0);
            $t = (float) $berencana->sum(fn ($b) => $b['dana_terpakai'][$kolom] ?? 0);
            $hasil[] = [
                'sumber'   => $label,
                'kegiatan' => $berencana->filter(fn ($b) => ($b['dana_terpakai'][$kolom] ?? null) !== null)->count(),
                'rencana'  => $r,
                'terpakai' => $t,
                'selisih'  => $r - $t,
            ];
        }
        // Sisi yang belum dirinci (data sebelum S-80) dihitung dari total anggarannya.
        $r = (float) $berencana->sum(fn ($b) => $b['rencana_dirinci'] ? 0 : ($b['anggaran_rencana'] ?? 0));
        $t = (float) $berencana->sum(fn ($b) => $b['dirinci'] ? 0 : ($b['anggaran_terpakai'] ?? 0));
        $hasil[] = [
            'sumber'   => 'Belum dirinci (data lama)',
            'kegiatan' => $berencana->filter(fn ($b) => !$b['dirinci'])->count(),
            'rencana'  => $r,
            'terpakai' => $t,
            'selisih'  => $r - $t,
        ];

        $tr = array_sum(array_column($hasil, 'rencana'));
        $tt = array_sum(array_column($hasil, 'terpakai'));

        return [
            'baris'          => $hasil,
            'banding'        => ['jumlah' => $berencana->count(), 'rencana' => $tr, 'terpakai' => $tt, 'selisih' => $tr - $tt],
            'semua_terpakai' => (float) $baris->sum(fn ($b) => $b['anggaran_terpakai'] ?? 0),
            'semua_jumlah'   => $baris->count(),
        ];
    }

    private function baris(Kegiatan $k, int $no): array
    {
        $rencana = $k->rencana;

        // Rp 0 sah ("isi 0 bila tanpa biaya"), jadi null dibedakan dari nol — sama dengan S-70.
        $anggaranRencana  = $rencana?->anggaran !== null ? (float) $rencana->anggaran : null;
        $anggaranTerpakai = $k->anggaran !== null ? (float) $k->anggaran : null;
        $selisih = $anggaranRencana !== null && $anggaranTerpakai !== null
            ? $anggaranRencana - $anggaranTerpakai
            : null;

        return [
            'no'                => $no,
            'judul'             => $k->judul,
            'bidang'            => $this->labelBidang($k),
            'periode'           => ($p = PeriodeKepengurusan::untukTanggal($k->tanggal_mulai)) ? PeriodeKepengurusan::label($p) : '—',
            'tanggal'           => $this->teksTanggal($k),
            'lokasi'            => filled($k->lokasi) ? $k->lokasi : '—',
            'ketua'             => $k->ketuaPelaksana?->user?->name ?? '—',
            'punya_rencana'     => $rencana !== null,
            'peserta_target'    => $rencana?->target_peserta !== null ? (int) $rencana->target_peserta : null,
            'peserta_hadir'     => $k->target_peserta !== null ? (int) $k->target_peserta : null,
            'anggaran_rencana'  => $anggaranRencana,
            'anggaran_terpakai' => $anggaranTerpakai,
            'selisih'           => $selisih,
            'keterangan'        => $this->keterangan($rencana !== null, $anggaranRencana, $anggaranTerpakai, $selisih),
            // Rincian per sumber dana (S-80): null per kolom = tidak diisi.
            'dirinci'           => SumberDana::terisi($k),
            'rencana_dirinci'   => $rencana !== null && SumberDana::terisi($rencana),
            'dana_terpakai'     => $this->dana($k),
            'dana_rencana'      => $rencana ? $this->dana($rencana) : [],
        ];
    }

    /** @return array<string, ?float> */
    private function dana(Kegiatan $k): array
    {
        $hasil = [];
        foreach (array_keys(SumberDana::KOLOM) as $kolom) {
            $hasil[$kolom] = $k->{$kolom} !== null ? (float) $k->{$kolom} : null;
        }

        return $hasil;
    }

    /** Sama dengan badge kartu (kegiatan-list/_kartu): bidang, atau "Prodi" bila tanpa bidang. */
    private function labelBidang(Kegiatan $k): string
    {
        if ($k->bidangs->isNotEmpty()) {
            return $k->bidangs->pluck('nama_bidang')->implode(', ');
        }
        if ($k->bidang) {
            return $k->bidang->nama_bidang;
        }

        return $k->kategoris->contains(fn ($kat) => str_contains($kat->nama_kategori, 'Prodi'))
            ? 'Prodi'
            : 'Tanpa bidang';
    }

    private function teksTanggal(Kegiatan $k): string
    {
        $mulai   = $k->tanggal_mulai;
        $selesai = $k->tanggal_selesai;

        if (!$mulai) {
            return '—';
        }
        // locale('id') eksplisit seperti kartu & detail kegiatan: locale aplikasi tidak
        // selalu ikut ke Carbon, sehingga bulan bisa tercetak "Oct".
        $teks = fn ($tgl) => $tgl->locale('id')->translatedFormat('j M Y');

        if (!$selesai || $selesai->isSameDay($mulai)) {
            return $teks($mulai);
        }

        return $teks($mulai) . ' – ' . $teks($selesai);
    }

    private function keterangan(bool $punyaRencana, ?float $rencana, ?float $terpakai, ?float $selisih): string
    {
        $rp = fn (float $n) => 'Rp ' . number_format(abs($n), 0, ',', '.');

        return match (true) {
            !$punyaRencana     => 'Tanpa Rencana Proker',
            $rencana === null  => 'Anggaran rencana kosong',
            $terpakai === null => 'Anggaran terpakai kosong',
            $selisih > 0       => 'Hemat ' . $rp($selisih),
            $selisih < 0       => 'Lebih ' . $rp($selisih),
            default            => 'Sesuai rencana',
        };
    }

    /**
     * Dua kelompok total, supaya rencana hanya dibandingkan dengan kegiatan yang
     * memang punya rencana:
     *  - banding_* : kegiatan ber-Rencana Proker → rencana vs terpakai vs selisih
     *    saling cocok (rencana − terpakai = selisih).
     *  - semua_*   : seluruh kegiatan, termasuk yang dicatat langsung di Arsip →
     *    total anggaran terpakai & peserta hadir sebenarnya.
     * Menjumlah semuanya dalam satu baris menyesatkan: data 10 Nov 2026 memberi
     * rencana Rp 247 jt vs terpakai Rp 562 jt padahal selisih sebenarnya hemat Rp 8 jt.
     */
    private function jumlahkan(Collection $baris): array
    {
        $jumlah = fn (Collection $b, string $kolom) => $b->pluck($kolom)->filter(fn ($n) => $n !== null)->sum();
        $banding = $baris->where('punya_rencana', true);
        // Anggaran dibandingkan hanya bila kedua angka ada, supaya rencana − terpakai = selisih tepat.
        $cocok = $banding->whereNotNull('selisih');

        return [
            'jumlah'                 => $baris->count(),
            'punya_rencana'          => $banding->count(),
            'banding_peserta_target' => (int) $jumlah($banding, 'peserta_target'),
            'banding_peserta_hadir'  => (int) $jumlah($banding, 'peserta_hadir'),
            'banding_rencana'        => (float) $jumlah($cocok, 'anggaran_rencana'),
            'banding_terpakai'       => (float) $jumlah($cocok, 'anggaran_terpakai'),
            'banding_selisih'        => (float) $jumlah($cocok, 'selisih'),
            'semua_peserta_hadir'    => (int) $jumlah($baris, 'peserta_hadir'),
            'semua_terpakai'         => (float) $jumlah($baris, 'anggaran_terpakai'),
        ];
    }
}
