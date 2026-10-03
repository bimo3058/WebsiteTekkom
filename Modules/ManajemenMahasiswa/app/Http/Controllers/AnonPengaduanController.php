<?php

namespace Modules\ManajemenMahasiswa\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Modules\ManajemenMahasiswa\Http\Requests\PengaduanPayloadRequest;
use Modules\ManajemenMahasiswa\Models\Pengaduan;
use Modules\ManajemenMahasiswa\Models\PengaduanLog;
use Modules\ManajemenMahasiswa\Support\PengaduanBukti;
use Modules\ManajemenMahasiswa\Support\PengaduanQuota;

class AnonPengaduanController extends Controller
{
    /** Draft konfidensial yang tak pernah dikirim dibuang setelah sekian hari. */
    private const DRAFT_TTL_DAYS = 7;

    /** Session: token draft milik sesi ini, agar klik ulang memakai tautan yang sama. */
    private const SESSION_DRAFT = 'mm_pengaduan_anon_draft';

    /**
     * Membuat tiket draft (Magic Link) ketika mahasiswa memilih jalur Konfidensial.
     *
     * Hanya mahasiswa, dan hanya lewat POST. Sebelumnya ini route GET tanpa guard
     * role sehingga (a) semua role yang login bisa membuat tiket dan (b) setiap
     * klik/refresh/prefetch menambah satu baris draft yang tidak muncul di UI
     * mana pun.
     *
     * Draft TIDAK menyimpan user_id (anonimitas sejati). Pemakaian ulang draft
     * karena itu tidak lagi lewat user_id, melainkan lewat session (ephemeral),
     * dan pembuatan draft baru dibatasi kuota harian per akun (PengaduanQuota,
     * penghitung di cache, bukan di tabel tiket).
     */
    public function generate(Request $request)
    {
        $user = $request->user();
        $this->ensureMahasiswa($user);

        $pengaduan = null;
        if ($token = $request->session()->get(self::SESSION_DRAFT)) {
            $pengaduan = Pengaduan::query()
                ->where('anon_token', $token)
                ->where('is_anonim', true)
                ->where('status', Pengaduan::STATUS_DRAFT)
                ->first();
        }

        if (!$pengaduan) {
            if (PengaduanQuota::exhausted($user->id)) {
                $message = PengaduanQuota::message($user->id);

                return $request->expectsJson()
                    ? response()->json(['message' => $message], 429)
                    : redirect()->route('manajemenmahasiswa.pengaduan.index')->with('error', $message);
            }

            // Bersih-bersih oportunistik: draft yatim (tanpa pemilik, tak pernah dikirim).
            Pengaduan::withTrashed()
                ->where('is_anonim', true)
                ->where('status', Pengaduan::STATUS_DRAFT)
                ->where('created_at', '<', now()->subDays(self::DRAFT_TTL_DAYS))
                ->forceDelete();

            $pengaduan = Pengaduan::create([
                'user_id' => null,
                'kategori' => Pengaduan::KATEGORI_LAINNYA, // Sementara, diganti saat submit
                'is_anonim' => true,
                'anon_token' => Str::random(32),
                'status' => Pengaduan::STATUS_DRAFT,
                'data_template' => [],
            ]);

            $request->session()->put(self::SESSION_DRAFT, $pengaduan->anon_token);
            PengaduanQuota::consume($user->id);
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

        if ($pengaduan->status === Pengaduan::STATUS_DRAFT) {
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

            $buktiPendingItems = $this->buktiPendingItems($token);

            return view('manajemenmahasiswa::pengaduan.anon.create', compact('pengaduan', 'token', 'kategoriList', 'dosenList', 'frekuensiList', 'buktiPendingItems'));
        }

        // Label resmi kategori, sama dengan yang tampil di daftar & detail staff.
        $kategoriLabel = data_get($kategoriList, Pengaduan::normalizeKategori((string) $pengaduan->kategori) . '.label');

        return view('manajemenmahasiswa::pengaduan.anon.track', compact('pengaduan', 'kategoriLabel'));
    }

    /**
     * Membuka satu bukti dukung tiket konfidensial. Hak akses = kepemilikan magic
     * link (sama seperti halaman track); draft tidak punya bukti yang boleh dibuka.
     */
    public function bukti(Request $request, $token, int $index)
    {
        $pengaduan = Pengaduan::where('anon_token', $token)
            ->where('status', '!=', Pengaduan::STATUS_DRAFT)
            ->firstOrFail();

        return PengaduanBukti::respond($pengaduan, $index);
    }

    /**
     * Konfirmasi form pengaduan anonim sebelum disubmit.
     */
    public function confirm(PengaduanPayloadRequest $request, $token)
    {
        $pengaduan = Pengaduan::where('anon_token', $token)
            ->where('status', Pengaduan::STATUS_DRAFT)
            ->firstOrFail();

        // Berkas baru menggantikan bukti pending; tanpa berkas baru, yang lama dipertahankan.
        if ($request->hasFile('bukti')) {
            PengaduanBukti::stage($request->file('bukti'), $this->buktiScope($token));
        }

        $request->flash();

        return view('manajemenmahasiswa::pengaduan.anon.confirm', [
            'pengaduan' => $pengaduan,
            'token' => $token,
            'payload' => [
                'kategori' => $request->validated('kategori'),
                'template' => $request->normalizedTemplate(),
                'bukti_items' => $this->buktiPendingItems($token),
            ],
        ]);
    }

    /**
     * Pratinjau bukti yang baru diunggah pada draft ini. Sumbernya session, jadi
     * hanya sesi yang mengunggahnya yang bisa membuka (pemegang tautan lain: 404).
     */
    public function buktiPending(Request $request, $token, int $index)
    {
        Pengaduan::where('anon_token', $token)
            ->where('status', Pengaduan::STATUS_DRAFT)
            ->firstOrFail();

        return PengaduanBukti::respondPending($this->buktiScope($token), $index);
    }

    /**
     * @return array<int, array{url:string,kind:string,size:int,label:string}> baris daftar tiap bukti pending
     */
    private function buktiPendingItems(string $token): array
    {
        return PengaduanBukti::toItems(
            PengaduanBukti::pending($this->buktiScope($token)),
            fn (int $i) => route('manajemenmahasiswa.pengaduan.anon.bukti.pending', ['token' => $token, 'index' => $i])
        );
    }

    private function buktiScope(string $token): string
    {
        return 'anon:' . $token;
    }

    /**
     * Submit form pengaduan anonim, mengubah status draft menjadi baru.
     */
    public function store(PengaduanPayloadRequest $request, $token)
    {
        $pengaduan = Pengaduan::where('anon_token', $token)
            ->where('status', Pengaduan::STATUS_DRAFT)
            ->firstOrFail();

        $template = $request->normalizedTemplate();

        $bukti = PengaduanBukti::commit($this->buktiScope($token));
        if ($bukti !== []) {
            $template['bukti'] = $bukti;
        }

        $pengaduan->update([
            'kategori' => $request->validated('kategori'),
            'data_template' => $template,
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
