<?php

namespace Modules\Capstone\Tests;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use Modules\Capstone\Http\Controllers\LocationController;
use Modules\Capstone\Models\ExpoEvent;
use Modules\Capstone\Models\Location;
use Modules\Capstone\Models\SeminarSchedule;
use Modules\Capstone\Services\EofficeAvailabilityService;
use Modules\EOffice\Models\Peminjaman;
use Modules\EOffice\Models\Ruangan;
use Tests\TestCase;

/** Isolated real SQL: never migrate the application's configured database. */
class EofficeRoomIntegrationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.connections.capstone_eoffice_test' => ['driver' => 'sqlite', 'database' => ':memory:', 'foreign_key_constraints' => false]]);
        DB::setDefaultConnection('capstone_eoffice_test');

        Schema::create('users', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->string('email')->unique();
            $t->string('password')->nullable();
            $t->timestamp('last_login')->nullable();
            $t->boolean('is_online')->default(false);
            $t->timestamps();
            $t->softDeletes();
        });
        Schema::create('roles', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->string('guard_name')->default('web');
            $t->timestamps();
        });
        Schema::create('model_has_roles', function (Blueprint $t) {
            $t->unsignedBigInteger('role_id');
            $t->string('model_type');
            $t->unsignedBigInteger('model_id');
            $t->index(['model_id', 'model_type']);
        });
        Schema::create('eo_mr_ruangans', function (Blueprint $t) {
            $t->id();
            $t->string('nama');
            $t->string('lokasi')->nullable();
            $t->integer('lantai')->nullable();
            $t->integer('kapasitas')->default(0);
            $t->json('fasilitas')->nullable();
            $t->boolean('is_active')->default(true);
            $t->timestamps();
        });
        Schema::create('eo_mr_peminjamans', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('user_id');
            $t->unsignedBigInteger('created_by')->nullable();
            $t->unsignedBigInteger('ruangan_id');
            $t->string('nomor_telepon')->nullable();
            $t->text('tujuan');
            $t->date('tanggal_pinjam');
            $t->time('jam_mulai');
            $t->time('jam_selesai');
            $t->string('berkas_pendukung')->nullable();
            $t->string('status')->default('menunggu');
            $t->text('alasan_penolakan')->nullable();
            $t->timestamp('waktu_approval')->nullable();
            $t->timestamps();
        });
        Schema::create('eo_mr_jadwal_internal', function (Blueprint $t) {
            $t->uuid('id')->primary();
            $t->unsignedBigInteger('ruangan_id');
            $t->string('tipe_jadwal')->default('spesifik');
            $t->string('kategori');
            $t->tinyInteger('hari')->nullable();
            $t->date('tanggal_spesifik')->nullable();
            $t->time('jam_mulai');
            $t->time('jam_selesai');
            $t->date('tgl_mulai_efektif')->nullable();
            $t->date('tgl_selesai_efektif')->nullable();
            $t->string('keterangan')->nullable();
            $t->timestamps();
        });
        Schema::create('capstone_locations', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('eoffice_ruangan_id')->nullable();
            $t->string('name');
            $t->integer('capacity')->nullable();
            $t->boolean('is_active')->default(true);
            $t->string('type')->default('offline');
            $t->text('description')->nullable();
            $t->timestamps();
        });
        Schema::create('capstone_seminar_schedules', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('group_id');
            $t->string('type');
            $t->date('date');
            $t->time('start_time');
            $t->time('end_time');
            $t->string('room')->nullable();
            $t->unsignedBigInteger('examiner_1_id');
            $t->unsignedBigInteger('examiner_2_id');
            $t->string('status')->default('SCHEDULED');
            $t->unsignedBigInteger('requested_by')->nullable();
            $t->text('rejection_reason')->nullable();
            $t->unsignedBigInteger('location_id')->nullable();
            $t->unsignedBigInteger('eoffice_ruangan_id')->nullable();
            $t->unsignedBigInteger('eoffice_peminjaman_id')->nullable();
            $t->decimal('final_score', 5, 2)->nullable();
            $t->timestamps();
            $t->softDeletes();
        });
        Schema::create('capstone_expo_events', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('period_id')->nullable();
            $t->string('name');
            $t->date('date');
            $t->time('start_time');
            $t->time('end_time');
            $t->string('room')->nullable();
            $t->unsignedBigInteger('eoffice_ruangan_id')->nullable();
            $t->unsignedBigInteger('eoffice_peminjaman_id')->nullable();
            $t->integer('capacity')->nullable();
            $t->boolean('is_published')->default(false);
            $t->unsignedBigInteger('created_by')->nullable();
            $t->timestamps();
            $t->softDeletes();
        });
        Schema::create('capstone_ta_defense_schedules', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('group_id');
            $t->date('date');
            $t->time('start_time');
            $t->time('end_time');
            $t->string('room')->nullable();
            $t->string('status')->default('SCHEDULED');
            $t->unsignedBigInteger('location_id')->nullable();
            $t->unsignedBigInteger('eoffice_ruangan_id')->nullable();
            $t->unsignedBigInteger('eoffice_peminjaman_id')->nullable();
            $t->timestamps();
        });
        Schema::create('capstone_ta_defense_examiners', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('schedule_id');
            $t->unsignedBigInteger('examiner_id');
            $t->timestamps();
        });
    }

    protected function tearDown(): void
    {
        DB::purge('capstone_eoffice_test');
        parent::tearDown();
    }

    private function seedRoom(string $nama = 'Ruang Sidang 1'): array
    {
        $userId = DB::table('users')->insertGetId([
            'name' => 'Admin Capstone', 'email' => 'admin-capstone@example.test',
            'created_at' => now(), 'updated_at' => now(),
        ]);
        $room = Ruangan::create(['nama' => $nama, 'kapasitas' => 30]);
        $location = Location::create(['name' => $nama, 'type' => 'offline', 'eoffice_ruangan_id' => $room->id]);

        return [$userId, $room, $location];
    }

    private function makeAdmin(string $email = 'admin@example.test'): User
    {
        $userId = DB::table('users')->insertGetId([
            'name' => 'Admin', 'email' => $email,
            'created_at' => now(), 'updated_at' => now(),
        ]);
        $roleId = DB::table('roles')->insertGetId([
            'name' => 'admin_capstone', 'guard_name' => 'web',
            'created_at' => now(), 'updated_at' => now(),
        ]);
        DB::table('model_has_roles')->insert([
            'role_id' => $roleId, 'model_type' => User::class, 'model_id' => $userId,
        ]);
        $user = User::find($userId);
        Auth::login($user);

        return $user;
    }

    public function test_only_disetujui_bookings_block(): void
    {
        [$userId, $room, $location] = $this->seedRoom();
        $service = app(EofficeAvailabilityService::class);

        $this->assertSame($room->id, $service->resolveEofficeId('Ruang Sidang 1', $location->id));

        Peminjaman::create([
            'user_id' => $userId, 'ruangan_id' => $room->id, 'tujuan' => 'Rapat jurusan',
            'tanggal_pinjam' => '2026-10-06', 'jam_mulai' => '09:00:00', 'jam_selesai' => '11:00:00',
            'status' => 'menunggu',
        ]);

        // Menunggu does not block; adjacent slot does not overlap.
        $this->assertNull($service->checkByRoom('Ruang Sidang 1', $location->id, '2026-10-06', '09:30', '10:30'));
        $this->assertNull($service->checkByRoom('Ruang Sidang 1', $location->id, '2026-10-06', '11:00', '12:00'));

        Peminjaman::where('status', 'menunggu')->update(['status' => 'disetujui']);

        $conflict = $service->checkByRoom('Ruang Sidang 1', $location->id, '2026-10-06', '09:30', '10:30');
        $this->assertNotNull($conflict);
        $this->assertSame('peminjaman', $conflict['source']);
        $this->assertStringContainsString('EOffice', $conflict['message']);

        // Non-overlapping edge still free.
        $this->assertNull($service->checkByRoom('Ruang Sidang 1', $location->id, '2026-10-06', '11:00', '12:00'));
    }

    public function test_internal_schedules_block_and_busy_names_map(): void
    {
        [$userId, $room, $location] = $this->seedRoom();
        $service = app(EofficeAvailabilityService::class);

        DB::table('eo_mr_jadwal_internal')->insert([
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'ruangan_id' => $room->id,
            'tipe_jadwal' => 'spesifik',
            'kategori' => 'Kuliah',
            'tanggal_spesifik' => '2026-10-06',
            'jam_mulai' => '13:00:00',
            'jam_selesai' => '15:00:00',
            'keterangan' => 'Kuliah IF-101',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $conflict = $service->checkByRoom('Ruang Sidang 1', $location->id, '2026-10-06', '13:30', '14:30');
        $this->assertNotNull($conflict);
        $this->assertSame('internal', $conflict['source']);

        $this->assertSame(['Ruang Sidang 1'], $service->busyLocationNames('2026-10-06', '13:30', '14:30'));
        $this->assertSame([], $service->busyLocationNames('2026-10-06', '15:00', '16:00'));
    }

    public function test_online_locations_are_never_linked(): void
    {
        $this->seedRoom();
        $service = app(EofficeAvailabilityService::class);

        $online = Location::create(['name' => 'Zoom Meeting', 'type' => 'online']);
        $this->assertNull($service->resolveEofficeId('Zoom Meeting', $online->id));
        $this->assertNull($service->resolveEofficeId('Ruangan Tidak Ada', null));
    }

    public function test_observer_auto_books_and_releases_eoffice(): void
    {
        [$userId, $room, $location] = $this->seedRoom();

        $schedule = SeminarSchedule::create([
            'group_id' => 1, 'type' => 'SEMPRO', 'date' => '2026-10-07',
            'start_time' => '09:00:00', 'end_time' => '10:00:00',
            'room' => 'Ruang Sidang 1', 'examiner_1_id' => 1, 'examiner_2_id' => 2,
            'status' => 'SCHEDULED', 'requested_by' => $userId, 'location_id' => $location->id,
        ]);

        $booking = Peminjaman::where('tujuan', 'like', '[CAPSTONE%')->first();
        $this->assertNotNull($booking);
        $this->assertSame('disetujui', $booking->status);
        $this->assertSame($room->id, (int) $booking->ruangan_id);
        $this->assertSame((int) $booking->id, (int) $schedule->fresh()->eoffice_peminjaman_id);

        // Two-way: same slot is now blocked for others.
        $conflict = app(EofficeAvailabilityService::class)->checkByRoom(
            'Ruang Sidang 1', $location->id, '2026-10-07', '09:00', '10:00', $booking->id
        );
        $this->assertNull($conflict);

        $schedule->update(['status' => 'CANCELLED']);
        $this->assertSame(0, Peminjaman::where('tujuan', 'like', '[CAPSTONE%')->count());
        $this->assertNull($schedule->fresh()->eoffice_peminjaman_id);
    }

    public function test_scheduling_service_includes_eoffice_conflict(): void
    {
        [$userId, $room, $location] = $this->seedRoom();

        Peminjaman::create([
            'user_id' => $userId, 'ruangan_id' => $room->id, 'tujuan' => 'Yudisium',
            'tanggal_pinjam' => '2026-10-08', 'jam_mulai' => '10:00:00', 'jam_selesai' => '12:00:00',
            'status' => 'disetujui',
        ]);

        $errors = app(\Modules\Capstone\Services\SchedulingService::class)->validateScheduleConflicts(
            [11, 12], '2026-10-08', '10:30', '11:30', 'Ruang Sidang 1', null, null, $location->id
        );

        $this->assertNotEmpty($errors);
        $this->assertTrue(collect($errors)->contains(fn ($e) => str_contains($e, 'EOffice')));
    }

    public function test_direct_eoffice_id_takes_priority(): void
    {
        [$userId, $room] = $this->seedRoom();
        $service = app(EofficeAvailabilityService::class);

        // Direct id wins even when the name does not match anything.
        $this->assertSame($room->id, $service->resolveEofficeId('Nama Ngawur', null, $room->id));
        $this->assertSame($room->id, $service->resolveEofficeId(null, null, $room->id));
        $this->assertNull($service->resolveEofficeId(null, null, 999999));

        Peminjaman::create([
            'user_id' => $userId, 'ruangan_id' => $room->id, 'tujuan' => 'Rapat',
            'tanggal_pinjam' => '2026-10-09', 'jam_mulai' => '09:00:00', 'jam_selesai' => '11:00:00',
            'status' => 'disetujui',
        ]);

        // Empty room name + direct id (new single-source flow) still detects the booking.
        $conflict = $service->checkByRoom(null, null, '2026-10-09', '09:30', '10:30', null, $room->id);
        $this->assertNotNull($conflict);
        $this->assertSame('peminjaman', $conflict['source']);

        $this->assertNull($service->checkByRoom(null, null, '2026-10-09', '11:00', '12:00', null, $room->id));
        $this->assertNull($service->checkByRoom(null, null, '2026-10-09', '09:30', '10:30'));
    }

    public function test_seminar_books_via_own_eoffice_column_without_location(): void
    {
        [$userId, $room] = $this->seedRoom();

        $schedule = SeminarSchedule::create([
            'group_id' => 1, 'type' => 'SEMPRO', 'date' => '2026-10-10',
            'start_time' => '09:00:00', 'end_time' => '10:00:00',
            'room' => null, 'location_id' => null, 'eoffice_ruangan_id' => $room->id,
            'examiner_1_id' => 1, 'examiner_2_id' => 2,
            'status' => 'SCHEDULED', 'requested_by' => $userId,
        ]);

        $booking = Peminjaman::where('tujuan', 'like', '[CAPSTONE%')->first();
        $this->assertNotNull($booking);
        $this->assertSame($room->id, (int) $booking->ruangan_id);
        $this->assertSame((int) $booking->id, (int) $schedule->fresh()->eoffice_peminjaman_id);

        $schedule->update(['status' => 'CANCELLED']);
        $this->assertSame(0, Peminjaman::where('tujuan', 'like', '[CAPSTONE%')->count());
        $this->assertNull($schedule->fresh()->eoffice_peminjaman_id);
    }

    public function test_expo_auto_books_reschedules_and_releases(): void
    {
        [$userId, $room] = $this->seedRoom();

        $event = ExpoEvent::create([
            'name' => 'Expo 2026', 'date' => '2026-10-11',
            'start_time' => '09:00:00', 'end_time' => '12:00:00',
            'room' => 'Ruang Sidang 1', 'eoffice_ruangan_id' => $room->id,
            'created_by' => $userId,
        ]);

        $booking = Peminjaman::where('tujuan', 'like', '[CAPSTONE EXPO%')->first();
        $this->assertNotNull($booking);
        $this->assertSame('disetujui', $booking->status);
        $this->assertSame($room->id, (int) $booking->ruangan_id);
        $this->assertSame((int) $booking->id, (int) $event->fresh()->eoffice_peminjaman_id);

        // Reschedule moves the same booking instead of creating a new one.
        $event->update(['date' => '2026-10-12']);
        $this->assertSame(1, Peminjaman::where('tujuan', 'like', '[CAPSTONE EXPO%')->count());
        $this->assertSame('2026-10-12', Peminjaman::find($booking->id)->tanggal_pinjam->format('Y-m-d'));

        $event->delete();
        $this->assertSame(0, Peminjaman::where('tujuan', 'like', '[CAPSTONE EXPO%')->count());
    }

    public function test_conflict_check_with_eoffice_id_only_and_no_room(): void
    {
        [$userId, $room] = $this->seedRoom();

        Peminjaman::create([
            'user_id' => $userId, 'ruangan_id' => $room->id, 'tujuan' => 'Yudisium',
            'tanggal_pinjam' => '2026-10-13', 'jam_mulai' => '10:00:00', 'jam_selesai' => '12:00:00',
            'status' => 'disetujui',
        ]);

        $errors = app(\Modules\Capstone\Services\SchedulingService::class)->validateScheduleConflicts(
            [11, 12], '2026-10-13', '10:30', '11:30', null, null, null, null, null, $room->id
        );

        $this->assertNotEmpty($errors);
        $this->assertTrue(collect($errors)->contains(fn ($e) => str_contains($e, 'EOffice')));
    }

    public function test_store_rejects_offline_and_link_but_accepts_online(): void
    {
        $this->makeAdmin();
        $controller = new LocationController;

        try {
            $controller->store(Request::create('/locations', 'POST', ['name' => 'Lab X', 'type' => 'offline']));
            $this->fail('Offline location creation must be rejected.');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('type', $e->errors());
        }

        try {
            $controller->store(Request::create('/locations', 'POST', [
                'name' => 'Zoom', 'type' => 'online', 'eoffice_ruangan_id' => 1,
            ]));
            $this->fail('eoffice_ruangan_id must be prohibited on locations.');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('eoffice_ruangan_id', $e->errors());
        }

        $response = $controller->store(Request::create('/locations', 'POST', [
            'name' => 'Zoom', 'type' => 'online', 'capacity' => 100,
        ]));
        $this->assertSame(201, $response->getStatusCode());
        $payload = json_decode($response->getContent(), true);
        $this->assertTrue($payload['success']);
        $this->assertSame('online', Location::where('name', 'Zoom')->value('type'));
        $this->assertNull(Location::where('name', 'Zoom')->value('eoffice_ruangan_id'));
    }

    public function test_available_returns_eoffice_rooms_excluding_busy(): void
    {
        [$userId, $room] = $this->seedRoom('Ruang Sidang 1');
        $freeRoom = Ruangan::create(['nama' => 'Ruang Bebas', 'kapasitas' => 20]);

        Peminjaman::create([
            'user_id' => $userId, 'ruangan_id' => $room->id, 'tujuan' => 'Rapat',
            'tanggal_pinjam' => '2026-10-14', 'jam_mulai' => '09:00:00', 'jam_selesai' => '11:00:00',
            'status' => 'disetujui',
        ]);

        // A Capstone seminar on the free room also marks it busy.
        SeminarSchedule::create([
            'group_id' => 9, 'type' => 'SEMPRO', 'date' => '2026-10-14',
            'start_time' => '09:00:00', 'end_time' => '10:00:00',
            'room' => 'Ruang Bebas', 'eoffice_ruangan_id' => $freeRoom->id,
            'examiner_1_id' => 1, 'examiner_2_id' => 2,
            'status' => 'SCHEDULED', 'requested_by' => $userId,
        ]);

        $controller = new LocationController;
        $request = Request::create('/x', 'GET', [
            'date' => '2026-10-14', 'start_time' => '09:30', 'end_time' => '10:30',
        ]);
        $payload = json_decode($controller->available($request)->getContent(), true);

        $this->assertTrue($payload['success']);
        $this->assertSame([], $payload['data']);
        $this->assertEqualsCanonicalizing([$room->id, $freeRoom->id], $payload['busy_eoffice_ids']);

        // Non-overlapping slot frees both rooms again.
        $request = Request::create('/x', 'GET', [
            'date' => '2026-10-14', 'start_time' => '13:00', 'end_time' => '14:00',
        ]);
        $payload = json_decode($controller->available($request)->getContent(), true);
        $names = array_column($payload['data'], 'nama');
        $this->assertEqualsCanonicalizing(['Ruang Bebas', 'Ruang Sidang 1'], $names);
        $this->assertTrue(collect($payload['data'])->every(fn ($r) => $r['available'] === true));
    }

    public function test_eoffice_rooms_view_only_payload_and_admin_gate(): void
    {
        [$userId, $room] = $this->seedRoom();
        Peminjaman::create([
            'user_id' => $userId, 'ruangan_id' => $room->id, 'tujuan' => 'Besok',
            'tanggal_pinjam' => now()->addDay()->format('Y-m-d'),
            'jam_mulai' => '09:00:00', 'jam_selesai' => '10:00:00',
            'status' => 'disetujui',
        ]);

        // Non-admin (logged in, no roles) is rejected.
        $plainId = DB::table('users')->insertGetId([
            'name' => 'Dosen', 'email' => 'dosen@example.test',
            'created_at' => now(), 'updated_at' => now(),
        ]);
        Auth::login(User::find($plainId));
        $this->assertSame(403, (new LocationController)->eofficeRooms()->getStatusCode());

        $this->makeAdmin();
        $payload = json_decode((new LocationController)->eofficeRooms()->getContent(), true);

        $this->assertTrue($payload['success']);
        $this->assertCount(1, $payload['data']);
        $item = $payload['data'][0];
        foreach (['id', 'nama', 'lokasi', 'lantai', 'kapasitas', 'fasilitas', 'is_active', 'upcoming_bookings_count'] as $key) {
            $this->assertArrayHasKey($key, $item);
        }
        $this->assertSame(1, $item['upcoming_bookings_count']);
    }
}
