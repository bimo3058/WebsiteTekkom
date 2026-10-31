<?php

namespace Modules\ManajemenMahasiswa\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Modules\ManajemenMahasiswa\Models\Kegiatan;
use Modules\ManajemenMahasiswa\Models\RepoMulmed;

/**
 * Rencana Proker berdiri sendiri (S-49).
 *
 * "Ajukan Proker" tidak lagi memindahkan baris rencana ke Pelaksanaan. Rencana dikunci
 * (status `diajukan`) dan Pelaksanaan mendapat SALINAN baris baru beserta banner,
 * kategori, bidang, panitia, dosen pendamping, Akses Kelola, dan dokumen rencana
 * (proposal, RAB, surat undangan, surat peminjaman). Akibatnya edit di Pelaksanaan/Arsip
 * tidak mengubah rencana, dan menghapus salinan tidak menghapus rencana beserta
 * dokumennya. File disalin fisik ke path baru, karena menghapus salinan ikut
 * menghapus file storage miliknya.
 */
class RencanaProkerService
{
    public function __construct(private RepoMulmedService $repoMulmedService) {}

    /**
     * Kunci rencana lalu buat salinannya untuk Pelaksanaan.
     *
     * File disalin SEBELUM transaksi database. Bila satu langkah gagal, file yang sudah
     * tersalin dibersihkan, transaksi batal, dan rencana tetap draft.
     *
     * @throws \DomainException bila rencana sudah diajukan lebih dulu (klik ganda, tab lain)
     * @throws \Throwable       bila penyalinan file atau penyimpanan gagal
     */
    public function ajukan(Kegiatan $rencana): Kegiatan
    {
        $rencana->loadMissing(['repoMulmed', 'kategoris', 'bidangs', 'panitia', 'dosenPendampings', 'pengelola']);

        $pathBaru = [];

        try {
            $bannerBaru = null;
            if ($rencana->banner) {
                $bannerBaru = $this->repoMulmedService->salinObjek($rencana->banner, 'mk_mulmed/image');
                $pathBaru[] = $bannerBaru;
            }

            $fileBaru = [];
            foreach ($rencana->repoMulmed as $file) {
                $path = $this->repoMulmedService->salinObjek($file->path_file, 'mk_mulmed/' . $file->tipe_file);
                $pathBaru[] = $path;
                $fileBaru[] = [$file, $path];
            }

            return DB::transaction(function () use ($rencana, $bannerBaru, $fileBaru) {
                // Kunci baris rencana: dua permintaan Ajukan yang hampir bersamaan (klik
                // ganda, dua tab) tidak boleh menghasilkan dua salinan.
                $terkini = Kegiatan::whereKey($rencana->id)->lockForUpdate()->first();
                if (!$terkini
                    || $terkini->status !== Kegiatan::STATUS_DRAFT
                    || Kegiatan::where('rencana_id', $rencana->id)->exists()) {
                    throw new \DomainException('Rencana proker ini sudah diajukan.');
                }

                // replicate() ikut menyalin relasi yang sudah dimuat; dikosongkan supaya
                // salinan tidak membawa koleksi milik rencana di memori.
                $salinan = $rencana->replicate(['surat_proker', 'surat_proker_original'])->setRelations([]);
                $salinan->fill([
                    'status'                 => Kegiatan::STATUS_DISETUJUI,
                    'rencana_id'             => $rencana->id,
                    'banner'                 => $bannerBaru,
                    'is_pelaksanaan_updated' => false,
                ]);
                $salinan->save();

                $salinan->kategoris()->sync($rencana->kategoris->pluck('id'));
                $salinan->bidangs()->sync($rencana->bidangs->pluck('id'));
                $salinan->panitia()->sync(
                    $rencana->panitia->mapWithKeys(fn ($mhs) => [$mhs->id => ['peran' => $mhs->pivot->peran]])
                );
                $salinan->dosenPendampings()->sync(
                    $rencana->dosenPendampings->mapWithKeys(fn ($dosen) => [$dosen->id => ['peran' => $dosen->pivot->peran]])
                );
                $salinan->pengelola()->sync(
                    $rencana->pengelola->mapWithKeys(fn ($user) => [$user->id => ['boleh_hapus' => false]])
                );

                foreach ($fileBaru as [$asli, $path]) {
                    RepoMulmed::create([
                        'kegiatan_id'       => $salinan->id,
                        'nama_file'         => $asli->nama_file,
                        'path_file'         => $path,
                        'tipe_file'         => $asli->tipe_file,
                        'jenis_dokumen'     => $asli->jenis_dokumen,
                        'dari_rencana'      => true,
                        'judul_file'        => $asli->judul_file,
                        'deskripsi_meta'    => $asli->deskripsi_meta,
                        'visibility_status' => $asli->visibility_status,
                        'status_arsip'      => $asli->status_arsip,
                    ]);
                }

                $rencana->update(['status' => Kegiatan::STATUS_DIAJUKAN]);

                return $salinan;
            });
        } catch (\Throwable $e) {
            $this->repoMulmedService->hapusObjek($pathBaru);

            throw $e;
        }
    }

    /**
     * Tab "Rencana vs Pelaksanaan" di detail Laporan & Arsip (S-70).
     *
     * Kolom rencana = baris Rencana Proker yang terkunci sejak Ajukan; kolom pelaksanaan =
     * salinannya, yang isinya diganti angka nyata selama Pelaksanaan dan Arsip. Dokumen
     * dipisah lewat penanda `dari_rencana` (S-59), bukan dibandingkan dengan dokumen milik
     * baris rencana.
     *
     * @return array{rencana: Kegiatan, baris: list<array>, jumlahBeda: int, dokumenRencana: Collection, dokumenTambahan: Collection, jumlahFoto: int}|null
     *         null bila kegiatan dicatat langsung tanpa Rencana Proker (data sebelum S-49)
     */
    public function bandingkan(Kegiatan $salinan): ?array
    {
        $rencana = $salinan->rencana()->with(['ketuaPelaksana.user', 'dosenPendampings.user', 'panitia.user'])->first();
        if (!$rencana) {
            return null;
        }

        $salinan->loadMissing(['ketuaPelaksana.user', 'dosenPendampings.user', 'panitia.user', 'repoMulmed']);

        $baris = [
            $this->barisTanggal($rencana, $salinan),
            $this->barisTeks('Jam', $this->teksJam($rencana), $this->teksJam($salinan)),
            $this->barisTeks('Lokasi', $this->teksBebas($rencana->lokasi), $this->teksBebas($salinan->lokasi)),
            $this->barisPeserta($rencana, $salinan),
            $this->barisAnggaran($rencana, $salinan),
            $this->barisTeks('Ketua Pelaksana', $this->namaKetua($rencana), $this->namaKetua($salinan), 'Berganti'),
            $this->barisOrang('Dosen Pendamping', $rencana->dosenPendampings, $salinan->dosenPendampings, 'dosen', true),
            $this->barisOrang('Panitia', $rencana->panitia, $salinan->panitia, 'orang', false),
        ];

        $dokumen = $salinan->repoMulmed->where('tipe_file', 'document');

        return [
            'rencana'         => $rencana,
            'baris'           => $baris,
            'jumlahBeda'      => collect($baris)->where('sesuai', false)->count(),
            'dokumenRencana'  => $dokumen->where('dari_rencana', true)->values(),
            'dokumenTambahan' => $dokumen->where('dari_rencana', false)->values(),
            'jumlahFoto'      => $salinan->repoMulmed->where('tipe_file', 'image')->count(),
        ];
    }

    /**
     * Satu baris tabel. Keterangan bawaan: Sesuai, Baru diisi, Dikosongkan, atau $ketBeda.
     */
    private function baris(string $label, ?string $rencana, ?string $pelaksanaan, bool $sesuai, string $ketBeda = 'Berubah', ?string $catatan = null): array
    {
        $keterangan = match (true) {
            $sesuai && $rencana === null => 'Tidak diisi',
            $sesuai                      => 'Sesuai',
            $rencana === null            => 'Baru diisi',
            $pelaksanaan === null        => 'Dikosongkan',
            default                      => $ketBeda,
        };

        return [
            'label'       => $label,
            'rencana'     => $rencana ?? '—',
            'pelaksanaan' => $pelaksanaan ?? '—',
            'sesuai'      => $sesuai,
            'nada'        => $sesuai ? ($rencana === null ? 'neutral' : 'success') : 'warning',
            'keterangan'  => $keterangan,
            'catatan'     => $catatan,
        ];
    }

    private function barisTeks(string $label, ?string $rencana, ?string $pelaksanaan, string $ketBeda = 'Berubah'): array
    {
        return $this->baris($label, $rencana, $pelaksanaan, $rencana === $pelaksanaan, $ketBeda);
    }

    private function barisTanggal(Kegiatan $rencana, Kegiatan $salinan): array
    {
        $baris = $this->barisTeks('Tanggal', $this->teksTanggal($rencana), $this->teksTanggal($salinan));

        if (!$baris['sesuai'] && $rencana->tanggal_mulai && $salinan->tanggal_mulai) {
            $selisih = (int) round($rencana->tanggal_mulai->diffInDays($salinan->tanggal_mulai, false));
            if ($selisih !== 0) {
                $baris['keterangan'] = ($selisih > 0 ? 'Mundur ' : 'Maju ') . abs($selisih) . ' hari';
            }
        }

        return $baris;
    }

    private function barisPeserta(Kegiatan $rencana, Kegiatan $salinan): array
    {
        $r = $rencana->target_peserta;
        $p = $salinan->target_peserta;
        $teks = fn ($n, string $akhiran) => $n ? number_format($n, 0, ',', '.') . ' orang ' . $akhiran : null;

        // Teks kedua kolom berbeda akhiran ("(target)" vs "hadir"), jadi angkanya yang dibandingkan.
        $baris = $this->baris('Peserta', $teks($r, '(target)'), $teks($p, 'hadir'), (int) $r === (int) $p);
        if (!$baris['sesuai'] && $r && $p) {
            $baris['keterangan'] = round($p / $r * 100) . '% dari target';
        }

        return $baris;
    }

    private function barisAnggaran(Kegiatan $rencana, Kegiatan $salinan): array
    {
        // Rp 0 sah ("isi 0 bila tanpa biaya"), jadi null dibedakan dari nol.
        $r = $rencana->anggaran !== null ? (float) $rencana->anggaran : null;
        $p = $salinan->anggaran !== null ? (float) $salinan->anggaran : null;
        $teks = fn (?float $n) => $n === null ? null : 'Rp ' . number_format($n, 0, ',', '.');

        $baris = $this->baris('Anggaran', $teks($r), $p === null ? null : $teks($p) . ' terpakai', $teks($r) === $teks($p));
        if (!$baris['sesuai'] && $r !== null && $p !== null) {
            $baris['keterangan'] = ($p < $r ? 'Hemat ' : 'Lebih ') . $teks(abs($r - $p));
        }

        return $baris;
    }

    /**
     * Dosen pendamping & panitia dibandingkan per orang (id), bukan per teks, supaya
     * urutan nama yang berbeda tidak dihitung berubah.
     */
    private function barisOrang(string $label, Collection $rencana, Collection $salinan, string $satuan, bool $tampilNama): array
    {
        $nama   = fn ($orang) => $orang->user->name ?? '-';
        $baru   = $salinan->whereNotIn('id', $rencana->pluck('id'));
        $keluar = $rencana->whereNotIn('id', $salinan->pluck('id'));
        $teks   = fn (Collection $daftar) => $daftar->isEmpty()
            ? null
            : ($tampilNama ? $daftar->map($nama)->implode(', ') : $daftar->count() . ' orang');

        $sesuai = $baru->isEmpty() && $keluar->isEmpty();

        // Panitia ditampilkan sebagai jumlah, jadi nama yang masuk/keluar ditulis di bawahnya.
        $catatan = null;
        if (!$sesuai && !$tampilNama) {
            $catatan = collect([
                $baru->isNotEmpty() ? 'Baru: ' . $baru->map($nama)->implode(', ') : null,
                $keluar->isNotEmpty() ? 'Keluar: ' . $keluar->map($nama)->implode(', ') : null,
            ])->filter()->implode(' · ');
        }

        $baris = $this->baris($label, $teks($rencana), $teks($salinan), $sesuai, 'Berubah', $catatan);
        if (!$sesuai && $rencana->isNotEmpty() && $salinan->isNotEmpty()) {
            $baris['keterangan'] = collect([
                $baru->isNotEmpty() ? '+' . $baru->count() . ' ' . $satuan : null,
                $keluar->isNotEmpty() ? '−' . $keluar->count() . ' ' . $satuan : null,
            ])->filter()->implode(', ');
        }

        return $baris;
    }

    private function teksTanggal(Kegiatan $kegiatan): ?string
    {
        if (!$kegiatan->tanggal_mulai) {
            return null;
        }

        $teks = $kegiatan->tanggal_mulai->locale('id')->translatedFormat('j F Y');
        if ($kegiatan->tanggal_selesai && !$kegiatan->tanggal_selesai->isSameDay($kegiatan->tanggal_mulai)) {
            $teks .= ' — ' . $kegiatan->tanggal_selesai->locale('id')->translatedFormat('j F Y');
        }

        return $teks;
    }

    private function teksJam(Kegiatan $kegiatan): ?string
    {
        if (!$kegiatan->jam_mulai_formatted) {
            return null;
        }

        // Penulisan jam Indonesia memakai titik (07.00), sesuai mockup pilihan Surya.
        return str_replace(':', '.', $kegiatan->jam_mulai_formatted
            . ($kegiatan->jam_selesai_formatted ? ' – ' . $kegiatan->jam_selesai_formatted : ''))
            . ' WIB';
    }

    private function teksBebas(?string $teks): ?string
    {
        return filled($teks) ? trim($teks) : null;
    }

    private function namaKetua(Kegiatan $kegiatan): ?string
    {
        return $kegiatan->ketuaPelaksana?->user?->name ?? $this->teksBebas($kegiatan->penanggung_jawab);
    }
}
