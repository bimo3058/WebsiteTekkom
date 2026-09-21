<?php

namespace Modules\ManajemenMahasiswa\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Modules\ManajemenMahasiswa\Http\Requests\PengaduanPayloadRequest;
use Modules\ManajemenMahasiswa\Models\Pengaduan;
use Modules\ManajemenMahasiswa\Models\PengaduanLog;

class AnonPengaduanController extends Controller
{
    /**
     * Membuat tiket draft (Magic Link) ketika mahasiswa memilih jalur Konfidensial.
     *
     * Hanya mahasiswa, dan hanya lewat POST. Sebelumnya ini route GET tanpa guard
     * role sehingga (a) semua role yang login bisa membuat tiket dan (b) setiap
     * klik/refresh/prefetch menambah satu baris draft yang tidak muncul di UI
     * mana pun. Draft yang belum disubmit kini dipakai ulang.
     */
    public function generate(Request $request)
    {
        $user = $request->user();
        $this->ensureMahasiswa($user);

        $pengaduan = Pengaduan::query()
            ->where('user_id', $user->id)
            ->where('is_anonim', true)
            ->where('status', Pengaduan::STATUS_DRAFT)
            ->latest('id')
            ->first();

        if (!$pengaduan) {
            $pengaduan = Pengaduan::create([
                'user_id' => $user->id,
                'kategori' => Pengaduan::KATEGORI_LAINNYA, // Sementara, diganti saat submit
                'is_anonim' => true,
                'anon_token' => Str::random(32),
                'status' => Pengaduan::STATUS_DRAFT,
                'data_template' => [],
            ]);
        }

        // Modal pemilih jalur meminta JSON dan menampilkan tautannya di tempat;
        // halaman init tetap menjadi cadangan bila JavaScript tidak jalan.
        if ($request->expectsJson()) {
            return response()->json([
                'url' => route('manajemenmahasiswa.pengaduan.track', ['token' => $pengaduan->anon_token]),
            ]);
        }

        return view('manajemenmahasiswa::pengaduan.anon.init', compact('pengaduan'));
    }

    private function ensureMahasiswa($user): void
    {
        if (!$user || !method_exists($user, 'hasAnyRole') || !$user->hasAnyRole(PengaduanController::PELAPOR_ROLES)) {
            abort(403, 'Hanya mahasiswa yang dapat membuat pengaduan.');
        }
    }

    /**
     * Menampilkan form (jika draft) atau detail tiket (jika sudah disubmit) via magic link.
     */
    public function track(Request $request, $token)
    {
        $pengaduan = Pengaduan::with(['logs.actor'])
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
    public function confirm(PengaduanPayloadRequest $request, $token)
    {
        $pengaduan = Pengaduan::where('anon_token', $token)
            ->where('status', Pengaduan::STATUS_DRAFT)
            ->firstOrFail();

        $request->flash();

        return view('manajemenmahasiswa::pengaduan.anon.confirm', [
            'pengaduan' => $pengaduan,
            'token' => $token,
            'payload' => [
                'kategori' => $request->validated('kategori'),
                'template' => $request->normalizedTemplate(),
            ],
        ]);
    }

    /**
     * Submit form pengaduan anonim, mengubah status draft menjadi baru.
     */
    public function store(PengaduanPayloadRequest $request, $token)
    {
        $pengaduan = Pengaduan::where('anon_token', $token)
            ->where('status', Pengaduan::STATUS_DRAFT)
            ->firstOrFail();

        $pengaduan->update([
            'kategori' => $request->validated('kategori'),
            'data_template' => $request->normalizedTemplate(),
            'status' => Pengaduan::STATUS_BARU,
        ]);

        $pengaduan->logs()->create([
            // Tiket konfidensial: jangan pernah menyimpan identitas pelapor di log,
            // karena panel "Riwayat Tiket" menampilkan nama actor kepada staf.
            'actor_user_id' => null,
            'action' => PengaduanLog::ACTION_DIBUAT,
            'created_at' => now(),
        ]);

        return redirect()->route('manajemenmahasiswa.pengaduan.track', ['token' => $token])
            ->with('success', 'Pengaduan konfidensial Anda telah berhasil dikirim.');
    }

}
