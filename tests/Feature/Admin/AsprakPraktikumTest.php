<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Pengguna;
use App\Models\Praktikum;
use App\Models\AsprakPraktikum;

class AsprakPraktikumTest extends TestCase
{
    protected $adminToken;
    protected $nonAdminToken;
    protected $praktikumId;
    protected $userId1;
    protected $userId2;

    protected function setUp(): void
    {
        parent::setUp();

        // Admin (from Pengguna table for auth guard)
        $admin = Pengguna::where('email', 'admin@praktikum.ac.id')->first();
        if ($admin) {
            $this->adminToken = $admin->createToken('test')->plainTextToken;
        }

        // Non-admin (from Pengguna table)
        $mhs = Pengguna::where('email', 'citra@mhs.ac.id')->first();
        if ($mhs) {
            $this->nonAdminToken = $mhs->createToken('test')->plainTextToken;
        }

        // Get an existing praktikum
        $praktikum = Praktikum::first();
        if ($praktikum) {
            $this->praktikumId = $praktikum->id;
        } else {
            $this->praktikumId = '00000000-0000-0000-0000-000000000000';
        }

        // Get two users from Users table (since Asprak uses User model)
        $users = User::take(2)->get();
        if ($users->count() >= 2) {
            $this->userId1 = $users[0]->id;
            $this->userId2 = $users[1]->id;
        } else {
            $this->userId1 = 1;
            $this->userId2 = 2;
        }
    }

    public function test_get_asparaks_returns_empty_or_list()
    {
        if (!$this->praktikumId) $this->markTestSkipped('No praktikum found');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->adminToken,
        ])->get("/api/admin/praktikum/{$this->praktikumId}/asparaks");

        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'message', 'data']);
    }

    public function test_assign_asprak_success()
    {
        if (!$this->praktikumId) $this->markTestSkipped('No praktikum found');
        AsprakPraktikum::where('user_id', $this->userId1)->delete(); // reset

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->adminToken,
        ])->post("/api/admin/praktikum/{$this->praktikumId}/assign-asprak", [
            'user_id' => $this->userId1,
            'role' => 'asprak'
        ]);

        $response->assertStatus(201);
        $response->assertJsonStructure(['success', 'message', 'data' => ['id', 'user', 'role']]);
    }

    public function test_assign_koor_success()
    {
        if (!$this->praktikumId) $this->markTestSkipped('No praktikum found');
        AsprakPraktikum::where('user_id', $this->userId2)->delete(); // reset

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->adminToken,
        ])->post("/api/admin/praktikum/{$this->praktikumId}/assign-asprak", [
            'user_id' => $this->userId2,
            'role' => 'koor'
        ]);

        $response->assertStatus(201);
    }

    public function test_assign_duplicate_user_fails()
    {
        if (!$this->praktikumId) $this->markTestSkipped('No praktikum found');
        
        // Ensure assigned
        AsprakPraktikum::firstOrCreate([
            'praktikum_id' => $this->praktikumId,
            'user_id' => $this->userId1,
        ], ['role' => 'asprak']);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->adminToken,
        ])->post("/api/admin/praktikum/{$this->praktikumId}/assign-asprak", [
            'user_id' => $this->userId1,
            'role' => 'koor'
        ]);

        $response->assertStatus(409);
    }

    public function test_list_asparaks_includes_assigned_user()
    {
        if (!$this->praktikumId) $this->markTestSkipped('No praktikum found');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->adminToken,
        ])->get("/api/admin/praktikum/{$this->praktikumId}/asparaks");

        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertNotEmpty($data);
    }

    public function test_assign_fails_if_user_not_exist()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->adminToken,
        ])->post("/api/admin/praktikum/{$this->praktikumId}/assign-asprak", [
            'user_id' => 9999999,
            'role' => 'asprak'
        ]);

        $response->assertStatus(422);
    }

    public function test_assign_fails_if_invalid_role()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->adminToken,
        ])->post("/api/admin/praktikum/{$this->praktikumId}/assign-asprak", [
            'user_id' => $this->userId1,
            'role' => 'invalid_role'
        ]);

        $response->assertStatus(422);
    }

    public function test_assign_fails_if_praktikum_not_exist()
    {
        $fakeUuid = '11111111-1111-1111-1111-111111111111';
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->adminToken,
        ])->post("/api/admin/praktikum/{$fakeUuid}/assign-asprak", [
            'user_id' => $this->userId1,
            'role' => 'asprak'
        ]);

        $response->assertStatus(404);
    }

    public function test_non_admin_cannot_assign()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->nonAdminToken,
        ])->post("/api/admin/praktikum/{$this->praktikumId}/assign-asprak", [
            'user_id' => $this->userId1,
            'role' => 'asprak'
        ]);

        $response->assertStatus(403);
    }

    public function test_non_admin_cannot_list()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->nonAdminToken,
        ])->get("/api/admin/praktikum/{$this->praktikumId}/asparaks");

        $response->assertStatus(403);
    }

    public function test_unassign_asprak_success()
    {
        $record = AsprakPraktikum::firstOrCreate([
            'praktikum_id' => $this->praktikumId,
            'user_id' => $this->userId1,
        ], ['role' => 'asprak']);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->adminToken,
        ])->delete("/api/admin/praktikum/{$this->praktikumId}/asprak/{$record->id}");

        $response->assertStatus(200);
        $this->assertSoftDeleted('asprak_praktikum', ['id' => $record->id]);
    }

    public function test_unassign_fails_if_not_found()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->adminToken,
        ])->delete("/api/admin/praktikum/{$this->praktikumId}/asprak/99999");

        $response->assertStatus(404);
    }
}
