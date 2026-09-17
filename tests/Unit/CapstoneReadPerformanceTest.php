<?php

namespace Tests\Unit;

use App\Models\Lecturer;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Modules\Capstone\Http\Controllers\DashboardController;
use Modules\Capstone\Http\Controllers\UserController;
use Modules\Capstone\Models\Period;
use Modules\Capstone\Support\CapstoneActor;
use Tests\TestCase;

/** Real SQL checks on a dedicated in-memory connection, independent of EOffice migrations. */
class CapstoneReadPerformanceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config(['database.connections.capstone_read_test' => [
            'driver' => 'sqlite', 'database' => ':memory:', 'foreign_key_constraints' => true,
        ]]);
        DB::setDefaultConnection('capstone_read_test');

        foreach ([
            'database/migrations/2026_02_15_111353_create_users_table.php',
            'database/migrations/2026_02_15_114337_create_students_tables.php',
            'database/migrations/2026_02_15_114539_create_lecturers_tables.php',
            'Modules/Capstone/database/migrations/2026_05_05_000001_create_capstone_periods_table.php',
            'Modules/Capstone/database/migrations/2026_05_05_000002_create_capstone_groups_table.php',
        ] as $migration) {
            (require base_path($migration))->up();
        }
        Schema::table('capstone_groups', fn (Blueprint $table) => $table->string('code')->nullable());
    }

    protected function tearDown(): void
    {
        DB::purge('capstone_read_test');
        parent::tearDown();
    }

    public function test_identity_reuses_owner_and_loaded_profiles(): void
    {
        $user = User::withoutEvents(fn () => User::create([
            'external_id' => 'fast-user', 'name' => 'Test User', 'email' => 'fast@example.test',
        ]));
        Student::create(['user_id' => $user->id, 'student_number' => 'FAST-NIM', 'cohort_year' => 2024]);
        Lecturer::create(['user_id' => $user->id, 'employee_number' => 'FAST-NIP']);
        $user->setRelation('roles', collect([new Role(['name' => 'mahasiswa'])]));

        DB::enableQueryLog();
        $user->fresh()->loadMissing(['student', 'lecturer']);
        $legacyQueryCount = count(DB::getQueryLog()) - 1; // Exclude fresh() owner lookup.
        DB::flushQueryLog();
        $payload = CapstoneActor::payload($user);

        $this->assertSame('FAST-NIM', $payload['nim']);
        $this->assertSame('FAST-NIP', $payload['nip']);
        $this->assertSame('mahasiswa', $payload['active_role']);
        $this->assertSame(4, $legacyQueryCount);
        $this->assertCount(2, DB::getQueryLog());
        $this->assertFalse(collect(DB::getQueryLog())->contains(
            fn ($query) => str_contains($query['query'], 'from "users"')
        ));
        DB::flushQueryLog();
        $this->assertSame($payload, CapstoneActor::payload($user));
        $this->assertCount(0, DB::getQueryLog());
        DB::disableQueryLog();
    }

    public function test_dashboard_query_count_stays_constant_as_groups_grow(): void
    {
        $period = Period::create([
            'name' => 'Test', 'start_date' => '2026-01-01', 'end_date' => '2026-12-31', 'is_active' => true,
        ]);
        $archived = $period->replicate();
        $archived->save();
        $archived->delete();

        foreach ([8, 80] as $size) {
            DB::table('capstone_groups')->delete();
            for ($i = 0; $i < $size; $i++) {
                DB::table('capstone_groups')->insert([
                    'period_id' => $period->id, 'code' => 'GROUP-'.$i,
                    'status' => $i < 6 ? 'READY_FOR_FINALIZATION' : 'FORMING',
                    'created_at' => '2026-01-01 00:00:00',
                ]);
            }

            DB::enableQueryLog();
            DB::flushQueryLog();
            $payload = (new DashboardController)->admin($this->adminRequest(true))->getData(true);

            $this->assertSame($size, $payload['total_groups']);
            $this->assertSame(6, $payload['pending_finalization']);
            $this->assertSame(1, $payload['total_periods']);
            $this->assertCount(5, $payload['recent_groups']);
            $this->assertSame('GROUP-'.($size - 1), $payload['recent_groups'][0]['code']);
            $this->assertEqualsCanonicalizing(['id', 'code', 'status'], array_keys($payload['recent_groups'][0]));
            $this->assertCount(7, DB::getQueryLog());
            DB::disableQueryLog();
        }
    }

    public function test_dashboard_skips_group_queries_without_permission(): void
    {
        DB::enableQueryLog();
        $payload = (new DashboardController)->admin($this->adminRequest(false))->getData(true);
        $this->assertSame(0, $payload['total_groups']);
        $this->assertSame([], $payload['recent_groups']);
        $this->assertFalse(collect(DB::getQueryLog())->contains(
            fn ($query) => str_contains($query['query'], 'capstone_groups')
        ));
        DB::disableQueryLog();
    }

    public function test_recent_groups_index_can_be_applied_and_rolled_back(): void
    {
        $migration = require base_path('Modules/Capstone/database/migrations/2026_09_08_000000_add_capstone_recent_groups_index.php');
        $migration->up();
        $this->assertTrue(Schema::hasIndex('capstone_groups', ['created_at', 'id']));
        $migration->down();
        $this->assertFalse(Schema::hasIndex('capstone_groups', 'cap_group_recent_idx'));
    }

    public function test_user_selector_preserves_ids_without_loading_owner_data_again(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('guard_name');
        });
        Schema::create('model_has_roles', function (Blueprint $table) {
            $table->unsignedBigInteger('role_id');
            $table->unsignedBigInteger('model_id');
            $table->string('model_type');
        });
        DB::table('roles')->insert(['id' => 1, 'name' => 'mahasiswa', 'guard_name' => 'web']);
        foreach ([1, 2] as $id) {
            DB::table('users')->insert([
                'id' => $id, 'external_id' => 'user-'.$id, 'name' => 'User '.$id,
                'email' => 'user'.$id.'@example.test', 'sso_data' => '{"large":"unused"}',
            ]);
            DB::table('students')->insert([
                'id' => $id + 100, 'user_id' => $id, 'student_number' => 'NIM-'.$id, 'cohort_year' => 2024,
            ]);
            DB::table('model_has_roles')->insert([
                'role_id' => 1, 'model_id' => $id, 'model_type' => (new User)->getMorphClass(),
            ]);
        }

        DB::enableQueryLog();
        $page = (new UserController)->index(Request::create('/users?role=mahasiswa&per_page=1'))->toArray();
        $this->assertSame(2, $page['total']);
        $this->assertCount(1, $page['data']);
        $this->assertSame(101, $page['data'][0]['id']);
        $this->assertSame(1, $page['data'][0]['user_id']);
        $this->assertSame('mahasiswa', $page['data'][0]['role']);
        $this->assertArrayNotHasKey('sso_data', $page['data'][0]);
        $this->assertCount(5, DB::getQueryLog());
        DB::disableQueryLog();

        $largePage = (new UserController)->index(Request::create('/users?per_page=1000'));
        $this->assertSame(100, $largePage->perPage());
        $smallPage = (new UserController)->index(Request::create('/users?per_page=-1'));
        $this->assertSame(1, $smallPage->perPage());
    }

    private function adminRequest(bool $canViewGroups): Request
    {
        $user = \Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('can')->with('capstone.view')->once()->andReturn($canViewGroups);
        $request = Request::create('/api/capstone/admin/dashboard');
        $request->setUserResolver(fn () => $user);

        return $request;
    }
}
