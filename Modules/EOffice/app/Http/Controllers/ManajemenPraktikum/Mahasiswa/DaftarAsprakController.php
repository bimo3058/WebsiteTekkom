<?php

namespace Modules\EOffice\Http\Controllers\ManajemenPraktikum\Mahasiswa;

use App\Services\SupabaseStorage;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\EOffice\Models\AsprakPraktikum;
use Modules\EOffice\Models\PendaftaranAsprak;
use Modules\EOffice\Models\PendaftaranKoordinator;
use Modules\EOffice\Models\PeriodePendaftaran;
use Modules\EOffice\Models\Praktikum;

class DaftarAsprakController extends Controller
{
    public function __construct(private SupabaseStorage $supabase) {}

    private function getPeriodeAktif(string $praktikumId, string $jenis): ?PeriodePendaftaran
    {
        return PeriodePendaftaran::where('praktikum_id', $praktikumId)
            ->where('jenis', $jenis)
            ->where('is_aktif', true)
            ->where(fn($q) => $q->whereNull('dibuka_pada')->orWhere('dibuka_pada', '<=', now()))
            ->where(fn($q) => $q->whereNull('ditutup_pada')->orWhere('ditutup_pada', '>', now()))
            ->latest('created_at')
            ->first();
    }

    public function index(Request $request)
    {
        $user = auth()->user();

        // Muat periode sekaligus agar jumlah query tidak bertambah per praktikum.
        $praktikumDenganPeriode = Praktikum::where('status', 'aktif')
            ->whereHas('periodeAktif')
            ->with(['matkul', 'periodeAktif' => fn ($q) => $q->latest('created_at')])
            ->get();

        // Cek periode aktif per praktikum (asprak & koor)
        $periodeAktif = [];
        foreach ($praktikumDenganPeriode as $p) {
            $periodeAktif[$p->id] = [
                'asprak' => $p->periodeAktif->firstWhere('jenis', 'asprak'),
                'koor'   => $p->periodeAktif->firstWhere('jenis', 'koor'),
            ];
        }

        // Sudah punya role aktif?
        $sudahJadiAsprak = $user->hasRole('asprak');
        $sudahJadiKoor   = $user->hasRole('koor_prak');

        $selectedPraktikumId = $request->input('praktikum_id', $praktikumDenganPeriode->first()?->id);
        $selectedPraktikum = $praktikumDenganPeriode->firstWhere('id', $selectedPraktikumId);
        $existingAsprak = null;
        $existingKoor = null;
        $rolesPraktikum = collect();

        if ($selectedPraktikum) {
            $existingAsprak = PendaftaranAsprak::where('user_id', $user->id)
                ->where('praktikum_id', $selectedPraktikum->id)
                ->latest('created_at')->first();
            $existingKoor = PendaftaranKoordinator::where('user_id', $user->id)
                ->where('praktikum_id', $selectedPraktikum->id)
                ->latest('created_at')->first();
            $rolesPraktikum = AsprakPraktikum::where('user_id', $user->id)
                ->where('praktikum_id', $selectedPraktikum->id)
                ->pluck('role');
        }

        $isAsprakDiPraktikumIni = $rolesPraktikum->contains('asprak');
        $isKoorDiPraktikumIni = $rolesPraktikum->contains('koor');

        return view('eoffice::manajemen-praktikum.mahasiswa.daftar-asprak', compact(
            'praktikumDenganPeriode',
            'periodeAktif',
            'sudahJadiAsprak',
            'sudahJadiKoor',
            'selectedPraktikumId',
            'selectedPraktikum',
            'existingAsprak',
            'existingKoor',
            'isAsprakDiPraktikumIni',
            'isKoorDiPraktikumIni'
        ));
    }

    /**
     * Submit pendaftaran asprak.
     * Syarat: periode asprak harus terbuka. Tidak perlu sudah terdaftar sebagai praktikan.
     */
    public function store(Request $request)
    {
        $request->validate([
            'praktikum_id' => 'required|uuid|exists:eo_praktikum,id',
            'ipk'          => 'required|numeric|min:0|max:4',
            'motivasi'     => 'nullable|string|max:1000',
            'transkrip'    => 'required|file|max:5120|mimes:pdf',
            'berkas_cerc'  => 'nullable|file|max:5120|mimes:pdf,jpg,jpeg,png,xlsx,csv',
            'jadwal'       => 'nullable|array',
        ]);

        $user = auth()->user();

        // Cek periode asprak masih terbuka
        $periode = $this->getPeriodeAktif($request->praktikum_id, 'asprak');
        if (!$periode) {
            return back()->with('error', 'Pendaftaran Asisten Praktikum untuk praktikum ini sedang tidak dibuka.');
        }

        // Cek sudah daftar sebelumnya (pending/approved)
        $existing = PendaftaranAsprak::where('user_id', $user->id)
            ->where('praktikum_id', $request->praktikum_id)
            ->whereIn('status', ['pending', 'approved'])
            ->exists();
        if ($existing) {
            return back()->with('error', 'Anda sudah memiliki pendaftaran asprak aktif untuk praktikum ini.');
        }


        $transkripPath = $request->hasFile('transkrip')
            ? $this->supabase->upload($request->file('transkrip'), 'asprak-transkrip/' . $user->id, 'eoffice')
            : null;

        $berkasCercPath = $request->hasFile('berkas_cerc')
            ? $this->supabase->upload($request->file('berkas_cerc'), 'asprak-cerc/' . $user->id, 'eoffice')
            : null;

        PendaftaranAsprak::create([
            'user_id'        => $user->id,
            'praktikum_id'   => $request->praktikum_id,
            'ipk'            => $request->ipk,
            'motivasi'       => $request->motivasi,
            'cv_path'        => null,
            'transkrip_path' => $transkripPath,
            'berkas_cerc_path' => $berkasCercPath,
            'jadwal'         => $request->jadwal ?? [],
            'status'         => 'pending',
        ]);

        return back()->with('success', 'Pendaftaran Asisten Praktikum berhasil dikirim! Tunggu konfirmasi dari Koordinator.');
    }

    /**
     * Submit pendaftaran koordinator.
     * Syarat: periode koor harus terbuka.
     */
    public function daftarKoor(Request $request)
    {
        $request->validate([
            'praktikum_id' => 'required|uuid|exists:eo_praktikum,id',
            'ipk'          => 'required|numeric|min:0|max:4',
            'motivasi'     => 'nullable|string|max:1000',
            'transkrip'    => 'required|file|max:5120|mimes:pdf',
            'berkas_cerc'  => 'nullable|file|max:5120|mimes:pdf,jpg,jpeg,png,xlsx,csv',
        ]);

        $user = auth()->user();

        // Cek periode koor masih terbuka
        $periode = $this->getPeriodeAktif($request->praktikum_id, 'koor');
        if (!$periode) {
            return back()->with('error', 'Pendaftaran Koordinator untuk praktikum ini sedang tidak dibuka.');
        }

        // Cek sudah daftar sebelumnya
        $existing = PendaftaranKoordinator::where('user_id', $user->id)
            ->where('praktikum_id', $request->praktikum_id)
            ->whereIn('status', ['pending', 'approved'])
            ->exists();
        if ($existing) {
            return back()->with('error', 'Anda sudah memiliki pendaftaran koordinator aktif untuk praktikum ini.');
        }

        $transkripPath = $request->hasFile('transkrip')
            ? $this->supabase->upload($request->file('transkrip'), 'koor-transkrip/' . $user->id, 'eoffice')
            : null;

        $berkasCercPath = $request->hasFile('berkas_cerc')
            ? $this->supabase->upload($request->file('berkas_cerc'), 'koor-cerc/' . $user->id, 'eoffice')
            : null;

        PendaftaranKoordinator::create([
            'user_id'        => $user->id,
            'praktikum_id'   => $request->praktikum_id,
            'ipk'            => $request->ipk,
            'motivasi'       => $request->motivasi,
            'transkrip_path' => $transkripPath,
            'berkas_cerc_path' => $berkasCercPath,
            'status'         => 'pending',
        ]);

        return back()->with('success', 'Pendaftaran Koordinator berhasil dikirim! Tunggu konfirmasi dari Dosen.');
    }
}
