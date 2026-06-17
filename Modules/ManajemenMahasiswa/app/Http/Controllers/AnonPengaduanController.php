<?php

namespace Modules\ManajemenMahasiswa\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
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
        $pengaduan = Pengaduan::with(['logs.actor', 'delegasiAktif.delegatedTo', 'delegasiTerakhir'])
            ->where('anon_token', $token)
            ->firstOrFail();

        if ($pengaduan->status === Pengaduan::STATUS_DRAFT) {
            $kategoriList = [
                Pengaduan::KATEGORI_AKADEMIK_ADMINISTRASI => [
                    'label' => 'Akademik dan Administrasi',
                    'example' => 'KRS, transkrip, surat-menyurat, masalah administrasi akademik',
                ],
                Pengaduan::KATEGORI_PROSES_PEMBELAJARAN => [
                    'label' => 'Proses Pembelajaran di Kelas',
                    'example' => 'Metode mengajar, penilaian, materi tidak sesuai, jadwal perkuliahan',
                ],
                Pengaduan::KATEGORI_FASILITAS_KAMPUS => [
                    'label' => 'Fasilitas Kampus (Sarana dan Prasarana)',
                    'example' => 'AC/infocus rusak, kursi/kelas, kebersihan, lab/praktikum',
                ],
                Pengaduan::KATEGORI_LAYANAN_IT_SSO => [
                    'label' => 'Layanan IT dan Akun SSO',
                    'example' => 'SSO/login, email kampus, akses WiFi, LMS/portal bermasalah',
                ],
                Pengaduan::KATEGORI_KEGIATAN_KEMAHASISWAAN => [
                    'label' => 'Kegiatan Kemahasiswaan',
                    'example' => 'UKM/Himpunan, proposal kegiatan, perizinan, pendanaan',
                ],
                Pengaduan::KATEGORI_KEAMANAN_KETERTIBAN => [
                    'label' => 'Keamanan dan Ketertiban Kampus',
                    'example' => 'Parkir, kehilangan barang, keamanan area kampus, keributan',
                ],
                Pengaduan::KATEGORI_KESEHATAN_KONSELING => [
                    'label' => 'Layanan Kesehatan dan Konseling Mahasiswa',
                    'example' => 'Konseling, kesehatan mental, layanan klinik kampus, rujukan',
                ],
                Pengaduan::KATEGORI_TINDAKAN_TIDAK_MENYENANGKAN => [
                    'label' => 'Tindakan Tidak Menyenangkan di Lingkungan Kampus',
                    'example' => 'Perundungan, pelecehan, intimidasi, perlakuan tidak pantas',
                ],
            ];

            $dosenList = User::whereHas('roles', fn($q) => $q->whereIn('name', ['dosen', 'dosen_koordinator']))
                ->orderBy('name')
                ->pluck('name')
                ->toArray();

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
     * Konfirmasi form pengaduan anonim sebelum disubmit.
     */
    public function confirm(Request $request, $token)
    {
        $pengaduan = Pengaduan::where('anon_token', $token)
            ->where('status', Pengaduan::STATUS_DRAFT)
            ->firstOrFail();

        $validated = $request->validate([
            'kategori' => 'required|string',
            'template' => 'required|array',
            'template.judul' => 'required|string|max:255',
            'template.hal_aduan' => 'required|string',
            'template.kronologi' => 'required|string|min:20',
            'template.angkatan' => 'nullable|string|max:20',
            'template.lokasi' => 'nullable|string|max:255',
            'template.waktu_kejadian' => 'nullable|date',
            'template.tanggal_kejadian' => 'nullable|date',
            'template.mata_kuliah' => 'nullable|string|max:255',
            'template.nama_dosen' => 'nullable|string|max:255',
            'template.nama_tendik' => 'nullable|string|max:255',
            'template.frekuensi' => 'nullable|string|max:100',
            'template.link_bukti' => 'nullable|url|max:2048',
        ]);

        $request->flash();

        return view('manajemenmahasiswa::pengaduan.anon.confirm', [
            'pengaduan' => $pengaduan,
            'token' => $token,
            'payload' => [
                'kategori' => $validated['kategori'],
                'template' => $validated['template'],
            ],
        ]);
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

}
