<?php

namespace Modules\EOffice\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\EOffice\Models\KpMahasiswa;
use Modules\EOffice\Models\KerjaPraktik;

class KerjaPraktikController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('eoffice::index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('eoffice::kp.register');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 1. Validasi data yang dikirim mahasiswa
        $validatedData = $request->validate([
            'rencana_tempat' => 'required|string|max:255',
            'rencana_judul' => 'required|string|max:255',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
        ]);

        // 2. Ambil data profil mahasiswa yang sedang login (atau buat jika belum ada)
        $mahasiswa = KpMahasiswa::getOrCreateFromAuth();

        // 3. Tambahkan data pendukung (NIM, ID Mahasiswa, status awal)
        $validatedData['mahasiswa_id'] = $mahasiswa->id;
        $validatedData['nim'] = $mahasiswa->nim;
        $validatedData['status_kp'] = 'Pra-KP'; // Sesuai default di migrasi
        $validatedData['is_acc_admin'] = false;
        
        // 4. Simpan ke database (tabel eo_kerja_praktik di Supabase)
        KerjaPraktik::create($validatedData);

        // 4. Kembalikan mahasiswa ke halaman form dengan pesan sukses
        return redirect()->back()->with('success', 'Pengajuan Kerja Praktik berhasil dikirim! Silakan tunggu konfirmasi.');
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('eoffice::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('eoffice::edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id) {}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id) {}
}
