<?php

namespace Modules\BankSoal\Http\Controllers\Komprehensif;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\BankSoal\Models\PeriodeUjian;
use Modules\BankSoal\Models\PendaftarUjian;
use Modules\BankSoal\Models\JadwalUjian;

class AlokasiSesiController extends Controller
{
    public function index(Request $request)
    {
        $periodes = PeriodeUjian::orderBy('created_at', 'desc')->get();
        $selectedPeriodeId = $request->periode_id ?? ($periodes->first()->id ?? null);
        
        $jadwals = collect();
        $pendaftars = collect();
        $selectedPeriode = null;
        $activePeriode = null;

        if ($periodes->isEmpty()) {
            return view('banksoal::alokasi-sesi.index', compact('periodes', 'selectedPeriodeId', 'selectedPeriode', 'activePeriode', 'jadwals', 'pendaftars'));
        }

        if ($selectedPeriodeId) {
            $selectedPeriode = PeriodeUjian::find($selectedPeriodeId);
            $activePeriode = $selectedPeriode;

            // Ambil semua jadwal ujian yang terkait dengan periode ini beserta hitung pesertanya
            $jadwals = JadwalUjian::where('periode_ujian_id', $selectedPeriodeId)
                ->withCount(['pendaftars as terisi'])
                ->orderBy('tanggal_ujian')
                ->orderBy('waktu_mulai')
                ->get();
                
            // Ambil mahasiswa yang status pendaftarannya approved
            $pendaftars = PendaftarUjian::with(['mahasiswa', 'jadwal'])
                ->where('periode_ujian_id', $selectedPeriodeId)
                ->where('status_pendaftaran', 'approved')
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return view('banksoal::alokasi-sesi.index', compact('periodes', 'selectedPeriodeId', 'selectedPeriode', 'activePeriode', 'jadwals', 'pendaftars'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'pendaftar_ids' => 'required|array|min:1',
            'jadwal_id' => 'required|exists:bs_jadwal_ujians,id'
        ], [
            'pendaftar_ids.required' => 'Pilih minimal satu mahasiswa untuk dialokasikan.',
            'jadwal_id.required' => 'Anda harus memilih sesi ujian tujuan.'
        ]);

        $jadwal = JadwalUjian::findOrFail($request->jadwal_id);

        // Cari tahu jumlah slot yang terisi saat ini
        $currentFilled = PendaftarUjian::where('jadwal_ujian_id', $jadwal->id)->count();

        // Cari tahu dari request, mana mahasiswa yang sebelumnya BELUM masuk ke jadwal ini
        // (Supaya jika mereka dichecklist kembali ke jadwal yang sama, tidak dihitung memakan kuota tambahan)
        $newAssigneesCount = PendaftarUjian::whereIn('id', $request->pendaftar_ids)
            ->where(function ($q) use ($jadwal) {
                $q->whereNull('jadwal_ujian_id')
                  ->orWhere('jadwal_ujian_id', '!=', $jadwal->id);
            })->count();

        // Validasi Kapasitas
        if (($currentFilled + $newAssigneesCount) > $jadwal->kuota) {
            return back()->with('error', "Gagal! Sesi '{$jadwal->nama_sesi}' hanya memiliki sisa " . ($jadwal->kuota - $currentFilled) . " kuota.");
        }

        // Eksekusi Pindah Sesi
        PendaftarUjian::whereIn('id', $request->pendaftar_ids)->update([
            'jadwal_ujian_id' => $jadwal->id
        ]);

        return back()->with('success', 'Berhasil! Peserta telah sukses dialokasikan ke Sesi: ' . $jadwal->nama_sesi);
    }

    public function remove(Request $request)
    {
        $request->validate([
            'pendaftar_ids' => 'required|array|min:1'
        ], [
            'pendaftar_ids.required' => 'Pilih minimal satu mahasiswa untuk dicabut sesinya.'
        ]);

        PendaftarUjian::whereIn('id', $request->pendaftar_ids)->update([
            'jadwal_ujian_id' => null
        ]);

        return back()->with('success', 'Berhasil! Peserta telah dicabut dari sesi ujian terkait.');
    }
}
