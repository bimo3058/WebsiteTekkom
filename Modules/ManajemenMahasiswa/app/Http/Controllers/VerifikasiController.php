<?php

namespace Modules\ManajemenMahasiswa\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Services\SupabaseStorage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\ManajemenMahasiswa\Models\Kemahasiswaan;
use Modules\ManajemenMahasiswa\Models\RiwayatKegiatan;
use Modules\ManajemenMahasiswa\Models\Prestasi;
use Modules\ManajemenMahasiswa\Models\VerifikasiBukti;

class VerifikasiController extends Controller
{
    /**
     * Batas panjang teks pengajuan.
     *
     * Satu tempat, karena angka yang sama dipakai form mahasiswa, form admin di
     * Direktori, dan validasi keduanya. Sebelumnya form mahasiswa dibatasi 50
     * huruf sementara form admin 255 — nama lomba resmi yang ditolak di satu
     * pintu jadi diterima di pintu lain.
     */
    public const MAKS_NAMA  = 150;
    public const MAKS_PERAN = 60;

    /** Jumlah baris per halaman pada daftar milik mahasiswa. */
    private const PER_HALAMAN_MAHASISWA = 10;

    private const PESAN_UNGGAH_GAGAL = 'Berkas bukti gagal diunggah, jadi pengajuan belum tersimpan. Periksa koneksi Anda lalu coba lagi — bila berkasnya besar, kecilkan dulu ukurannya.';

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function hasRole(string ...$roles): bool
    {
        $userRoles = Auth::user()->roles->pluck('name')->toArray();
        foreach ($roles as $r) {
            if (\in_array($r, $userRoles)) {
                return true;
            }
        }
        return false;
    }

    private function isVerificator(): bool
    {
        return $this->hasRole('superadmin', 'admin', 'admin_kemahasiswaan', 'dpm');
    }

    /**
     * Pengawas (read-only): Ketua Departemen.
     * Boleh MELIHAT seluruh daftar verifikasi, tetapi TIDAK boleh menyetujui/menolak.
     *
     * GPM tidak termasuk. Penjaminan mutu bekerja pada angka agregat — itu sudah
     * disediakan dashboard analitik scope evaluasi mutu — bukan pada berkas
     * verifikasi milik mahasiswa per orang. Batas ini ditegakkan berlapis: route
     * verifikasi menolak GPM, sidebar tidak menampilkan menunya, dan pintasan
     * "Verifikasi →" pada dashboard analitik disembunyikan untuk scope GPM.
     */
    private function isPengawas(): bool
    {
        return $this->hasRole('ketua_departemen');
    }

    /**
     * Boleh membuka halaman Klaim Reward Prestasi.
     * Verifikator (kelola) + Ketua Departemen (read-only).
     */
    private function canAccessReward(): bool
    {
        return $this->isVerificator() || $this->isPengawas();
    }

    private function resolveLayout(): string
    {
        $user  = Auth::user();
        $roles = $user->roles->pluck('name')->toArray();

        if (\in_array('superadmin', $roles) || \in_array('admin', $roles) || \in_array('admin_kemahasiswaan', $roles) || \in_array('dpm', $roles)
            || \in_array('ketua_departemen', $roles)) {
            // Ketua Departemen melihat tabel monitoring read-only → gunakan shell admin
            return 'manajemenmahasiswa::layouts.admin';
        }


        // Semua jenis pengurus himpunan menggunakan layout admin
        $pengurus = ['pengurus_himpunan', 'ketua_himpunan', 'ketua_bidang', 'ketua_unit', 'staff_himpunan'];
        foreach ($pengurus as $role) {
            if (\in_array($role, $roles)) {
                return 'manajemenmahasiswa::layouts.admin';
            }
        }

        return 'manajemenmahasiswa::layouts.mahasiswa';
    }

    // -------------------------------------------------------------------------
    // Helper — Pencarian bebas pada daftar verifikasi
    //
    // Cakupan pencarian sengaja dibuat sama persis dengan kolom yang tampil di
    // tabel: apa pun yang admin baca pada sebuah baris harus bisa dipakai untuk
    // menemukan baris itu kembali. Menampilkan kolom yang tidak bisa dicari
    // membuat admin mengira datanya hilang.
    // -------------------------------------------------------------------------

    /**
     * Pola pencocokan untuk ILIKE.
     *
     * ILIKE, bukan LIKE: basis data ini PostgreSQL, dan di sana LIKE bersifat
     * case-sensitive — "surya" tidak akan menemukan "Surya Hari Putra".
     * % dan _ yang diketik admin di-escape agar diperlakukan sebagai teks biasa,
     * bukan wildcard yang menarik seluruh isi tabel.
     */
    private function likePattern(string $search): string
    {
        return '%' . str_replace(['%', '_'], ['\\%', '\\_'], $search) . '%';
    }

    /**
     * Tingkat prestasi yang cocok dengan kata kunci.
     *
     * Dicocokkan dari awal kata, bukan LIKE %...%, supaya "nasional" tidak ikut
     * menarik "internasional". Daftarnya tetap dan pendek, jadi pencocokan di
     * PHP lebih murah sekaligus lebih mudah ditebak hasilnya.
     *
     * @return list<string>
     */
    private function cocokkanTingkat(string $search): array
    {
        $kata = mb_strtolower(trim($search));

        if ($kata === '') {
            return [];
        }

        return array_values(array_filter(
            Prestasi::TINGKAT_LIST,
            fn (string $tingkat) => str_starts_with($tingkat, $kata)
        ));
    }

    /**
     * Cari riwayat kegiatan berdasarkan kolom yang tampil di tabel admin:
     * nama & NIM mahasiswa, nama kegiatan, dan peran.
     */
    private function applyRiwayatSearch(Builder $query, string $search): void
    {
        $like = $this->likePattern($search);

        $query->where(function ($q) use ($like) {
            $q->whereHas('student.user', function ($u) use ($like) {
                $u->where('name', 'ilike', $like);
            })
            ->orWhereHas('student', function ($s) use ($like) {
                $s->where('student_number', 'ilike', $like);
            })
            ->orWhere('nama_kegiatan_manual', 'ilike', $like)
            // Dua kolom peran: daftar admin hanya memuat entri manual, tetapi
            // keduanya ikut dicari agar sama dengan yang ditampilkan kolom Peran
            // (lihat RiwayatKegiatan::getPeranLabelAttribute).
            ->orWhere('peran_manual', 'ilike', $like)
            ->orWhere('peran', 'ilike', $like);
        });
    }

    /**
     * Cari prestasi berdasarkan kolom yang tampil di tabel Verifikasi Prestasi
     * dan Klaim Reward: nama & NIM mahasiswa, nama prestasi, dan tingkat.
     */
    private function applyPrestasiSearch(Builder $query, string $search): void
    {
        $like    = $this->likePattern($search);
        $tingkat = $this->cocokkanTingkat($search);

        $query->where(function ($q) use ($like, $tingkat) {
            // Closure dalam wajib: tanpa itu orWhere('nim') lepas dari kunci
            // relasi, sehingga exists() cocok untuk semua baris prestasi.
            $q->whereHas('kemahasiswaan', function ($k) use ($like) {
                $k->where(function ($w) use ($like) {
                    $w->where('nama', 'ilike', $like)
                      ->orWhere('nim', 'ilike', $like);
                });
            })
            ->orWhere('nama_prestasi', 'ilike', $like);

            if ($tingkat) {
                $q->orWhereIn('tingkat', $tingkat);
            }
        });
    }

    // -------------------------------------------------------------------------
    // Helper — Auto-provision student + kemahasiswaan record jika belum ada
    // Digunakan agar pengurus (ketua_unit, dll) bisa langsung submit tanpa
    // harus didaftarkan manual oleh admin terlebih dahulu.
    // -------------------------------------------------------------------------

    /**
     * Mahasiswa yang belum terdaftar di Direktori tidak boleh diberi NIM darurat.
     *
     * Auto-provision di bawah memakai NIM palsu "PENGURUS-<id>" — masuk akal untuk
     * pengurus/admin yang memang tidak punya baris kemahasiswaan, tetapi untuk
     * mahasiswa NIM palsu itu ikut tampil di tabel verifikasi admin dan mengotori
     * dropdown filter angkatan dengan tahun berjalan. Untuk mereka, pengajuan
     * ditahan sampai datanya dimasukkan admin.
     */
    private function belumTerdaftarSebagaiMahasiswa(): bool
    {
        return $this->hasRole('mahasiswa')
            && !Kemahasiswaan::where('user_id', Auth::id())->exists();
    }

    private function pesanBelumTerdaftar()
    {
        return redirect()
            ->back()
            ->with('error', 'Data mahasiswa Anda belum terdaftar di Direktori Mahasiswa, jadi pengajuan belum bisa dikirim. Hubungi admin kemahasiswaan untuk didaftarkan lebih dulu.');
    }

    private function ensureStudentRecord(\App\Models\User $user): Student
    {
        // Cari atau buat record mk_kemahasiswaan terlebih dahulu
        $mhs = Kemahasiswaan::firstOrCreate(
            ['user_id' => $user->id],
            [
                'nama'     => $user->name,
                'nim'      => 'PENGURUS-' . $user->id,
                'angkatan' => (int) date('Y'),
                'status'   => Kemahasiswaan::STATUS_AKTIF,
            ]
        );

        // Cari atau buat record students
        $student = Student::firstOrCreate(
            ['user_id' => $user->id],
            [
                'student_number' => $mhs->nim,
                'cohort_year'    => $mhs->angkatan ?? (int) date('Y'),
            ]
        );

        return $student;
    }

    // -------------------------------------------------------------------------
    // Index — Render view sesuai role
    // -------------------------------------------------------------------------

    public function index(Request $request)
    {
        $user = Auth::user();

        if ($this->isVerificator()) {
            return $this->adminIndex($request);
        }

        // GPM & Ketua Departemen — pengawas mutu: lihat seluruh daftar verifikasi
        // dalam mode read-only (tanpa tombol setujui/tolak/klaim reward).
        if ($this->isPengawas()) {
            return $this->adminIndex($request, readOnly: true);
        }

        return $this->mahasiswaIndex($request);
    }

    // -------------------------------------------------------------------------
    // Admin View — Dashboard verifikasi semua data
    // -------------------------------------------------------------------------

    private function adminIndex(Request $request, bool $readOnly = false)
    {
        $tab    = $request->get('tab', 'prestasi');
        $search = $request->get('search');
        $angkatan = $request->get('angkatan');

        // Filter status verifikasi — pilihannya dikunci ke isi dropdown. Nilai di
        // luar daftar dianggap "semua" agar URL yang diubah manual tidak berujung
        // tabel kosong tanpa penjelasan, sama seperti perlakuan filter tingkat.
        $status = $request->get('status', 'semua');
        if (!\in_array($status, ['pending', 'approved', 'rejected'], true)) {
            $status = 'semua';
        }

        // Filter tingkat — hanya berlaku pada tab Prestasi; riwayat kegiatan tidak
        // punya kolom tingkat. Nilai di luar daftar resmi dianggap "semua" supaya
        // URL yang diubah manual tidak menghasilkan tabel kosong tanpa penjelasan.
        $tingkat = $request->get('tingkat');
        if (!\in_array($tingkat, Prestasi::TINGKAT_LIST, true)) {
            $tingkat = 'semua';
        }

        // ── Riwayat Kegiatan (manual only) ──
        $riwayatQuery = RiwayatKegiatan::with(['student.user', 'kegiatan', 'verifiedBy', 'buktiFiles'])
            ->manualOnly();

        if ($status && $status !== 'semua') {
            $riwayatQuery->where('verification_status', $status);
        }

        if ($search) {
            $this->applyRiwayatSearch($riwayatQuery, $search);
        }

        if ($angkatan && $angkatan !== 'semua') {
            // Samakan sumber angkatan dengan dropdown (mk_kemahasiswaan.angkatan),
            // dipetakan ke student lewat user_id agar konsisten dengan tab prestasi.
            $userIds = Kemahasiswaan::where('angkatan', $angkatan)->pluck('user_id');
            $riwayatQuery->whereHas('student', function ($q) use ($userIds) {
                $q->whereIn('user_id', $userIds);
            });
        }

        $riwayatData = $riwayatQuery->orderByDesc('created_at')->paginate(15, ['*'], 'riwayat_page');

        // ── Prestasi ──
        $prestasiQuery = Prestasi::with(['kemahasiswaan.user', 'verifiedBy', 'claimedBy', 'reviewedBy', 'buktiFiles']);

        if ($status && $status !== 'semua') {
            $prestasiQuery->where('verification_status', $status);
        }

        if ($search) {
            $this->applyPrestasiSearch($prestasiQuery, $search);
        }

        if ($angkatan && $angkatan !== 'semua') {
            $prestasiQuery->whereHas('kemahasiswaan', function ($q) use ($angkatan) {
                $q->where('angkatan', $angkatan);
            });
        }

        if ($tingkat !== 'semua') {
            $prestasiQuery->where('tingkat', $tingkat);
        }

        $prestasiData = $prestasiQuery->orderByDesc('created_at')->paginate(15, ['*'], 'prestasi_page');

        // Counters
        $pendingRiwayat  = RiwayatKegiatan::manualOnly()->pending()->count();
        $pendingPrestasi = Prestasi::pending()->count();
        $pendingPrestasiReward = Prestasi::rewardDiajukan()->count(); // utk badge tombol "Klaim Reward"

        // ── Stat cards — global counts per tab (tidak terpengaruh filter search/angkatan) ──
        if ($tab === 'riwayat') {
            $adminStats = [
                'pending'  => RiwayatKegiatan::manualOnly()->pending()->count(),
                'approved' => RiwayatKegiatan::manualOnly()->where('verification_status', 'approved')->count(),
                'rejected' => RiwayatKegiatan::manualOnly()->where('verification_status', 'rejected')->count(),
            ];
        } else {
            $adminStats = [
                'pending'  => Prestasi::pending()->count(),
                'approved' => Prestasi::where('verification_status', 'approved')->count(),
                'rejected' => Prestasi::where('verification_status', 'rejected')->count(),
            ];
        }

        // Angkatan list for filter
        $angkatanList = Kemahasiswaan::select('angkatan')
            ->distinct()
            ->orderBy('angkatan', 'desc')
            ->pluck('angkatan');

        // Daftar tingkat diambil dari konstanta, bukan dari data: tingkat adalah
        // enum tetap, jadi pilihannya harus utuh dan urutannya konsisten meskipun
        // salah satu tingkat belum pernah dipakai.
        $tingkatList = Prestasi::TINGKAT_LIST;

        // Pengawas (GPM/Kadep) hanya melihat — sembunyikan tombol setujui/tolak.
        $canVerify = !$readOnly;
        // Tombol "Klaim Reward" tetap tampil untuk pengawas (akses halaman read-only).
        $canViewReward = $this->canAccessReward();

        return view('manajemenmahasiswa::verifikasi.admin', compact(
            'riwayatData',
            'prestasiData',
            'pendingRiwayat',
            'pendingPrestasi',
            'tab',
            'status',
            'search',
            'angkatan',
            'angkatanList',
            'tingkat',
            'tingkatList',
            'pendingPrestasiReward',
            'adminStats',
            'canVerify',
            'canViewReward',
        ))->with('layout', $this->resolveLayout());
    }

    // -------------------------------------------------------------------------
    // Admin View — Halaman khusus daftar Klaim Reward Prestasi (Request Bu Bellia / B.2)
    // -------------------------------------------------------------------------

    public function rewardIndex(Request $request)
    {
        $search   = $request->get('search');
        $angkatan = $request->get('angkatan');

        // Nilai di luar isi dropdown dikembalikan ke default 'menunggu' supaya
        // dropdown selalu punya opsi terpilih dan tabelnya tidak kosong misterius.
        $reward = $request->get('reward', 'menunggu');
        if (!\in_array($reward, ['semua', 'menunggu', 'disetujui', 'ditolak'], true)) {
            $reward = 'menunggu';
        }

        $rewardStatusMap = [
            'menunggu'  => Prestasi::CLAIM_DIAJUKAN,
            'disetujui' => Prestasi::CLAIM_DISETUJUI,
            'ditolak'   => Prestasi::CLAIM_DITOLAK,
        ];

        $rewardQuery = Prestasi::with(['kemahasiswaan.user', 'reviewedBy', 'buktiFiles']);

        if ($reward === 'semua') {
            // "Semua" tetap dibatasi ketiga status klaim — prestasi yang rewardnya
            // belum pernah diajukan bukan bagian dari antrean halaman ini.
            $rewardQuery->whereIn('claim_status', array_values($rewardStatusMap));
        } else {
            $rewardQuery->where('claim_status', $rewardStatusMap[$reward] ?? Prestasi::CLAIM_DIAJUKAN);
        }

        if ($search) {
            $this->applyPrestasiSearch($rewardQuery, $search);
        }

        if ($angkatan && $angkatan !== 'semua') {
            $rewardQuery->whereHas('kemahasiswaan', function ($q) use ($angkatan) {
                $q->where('angkatan', $angkatan);
            });
        }

        $rewardData = $rewardQuery->orderByDesc('claimed_at')->paginate(15)->withQueryString();

        $pendingPrestasiReward = Prestasi::rewardDiajukan()->count();

        // Hitungan status klaim untuk kartu statistik (selaras dgn Verifikasi Prestasi)
        $rewardStats = [
            'menunggu'  => $pendingPrestasiReward,
            'disetujui' => Prestasi::rewardDisetujui()->count(),
            'ditolak'   => Prestasi::rewardDitolak()->count(),
        ];

        $kuotaMap = $this->rewardKuotaMap(
            $rewardData->pluck('kemahasiswaan_id')->filter()->unique()->all()
        );

        $angkatanList = Kemahasiswaan::select('angkatan')
            ->distinct()
            ->orderBy('angkatan', 'desc')
            ->pluck('angkatan');

        // Pengawas mutu (GPM/Kadep) hanya melihat — sembunyikan tinjau/setujui/tolak/batalkan.
        $canReview = $this->isVerificator();

        return view('manajemenmahasiswa::verifikasi.reward', compact(
            'rewardData',
            'reward',
            'search',
            'angkatan',
            'angkatanList',
            'pendingPrestasiReward',
            'rewardStats',
            'kuotaMap',
            'canReview',
        ))->with('layout', $this->resolveLayout());
    }

    // -------------------------------------------------------------------------
    // Mahasiswa View — Data pengajuan milik sendiri
    // -------------------------------------------------------------------------

    private function mahasiswaIndex(Request $request)
    {
        $user = Auth::user();
        $tab  = $request->get('tab', 'prestasi');
        $student = Student::where('user_id', $user->id)->first();
        $mhs = Kemahasiswaan::where('user_id', $user->id)->first();

        // Dipisah per tab & dihalamankan: daftar milik satu mahasiswa bisa
        // panjang, dan sebelumnya seluruhnya dimuat sekaligus tanpa alat bantu.
        // Paginator kosong dibuat lewat query yang pasti tidak cocok, supaya
        // tampilan tetap punya objek paginator meski datanya belum ada.
        $riwayatData = ($student
                ? RiwayatKegiatan::with(['kegiatan', 'verifiedBy', 'buktiFiles'])
                    ->where('student_id', $student->id)
                    ->manualOnly()
                : RiwayatKegiatan::whereRaw('1 = 0'))
            ->orderByDesc('created_at')
            ->paginate(self::PER_HALAMAN_MAHASISWA, ['*'], 'riwayat_page')
            ->withQueryString();

        $prestasiData = ($mhs
                ? Prestasi::with(['verifiedBy', 'reviewedBy', 'buktiFiles'])
                    ->where('kemahasiswaan_id', $mhs->id)
                : Prestasi::whereRaw('1 = 0'))
            ->orderByDesc('created_at')
            ->paginate(self::PER_HALAMAN_MAHASISWA, ['*'], 'prestasi_page')
            ->withQueryString();

        // Rincian dulu, angkanya diturunkan dari situ — supaya "2/2" yang dibaca
        // mahasiswa selalu sama isinya dengan daftar yang menjelaskannya.
        $kuotaDipakai = $mhs
            ? $this->rewardKuotaDipakai($mhs->id)
            : [Prestasi::KUOTA_UMUM => [], Prestasi::KUOTA_INVENTION => []];

        $kuota = array_map('count', $kuotaDipakai);

        // Jatah yang sedang dipesan klaim yang masih menunggu. Ditampilkan
        // terpisah dari yang sudah disetujui — keduanya beda arti bagi mahasiswa
        // — tetapi dijumlahkan saat menentukan masih ada slot atau tidak.
        $kuotaMenungguDipakai = $mhs
            ? $this->rewardKuotaMenungguDaftar($mhs->id)
            : [Prestasi::KUOTA_UMUM => [], Prestasi::KUOTA_INVENTION => []];

        $kuotaMenunggu = array_map('count', $kuotaMenungguDipakai);

        $kuotaTerpakai = [];
        foreach (Prestasi::KUOTA_MAKS as $grup => $maks) {
            $kuotaTerpakai[$grup] = ($kuota[$grup] ?? 0) + ($kuotaMenunggu[$grup] ?? 0);
        }

        $isAlumni = $this->hasRole('alumni');

        return view('manajemenmahasiswa::verifikasi.mahasiswa', compact(
            'riwayatData',
            'prestasiData',
            'kuota',
            'kuotaDipakai',
            'kuotaMenunggu',
            'kuotaMenungguDipakai',
            'kuotaTerpakai',
            'tab',
            'isAlumni',
        ))->with('layout', $this->resolveLayout());
    }

    // -------------------------------------------------------------------------
    // Store Riwayat — Mahasiswa/Pengurus/Alumni ajukan riwayat kegiatan
    // -------------------------------------------------------------------------

    public function storeRiwayat(Request $request)
    {
        if ($this->hasRole('alumni')) {
            return redirect()->back()->with('error', 'Role alumni tidak dapat mengajukan data baru.');
        }

        if ($this->belumTerdaftarSebagaiMahasiswa()) {
            return $this->pesanBelumTerdaftar();
        }

        $request->validate([
            'nama_kegiatan_manual' => 'required|string|max:' . self::MAKS_NAMA,
            'peran_manual'         => 'required|string|max:' . self::MAKS_PERAN,
            // Kegiatan yang belum terjadi tidak punya bukti — tanpa batas ini
            // tahun kegiatan bisa terisi masa depan dan ikut terbawa ke statistik.
            'tanggal_kegiatan'     => 'required|date|before_or_equal:today',
            'bukti_docs'           => 'required|array|size:1',
            'bukti_docs.*'         => 'file|mimes:pdf|max:10240',
        ], [
            'tanggal_kegiatan.before_or_equal' => 'Tanggal kegiatan tidak boleh melewati hari ini.',
        ], ['bukti_docs' => 'bukti kegiatan']);

        $user    = Auth::user();
        $student = $this->ensureStudentRecord($user);

        if ($this->riwayatDuplikat($student->id, $request->nama_kegiatan_manual, $request->tanggal_kegiatan)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Kegiatan dengan nama & tanggal yang sama sudah pernah Anda ajukan. Buka daftar di halaman ini untuk melihat statusnya.');
        }

        // Unggah dulu, simpan belakangan: pengajuan yang berkasnya gagal naik
        // tidak boleh tertinggal di antrean admin tanpa bukti apa pun.
        $berkas = $this->unggahBukti($request, VerifikasiBukti::TYPE_RIWAYAT);

        if ($berkas === null) {
            return redirect()->back()->withInput()->with('error', self::PESAN_UNGGAH_GAGAL);
        }

        $riwayat = RiwayatKegiatan::create([
            'student_id'           => $student->id,
            'kegiatan_id'          => null,
            'peran'                => null,
            'nama_kegiatan_manual' => $request->nama_kegiatan_manual,
            'peran_manual'         => $request->peran_manual,
            'tanggal_kegiatan'     => $request->tanggal_kegiatan,
            'verification_status'  => 'pending',
        ]);

        $this->simpanBukti(VerifikasiBukti::TYPE_RIWAYAT, $riwayat->id, $berkas);

        return redirect()
            ->route('manajemenmahasiswa.verifikasi.index', ['tab' => 'riwayat'])
            ->with('success', 'Riwayat kegiatan berhasil diajukan untuk verifikasi.');
    }

    // -------------------------------------------------------------------------
    // Store Prestasi — Mahasiswa/Pengurus/Alumni ajukan prestasi
    // -------------------------------------------------------------------------

    public function storePrestasi(Request $request)
    {
        if ($this->hasRole('alumni')) {
            return redirect()->back()->with('error', 'Role alumni tidak dapat mengajukan data baru.');
        }

        if ($this->belumTerdaftarSebagaiMahasiswa()) {
            return $this->pesanBelumTerdaftar();
        }

        $request->validate([
            'nama_prestasi' => 'required|string|max:' . self::MAKS_NAMA,
            'tingkat'       => 'required|in:' . implode(',', Prestasi::TINGKAT_LIST),
            // Prestasi yang belum diraih tidak punya sertifikat — lihat catatan
            // yang sama pada tanggal kegiatan.
            'tanggal'       => 'required|date|before_or_equal:today',
            'bukti_docs'    => 'required|array|size:1',
            'bukti_docs.*'  => 'file|mimes:pdf|max:10240',
        ], [
            'tanggal.before_or_equal' => 'Tanggal prestasi tidak boleh melewati hari ini.',
        ], ['bukti_docs' => 'bukti kegiatan']);

        $user = Auth::user();

        // Auto-provision mk_kemahasiswaan jika belum ada (untuk pengurus seperti ketua_unit)
        $mhs = Kemahasiswaan::firstOrCreate(
            ['user_id' => $user->id],
            [
                'nama'     => $user->name,
                'nim'      => 'PENGURUS-' . $user->id,
                'angkatan' => (int) date('Y'),
                'status'   => Kemahasiswaan::STATUS_AKTIF,
            ]
        );

        if ($this->prestasiDuplikat($mhs->id, $request->nama_prestasi, $request->tanggal)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Prestasi dengan nama & tanggal yang sama sudah pernah Anda ajukan. Buka daftar di halaman ini untuk melihat statusnya.');
        }

        // Unggah dulu, simpan belakangan — lihat catatan pada storeRiwayat().
        $berkas = $this->unggahBukti($request, VerifikasiBukti::TYPE_PRESTASI);

        if ($berkas === null) {
            return redirect()->back()->withInput()->with('error', self::PESAN_UNGGAH_GAGAL);
        }

        $prestasi = Prestasi::create([
            'kemahasiswaan_id'    => $mhs->id,
            'nama_prestasi'       => $request->nama_prestasi,
            'tingkat'             => $request->tingkat,
            'tanggal'             => $request->tanggal,
            'tahun'               => date('Y', strtotime($request->tanggal)),
            'verification_status' => 'pending',
            'claim_status'        => Prestasi::CLAIM_BELUM_AJUKAN,
        ]);

        $this->simpanBukti(VerifikasiBukti::TYPE_PRESTASI, $prestasi->id, $berkas);

        return redirect()
            ->route('manajemenmahasiswa.verifikasi.index', ['tab' => 'prestasi'])
            ->with('success', 'Prestasi berhasil diajukan untuk verifikasi.');
    }

    // -------------------------------------------------------------------------
    // Tarik Pengajuan — pemilik menghapus pengajuannya sendiri selama pending
    //
    // Sebelum ini satu-satunya cara membetulkan salah ketik atau salah berkas
    // adalah menunggu admin menolaknya lebih dulu.
    // -------------------------------------------------------------------------

    public function destroyRiwayat(int $id)
    {
        $riwayat = RiwayatKegiatan::with('student')->findOrFail($id);

        if (!$this->ownsRiwayat($riwayat)) {
            abort(403, 'Anda hanya dapat menarik pengajuan milik sendiri.');
        }

        if ($riwayat->verification_status !== RiwayatKegiatan::VERIF_PENDING) {
            return redirect()
                ->route('manajemenmahasiswa.verifikasi.index', ['tab' => 'riwayat'])
                ->with('error', 'Pengajuan yang sudah diverifikasi tidak dapat ditarik.');
        }

        $this->hapusBukti(VerifikasiBukti::TYPE_RIWAYAT, $riwayat->id);
        $riwayat->delete();

        return redirect()
            ->route('manajemenmahasiswa.verifikasi.index', ['tab' => 'riwayat'])
            ->with('success', 'Pengajuan riwayat kegiatan berhasil ditarik. Anda bisa mengajukannya kembali.');
    }

    public function destroyPrestasi(int $id)
    {
        $prestasi = Prestasi::with('kemahasiswaan')->findOrFail($id);

        if (!$this->ownsPrestasi($prestasi)) {
            abort(403, 'Anda hanya dapat menarik pengajuan milik sendiri.');
        }

        if ($prestasi->verification_status !== Prestasi::VERIF_PENDING) {
            return redirect()
                ->route('manajemenmahasiswa.verifikasi.index', ['tab' => 'prestasi'])
                ->with('error', 'Pengajuan yang sudah diverifikasi tidak dapat ditarik.');
        }

        $this->hapusBukti(VerifikasiBukti::TYPE_PRESTASI, $prestasi->id);
        $prestasi->delete();

        return redirect()
            ->route('manajemenmahasiswa.verifikasi.index', ['tab' => 'prestasi'])
            ->with('success', 'Pengajuan prestasi berhasil ditarik. Anda bisa mengajukannya kembali.');
    }

    // -------------------------------------------------------------------------
    // Approve Riwayat
    // -------------------------------------------------------------------------

    public function approveRiwayat(Request $request, int $id)
    {
        $request->validate([
            'verification_note' => 'nullable|string|max:200',
        ]);

        $riwayat = RiwayatKegiatan::with('student')->findOrFail($id);

        // Guard: hanya pengajuan yang masih menunggu yang dapat diverifikasi
        if ($riwayat->verification_status !== 'pending') {
            return redirect()
                ->route('manajemenmahasiswa.verifikasi.index', ['tab' => 'riwayat'])
                ->with('error', 'Riwayat kegiatan ini sudah pernah diverifikasi sebelumnya.');
        }

        // Guard: verifikator tidak boleh memverifikasi pengajuannya sendiri
        if ($this->ownsRiwayat($riwayat)) {
            return redirect()
                ->route('manajemenmahasiswa.verifikasi.index', ['tab' => 'riwayat'])
                ->with('error', 'Anda tidak dapat memverifikasi riwayat kegiatan yang Anda ajukan sendiri.');
        }

        $riwayat->update([
            'verification_status' => 'approved',
            'verified_by'         => Auth::id(),
            'verified_at'         => now(),
            'verification_note'   => $request->verification_note ?: null,
        ]);

        return redirect()
            ->route('manajemenmahasiswa.verifikasi.index', ['tab' => 'riwayat'])
            ->with('success', 'Riwayat kegiatan berhasil disetujui.');
    }

    // -------------------------------------------------------------------------
    // Reject Riwayat
    // -------------------------------------------------------------------------

    public function rejectRiwayat(Request $request, int $id)
    {
        $request->validate([
            'verification_note' => 'required|string|max:200',
        ]);

        $riwayat = RiwayatKegiatan::with('student')->findOrFail($id);

        // Guard: hanya pengajuan yang masih menunggu yang dapat diverifikasi
        if ($riwayat->verification_status !== 'pending') {
            return redirect()
                ->route('manajemenmahasiswa.verifikasi.index', ['tab' => 'riwayat'])
                ->with('error', 'Riwayat kegiatan ini sudah pernah diverifikasi sebelumnya.');
        }

        // Guard: verifikator tidak boleh memverifikasi pengajuannya sendiri
        if ($this->ownsRiwayat($riwayat)) {
            return redirect()
                ->route('manajemenmahasiswa.verifikasi.index', ['tab' => 'riwayat'])
                ->with('error', 'Anda tidak dapat memverifikasi riwayat kegiatan yang Anda ajukan sendiri.');
        }

        $riwayat->update([
            'verification_status' => 'rejected',
            'verified_by'         => Auth::id(),
            'verified_at'         => now(),
            'verification_note'   => $request->verification_note,
        ]);

        return redirect()
            ->route('manajemenmahasiswa.verifikasi.index', ['tab' => 'riwayat'])
            ->with('success', 'Riwayat kegiatan telah ditolak.');
    }

    // -------------------------------------------------------------------------
    // Approve Prestasi
    // -------------------------------------------------------------------------

    public function approvePrestasi(Request $request, int $id)
    {
        $request->validate([
            'verification_note' => 'nullable|string|max:200',
        ]);

        $prestasi = Prestasi::with('kemahasiswaan')->findOrFail($id);

        // Guard: hanya pengajuan yang masih menunggu yang dapat diverifikasi
        if ($prestasi->verification_status !== Prestasi::VERIF_PENDING) {
            return redirect()
                ->route('manajemenmahasiswa.verifikasi.index', ['tab' => 'prestasi'])
                ->with('error', 'Prestasi ini sudah pernah diverifikasi sebelumnya.');
        }

        // Guard: verifikator tidak boleh memverifikasi prestasinya sendiri
        if ($this->ownsPrestasi($prestasi)) {
            return redirect()
                ->route('manajemenmahasiswa.verifikasi.index', ['tab' => 'prestasi'])
                ->with('error', 'Anda tidak dapat memverifikasi prestasi yang Anda ajukan sendiri.');
        }

        $prestasi->update([
            'verification_status' => 'approved',
            'verified_by'         => Auth::id(),
            'verified_at'         => now(),
            'verification_note'   => $request->verification_note ?: null,
        ]);

        return redirect()
            ->route('manajemenmahasiswa.verifikasi.index', ['tab' => 'prestasi'])
            ->with('success', 'Prestasi berhasil disetujui.');
    }

    // -------------------------------------------------------------------------
    // Reject Prestasi
    // -------------------------------------------------------------------------

    public function rejectPrestasi(Request $request, int $id)
    {
        $request->validate([
            'verification_note' => 'required|string|max:200',
        ]);

        $prestasi = Prestasi::with('kemahasiswaan')->findOrFail($id);

        // Guard: hanya pengajuan yang masih menunggu yang dapat diverifikasi
        if ($prestasi->verification_status !== Prestasi::VERIF_PENDING) {
            return redirect()
                ->route('manajemenmahasiswa.verifikasi.index', ['tab' => 'prestasi'])
                ->with('error', 'Prestasi ini sudah pernah diverifikasi sebelumnya.');
        }

        // Guard: verifikator tidak boleh memverifikasi prestasinya sendiri
        if ($this->ownsPrestasi($prestasi)) {
            return redirect()
                ->route('manajemenmahasiswa.verifikasi.index', ['tab' => 'prestasi'])
                ->with('error', 'Anda tidak dapat memverifikasi prestasi yang Anda ajukan sendiri.');
        }

        $prestasi->update([
            'verification_status' => 'rejected',
            'verified_by'         => Auth::id(),
            'verified_at'         => now(),
            'verification_note'   => $request->verification_note,
        ]);

        return redirect()
            ->route('manajemenmahasiswa.verifikasi.index', ['tab' => 'prestasi'])
            ->with('success', 'Prestasi telah ditolak.');
    }

    // -------------------------------------------------------------------------
    // Batalkan Verifikasi — kembalikan keputusan ke "menunggu"
    //
    // Persetujuan/penolakan sebelumnya bersifat final: satu klik keliru tidak
    // punya jalan pulang, termasuk untuk pengajuan yang ternyata disetujui tanpa
    // berkas bukti. Keputusan dikosongkan seluruhnya (verifikator, waktu, catatan)
    // supaya barisnya kembali seperti belum pernah ditinjau.
    // -------------------------------------------------------------------------

    public function batalkanVerifikasiRiwayat(int $id)
    {
        $riwayat = RiwayatKegiatan::findOrFail($id);

        if ($riwayat->verification_status === RiwayatKegiatan::VERIF_PENDING) {
            return redirect()
                ->route('manajemenmahasiswa.verifikasi.index', ['tab' => 'riwayat'])
                ->with('error', 'Pengajuan ini memang masih menunggu verifikasi.');
        }

        $riwayat->update([
            'verification_status' => RiwayatKegiatan::VERIF_PENDING,
            'verified_by'         => null,
            'verified_at'         => null,
            'verification_note'   => null,
        ]);

        return redirect()
            ->route('manajemenmahasiswa.verifikasi.index', ['tab' => 'riwayat'])
            ->with('success', 'Verifikasi dibatalkan. Pengajuan kembali ke daftar menunggu.');
    }

    public function batalkanVerifikasiPrestasi(int $id)
    {
        $prestasi = Prestasi::findOrFail($id);

        if ($prestasi->verification_status === Prestasi::VERIF_PENDING) {
            return redirect()
                ->route('manajemenmahasiswa.verifikasi.index', ['tab' => 'prestasi'])
                ->with('error', 'Pengajuan ini memang masih menunggu verifikasi.');
        }

        // Reward hanya boleh lahir dari prestasi yang sudah disetujui. Kalau
        // rewardnya sudah berjalan, urutannya dibalik dulu dari halaman Klaim
        // Reward — kalau tidak, akan ada klaim yang menggantung pada prestasi
        // yang statusnya kembali menunggu.
        if ($prestasi->reward_status !== Prestasi::CLAIM_BELUM_AJUKAN) {
            return redirect()
                ->route('manajemenmahasiswa.verifikasi.index', ['tab' => 'prestasi'])
                ->with('error', 'Prestasi ini sudah punya klaim reward. Batalkan klaim rewardnya lebih dulu di halaman Klaim Reward.');
        }

        $prestasi->update([
            'verification_status' => Prestasi::VERIF_PENDING,
            'verified_by'         => null,
            'verified_at'         => null,
            'verification_note'   => null,
        ]);

        return redirect()
            ->route('manajemenmahasiswa.verifikasi.index', ['tab' => 'prestasi'])
            ->with('success', 'Verifikasi dibatalkan. Pengajuan kembali ke daftar menunggu.');
    }

    // -------------------------------------------------------------------------
    // Ajukan Reward — Mahasiswa pemilik mengajukan reward prestasinya
    // (Request Bu Bellia / B.2 — dasar SK FT 774/2025)
    // -------------------------------------------------------------------------

    public function ajukanReward(Request $request, int $id)
    {
        $prestasi = Prestasi::with('kemahasiswaan')->findOrFail($id);

        // Hanya pemilik prestasi yang boleh mengajukan
        if (!$this->ownsPrestasi($prestasi)) {
            abort(403, 'Anda hanya dapat mengajukan reward untuk prestasi milik sendiri.');
        }

        // Guard 1: hanya prestasi yang sudah diverifikasi (approved)
        if ($prestasi->verification_status !== Prestasi::VERIF_APPROVED) {
            return redirect()
                ->route('manajemenmahasiswa.verifikasi.index', ['tab' => 'prestasi'])
                ->with('error', 'Prestasi harus diverifikasi (disetujui) dulu sebelum bisa diajukan reward.');
        }

        // Guard 2: belum ada pengajuan aktif (boleh ajukan ulang bila sebelumnya ditolak)
        if (!$prestasi->rewardBisaDiajukan()) {
            return redirect()
                ->route('manajemenmahasiswa.verifikasi.index', ['tab' => 'prestasi'])
                ->with('error', 'Reward prestasi ini sudah diajukan atau sudah disetujui.');
        }

        $validated = $request->validate([
            'reward_penyelenggara' => 'required|in:' . implode(',', Prestasi::PENYELENGGARA_LIST),
            'reward_capaian'       => 'required|string',
            'reward_is_invention'  => 'nullable|boolean',
        ]);

        $penyelenggara = $validated['reward_penyelenggara'];
        $capaian       = $validated['reward_capaian'];
        // is_invention hanya relevan untuk kategori "lainnya"
        $isInvention   = $penyelenggara === Prestasi::PENYELENGGARA_LAINNYA
            ? $request->boolean('reward_is_invention')
            : false;

        // Validasi capaian harus valid untuk penyelenggara yang dipilih
        if (!\in_array($capaian, Prestasi::CAPAIAN_BY_PENYELENGGARA[$penyelenggara], true)) {
            return redirect()
                ->route('manajemenmahasiswa.verifikasi.index', ['tab' => 'prestasi'])
                ->with('error', 'Kombinasi penyelenggara dan capaian tidak valid.');
        }

        // Guard 3: kuota grup ini sudah penuh (SK 774 poin 4 & 5).
        //
        // Klaim yang masih menunggu ikut dihitung memakai jatah. Tanpa itu kuota
        // hanya menahan di meja admin: mahasiswa yang punya 1 klaim disetujui
        // masih membaca "1/2" dan bisa mengantrekan berapa pun klaim baru, yang
        // semuanya pasti tertahan saat ditinjau satu per satu.
        $grup = Prestasi::tentukanKuotaGrup($penyelenggara, $isInvention);
        $maks = Prestasi::KUOTA_MAKS[$grup];

        $disetujui = $this->rewardKuotaTerpakai($prestasi->kemahasiswaan_id)[$grup] ?? 0;
        $menunggu  = $this->rewardKuotaMenunggu($prestasi->kemahasiswaan_id)[$grup] ?? 0;

        if (($disetujui + $menunggu) >= $maks) {
            $labelGrup = Prestasi::KUOTA_LABELS[$grup];
            $alasan    = $menunggu > 0
                ? "sudah terpakai atau sedang dipesan pengajuan yang menunggu ({$disetujui} disetujui + {$menunggu} menunggu dari maks {$maks}×)"
                : "sudah penuh (maks {$maks}×)";

            return redirect()
                ->route('manajemenmahasiswa.verifikasi.index', ['tab' => 'prestasi'])
                ->with('error', "Kuota reward {$labelGrup} Anda {$alasan}. Batalkan salah satu pengajuan yang masih menunggu, atau buka \"Rincian kuota\" pada keterangan di atas tabel untuk melihat prestasi mana yang memakainya.");
        }

        $jatah = Prestasi::hitungJatahReward($penyelenggara, $capaian, $isInvention);

        // Usulan MK: hanya MK kurikulum yang valid, jumlah maks sesuai jatah (SK 774)
        $mkFlat     = Prestasi::mataKuliahFlat();
        $mkValid    = array_keys($mkFlat);
        $mkInput    = (array) $request->input('reward_mk_diajukan', []);
        $mkDiajukan = array_values(array_unique(array_filter(
            $mkInput,
            fn ($mk) => \in_array($mk, $mkValid, true)
        )));

        if (empty($mkDiajukan)) {
            return redirect()
                ->route('manajemenmahasiswa.verifikasi.index', ['tab' => 'prestasi'])
                ->with('error', 'Pilih minimal satu mata kuliah yang ingin dinaikkan nilainya sebelum mengajukan reward.');
        }

        if (count($mkDiajukan) > $jatah['jml_mk_max']) {
            return redirect()
                ->route('manajemenmahasiswa.verifikasi.index', ['tab' => 'prestasi'])
                ->with('error', "Maksimal {$jatah['jml_mk_max']} mata kuliah untuk capaian ini.");
        }

        // Total SKS usulan tidak boleh melebihi plafon SKS jatah (SK 774)
        $totalSks = array_sum(array_map(fn ($mk) => $mkFlat[$mk] ?? 0, $mkDiajukan));
        if ($totalSks > $jatah['sks_max']) {
            return redirect()
                ->route('manajemenmahasiswa.verifikasi.index', ['tab' => 'prestasi'])
                ->with('error', "Total SKS mata kuliah yang dipilih ({$totalSks} SKS) melebihi batas {$jatah['sks_max']} SKS untuk capaian ini.");
        }

        $prestasi->update([
            'reward_penyelenggara' => $penyelenggara,
            'reward_capaian'       => $capaian,
            'reward_is_invention'  => $isInvention,
            // Dasar aturan dibekukan di sini, satu momen dengan jatah MK/SKS,
            // supaya satu klaim selalu tunduk pada satu SK secara utuh —
            // termasuk bila SK berganti sementara klaim ini masih menunggu.
            'reward_kuota_grup'    => Prestasi::tentukanKuotaGrup($penyelenggara, $isInvention),
            'reward_sk_ref'        => Prestasi::SK_BERLAKU,
            'reward_jml_mk_max'    => $jatah['jml_mk_max'],
            'reward_sks_max'       => $jatah['sks_max'],
            'reward_mk_diajukan'   => $mkDiajukan,
            'claim_status'         => Prestasi::CLAIM_DIAJUKAN,
            'claimed_by'           => Auth::id(),
            'claimed_at'           => now(),
            // bersihkan jejak review lama jika ini pengajuan ulang setelah ditolak
            'reward_mk_disetujui'  => null,
            'reward_reviewed_by'   => null,
            'reward_reviewed_at'   => null,
            'reward_note'          => null,
        ]);

        return redirect()
            ->route('manajemenmahasiswa.verifikasi.index', ['tab' => 'prestasi'])
            ->with('success', 'Pengajuan reward berhasil dikirim. Menunggu persetujuan departemen.');
    }

    // -------------------------------------------------------------------------
    // Batalkan Reward — Mahasiswa pemilik membatalkan pengajuan (selama diajukan)
    // -------------------------------------------------------------------------

    public function batalkanReward(int $id)
    {
        $prestasi = Prestasi::with('kemahasiswaan')->findOrFail($id);

        if (!$this->ownsPrestasi($prestasi)) {
            abort(403, 'Anda hanya dapat membatalkan pengajuan reward milik sendiri.');
        }

        if (!$prestasi->isRewardDiajukan()) {
            return redirect()
                ->route('manajemenmahasiswa.verifikasi.index', ['tab' => 'prestasi'])
                ->with('error', 'Hanya pengajuan yang masih menunggu persetujuan yang dapat dibatalkan.');
        }

        $prestasi->update([
            'claim_status'         => Prestasi::CLAIM_BELUM_AJUKAN,
            'claimed_by'           => null,
            'claimed_at'           => null,
            'reward_penyelenggara' => null,
            'reward_capaian'       => null,
            'reward_is_invention'  => false,
            // Cap aturan ikut dilepas: klaim yang sudah dibatalkan tidak boleh
            // menyandang kelompok kuota & nomor SK milik pengajuan yang tak ada.
            'reward_kuota_grup'    => null,
            'reward_sk_ref'        => null,
            'reward_jml_mk_max'    => null,
            'reward_sks_max'       => null,
            'reward_mk_diajukan'   => null,
        ]);

        return redirect()
            ->route('manajemenmahasiswa.verifikasi.index', ['tab' => 'prestasi'])
            ->with('success', 'Pengajuan reward berhasil dibatalkan.');
    }

    // -------------------------------------------------------------------------
    // Setujui Reward — Admin/Departemen menyetujui & menetapkan MK
    // -------------------------------------------------------------------------

    public function setujuiReward(Request $request, int $id)
    {
        $prestasi = Prestasi::with('kemahasiswaan')->findOrFail($id);

        if (!$prestasi->isRewardDiajukan()) {
            return redirect()
                ->route('manajemenmahasiswa.verifikasi.reward.index')
                ->with('error', 'Hanya pengajuan yang menunggu persetujuan yang dapat disetujui.');
        }

        if ($tolak = $this->tolakTinjauKlaimSendiri($prestasi)) {
            return $tolak;
        }

        $request->validate([
            'reward_note' => 'nullable|string|max:300',
        ]);

        // MK final = usulan mahasiswa (admin hanya melihat, tidak mengubah)
        $mkFinal = implode(', ', $prestasi->reward_mk_diajukan ?? []);
        if ($mkFinal === '') {
            return redirect()
                ->route('manajemenmahasiswa.verifikasi.reward.index')
                ->with('error', 'Pengajuan ini belum memuat usulan mata kuliah dari mahasiswa, tidak dapat disetujui.');
        }

        // Guard kuota keras (SK 774 poin 4 = umum maks 2x, poin 5 = invention maks 1x)
        $grup     = $prestasi->rewardKuotaGrup();
        $maks     = Prestasi::KUOTA_MAKS[$grup];
        $terpakai = $this->rewardKuotaTerpakai($prestasi->kemahasiswaan_id);

        if (($terpakai[$grup] ?? 0) >= $maks) {
            $labelGrup = Prestasi::KUOTA_LABELS[$grup];
            return redirect()
                ->route('manajemenmahasiswa.verifikasi.reward.index')
                ->with('error', "Kuota reward {$labelGrup} mahasiswa ini sudah penuh (maks {$maks}×). Pengajuan tidak dapat disetujui.");
        }

        $prestasi->update([
            'claim_status'        => Prestasi::CLAIM_DISETUJUI,
            'reward_mk_disetujui' => $mkFinal,
            'reward_note'         => $request->reward_note ?: null,
            'reward_reviewed_by'  => Auth::id(),
            'reward_reviewed_at'  => now(),
        ]);

        return redirect()
            ->route('manajemenmahasiswa.verifikasi.reward.index')
            ->with('success', 'Pengajuan reward disetujui.');
    }

    // -------------------------------------------------------------------------
    // Tolak Reward — Admin/Departemen menolak pengajuan dengan alasan
    // -------------------------------------------------------------------------

    public function tolakReward(Request $request, int $id)
    {
        $prestasi = Prestasi::with('kemahasiswaan')->findOrFail($id);

        if (!$prestasi->isRewardDiajukan()) {
            return redirect()
                ->route('manajemenmahasiswa.verifikasi.reward.index')
                ->with('error', 'Hanya pengajuan yang menunggu persetujuan yang dapat ditolak.');
        }

        if ($tolak = $this->tolakTinjauKlaimSendiri($prestasi)) {
            return $tolak;
        }

        $request->validate([
            'reward_note' => 'required|string|max:300',
        ]);

        $prestasi->update([
            'claim_status'       => Prestasi::CLAIM_DITOLAK,
            'reward_note'        => $request->reward_note,
            'reward_reviewed_by' => Auth::id(),
            'reward_reviewed_at' => now(),
        ]);

        return redirect()
            ->route('manajemenmahasiswa.verifikasi.reward.index')
            ->with('success', 'Pengajuan reward ditolak.');
    }

    // -------------------------------------------------------------------------
    // Batalkan Persetujuan Reward — admin membatalkan reward yg sudah disetujui
    // (mis. Fakultas menolak menaikkan nilai). Jadi DITOLAK + alasan, kuota balik.
    // -------------------------------------------------------------------------

    public function batalkanPersetujuanReward(Request $request, int $id)
    {
        $prestasi = Prestasi::with('kemahasiswaan')->findOrFail($id);

        if (!$prestasi->isRewardDisetujui()) {
            return redirect()
                ->route('manajemenmahasiswa.verifikasi.reward.index', ['reward' => 'disetujui'])
                ->with('error', 'Hanya reward yang sudah disetujui yang dapat dibatalkan.');
        }

        if ($tolak = $this->tolakTinjauKlaimSendiri($prestasi)) {
            return $tolak;
        }

        $request->validate([
            'reward_note' => 'required|string|max:300',
        ]);

        $prestasi->update([
            'claim_status'       => Prestasi::CLAIM_DITOLAK,
            // MK yang pernah ditetapkan ikut dilepas: klaim yang batal tidak boleh
            // tetap memajang "MK disetujui" bersebelahan dengan lencana Ditolak.
            'reward_mk_disetujui' => null,
            'reward_note'        => $request->reward_note,
            'reward_reviewed_by' => Auth::id(),
            'reward_reviewed_at' => now(),
        ]);

        return redirect()
            ->route('manajemenmahasiswa.verifikasi.reward.index', ['reward' => 'ditolak'])
            ->with('success', 'Persetujuan reward dibatalkan. Status diubah menjadi ditolak & kuota mahasiswa dikembalikan.');
    }

    // -------------------------------------------------------------------------
    // Helper — Cek apakah prestasi milik user yang sedang login
    // -------------------------------------------------------------------------

    private function ownsPrestasi(Prestasi $prestasi): bool
    {
        return $prestasi->kemahasiswaan
            && $prestasi->kemahasiswaan->user_id === Auth::id();
    }

    // -------------------------------------------------------------------------
    // Helper — Cek apakah riwayat kegiatan milik user yang sedang login
    // -------------------------------------------------------------------------

    private function ownsRiwayat(RiwayatKegiatan $riwayat): bool
    {
        return $riwayat->student
            && $riwayat->student->user_id === Auth::id();
    }

    // -------------------------------------------------------------------------
    // Helper — Kuota reward terpakai (disetujui) untuk satu mahasiswa
    // -------------------------------------------------------------------------

    /**
     * Klaim yang memakan kuota, per grup — selalu kedua grup ada kuncinya.
     *
     * Dipakai halaman mahasiswa untuk merinci angka kuotanya sendiri. Sengaja
     * lewat rewardKuotaMap(): daftar & jumlahnya lahir dari baris yang sama,
     * jadi rincian yang dibaca mahasiswa tidak mungkin berbeda dari angka yang
     * dipakai guard saat admin menyetujui.
     */
    private function rewardKuotaDipakai(int $kemahasiswaanId): array
    {
        $map = $this->rewardKuotaMap([$kemahasiswaanId])[$kemahasiswaanId] ?? [];

        return [
            Prestasi::KUOTA_UMUM      => $map[Prestasi::KUOTA_UMUM] ?? [],
            Prestasi::KUOTA_INVENTION => $map[Prestasi::KUOTA_INVENTION] ?? [],
        ];
    }

    private function rewardKuotaTerpakai(int $kemahasiswaanId): array
    {
        return array_map('count', $this->rewardKuotaDipakai($kemahasiswaanId));
    }

    /**
     * Klaim yang sedang menunggu persetujuan, per grup — jatah yang sudah dipesan.
     *
     * Belum memakan kuota secara resmi, tetapi sudah mengunci slotnya: menyetujui
     * lebih banyak dari maks tidak mungkin, jadi pengajuan berikutnya ditahan di
     * depan alih-alih menumpuk di antrean admin sebagai pekerjaan yang pasti
     * berakhir ditolak.
     */
    private function rewardKuotaMenungguDaftar(int $kemahasiswaanId): array
    {
        $daftar = [Prestasi::KUOTA_UMUM => [], Prestasi::KUOTA_INVENTION => []];

        $rows = Prestasi::rewardDiajukan()
            ->where('kemahasiswaan_id', $kemahasiswaanId)
            ->orderBy('claimed_at')
            ->get(['id', 'nama_prestasi', 'claimed_at', 'reward_kuota_grup', 'reward_penyelenggara', 'reward_is_invention']);

        foreach ($rows as $r) {
            $daftar[$r->rewardKuotaGrup()][] = [
                'id'      => $r->id,
                'nama'    => $r->nama_prestasi,
                'tanggal' => $r->claimed_at?->translatedFormat('d M Y'),
            ];
        }

        return $daftar;
    }

    /** Sama seperti rewardKuotaTerpakai(): angkanya diturunkan dari daftarnya. */
    private function rewardKuotaMenunggu(int $kemahasiswaanId): array
    {
        return array_map('count', $this->rewardKuotaMenungguDaftar($kemahasiswaanId));
    }

    /**
     * Verifikator tidak boleh memutus klaim reward miliknya sendiri.
     *
     * Penjaga yang sama sudah ada pada verifikasi prestasi & riwayat; tanpa ini
     * seorang admin yang prestasinya disetujui rekannya bisa menyetujui sendiri
     * kenaikan nilai mata kuliahnya.
     */
    private function tolakTinjauKlaimSendiri(Prestasi $prestasi)
    {
        if (!$this->ownsPrestasi($prestasi)) {
            return null;
        }

        return redirect()
            ->route('manajemenmahasiswa.verifikasi.reward.index')
            ->with('error', 'Anda tidak dapat meninjau klaim reward yang Anda ajukan sendiri. Mintakan ke verifikator lain.');
    }

    // -------------------------------------------------------------------------
    // Helper — Peta kuota terpakai untuk banyak mahasiswa sekaligus (admin view)
    // -------------------------------------------------------------------------

    /**
     * Peta klaim yang sudah memakan kuota reward, per mahasiswa & per grup.
     *
     * Sengaja menyimpan barisnya, bukan hanya jumlahnya: modal Tinjau perlu
     * menunjukkan klaim mana yang memakai kuota, supaya angka "2/2" bisa
     * diperiksa dan admin tahu klaim mana yang harus dibatalkan bila keliru.
     * Jumlahnya tinggal count(). Tetap satu query untuk seluruh halaman.
     */
    private function rewardKuotaMap(array $kemahasiswaanIds): array
    {
        if (empty($kemahasiswaanIds)) {
            return [];
        }

        $rows = Prestasi::rewardDisetujui()
            ->whereIn('kemahasiswaan_id', $kemahasiswaanIds)
            ->orderBy('reward_reviewed_at')
            ->get([
                'id',
                'kemahasiswaan_id',
                'nama_prestasi',
                'reward_kuota_grup',
                'reward_penyelenggara',
                'reward_is_invention',
                'reward_mk_diajukan',
                'reward_mk_disetujui',
                'reward_reviewed_at',
            ]);

        $map = [];
        foreach ($rows as $r) {
            // Kelompok yang dibekukan saat pengajuan — bukan dihitung ulang,
            // supaya daftar ini tidak berubah isinya saat SK berganti.
            $grup = $r->rewardKuotaGrup();

            // MK final tetap teks di reward_mk_disetujui — itu yang diputus admin.
            // Bentuk daftarnya hanya dipakai bila keduanya memang identik: nama MK
            // bisa memuat koma ("Switching, Routing dan Jaringan Nirkabel"), jadi
            // teks itu tidak boleh dipecah sendiri di sisi tampilan.
            $mkList = $r->reward_mk_diajukan ?? [];
            if (implode(', ', $mkList) !== (string) $r->reward_mk_disetujui) {
                $mkList = [];
            }

            $map[$r->kemahasiswaan_id][$grup][] = [
                'id'      => $r->id,
                'nama'    => $r->nama_prestasi,
                'mk'      => $r->reward_mk_disetujui,
                'mk_list' => $mkList,
                'tanggal' => $r->reward_reviewed_at?->translatedFormat('d M Y'),
            ];
        }

        return $map;
    }


    // -------------------------------------------------------------------------
    // Helper — Berkas bukti
    // -------------------------------------------------------------------------

    /**
     * Unggah berkas bukti ke Supabase, sebelum baris pengajuan dibuat.
     *
     * Mengembalikan null bila ada satu saja berkas yang gagal naik; pemanggil
     * wajib membatalkan pengajuannya. Perilaku lama menelan kegagalan diam-diam:
     * pengajuan tetap tersimpan, mahasiswa tetap menerima notifikasi hijau, dan
     * barisnya sampai ke antrean admin tanpa berkas apa pun — beberapa di
     * antaranya bahkan sudah terlanjur disetujui tanpa bukti.
     *
     * Hanya melayani bukti_docs. Slot bukti_images lama ikut dihapus karena
     * aturan validasinya sudah tidak ada, sehingga jalur itu menerima berkas
     * jenis & ukuran apa pun.
     *
     * @return list<array{nama_file:string,path_file:string,tipe_file:string}>|null
     */
    private function unggahBukti(Request $request, string $type): ?array
    {
        $supabase = app(SupabaseStorage::class);
        $berkas   = [];

        foreach ((array) $request->file('bukti_docs', []) as $file) {
            $path = $supabase->upload($file, 'mk_verifikasi/' . $type . '/docs');

            if (!$path) {
                // Bersihkan berkas yang sempat naik agar tidak jadi sampah di bucket
                foreach ($berkas as $sudah) {
                    $supabase->delete($sudah['path_file']);
                }

                return null;
            }

            $berkas[] = [
                'nama_file' => $file->getClientOriginalName(),
                'path_file' => $path,
                'tipe_file' => VerifikasiBukti::TIPE_DOCUMENT,
            ];
        }

        return $berkas;
    }

    /**
     * @param list<array{nama_file:string,path_file:string,tipe_file:string}> $berkas
     */
    private function simpanBukti(string $type, int $parentId, array $berkas): void
    {
        foreach ($berkas as $b) {
            VerifikasiBukti::create($b + [
                'bukti_type' => $type,
                'bukti_id'   => $parentId,
            ]);
        }
    }

    /** Hapus berkas bukti sebuah pengajuan, dari basis data maupun dari bucket. */
    private function hapusBukti(string $type, int $parentId): void
    {
        $supabase = app(SupabaseStorage::class);

        $rows = VerifikasiBukti::where('bukti_type', $type)
            ->where('bukti_id', $parentId)
            ->get();

        foreach ($rows as $row) {
            $supabase->delete($row->path_file);
            $row->delete();
        }
    }

    // -------------------------------------------------------------------------
    // Helper — Deteksi pengajuan kembar
    //
    // Nama + tanggal yang sama dari orang yang sama hampir selalu berarti kiriman
    // ulang, bukan dua kegiatan berbeda. Yang sudah ditolak sengaja tidak
    // dihitung: mengajukan ulang setelah ditolak memang alurnya.
    // -------------------------------------------------------------------------

    private function riwayatDuplikat(int $studentId, string $nama, string $tanggal): bool
    {
        return RiwayatKegiatan::where('student_id', $studentId)
            ->manualOnly()
            ->whereIn('verification_status', [RiwayatKegiatan::VERIF_PENDING, RiwayatKegiatan::VERIF_APPROVED])
            ->whereRaw('lower(nama_kegiatan_manual) = ?', [mb_strtolower(trim($nama))])
            ->whereDate('tanggal_kegiatan', $tanggal)
            ->exists();
    }

    private function prestasiDuplikat(int $kemahasiswaanId, string $nama, string $tanggal): bool
    {
        return Prestasi::where('kemahasiswaan_id', $kemahasiswaanId)
            ->whereIn('verification_status', [Prestasi::VERIF_PENDING, Prestasi::VERIF_APPROVED])
            ->whereRaw('lower(nama_prestasi) = ?', [mb_strtolower(trim($nama))])
            ->whereDate('tanggal', $tanggal)
            ->exists();
    }
}
