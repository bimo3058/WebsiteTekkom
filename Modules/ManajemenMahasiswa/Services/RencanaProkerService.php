<?php

namespace Modules\ManajemenMahasiswa\Services;

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
}
