<?php

namespace Modules\EOffice\Http\Controllers\ManajemenPraktikum\Mahasiswa;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\EOffice\Models\Absensi;
use Modules\EOffice\Models\DaftarPraktikan;
use Modules\EOffice\Models\PendaftaranPraktikan;
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
        $daftarPraktikan = DaftarPraktikan::with(['praktikum.dosen', 'praktikum.koordinator'])
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
        $nilaiList      = collect();
        $pengumuman     = collect();
        $absensiStat    = ['hadir' => 0, 'total' => 0];

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
                    $t->sudah_kumpul   = !is_null($pengumpulan);
                    $t->status_tugas   = $pengumpulan?->status_pengumpulan ?? 'belum_dikumpul';
                    return $t;
                });

            // Nilai (hanya yang sudah dipublikasikan)
            $nilaiList = Nilai::where('daftar_praktikan_id', $dp->id)
                ->where('dipublikasikan', true)
                ->get();

            // Pengumuman terbaru yang published
            $pengumuman = Pengumuman::where('praktikum_id', $terdaftarDi->id)
                ->where('is_published', true)
                ->orderByDesc('created_at')
                ->limit(4)
                ->get();

            // Statistik absensi
            $absensiAll = Absensi::where('daftar_praktikan_id', $dp->id)->get();
            $absensiStat['total'] = $absensiAll->count();
            $absensiStat['hadir'] = $absensiAll->where('status', 'hadir')->count();
        }

        // Status pendaftaran asprak/koor
        $statusAsprak = PendaftaranAsprak::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->first();

        $siapGabung = PendaftaranPraktikan::with('praktikum')
            ->where('user_id', $user->id)
            ->where('status', PendaftaranPraktikan::STATUS_APPROVED)
            ->whereNotIn('praktikum_id', $daftarPraktikan->pluck('praktikum_id'))
            ->get();

        $pendaftaranPraktikanTerbaru = PendaftaranPraktikan::with('praktikum')
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        return view('eoffice::manajemen-praktikum.mahasiswa.dashboard', compact(
            'daftarPraktikan',
            'terdaftarDi',
            'tugasMendatang',
            'nilaiList',
            'pengumuman',
            'absensiStat',
            'statusAsprak',
            'belumTerdaftar',
            'siapGabung',
            'pendaftaranPraktikanTerbaru'
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

        $irsDisetujui = PendaftaranPraktikan::where('user_id', $user->id)
            ->where('praktikum_id', $praktikum->id)
            ->where('status', PendaftaranPraktikan::STATUS_APPROVED)
            ->exists();

        if (! $irsDisetujui) {
            return back()->with(
                'error',
                'Anda harus diverifikasi Koordinator (unggah Cetak IRS) sebelum bisa bergabung dengan kode. Buka menu Daftar Praktikan.'
            );
        }

        DaftarPraktikan::create([
            'user_id'      => $user->id,
            'praktikum_id' => $praktikum->id,
            'status'       => 'terdaftar',
        ]);

        return back()->with('success', "Berhasil bergabung ke praktikum: {$praktikum->nama}");
    }
}