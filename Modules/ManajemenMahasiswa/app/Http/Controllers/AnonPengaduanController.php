<?php

namespace Modules\ManajemenMahasiswa\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\ManajemenMahasiswa\Models\Pengaduan;
use Modules\ManajemenMahasiswa\Support\PengaduanBukti;

/**
 * Halaman tautan rahasia untuk pengaduan anonim.
 *
 * Pengaduan anonim tidak memakai user_id (akunnya disimpan terpisah di
 * pelapor_tersimpan_id, R-40), jadi tidak muncul di daftar
 * "Pengaduan Saya"; pelapornya melihat kembali lewat tautan ini. Pembuatan
 * pengaduan anonim kini lewat form biasa (PengaduanController) dengan centang
 * "Sembunyikan identitas saya" — alur magic link + draft yang lama sudah dihapus.
 */
class AnonPengaduanController extends Controller
{
    public function track(Request $request, $token)
    {
        $pengaduan = Pengaduan::where('anon_token', $token)->firstOrFail();

        // Draft dari alur magic link lama yang tak pernah dikirim: formnya sudah tidak ada.
        if ($pengaduan->status === Pengaduan::STATUS_DRAFT) {
            return response()->view('manajemenmahasiswa::pengaduan.anon.track', ['pengaduan' => null], 410);
        }

        return view('manajemenmahasiswa::pengaduan.anon.track', [
            'pengaduan'     => $pengaduan,
            'kategoriLabel' => Pengaduan::kategoriLabel((string) $pengaduan->kategori),
        ]);
    }

    /**
     * Membuka satu bukti dukung pengaduan anonim. Hak akses = kepemilikan tautan
     * (sama seperti halaman track); draft tidak punya bukti yang boleh dibuka.
     */
    public function bukti(Request $request, $token, int $index)
    {
        $pengaduan = Pengaduan::where('anon_token', $token)
            ->where('status', '!=', Pengaduan::STATUS_DRAFT)
            ->firstOrFail();

        return PengaduanBukti::respond($pengaduan, $index);
    }
}
