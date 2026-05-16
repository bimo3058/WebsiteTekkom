<?php

namespace Modules\EOffice\Http\Controllers\ManajemenPraktikum\Mahasiswa;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\EOffice\Models\DaftarPraktikan;
use Modules\EOffice\Models\Praktikum;
use Modules\EOffice\Models\Tugas;
use Modules\EOffice\Models\PengumpulanTugas;
use Modules\EOffice\Models\Pengumuman;
use Modules\EOffice\Models\Nilai;
use Modules\EOffice\Models\Absensi;
use Modules\EOffice\Models\PendaftaranAsprak;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        // Data praktikum yang diikuti mahasiswa
        $daftarPraktikan = DaftarPraktikan::with(['praktikum.dosen', 'praktikum.koordinator'])
            ->where('user_id', $user->id)
            ->get();

        $terdaftarDi = $daftarPraktikan->first()?->praktikum;

        // Jika sudah terdaftar, ambil data relevan
        $tugasMendatang = [];
        $nilaiList      = [];
        $pengumuman     = [];
        $absensiStat    = ['hadir' => 0, 'total' => 0];
        $statusAsprak   = null;

        if ($terdaftarDi) {
            // Tugas yang belum dikumpul
            $tugasMendatang = Tugas::whereHas('modul', fn($q) => $q->where('praktikum_id', $terdaftarDi->id))
                ->where('deadline', '>=', now())
                ->orderBy('deadline')
                ->limit(5)
                ->get()
                ->map(function ($t) use ($user) {
                    $sudahKumpul = PengumpulanTugas::whereHas('daftarPraktikan', fn($q) => $q->where('user_id', $user->id))
                        ->where('tugas_id', $t->id)->exists();
                    return array_merge($t->toArray(), ['sudah_kumpul' => $sudahKumpul]);
                });

            // Nilai per modul
            $nilaiList = Nilai::whereHas('daftarPraktikan', fn($q) => $q->where('user_id', $user->id))
                ->with('daftarPraktikan.praktikum')
                ->get();

            // Pengumuman praktikum
            $pengumuman = Pengumuman::where('praktikum_id', $terdaftarDi->id)
                ->where('is_published', true)
                ->orderByDesc('created_at')
                ->limit(4)
                ->get();

            // Statistik absensi
            $absensiAll         = Absensi::whereHas('daftarPraktikan', fn($q) => $q->where('user_id', $user->id))->get();
            $absensiStat['total']  = $absensiAll->count();
            $absensiStat['hadir']  = $absensiAll->where('status', 'hadir')->count();
        }

        // Status pendaftaran asprak/koor
        $statusAsprak = PendaftaranAsprak::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->first();

        // Apakah mahasiswa belum terdaftar (butuh input kode)
        $belumTerdaftar = $daftarPraktikan->isEmpty();

        return view('eoffice::manajemen-praktikum.mahasiswa.dashboard', compact(
            'daftarPraktikan',
            'terdaftarDi',
            'tugasMendatang',
            'nilaiList',
            'pengumuman',
            'absensiStat',
            'statusAsprak',
            'belumTerdaftar'
        ));
    }

    public function masukkanKode(Request $request)
    {
        $request->validate(['kode' => 'required|string']);
        $user = auth()->user();

        $praktikum = Praktikum::where('kode', $request->kode)
            ->where('status', 'aktif')
            ->first();

        if (!$praktikum) {
            return back()->with('error', 'Kode praktikum tidak valid atau praktikum sudah tidak aktif.');
        }

        $sudahDaftar = DaftarPraktikan::where('user_id', $user->id)
            ->where('praktikum_id', $praktikum->id)
            ->exists();

        if ($sudahDaftar) {
            return back()->with('error', 'Anda sudah terdaftar di praktikum ini.');
        }

        DaftarPraktikan::create([
            'user_id'      => $user->id,
            'praktikum_id' => $praktikum->id,
            'status'       => 'aktif',
        ]);

        return back()->with('success', "Berhasil bergabung ke praktikum: {$praktikum->nama}");
    }
}
