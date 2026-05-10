<?php

namespace Modules\ManajemenMahasiswa\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\ManajemenMahasiswa\Models\Pengaduan;
use Modules\ManajemenMahasiswa\Models\PengaduanLog;

class AnonPengaduanController extends Controller
{
    /**
     * Membuat tiket draft (Magic Link) ketika mahasiswa memilih jalur Konfidensial.
     */
    public function generate(Request $request)
    {
        $pengaduan = Pengaduan::create([
            'user_id' => $request->user()->id,
            'kategori' => Pengaduan::KATEGORI_LAINNYA, // Sementara
            'is_anonim' => true,
            'anon_token' => \Illuminate\Support\Str::random(32),
            'status' => Pengaduan::STATUS_DRAFT,
            'data_template' => [],
        ]);

        return view('manajemenmahasiswa::pengaduan.anon.init', compact('pengaduan'));
    }

    /**
     * Menampilkan form (jika draft) atau detail tiket (jika sudah disubmit) via magic link.
     */
    public function track(Request $request, $token)
    {
        $pengaduan = Pengaduan::with(['logs.actor', 'delegasiAktif.delegatedTo'])
            ->where('anon_token', $token)
            ->firstOrFail();

        if ($pengaduan->status === Pengaduan::STATUS_DRAFT) {
            $kategoriList = [
                'akademik' => ['label' => 'Akademik & Pembelajaran', 'example' => 'Jadwal bentrok, nilai tidak keluar'],
                'fasilitas' => ['label' => 'Fasilitas & Infrastruktur', 'example' => 'AC rusak, WiFi lambat'],
                'pelayanan' => ['label' => 'Pelayanan & Administrasi', 'example' => 'Surat keterangan lama, staf kurang ramah'],
                'lainnya' => ['label' => 'Lainnya', 'example' => 'Masalah di luar kategori di atas'],
            ];

            $dosenList = [
                'Prof. Dr. Adian Fatchur Rochim, S.T., M.T.',
                'Prof. Dr. Ir. R. Rizal Isnanto, S.T., M.M., M.T., IPU, ASEAN Eng.',
                'Dr. Oky Dwi Nurhayati, S.T., M.T.',
                'Agung Budi Prasetijo, S.T., M.I.T., Ph.D.',
                'Dr. Maman Somantri, S.T., M.T.',
                'Rinta Kridalukmana, S.Kom., M.T., Ph.D.',
                'Kuntoro Adi Nugroho, S.T., M.Eng., Ph.D.',
                'Yudi Eko Windarto, S.T., M.Kom.',
                'Dr. Delphi Hanggoro, S.T., M.T.',
                'Dania Eridani, S.T., M.Eng.',
                'Ike Pertiwi Windasari, S.T., M.T.',
                'Eko Didik Widianto, S.T., M.T.',
                'Kurniawan Teguh Martono, S.T., M.T.',
                'Risma Septiana, S.T., M.Eng.',
                'Adnan Fauzi, S.T., M.Kom.',
                'Patricia Evericho Mountaines, S.T., M.Cs.',
                'Bellia Dwi Cahya Putri, S.T., M.T.',
                'Ilmam Fauzi Hashbil Alim, S.T., M.Kom.',
                'Erwin Adriono, S.T., M.T.',
                'Arseto Satriyo Nugroho, S.T., M.Eng.',
            ];

            $frekuensiList = [
                'Sekali' => 'Sekali',
                'Kadang-kadang' => 'Kadang-kadang',
                'Sering' => 'Sering',
                'Hampir Setiap Pertemuan Kuliah' => 'Hampir Setiap Pertemuan Kuliah',
            ];

            return view('manajemenmahasiswa::pengaduan.anon.create', compact('pengaduan', 'token', 'kategoriList', 'dosenList', 'frekuensiList'));
        }

        return view('manajemenmahasiswa::pengaduan.anon.track', compact('pengaduan'));
    }

    /**
     * Submit form pengaduan anonim, mengubah status draft menjadi baru.
     */
    public function store(Request $request, $token)
    {
        $pengaduan = Pengaduan::where('anon_token', $token)
            ->where('status', Pengaduan::STATUS_DRAFT)
            ->firstOrFail();

        $request->validate([
            'kategori' => 'required|string',
            'template' => 'required|array',
            'template.judul' => 'required|string|max:255',
            'template.hal_aduan' => 'required|string',
            'template.kronologi' => 'required|string|min:20',
        ]);

        $template = [
            'judul'            => trim($request->input('template.judul', '')),
            'hal_aduan'        => trim($request->input('template.hal_aduan', '')),
            'kronologi'        => trim($request->input('template.kronologi', '')),
            'waktu_kejadian'   => trim($request->input('template.waktu_kejadian', '')),
            'lokasi'           => trim($request->input('template.lokasi', '')),
            'mata_kuliah'      => trim($request->input('template.mata_kuliah', '')),
            'nama_dosen'       => trim($request->input('template.nama_dosen', '')),
            'nama_tendik'      => trim($request->input('template.nama_tendik', '')),
            'angkatan'         => trim($request->input('template.angkatan', '')),
            'frekuensi'        => trim($request->input('template.frekuensi', '')),
            'link_bukti'       => trim($request->input('template.link_bukti', '')),
        ];

        $pengaduan->update([
            'kategori' => $request->input('kategori'),
            'data_template' => $template,
            'status' => Pengaduan::STATUS_BARU,
        ]);

        $pengaduan->logs()->create([
            'actor_user_id' => $pengaduan->user_id,
            'action' => PengaduanLog::ACTION_DIBUAT,
            'created_at' => now(),
        ]);

        return redirect()->route('manajemenmahasiswa.pengaduan.track', ['token' => $token])
            ->with('success', 'Pengaduan konfidensial Anda telah berhasil dikirim.');
    }

    /**
     * Mahasiswa menandai pengaduan selesai melalui magic link.
     */
    public function close(Request $request, $token)
    {
        $pengaduan = Pengaduan::where('anon_token', $token)->firstOrFail();

        if ($pengaduan->status !== Pengaduan::STATUS_DIJAWAB) {
            return redirect()->back()->withErrors(['message' => 'Hanya pengaduan yang sudah dijawab yang dapat ditutup.']);
        }

        $pengaduan->update([
            'status' => Pengaduan::STATUS_SELESAI,
            'closed_at' => now(),
        ]);

        return redirect()->route('manajemenmahasiswa.pengaduan.track', ['token' => $token])
            ->with('success', 'Pengaduan telah berhasil ditandai sebagai Selesai.');
    }

    /**
     * Mahasiswa mengajukan ulang melalui magic link.
     */
    public function reopen(Request $request, $token)
    {
        $pengaduan = Pengaduan::where('anon_token', $token)->firstOrFail();

        if (!$pengaduan->canReopen()) {
            return redirect()->back()->withErrors(['message' => 'Pengaduan ini tidak dapat diajukan ulang.']);
        }

        $request->validate([
            'reopen_reason' => 'required|string|min:10',
        ]);

        $pengaduan->update([
            'status' => Pengaduan::STATUS_DIAJUKAN_ULANG,
            'reopen_count' => $pengaduan->reopen_count + 1,
            'reopen_reason' => $request->input('reopen_reason'),
        ]);

        return redirect()->route('manajemenmahasiswa.pengaduan.track', ['token' => $token])
            ->with('success', 'Pengaduan telah diajukan ulang ke Admin.');
    }
}
