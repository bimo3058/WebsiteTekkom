<?php

namespace Modules\EOffice\Http\Controllers\ManajemenPraktikum\Mahasiswa;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\EOffice\Models\Absensi;
use Modules\EOffice\Models\DaftarPraktikan;
use Modules\EOffice\Models\Nilai;
use Modules\EOffice\Models\PendaftaranAsprak;
use Modules\EOffice\Models\Pengumuman;
use Modules\EOffice\Models\PengumpulanTugas;
use Modules\EOffice\Models\Praktikum;
use Modules\EOffice\Models\Tugas;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        // Semua praktikum yang diikuti mahasiswa (bisa lebih dari satu)
        $daftarPraktikan = DaftarPraktikan::with(['praktikum.dosens', 'praktikum.koordinator'])
            ->where('user_id', $user->id)
            ->get();

        $belumTerdaftar = $daftarPraktikan->isEmpty();

        // 1. Ambil dari request, fallback ke session, fallback terakhir ke data pertama
        $praktikumAktifId = $request->input('praktikum_id')
            ?? session('mhs_praktikum_id')
            ?? $daftarPraktikan->first()?->praktikum_id;

        // 2. Simpan pilihan ke session
        if ($praktikumAktifId) {
            session(['mhs_praktikum_id' => $praktikumAktifId]);
        }

        // 3. Cari entri pendaftaran yang cocok
        $terdaftarDi = $daftarPraktikan->firstWhere('praktikum_id', $praktikumAktifId)?->praktikum;

        // Fallback jika session menyimpan ID praktikum lama yang sudah tidak diikuti
        if (!$terdaftarDi && $daftarPraktikan->isNotEmpty()) {
            $terdaftarDi = $daftarPraktikan->first()->praktikum;
            session(['mhs_praktikum_id' => $terdaftarDi->id]);
        }

        $tugasMendatang = collect();
        $nilaiList = collect();
        $pengumuman = collect();
        $absensiStat = ['hadir' => 0, 'total' => 0];

        if ($terdaftarDi) {
            $dp = $daftarPraktikan->firstWhere('praktikum_id', $terdaftarDi->id);

            // Tugas belum dikumpul / mendatang
            $tugasMendatang = Tugas::whereHas('modul', fn($q) => $q->where('praktikum_id', $terdaftarDi->id))
                ->where('is_published', true)
                ->where('deadline', '>=', now())
                ->orderBy('deadline')
                ->limit(5)
                ->get()
                ->map(function ($t) use ($dp) {
                    $pengumpulan = PengumpulanTugas::where('tugas_id', $t->id)
                        ->where('daftar_praktikan_id', $dp->id)
                        ->first();
                    $t->sudah_kumpul = !is_null($pengumpulan);
                    $t->status_tugas = $pengumpulan?->status_pengumpulan ?? 'belum_dikumpul';
                    return $t;
                });

            // Nilai (hanya yang sudah dipublikasikan)
            $nilaiList = Nilai::where('daftar_praktikan_id', $dp->id)
                ->where('dipublikasikan', true)
                ->get();

            // Pengumuman terbaru yang published (terkait kelas ini atau pengumuman pendaftaran sistem)
            $pengumuman = Pengumuman::where(function ($q) use ($terdaftarDi) {
                $q->where('praktikum_id', $terdaftarDi->id)
                    ->orWhereIn('tipe_sistem', ['buka', 'tutup']);
            })
                ->where('is_published', true)
                ->orderByDesc('created_at')
                ->limit(4)
                ->get();

            // Statistik absensi
            $absensiAll = Absensi::where('daftar_praktikan_id', $dp->id)->get();
            $absensiStat['total'] = $absensiAll->count();
            $absensiStat['hadir'] = $absensiAll->where('status', 'hadir')->count();
        } else {
            // Jika belum terdaftar di mana pun, minimal ambil pengumuman sistem (global)
            $pengumuman = Pengumuman::whereIn('tipe_sistem', ['buka', 'tutup'])
                ->where('is_published', true)
                ->orderByDesc('created_at')
                ->limit(4)
                ->get();
        }

        // Status pendaftaran asprak/koor
        $statusAsprak = PendaftaranAsprak::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->first();

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

}