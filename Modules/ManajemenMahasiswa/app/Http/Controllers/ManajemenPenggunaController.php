<?php

namespace Modules\ManajemenMahasiswa\Http\Controllers;

use App\Models\Role;
use App\Models\Student;
use Modules\ManajemenMahasiswa\Support\PerPage;
use App\Models\User;
use App\Services\AuditLogger;
use App\Services\PermissionAssigner;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\ManajemenMahasiswa\Models\Alumni;
use Modules\ManajemenMahasiswa\Models\Bidang;
use Modules\ManajemenMahasiswa\Models\Kemahasiswaan;
use Modules\ManajemenMahasiswa\Models\PengurusBidang;
use Modules\ManajemenMahasiswa\Support\RoleHimpunan;

class ManajemenPenggunaController extends Controller
{
    // Semua role posisi himpunan (tidak termasuk pengurus_himpunan yang bersifat umbrella)
    private const HIMPUNAN_POSITION_ROLES = RoleHimpunan::POSISI;

    // Role yang dapat diassign oleh masing-masing level caller.
    // KAHIM = WAKAHIM; KABID = WAKABID = KANIT = WAKANIT (terbatas bidang/unitnya).
    private const ASSIGNABLE_BY = [
        'admin_kemahasiswaan'  => [...RoleHimpunan::POSISI, 'alumni'],
        'admin'                => [...RoleHimpunan::POSISI, 'alumni'],
        'superadmin'           => [...RoleHimpunan::POSISI, 'alumni'],
        'ketua_himpunan'       => RoleHimpunan::BERBIDANG,
        'wakil_ketua_himpunan' => RoleHimpunan::BERBIDANG,
        'ketua_bidang'         => [RoleHimpunan::STAFF],
        'wakil_ketua_bidang'   => [RoleHimpunan::STAFF],
        'ketua_unit'           => [RoleHimpunan::STAFF],
        'wakil_ketua_unit'     => [RoleHimpunan::STAFF],
    ];

    // ── Halaman Utama ─────────────────────────────────────────────────────────

    /**
     * Halaman utama Permission: ringkasan pengguna per kategori.
     *
     * Daftar dapat dipersempit lewat pencarian dan filter angkatan. Angkatan
     * dibaca dari `students.cohort_year`, kolom yang sama dengan yang tampil di
     * kolom "Angkatan" pada tabel, supaya hasil filter selalu cocok dengan yang
     * terlihat.
     */
    public function index(Request $request)
    {
        $actor = auth()->user();
        $search = $request->input('search');
        $angkatan = $request->input('angkatan');

        $query = User::with(['roles', 'student', 'pengurusBidang.bidang'])
            ->whereNull('deleted_at')
            ->whereHas('roles', fn($q) => $q->whereIn('name', ['mahasiswa', 'alumni', 'pengurus_himpunan', ...self::HIMPUNAN_POSITION_ROLES]));

        if ($search) {
            $query->where(fn($q) => $q
                ->where('name', 'LIKE', "%{$search}%")
                ->orWhere('email', 'LIKE', "%{$search}%")
                ->orWhereHas('student', fn($sq) => $sq->where('student_number', 'LIKE', "%{$search}%"))
            );
        }

        $this->batasiKeBidangActor($query, $actor);

        $semuaUser = $query->orderBy('name')->get();

        // Pilihan angkatan disusun SEBELUM filter angkatan dipakai, jadi dropdown
        // tetap memuat seluruh pilihan meski daftarnya sedang disaring. Relasi
        // student sudah di-eager load di atas, jadi ini tidak memicu N+1.
        $angkatanList = $semuaUser
            ->pluck('student.cohort_year')
            ->filter()
            ->unique()
            ->sortDesc()
            ->values();

        $users = $angkatan
            ? $semuaUser->filter(fn($u) => (string) ($u->student->cohort_year ?? '') === (string) $angkatan)->values()
            : $semuaUser;

        $categories = [
            'Mahasiswa Aktif'   => $users->filter(fn($u) => $this->isMahasiswaAktif($u))->sortBy('name')->take(5),
            'Pengurus Himpunan' => $users->filter(fn($u) => $this->isPengurus($u))->sortBy('name')->take(5),
            'Alumni'            => $users->filter(fn($u) => $u->roles->pluck('name')->contains('alumni'))->sortBy('name')->take(5),
        ];

        // Role himpunan hanya mengelola Mahasiswa Aktif & Pengurus Himpunan.
        $categories = array_intersect_key($categories, array_flip($this->kategoriUntuk($actor)));

        $assignableRoles = $this->getAssignableRoles($actor);
        $isFiltered      = filled($search) || filled($angkatan);
        $bisaUbah        = fn (User $target) => $this->bisaUbah($actor, $target);

        return view('manajemenmahasiswa::permissions.index', compact(
            'users', 'categories', 'assignableRoles', 'search', 'angkatan', 'angkatanList', 'isFiltered', 'bisaUbah'
        ) + $this->dataFormBidang($actor));
    }

    // ── Halaman Kategori ──────────────────────────────────────────────────────

    /**
     * Halaman daftar lengkap satu kategori (Mahasiswa Aktif / Pengurus / Alumni).
     *
     * Selain pencarian, role, dan jumlah baris, daftar juga bisa disaring per
     * angkatan (`students.cohort_year`) — sumber yang sama dengan halaman utama
     * Permission maupun kolom "Angkatan" di tabel.
     */
    public function category(Request $request, string $category)
    {
        $actor    = auth()->user();
        $search   = $request->input('search');
        $perPage  = PerPage::resolve($request);
        $roleFilter = $request->input('role', 'all');
        $angkatan = $request->input('angkatan');

        $validCategories = ['Mahasiswa Aktif', 'Pengurus Himpunan', 'Alumni'];
        abort_unless(in_array($category, $validCategories), 404);
        // Kategori Alumni hanya untuk admin — role himpunan tidak mengelola alumni.
        abort_unless(in_array($category, $this->kategoriUntuk($actor)), 403, 'Anda tidak memiliki akses ke kategori ini.');

        $query = User::with(['roles', 'student', 'pengurusBidang.bidang'])
            ->whereNull('deleted_at');

        match ($category) {
            'Mahasiswa Aktif'   => $query->whereHas('roles', fn($q) => $q->where('name', 'mahasiswa'))
                                         ->whereDoesntHave('roles', fn($q) => $q->whereIn('name', ['pengurus_himpunan', ...self::HIMPUNAN_POSITION_ROLES])),
            'Pengurus Himpunan' => $query->whereHas('roles', fn($q) => $q->whereIn('name', ['pengurus_himpunan', ...self::HIMPUNAN_POSITION_ROLES])),
            'Alumni'            => $query->whereHas('roles', fn($q) => $q->where('name', 'alumni')),
        };

        if ($search) {
            $query->where(fn($q) => $q
                ->where('name', 'LIKE', "%{$search}%")
                ->orWhere('email', 'LIKE', "%{$search}%")
                ->orWhereHas('student', fn($sq) => $sq->where('student_number', 'LIKE', "%{$search}%"))
            );
        }

        if ($category === 'Pengurus Himpunan' && $roleFilter !== 'all') {
            $query->whereHas('roles', fn($q) => $q->where('name', $roleFilter));
        }

        // Filter bidang/unit (Pengurus Himpunan) — untuk KAHIM/WAKAHIM & admin.
        $bidangFilter = $request->input('bidang', 'all');
        if ($category === 'Pengurus Himpunan' && $bidangFilter !== 'all') {
            $query->whereHas('pengurusBidang', fn($q) => $q->where('bidang_id', (int) $bidangFilter));
        }

        $this->batasiKeBidangActor($query, $actor);

        // Opsi angkatan diambil dari cakupan yang sama TAPI sebelum filter angkatan
        // dipasang, supaya dropdown tidak menyusut jadi satu pilihan begitu dipakai.
        $angkatanList = Student::query()
            ->whereNotNull('cohort_year')
            ->whereIn('user_id', (clone $query)->select('users.id'))
            ->distinct()
            ->orderByDesc('cohort_year')
            ->pluck('cohort_year');

        if ($angkatan) {
            $query->whereHas('student', fn($q) => $q->where('cohort_year', $angkatan));
        }

        $users = $query->orderBy('name')->paginate($perPage)->withQueryString();
        $assignableRoles = $this->getAssignableRoles($actor);
        $isFiltered = filled($search) || filled($angkatan)
            || ($category === 'Pengurus Himpunan' && ($roleFilter !== 'all' || $bidangFilter !== 'all'));

        $bisaUbah = fn (User $target) => $this->bisaUbah($actor, $target);

        return view('manajemenmahasiswa::permissions.category', compact(
            'users', 'category', 'assignableRoles', 'search', 'perPage', 'roleFilter', 'bidangFilter', 'angkatan', 'angkatanList', 'isFiltered', 'bisaUbah'
        ) + $this->dataFormBidang($actor));
    }

    // ── Update Role ───────────────────────────────────────────────────────────

    /**
     * Ubah role jabatan seorang pengguna beserta bidang/unit tempatnya bertugas.
     *
     * Role berbidang (KABID, WAKABID, KANIT, WAKANIT, Staff) wajib disertai satu
     * bidang/unit yang jenisnya cocok: KABID/WAKABID → Bidang, KANIT/WAKANIT → Unit,
     * Staff → keduanya boleh. Pimpinan bidang/unit hanya bisa mengatur staff di
     * bidang/unitnya sendiri; bidangnya dipaksa ke bidang actor.
     */
    public function updateRole(Request $request, User $user)
    {
        $actor = auth()->user();

        // Jangan boleh ubah role diri sendiri
        abort_if($actor->id === $user->id, 403, 'Tidak dapat mengubah role diri sendiri.');

        // Hanya akun dengan hak lebih rendah dari actor (lihat bisaUbah()): KABID tidak
        // bisa mengubah KAHIM/WAKAHIM maupun sesama KABID/WAKABID/KANIT/WAKANIT, tapi
        // bisa mengubah Mahasiswa & Staff Himpunan.
        abort_unless($this->bisaUbah($actor, $user), 403, 'Anda tidak dapat mengubah role pengguna dengan hak setara atau lebih tinggi.');

        $request->validate([
            'roles'     => ['required', 'array', 'min:1'],
            'roles.*'   => ['string'],
            'bidang_id' => ['nullable', 'integer', 'exists:mk_bidang,id'],
        ]);

        $selectedRoles = $request->input('roles', []);

        // Validasi: semua role yang dipilih harus dalam daftar yang boleh di-assign actor
        $assignable     = $this->getAssignableRoles($actor)->pluck('name')->toArray();
        $allowedChoices = array_merge($assignable, ['mahasiswa']);
        foreach ($selectedRoles as $role) {
            abort_unless(in_array($role, $allowedChoices), 403, "Anda tidak memiliki izin untuk mengassign role '{$role}'.");
        }

        // Pimpinan bidang/unit hanya menempatkan staff di bidang/unitnya sendiri
        // (kesamaan bidang target sudah dicek bisaUbah()).
        $bidangActor = null;
        if ($this->actorTerbatasBidang($actor)) {
            $bidangActor = RoleHimpunan::bidangId($actor);
            if (!$bidangActor) {
                return back()->with('error', 'Bidang/unit Anda belum diatur. Minta KAHIM/WAKAHIM atau admin mengaturnya terlebih dahulu.');
            }
        }

        // Jabatan berbidang yang benar-benar tersimpan: alumni & "Mahasiswa Biasa"
        // menghapus semua jabatan (lihat resolveMultipleRoleIds()).
        $jabatanBerbidang = array_intersect($selectedRoles, ['alumni', 'mahasiswa'])
            ? []
            : array_values(array_intersect($selectedRoles, RoleHimpunan::BERBIDANG));

        $bidang = null;
        if ($jabatanBerbidang) {
            $bidangId = $bidangActor ?? $request->integer('bidang_id') ?: null;
            $bidang   = $bidangId ? Bidang::find($bidangId) : null;

            if ($pesan = $this->tolakBidangJabatan($jabatanBerbidang, $bidang)) {
                return back()->with('error', $pesan);
            }
        }

        DB::beginTransaction();
        try {
            $oldRoleNames = $user->roles->pluck('name')->values()->toArray();
            $oldBidang    = $user->pengurusBidang?->bidang?->nama_bidang;

            $newRoleIds = $this->resolveMultipleRoleIds($selectedRoles, $user);
            $user->roles()->sync($newRoleIds);

            // Bidang/unit pengurus: disimpan bila masih memegang jabatan berbidang,
            // dilepas bila tidak (mis. jadi KAHIM, Mahasiswa Biasa, atau Alumni).
            if ($bidang) {
                PengurusBidang::updateOrCreate(['user_id' => $user->id], ['bidang_id' => $bidang->id]);
            } else {
                PengurusBidang::where('user_id', $user->id)->delete();
            }
            $user->unsetRelation('pengurusBidang');
            
            // Auto-create Alumni record jika diubah menjadi alumni
            if (in_array('alumni', $selectedRoles) && !in_array('alumni', $oldRoleNames)) {
                if (!Alumni::where('user_id', $user->id)->exists()) {
                    $student = $user->student;
                    Alumni::create([
                        'user_id' => $user->id,
                        'nim' => $student ? $student->student_number : '-',
                        'angkatan' => $student ? $student->cohort_year : date('Y'),
                        'tahun_lulus' => date('Y'),
                        'program_studi' => 'S1 Teknik Komputer',
                    ]);
                }
                
                // PERINGATAN (S-53, 29 Okt 2026 — Surya): status kemahasiswaan sudah dicabut dari
                // Direktori Mahasiswa (tidak tampil & tidak bisa diubah; sumber resmi = SIAP Undip).
                // Kini halaman Permission ini SATU-SATUNYA jalur menjadikan alumni. Baris di bawah tetap
                // perlu karena Dashboard Analitik masih menghitung Kemahasiswaan::alumni().
                // Direktori Mahasiswa sendiri memisahkan alumni lewat mk_alumni, bukan kolom status.
                // Update juga status di mk_kemahasiswaan agar tidak dihapus oleh auto-sync Direktori Alumni
                Kemahasiswaan::where('user_id', $user->id)->update([
                    'status' => Kemahasiswaan::STATUS_ALUMNI,
                    'tahun_lulus' => date('Y')
                ]);
            }

            $user->load('roles');
            $user->syncPermissionsFromRoles();
            $user->clearUserCache();

            $newRoleLabels = implode(', ', array_map(
                fn ($role) => in_array($role, RoleHimpunan::POSISI, true)
                    ? RoleHimpunan::labelLengkap($user, $role)
                    : $this->getRoleLabel($role),
                $selectedRoles
            ));

            AuditLogger::update(
                module:  'manajemen_mahasiswa',
                desc:    "Mengubah role pengguna: {$user->name} ({$user->email})",
                subject: $user,
                oldData: ['roles' => $oldRoleNames, 'bidang' => $oldBidang],
                newData: [
                    'roles'       => $user->roles->pluck('name')->values()->toArray(),
                    'bidang'      => $bidang?->nama_bidang,
                    'assigned_by' => $actor->name,
                ],
            );

            DB::commit();
            return back()->with('success', "Role \"{$user->name}\" berhasil diubah menjadi: {$newRoleLabels}.");

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal mengubah role: ' . $e->getMessage());
        }
    }

    // ── Cek Alumni Otomatis ───────────────────────────────────────────────────

    public function checkAlumni(Request $request)
    {
        abort_unless(auth()->user()->hasAnyRole(['admin_kemahasiswaan', 'admin', 'superadmin']), 403);

        $cutoffYear = now()->year - 5; // angkatan <= 2021 jika sekarang 2026
        $dryRun     = $request->boolean('dry_run', false);

        $alumniRole   = Role::where('name', 'alumni')->first();
        $mahasiswaRole = Role::where('name', 'mahasiswa')->first();

        if (!$alumniRole || !$mahasiswaRole) {
            return response()->json(['error' => 'Role alumni atau mahasiswa tidak ditemukan.'], 422);
        }

        $students = \App\Models\Student::where('cohort_year', '<=', $cutoffYear)
            ->with('user.roles')
            ->get();

        $toPromote = $students->filter(fn($s) => $s->user && !$s->user->hasRole('alumni'));
        $count     = $toPromote->count();

        if ($dryRun) {
            return response()->json([
                'count'   => $count,
                'message' => "Ditemukan {$count} mahasiswa yang akan dijadikan alumni (angkatan ≤ {$cutoffYear}).",
                'preview' => $toPromote->take(10)->map(fn($s) => [
                    'name'        => $s->user->name,
                    'nim'         => $s->student_number,
                    'cohort_year' => $s->cohort_year,
                ]),
            ]);
        }

        DB::beginTransaction();
        try {
            foreach ($toPromote as $student) {
                $user = $student->user;

                $currentRoles = $user->roles->pluck('id')->toArray();
                // Hapus role mahasiswa dan posisi himpunan, tambah alumni
                $positionRoleIds = Role::whereIn('name', ['mahasiswa', 'pengurus_himpunan', ...self::HIMPUNAN_POSITION_ROLES])
                    ->pluck('id')->toArray();
                $keptRoles  = array_diff($currentRoles, $positionRoleIds);
                $newRoleIds = array_unique([...$keptRoles, $alumniRole->id]);

                $user->roles()->sync($newRoleIds);
                
                // Auto-create Alumni record
                if (!Alumni::where('user_id', $user->id)->exists()) {
                    Alumni::create([
                        'user_id' => $user->id,
                        'nim' => $student->student_number,
                        'angkatan' => $student->cohort_year,
                        'tahun_lulus' => date('Y'),
                        'program_studi' => 'S1 Teknik Komputer',
                    ]);
                }
                
                // PERINGATAN (S-53, 29 Okt 2026 — Surya): status kemahasiswaan sudah dicabut dari
                // Direktori Mahasiswa (tidak tampil & tidak bisa diubah; sumber resmi = SIAP Undip).
                // Kini halaman Permission ini SATU-SATUNYA jalur menjadikan alumni. Baris di bawah tetap
                // perlu karena Dashboard Analitik masih menghitung Kemahasiswaan::alumni().
                // Direktori Mahasiswa sendiri memisahkan alumni lewat mk_alumni, bukan kolom status.
                // Update status kemahasiswaan
                Kemahasiswaan::where('user_id', $user->id)->update([
                    'status' => Kemahasiswaan::STATUS_ALUMNI,
                    'tahun_lulus' => date('Y')
                ]);
                $user->load('roles');
                $user->syncPermissionsFromRoles();
                $user->clearUserCache();
            }

            AuditLogger::update(
                module:  'manajemen_mahasiswa',
                desc:    "Auto-assign alumni: {$count} mahasiswa diubah menjadi alumni (angkatan ≤ {$cutoffYear})",
                subject: null,
                oldData: [],
                newData: ['count' => $count, 'cutoff_year' => $cutoffYear],
            );

            DB::commit();
            return response()->json([
                'success' => true,
                'count'   => $count,
                'message' => "{$count} mahasiswa berhasil diubah menjadi alumni.",
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // ── Reset Role Pengurus Himpunan ──────────────────────────────────────────

    /**
     * Preview / eksekusi reset seluruh role pengurus himpunan ke mahasiswa biasa.
     * Mendukung dry_run untuk preview sebelum eksekusi.
     */
    public function resetPengurusRoles(Request $request)
    {
        abort_unless(auth()->user()->hasAnyRole(['admin_kemahasiswaan', 'admin', 'superadmin']), 403);

        $dryRun       = $request->boolean('dry_run', false);
        $pengurusNames = ['pengurus_himpunan', ...self::HIMPUNAN_POSITION_ROLES];

        // Ambil semua user yang masih punya role pengurus himpunan
        $pengurusUsers = User::with('roles')
            ->whereNull('deleted_at')
            ->whereHas('roles', fn ($q) => $q->whereIn('name', $pengurusNames))
            ->get();

        $count = $pengurusUsers->count();

        if ($dryRun) {
            return response()->json([
                'count'   => $count,
                'message' => "Ditemukan {$count} pengurus himpunan yang akan direset menjadi mahasiswa biasa.",
                'preview' => $pengurusUsers->take(10)->map(fn ($u) => [
                    'name'  => $u->name,
                    'email' => $u->email,
                    'roles' => $u->roles->whereIn('name', $pengurusNames)->pluck('name')->join(', '),
                ]),
            ]);
        }

        if (!$this->roleMahasiswaId()) {
            return response()->json(['error' => 'Role mahasiswa tidak ditemukan.'], 422);
        }

        DB::beginTransaction();
        try {
            foreach ($pengurusUsers as $user) {
                // Hapus semua role pengurus, pertahankan role lain (misal: alumni jika ada).
                // Role mahasiswa juga dilepas dulu lalu dipasang tepat satu, supaya tidak
                // ada dua role "mahasiswa" (lihat roleMahasiswaId()).
                $keepRoles = $user->roles
                    ->whereNotIn('name', [...$pengurusNames, 'mahasiswa'])
                    ->pluck('id')
                    ->toArray();

                // Pastikan minimal ada role mahasiswa
                $newRoleIds = array_unique([...$keepRoles, $this->roleMahasiswaId($user)]);

                $user->roles()->sync($newRoleIds);
                $user->load('roles');
                $user->syncPermissionsFromRoles();
                $user->clearUserCache();
            }

            // Bidang/unit kepengurusan ikut dilepas — periode baru diatur ulang.
            PengurusBidang::whereIn('user_id', $pengurusUsers->pluck('id'))->delete();

            AuditLogger::update(
                module:  'manajemen_mahasiswa',
                desc:    "Reset role pengurus himpunan: {$count} pengguna dikembalikan ke mahasiswa biasa",
                subject: null,
                oldData: [],
                newData: ['count' => $count, 'reset_by' => auth()->user()->name],
            );

            DB::commit();
            return response()->json([
                'success' => true,
                'count'   => $count,
                'message' => "{$count} pengurus himpunan berhasil direset menjadi mahasiswa biasa.",
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // ── Private Helpers ───────────────────────────────────────────────────────

    /**
     * Kembalikan collection Role yang boleh diassign oleh actor ini.
     */
    private function getAssignableRoles(User $actor): \Illuminate\Support\Collection
    {
        $allowedNames = $this->assignableNames($actor);

        // Diurutkan sesuai senioritas jabatan (KAHIM, WAKAHIM, KABID, ...), alumni terakhir.
        $urutan = [...RoleHimpunan::POSISI, 'alumni'];

        return Role::whereIn('name', $allowedNames)->get()
            ->sortBy(fn ($role) => array_search($role->name, $urutan, true))
            ->values();
    }

    /**
     * Nama role yang boleh di-assign actor (gabungan ASSIGNABLE_BY dari semua role-nya).
     *
     * @return list<string>
     */
    private function assignableNames(User $actor): array
    {
        $actorRoles   = $actor->roles->pluck('name')->toArray();
        $allowedNames = [];

        foreach (self::ASSIGNABLE_BY as $callerRole => $allowed) {
            if (in_array($callerRole, $actorRoles)) {
                $allowedNames = array_unique(array_merge($allowedNames, $allowed));
            }
        }

        return array_values($allowedNames);
    }

    /**
     * Resolve role IDs dari array pilihan multi-role.
     *
     * Aturan:
     * - Alumni → menggantikan semua (tidak lagi mahasiswa aktif).
     * - Mahasiswa Biasa (dipilih tanpa posisi) → strip semua posisi, hanya mahasiswa.
     * - Role posisi → mahasiswa SELALU dipertahankan + posisi + pengurus_himpunan.
     *
     * Role "mahasiswa" selalu tepat SATU (roleMahasiswaId()). Dulu dicari lewat
     * whereIn('name', ...) sehingga kedua baris role bernama "mahasiswa" ikut
     * terpasang dan user tercatat punya dua role Mahasiswa.
     */
    private function resolveMultipleRoleIds(array $selectedRoles, ?User $user = null): array
    {
        if (in_array('alumni', $selectedRoles)) {
            $role = Role::where('name', 'alumni')->first();
            return $role ? [$role->id] : [];
        }

        $mahasiswaId = $this->roleMahasiswaId($user);

        if (in_array('mahasiswa', $selectedRoles)) {
            return $mahasiswaId ? [$mahasiswaId] : [];
        }

        // Role posisi: selalu tambahkan mahasiswa + pengurus_himpunan
        $positionNames = array_intersect($selectedRoles, self::HIMPUNAN_POSITION_ROLES);
        $finalNames    = array_unique([...$positionNames, 'pengurus_himpunan']);

        $ids = Role::whereIn('name', $finalNames)->pluck('id')->toArray();

        return array_values(array_unique(array_filter([...$ids, $mahasiswaId])));
    }

    /**
     * ID role "mahasiswa" yang dipakai — tepat satu.
     *
     * Tabel roles punya DUA baris bernama "mahasiswa": module `capstone` (data lama)
     * dan module `global` (dibuat login SSO, AcademicRoleSynchronizer). Pengecekan
     * akses di seluruh aplikasi memakai nama role, jadi cukup satu. Urutan pilihan:
     * role mahasiswa yang sudah dipegang user (versi `global` lebih dulu), lalu versi
     * `global`, lalu yang lain.
     */
    private function roleMahasiswaId(?User $user = null): ?int
    {
        $kandidat = Role::where('name', 'mahasiswa')
            ->orderByRaw("CASE WHEN module = 'global' THEN 0 ELSE 1 END")
            ->orderBy('id')
            ->pluck('id');

        if ($user) {
            $dipegang = $kandidat->intersect($user->roles->pluck('id'))->first();
            if ($dipegang) {
                return (int) $dipegang;
            }
        }

        return $kandidat->first() ? (int) $kandidat->first() : null;
    }

    /**
     * Kategori tabel yang boleh dilihat actor: admin melihat ketiganya, role
     * himpunan hanya Mahasiswa Aktif & Pengurus Himpunan.
     *
     * @return list<string>
     */
    private function kategoriUntuk(User $actor): array
    {
        return $actor->hasAnyRole(['admin_kemahasiswaan', 'admin', 'superadmin'])
            ? ['Mahasiswa Aktif', 'Pengurus Himpunan', 'Alumni']
            : ['Mahasiswa Aktif', 'Pengurus Himpunan'];
    }

    /**
     * Apakah actor boleh mengubah role target — hanya akun berhak LEBIH RENDAH.
     *
     *  - Bukan diri sendiri, dan bukan superadmin/admin/dosen/GPM/Kadep.
     *  - Role himpunan juga tidak bisa mengubah akun admin modul (admin_*), DPM,
     *    atau dosen koordinator/kaprodi.
     *  - Setiap jabatan himpunan & alumni yang sudah dipegang target harus termasuk
     *    role yang boleh di-assign actor. Jadi KABID (hanya boleh assign Staff) tidak
     *    bisa mengubah KAHIM/WAKAHIM maupun sesama KABID/WAKABID/KANIT/WAKANIT, tapi
     *    bisa mengubah Mahasiswa & Staff Himpunan; KAHIM tidak bisa mengubah WAKAHIM.
     *  - Pimpinan bidang/unit hanya untuk anggota bidang/unitnya, atau yang belum
     *    punya bidang/unit.
     */
    private function bisaUbah(User $actor, User $target): bool
    {
        if ($actor->id === $target->id) {
            return false;
        }

        if ($target->hasAnyRole(['superadmin', 'admin', 'dosen', 'gpm', 'ketua_departemen'])) {
            return false;
        }

        $actorAdmin = $actor->hasAnyRole(['admin_kemahasiswaan', 'admin', 'superadmin']);
        $roleTarget = $target->roles->pluck('name');

        if (!$actorAdmin && $roleTarget->contains(fn ($r) => str_starts_with($r, 'admin')
                || in_array($r, ['dpm', 'dosen_koordinator', 'kaprodi'], true))) {
            return false;
        }

        $assignable = $this->assignableNames($actor);
        $dilindungi = $roleTarget->intersect([...self::HIMPUNAN_POSITION_ROLES, 'alumni']);
        if ($dilindungi->diff($assignable)->isNotEmpty()) {
            return false;
        }

        if ($this->actorTerbatasBidang($actor)) {
            $bidangTarget = RoleHimpunan::bidangId($target);
            if ($bidangTarget && $bidangTarget !== RoleHimpunan::bidangId($actor)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Pimpinan bidang/unit (KABID, WAKABID, KANIT, WAKANIT) yang bukan KAHIM/WAKAHIM
     * atau admin — aksesnya di halaman ini terkunci ke bidang/unitnya.
     */
    private function actorTerbatasBidang(User $actor): bool
    {
        return RoleHimpunan::isPimpinanBidang($actor)
            && !$actor->hasAnyRole([...RoleHimpunan::INTI, 'admin_kemahasiswaan', 'admin', 'superadmin']);
    }

    /**
     * Pimpinan bidang/unit hanya melihat anggota bidang/unitnya sendiri, ditambah
     * pengguna yang belum punya bidang/unit (calon staff yang bisa ia rekrut).
     */
    private function batasiKeBidangActor($query, User $actor): void
    {
        if (!$this->actorTerbatasBidang($actor)) {
            return;
        }

        $bidangId = RoleHimpunan::bidangId($actor);

        $query->where(fn ($q) => $q
            ->whereDoesntHave('pengurusBidang')
            ->when($bidangId, fn ($q2) => $q2->orWhereHas('pengurusBidang', fn ($pb) => $pb->where('bidang_id', $bidangId)))
        );
    }

    /**
     * Data dropdown Bidang/Unit untuk form Ubah Role.
     *
     * @return array{bidangOptions: \Illuminate\Support\Collection, bidangTerkunci: ?int}
     */
    private function dataFormBidang(User $actor): array
    {
        $terkunci = $this->actorTerbatasBidang($actor) ? RoleHimpunan::bidangId($actor) : null;

        $bidangOptions = Bidang::orderBy('jenis')->orderBy('nama_bidang')
            ->when($this->actorTerbatasBidang($actor), fn ($q) => $q->whereKey($terkunci))
            ->get();

        return ['bidangOptions' => $bidangOptions, 'bidangTerkunci' => $terkunci];
    }

    /**
     * Pastikan bidang/unit cocok dengan jabatan berbidang yang dipilih.
     *
     * @param  list<string>  $jabatan
     * @return string|null pesan error, atau null bila valid
     */
    private function tolakBidangJabatan(array $jabatan, ?Bidang $bidang): ?string
    {
        if (!$bidang) {
            return 'Pilih Bidang/Unit untuk jabatan ' . implode(', ', array_map([RoleHimpunan::class, 'label'], $jabatan)) . '.';
        }

        $butuhBidang = array_intersect($jabatan, RoleHimpunan::ROLE_BIDANG);
        $butuhUnit   = array_intersect($jabatan, RoleHimpunan::ROLE_UNIT);

        if ($butuhBidang && $butuhUnit) {
            return 'Jabatan KABID/WAKABID dan KANIT/WAKANIT tidak bisa dipegang bersamaan — satu pengurus hanya di satu bidang/unit.';
        }
        if ($butuhBidang && $bidang->isUnit()) {
            return "{$bidang->nama_bidang} adalah Unit. KABID/WAKABID harus memegang Bidang.";
        }
        if ($butuhUnit && !$bidang->isUnit()) {
            return "{$bidang->nama_bidang} adalah Bidang. KANIT/WAKANIT harus memegang Unit.";
        }

        return null;
    }

    private function isMahasiswaAktif(User $u): bool
    {
        $names = $u->roles->pluck('name');
        return $names->contains('mahasiswa')
            && $names->intersect(['pengurus_himpunan', ...self::HIMPUNAN_POSITION_ROLES])->isEmpty()
            && !$names->contains('alumni');
    }

    private function isPengurus(User $u): bool
    {
        return $u->roles->pluck('name')
            ->intersect(['pengurus_himpunan', ...self::HIMPUNAN_POSITION_ROLES])
            ->isNotEmpty();
    }

    private function getRoleLabel(string $roleName): string
    {
        return match ($roleName) {
            'alumni'    => 'Alumni',
            'mahasiswa' => 'Mahasiswa Biasa',
            default     => RoleHimpunan::label($roleName),
        };
    }
}
