<?php

namespace Modules\EOffice\Http\Controllers\ManajemenPraktikum\Mahasiswa;

use App\Services\SupabaseStorage;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\EOffice\Models\DaftarPraktikan;
use Modules\EOffice\Models\PendaftaranPraktikan;
use Modules\EOffice\Models\PeriodePendaftaran;
use Modules\EOffice\Models\Praktikum;

class PendaftaranPraktikanController extends Controller
{
    public function __construct(private SupabaseStorage $supabase) {}

    private function getPeriodeAktif(string $praktikumId): ?PeriodePendaftaran
    {
        return PeriodePendaftaran::where('praktikum_id', $praktikumId)
            ->where('jenis', 'praktikan')
            ->where('is_aktif', true)
            ->where(fn ($q) => $q->whereNull('dibuka_pada')->orWhere('dibuka_pada', '<=', now()))
            ->where(fn ($q) => $q->whereNull('ditutup_pada')->orWhere('ditutup_pada', '>', now()))
            ->latest('created_at')
            ->first();
    }

    public function index(Request $request)
    {
        $user = auth()->user();

        $praktikumBuka = Praktikum::where('status', 'aktif')
            ->whereHas('periodePendaftaran', function ($q) {
                $q->where('jenis', 'praktikan')
                    ->where('is_aktif', true)
                    ->where(fn ($x) => $x->whereNull('dibuka_pada')->orWhere('dibuka_pada', '<=', now()))
                    ->where(fn ($x) => $x->whereNull('ditutup_pada')->orWhere('ditutup_pada', '>', now()));
            })
            ->with(['dosens', 'matkul'])
            ->orderBy('nama')
            ->get();

        $riwayat = PendaftaranPraktikan::with('praktikum')
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->limit(30)
            ->get();

        $periodeByPraktikum = [];
        foreach ($praktikumBuka as $p) {
            $periodeByPraktikum[$p->id] = $this->getPeriodeAktif($p->id);
        }

        // Ambil praktikum yang diikuti mahasiswa (dari daftar_praktikan)
        $daftarPraktikan = DaftarPraktikan::with(['praktikum.dosen', 'praktikum.koordinator'])
            ->where('user_id', $user->id)
            ->get();

        $activePraktikumId = $request->input('praktikum_id')
            ?? session('mhs_praktikum_id')
            ?? $daftarPraktikan->first()?->praktikum_id;

        if ($activePraktikumId) {
            session(['mhs_praktikum_id' => $activePraktikumId]);
        }

        $activePraktikum = $daftarPraktikan->firstWhere('praktikum_id', $activePraktikumId)?->praktikum;

        $classmates = collect();
        if ($activePraktikum) {
            $classmates = DaftarPraktikan::with(['user', 'user.student'])
                ->where('praktikum_id', $activePraktikum->id)
                ->orderByRaw("CASE WHEN (shift IS NULL OR shift = '') THEN 1 ELSE 0 END, shift ASC")
                ->orderByRaw("CASE WHEN (kelompok IS NULL OR kelompok = '') THEN 1 ELSE 0 END, kelompok ASC")
                ->orderBy('created_at')
                ->get();
        }

        return view('eoffice::manajemen-praktikum.mahasiswa.pendaftaran-praktikan', compact(
            'praktikumBuka',
            'riwayat',
            'periodeByPraktikum',
            'activePraktikum',
            'classmates'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'praktikum_id' => 'required|uuid|exists:eo_praktikum,id',
            'irs'          => 'required|file|max:5120|mimes:pdf,jpg,jpeg,png',
        ]);

        $user = auth()->user();

        $periode = $this->getPeriodeAktif($request->praktikum_id);
        if (! $periode) {
            return back()->with('error', 'Pendaftaran praktikan untuk praktikum ini sedang tidak dibuka.');
        }

        if (DaftarPraktikan::where('user_id', $user->id)->where('praktikum_id', $request->praktikum_id)->exists()) {
            return back()->with('error', 'Anda sudah terdaftar sebagai praktikan di praktikum ini.');
        }

        $pending = PendaftaranPraktikan::where('user_id', $user->id)
            ->where('praktikum_id', $request->praktikum_id)
            ->where('status', PendaftaranPraktikan::STATUS_PENDING)
            ->exists();
        if ($pending) {
            return back()->with('error', 'Anda sudah memiliki pendaftaran praktikan yang menunggu review.');
        }

        $approved = PendaftaranPraktikan::where('user_id', $user->id)
            ->where('praktikum_id', $request->praktikum_id)
            ->where('status', PendaftaranPraktikan::STATUS_APPROVED)
            ->exists();
        if ($approved) {
            return back()->with('error', 'Anda sudah disetujui. Gunakan kode praktikum di dashboard untuk bergabung ke kelas.');
        }

        $irsPath = $this->supabase->upload($request->file('irs'), 'praktikan-irs/' . $user->id, 'eoffice');

        PendaftaranPraktikan::create([
            'user_id'       => $user->id,
            'praktikum_id'  => $request->praktikum_id,
            'periode_id'    => $periode->id,
            'irs_path'      => $irsPath,
            'status'        => PendaftaranPraktikan::STATUS_PENDING,
        ]);

        return back()->with('success', 'Berkas IRS berhasil dikirim. Tunggu verifikasi dari Koordinator Praktikum.');
    }
}
