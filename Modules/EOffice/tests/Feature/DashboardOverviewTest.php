<?php

namespace Modules\EOffice\Tests\Feature;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\{DB, Schema};
use Modules\EOffice\Models\{KerjaPraktik, KpPeriode};
use Modules\EOffice\Services\DashboardOverview;
use Tests\TestCase;

class DashboardOverviewTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.connections.eoffice_dashboard_test' => ['driver' => 'sqlite', 'database' => ':memory:']]);
        DB::setDefaultConnection('eoffice_dashboard_test');
        Carbon::setTestNow(Carbon::parse('2026-09-10 10:00:00'));
        foreach ([
            'users' => ['name', 'email', 'deleted_at'],
            'eo_surat' => ['pemohon_id', 'nomor_surat', 'status', 'tanggal_pengajuan'],
            'eo_approval' => ['surat_id', 'approver_id', 'status'],
            'eo_mr_peminjamans' => ['user_id', 'ruangan_id', 'status', 'tanggal_pinjam', 'jam_mulai', 'jam_selesai', 'tujuan'],
            'eo_mr_ruangans' => ['nama'],
            'eo_kp_mahasiswa' => ['user_id', 'nama_lengkap'],
            'eo_kp_dosen' => ['user_id', 'nama_lengkap'],
            'eo_kerja_praktik' => ['mahasiswa_id', 'dosen_pembimbing_id', 'status_kp', 'periode_id', 'is_acc_admin', 'judul_kp', 'instansi_kp', 'tanggal_mulai', 'tanggal_selesai'],
            'eo_kp_periode' => ['tahun_ajaran', 'semester', 'is_active', 'tanggal_buka', 'tanggal_tutup', 'pra_kp_mulai', 'pra_kp_akhir', 'saat_kp_mulai', 'saat_kp_akhir', 'pasca_kp_mulai', 'pasca_kp_akhir'],
            'eo_kp_seminar' => ['kp_id', 'tanggal_seminar', 'status_validasi_dosen'],
            'eo_kp_dokumen' => ['kp_id', 'status_validasi', 'approval_status'],
            'eo_kp_pengumuman' => ['judul', 'konten', 'is_active', 'tipe'],
            'eo_praktikum' => ['nama', 'kode', 'status', 'deleted_at'],
            'eo_praktikum_dosen' => ['praktikum_id', 'dosen_id'],
            'daftar_praktikan' => ['praktikum_id', 'user_id'],
            'modul_praktikum' => ['praktikum_id'],
            'tugas_praktikum' => ['modul_id', 'judul', 'deadline', 'deadline_acc', 'is_published'],
            'pengumpulan_tugas' => ['tugas_id', 'daftar_praktikan_id', 'status_pengumpulan'],
            'absensi_praktikum' => ['daftar_praktikan_id', 'status'],
        ] as $table => $columns) Schema::create($table, function (Blueprint $t) use ($columns) {
            $t->id(); foreach ($columns as $column) $t->string($column)->nullable(); $t->timestamps();
        });
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        DB::purge('eoffice_dashboard_test');
        parent::tearDown();
    }

    private function actor(int $id = 1): User
    {
        $user = new User(['name' => 'Pengguna Uji', 'email' => 'test@example.test']);
        $user->id = $id;
        $user->setRelation('roles', new \Illuminate\Database\Eloquent\Collection);
        return $user;
    }

    public function test_letters_are_scoped_and_lecturer_approval_count_is_personal(): void
    {
        DB::table('eo_surat')->insert([
            ['id' => 1, 'pemohon_id' => 1, 'status' => 'diajukan', 'tanggal_pengajuan' => '2026-09-10'],
            ['id' => 2, 'pemohon_id' => 2, 'status' => 'selesai', 'tanggal_pengajuan' => '2026-09-10'],
        ]);
        DB::table('eo_approval')->insert([
            ['surat_id' => 2, 'approver_id' => 1, 'status' => 'menunggu'],
            ['surat_id' => 1, 'approver_id' => 2, 'status' => 'menunggu'],
        ]);
        $service = new DashboardOverview;
        $student = $service->letters($this->actor(), 'mahasiswa');
        $this->assertCount(1, $student['entries']);
        $this->assertSame(0, $student['metrics']['Disetujui / selesai']);
        $this->assertSame(1, $service->letters($this->actor(), 'dosen')['metrics']['Persetujuan ditugaskan kepada Anda']);
        $this->assertCount(2, $service->letters($this->actor(), 'admin')['entries']);
        Schema::drop('eo_surat');
        $this->assertSame([], $service->letters($this->actor(), 'mahasiswa')['metrics']);
    }

    public function test_bookings_hide_other_users_and_expire_counts_without_writing(): void
    {
        DB::table('eo_mr_ruangans')->insert(['id' => 1, 'nama' => 'Lab A']);
        foreach ([[1, 1, 'menunggu', '09:00'], [2, 1, 'menunggu', '11:00'], [3, 2, 'menunggu', '12:00'], [4, 1, 'disetujui', '11:00'], [5, 2, 'disetujui', '11:00']] as [$id, $uid, $status, $start]) {
            DB::table('eo_mr_peminjamans')->insert(['id' => $id, 'user_id' => $uid, 'ruangan_id' => 1, 'status' => $status, 'tanggal_pinjam' => '2026-09-10', 'jam_mulai' => $start, 'jam_selesai' => '13:00', 'tujuan' => 'Praktikum']);
        }
        $data = (new DashboardOverview)->rooms($this->actor(), 'dosen');
        $this->assertSame(1, $data['metrics']['Menunggu konfirmasi']);
        $this->assertSame(1, $data['metrics']['Menunggu kedaluwarsa']);
        $this->assertSame(1, $data['metrics']['Agenda mendatang']);
        $this->assertCount(3, $data['entries']);
        $this->assertSame('menunggu', DB::table('eo_mr_peminjamans')->where('id', 1)->value('status'));
    }

    public function test_timeline_distinguishes_calendar_expiry_from_student_completion(): void
    {
        $period = new KpPeriode(['tahun_ajaran' => '2026/2027', 'semester' => 'Ganjil', 'tanggal_buka' => '2026-08-01', 'tanggal_tutup' => '2026-08-31', 'pra_kp_mulai' => '2026-09-01', 'pra_kp_akhir' => '2026-09-10', 'saat_kp_mulai' => '2026-09-11', 'saat_kp_akhir' => '2026-10-30']);
        $data = (new DashboardOverview)->timeline($period, new KerjaPraktik(['status_kp' => 'Pra-KP']), 'eoffice.kp.mahasiswa.dashboard');
        $this->assertSame(['ended', 'active', 'upcoming', 'unscheduled'], array_column($data['steps'], 'state'));
        $this->assertSame('Batas akhir hari ini', $data['steps'][1]['deadline']);
        $this->assertSame('Pra-KP', $data['status']);
        Carbon::setTestNow(Carbon::parse('2026-09-10 23:59:59'));
        $this->assertSame('active', (new DashboardOverview)->timeline($period, null, '')['steps'][1]['state']);
        $period->pra_kp_akhir = '2026-08-20';
        $this->assertSame('invalid', (new DashboardOverview)->timeline($period, null, '')['steps'][1]['state']);
    }

    public function test_kp_scopes_use_profile_ids_and_student_keeps_original_period(): void
    {
        DB::table('eo_kp_mahasiswa')->insert([['id' => 11, 'user_id' => 1, 'nama_lengkap' => 'Milik saya'], ['id' => 12, 'user_id' => 2, 'nama_lengkap' => 'Orang lain']]);
        DB::table('eo_kp_dosen')->insert([['id' => 21, 'user_id' => 1], ['id' => 22, 'user_id' => 2]]);
        DB::table('eo_kp_periode')->insert([['id' => 1, 'tahun_ajaran' => '2025/2026', 'is_active' => false], ['id' => 2, 'tahun_ajaran' => '2026/2027', 'is_active' => true]]);
        foreach ([[1, 11, 21], [2, 12, 22]] as [$id, $student, $lecturer]) DB::table('eo_kerja_praktik')->insert(['id' => $id, 'mahasiswa_id' => $student, 'dosen_pembimbing_id' => $lecturer, 'periode_id' => 1, 'status_kp' => 'Pra-KP', 'is_acc_admin' => false, 'created_at' => now()]);
        DB::table('eo_kp_dokumen')->insert(['kp_id' => 2, 'status_validasi' => 'menunggu', 'approval_status' => 'pending']);
        $service = new DashboardOverview;
        foreach (['mahasiswa', 'dosen'] as $role) {
            $data = $service->kp($this->actor(), $role);
            $this->assertSame(1, $data['card']['metrics']['Pra-KP']);
            $this->assertSame(0, $data['card']['metrics']['Dokumen menunggu / perlu revisi']);
            $this->assertCount(1, $data['card']['entries']);
        }
        $this->assertStringContainsString('2025/2026', $service->kp($this->actor(), 'mahasiswa')['timeline']['period']);
        $this->assertSame(2, $service->kp($this->actor(), 'admin')['card']['metrics']['Pra-KP']);
        $this->assertSame(0, $service->kp($this->actor(9), 'dosen')['card']['metrics']['Pra-KP']);
    }

    public function test_kp_coordinator_card_links_to_coordinator_dashboard(): void
    {
        $user = $this->actor();
        $user->setRelation('roles', new \Illuminate\Database\Eloquent\Collection([
            new \App\Models\Role(['name' => 'dosen', 'guard_name' => 'web']),
            new \App\Models\Role(['name' => 'koor_kp', 'guard_name' => 'web']),
        ]));

        $this->assertSame('eoffice.kp.koordinator.dashboard', (new DashboardOverview)->kp($user, 'dosen')['card']['route']);
    }

    public function test_student_tasks_cover_all_enrollments_exclude_drafts_and_other_submissions(): void
    {
        foreach ([1, 2, 3] as $id) {
            DB::table('eo_praktikum')->insert(['id' => $id, 'nama' => 'Praktikum '.$id, 'status' => 'aktif']);
            DB::table('modul_praktikum')->insert(['id' => $id, 'praktikum_id' => $id]);
            DB::table('daftar_praktikan')->insert(['id' => $id, 'praktikum_id' => $id, 'user_id' => $id === 3 ? 2 : 1]);
            DB::table('tugas_praktikum')->insert(['id' => $id, 'modul_id' => $id, 'judul' => 'Tugas '.$id, 'is_published' => true, 'deadline' => '2026-09-09', 'deadline_acc' => '2026-09-20']);
        }
        DB::table('tugas_praktikum')->insert(['id' => 4, 'modul_id' => 1, 'judul' => 'Draf rahasia', 'is_published' => false]);
        DB::table('pengumpulan_tugas')->insert([['tugas_id' => 1, 'daftar_praktikan_id' => 3, 'status_pengumpulan' => 'acc'], ['tugas_id' => 2, 'daftar_praktikan_id' => 2, 'status_pengumpulan' => 'revisi']]);
        DB::table('absensi_praktikum')->insert([['daftar_praktikan_id' => 1, 'status' => 'hadir'], ['daftar_praktikan_id' => 2, 'status' => 'tidak_hadir'], ['daftar_praktikan_id' => 3, 'status' => 'hadir']]);
        $data = (new DashboardOverview)->practice($this->actor(), 'mahasiswa');
        $this->assertSame(2, $data['card']['metrics']['Praktikum aktif']);
        $this->assertSame(2, $data['data']['tugasPendingCount']);
        $this->assertSame(0, $data['card']['metrics']['Tenggat pengumpulan lewat']);
        $this->assertSame(50, $data['data']['absensiPct']);
        $this->assertSame(['Tugas 1', 'Tugas 2'], $data['data']['tugasMendatang']->pluck('judul')->all());
        $this->assertTrue($data['data']['tugasMendatang'][1]['revisi']);
        DB::table('users')->insert(['id' => 1, 'name' => 'Dosen']);
        DB::table('eo_praktikum_dosen')->insert(['praktikum_id' => 3, 'dosen_id' => 1]);
        $lecturer = (new DashboardOverview)->practice($this->actor(), 'dosen');
        $this->assertSame(1, $lecturer['card']['metrics']['Praktikum aktif']);
        $this->assertSame('Praktikum 3', $lecturer['card']['entries'][0]['title']);
    }

    public function test_all_role_views_render_four_services_and_timeline_with_no_data(): void
    {
        foreach (['admin' => 'admin_eoffice', 'mahasiswa' => 'mahasiswa', 'dosen' => 'dosen'] as $page => $role) {
            $user = $this->actor();
            $user->setRelation('roles', collect([new \Spatie\Permission\Models\Role(['name' => $role, 'guard_name' => 'web'])]));
            $user->setRelation('student', null);
            auth()->setUser($user);
            $data = (new DashboardOverview)->forUser($user, $page);
            $html = view('eoffice::dashboard.'.$page, $data)->render();
            foreach (['Manajemen Surat', 'Peminjaman Ruangan', 'Kerja Praktik', 'Manajemen Praktikum', 'Timeline Kerja Praktik', 'Belum dijadwalkan'] as $text) $this->assertStringContainsString($text, $html);
            $this->assertSame(4, substr_count($html, 'class="eo-overview-card"'));
            $this->assertStringNotContainsString('href="#"', $html);
            foreach ($data['dashboardServices'] as $card) if ($card['route']) $this->assertTrue(app('router')->has($card['route']));
        }
    }
}
