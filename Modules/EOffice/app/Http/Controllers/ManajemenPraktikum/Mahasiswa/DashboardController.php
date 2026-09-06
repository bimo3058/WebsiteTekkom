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
            if ($terdaftarDi) {
                session(['mhs_praktikum_id' => $terdaftarDi->id]);
            }
        }

        $praktikumIds = $daftarPraktikan->pluck('praktikum_id')->toArray();
        $dpIds = $daftarPraktikan->pluck('id', 'praktikum_id'); // [praktikum_id => daftar_praktikan_id]

        $semuaTugas = collect();
        if (!empty($praktikumIds)) {
            $semuaTugas = Tugas::with(['modul.praktikum'])
                ->whereHas('modul', fn($q) => $q->whereIn('praktikum_id', $praktikumIds))
                ->where('is_published', true)
                ->get()
                ->map(function ($t) use ($dpIds) {
                    $dpId = $dpIds[$t->modul->praktikum_id] ?? null;
                    $pengumpulan = $dpId ? PengumpulanTugas::where('tugas_id', $t->id)->where('daftar_praktikan_id', $dpId)->first() : null;
                    $t->sudah_kumpul = !is_null($pengumpulan);
                    $t->status_tugas = $pengumpulan?->status_pengumpulan ?? 'belum_dikumpul';
                    return $t;
                });
        }

        $now = now();
        $tugasMendatang = $semuaTugas->filter(function ($t) use ($now) {
            return $t->deadline && \Carbon\Carbon::parse($t->deadline)->gte($now) && !$t->sudah_kumpul;
        })->sortBy('deadline')->values();

        $tugasTerlambat = $semuaTugas->filter(function ($t) use ($now) {
            return $t->deadline && \Carbon\Carbon::parse($t->deadline)->lt($now) && !$t->sudah_kumpul;
        })->sortByDesc('deadline')->values();

        $pengumumanPraktikum = Pengumuman::with('praktikum')
            ->whereIn('praktikum_id', $praktikumIds)
            ->where('is_published', true)
            ->orderByDesc('created_at')
            ->get();

        $pengumumanRekrutmen = Pengumuman::whereIn('tipe_sistem', ['buka', 'tutup'])
            ->where('is_published', true)
            ->orderByDesc('created_at')
            ->get();

        // Status pendaftaran asprak/koor
        $statusAsprak = PendaftaranAsprak::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->first();

        $currentYear = now()->year;
        $currentSemester = now()->month <= 6 ? 'Genap' : 'Ganjil';
        $defaultTahunAjaran = $currentSemester === 'Genap' ? $currentYear - 1 : $currentYear;
        $semesterLabel = "Semester {$currentSemester} {$defaultTahunAjaran}/" . ($defaultTahunAjaran + 1);

        return view('eoffice::manajemen-praktikum.mahasiswa.dashboard', compact(
            'daftarPraktikan',
            'terdaftarDi',
            'tugasMendatang',
            'tugasTerlambat',
            'pengumumanPraktikum',
            'pengumumanRekrutmen',
            'statusAsprak',
            'belumTerdaftar',
            'semesterLabel'
        ));
    }

}