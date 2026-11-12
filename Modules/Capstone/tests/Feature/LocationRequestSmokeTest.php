<?php

namespace Modules\Capstone\Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Modules\Capstone\Http\Controllers\LocationController;
use Modules\Capstone\Http\Requests\Scheduling\StoreLocationRequest;
use Modules\Capstone\Models\Location;
use Tests\TestCase;

/**
 * Smoke: Chunk 2 location Request is wired to the controller correctly.
 * Proves the 201 path, the non-admin rejection, and the rule rejection
 * through the real FormRequest (a rules() pin alone cannot catch a
 * mis-bound signature or import).
 * Isolated real SQL: never migrate the application's configured database.
 */
class LocationRequestSmokeTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.connections.location_smoke_test' => ['driver' => 'sqlite', 'database' => ':memory:', 'foreign_key_constraints' => true]]);
        DB::setDefaultConnection('location_smoke_test');

        Schema::create('users', function (Blueprint $t) {
            $t->id();
            $t->string('name')->nullable();
            $t->string('email')->nullable();
            $t->timestamps();
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
        Schema::create('capstone_locations', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->integer('capacity')->nullable();
            $t->boolean('is_active')->default(true);
            $t->string('type')->default('physical');
            $t->text('description')->nullable();
            $t->unsignedBigInteger('eoffice_ruangan_id')->nullable();
            $t->timestamps();
        });
    }

    private function admin(): User
    {
        $user = User::create(['name' => 'Admin', 'email' => 'admin@example.test']);
        $role = Role::create(['name' => 'admin_capstone']);
        DB::table('model_has_roles')->insert([
            'role_id' => $role->id, 'model_type' => User::class, 'model_id' => $user->id,
        ]);

        return $user;
    }

    private function storeRequest(User $user, array $data): StoreLocationRequest
    {
        $request = StoreLocationRequest::create('/x', 'POST', $data);
        $request->setContainer(app());
        $request->setRedirector(app('redirect'));
        $request->setUserResolver(fn () => $user);
        $request->validateResolved();

        return $request;
    }

    public function test_store_location_returns_201_and_persists(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin);

        $response = (new LocationController)->store(
            $this->storeRequest($admin, ['name' => 'Room A', 'type' => 'online'])
        );

        $this->assertSame(201, $response->getStatusCode());
        $this->assertSame('Room A', Location::first()->name);
    }

    public function test_store_location_rejects_non_admin(): void
    {
        $user = User::create(['name' => 'Student', 'email' => 'student@example.test']);
        $this->actingAs($user);

        $response = (new LocationController)->store(
            $this->storeRequest($user, ['name' => 'Room A', 'type' => 'online'])
        );

        $this->assertSame(403, $response->getStatusCode());
        $this->assertSame(0, Location::count());
    }

    public function test_store_location_rejects_invalid_type(): void
    {
        $admin = $this->admin();

        $this->expectException(ValidationException::class);
        $this->storeRequest($admin, ['name' => 'Room A', 'type' => 'physical']);
    }
}
