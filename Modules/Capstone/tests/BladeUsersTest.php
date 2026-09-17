<?php

namespace Modules\Capstone\Tests;

use App\Models\{User,Role,Lecturer,SystemModule};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Modules\Capstone\Database\Seeders\CapstonePermissionsSeeder;
use Modules\Capstone\Support\CapstoneActor;
use Tests\TestCase;

class BladeUsersTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        SystemModule::updateOrCreate(['slug'=>'capstone'],['name'=>'Capstone','is_active'=>true,'is_maintenance'=>false]);
        $this->seed(CapstonePermissionsSeeder::class);
    }

    private function account(array $roles): User
    {
        $user=User::factory()->create(['sso_data'=>null]);
        foreach ($roles as $role) $user->roles()->attach(Role::where('name',$role)->firstOrFail()->id);
        return $user->fresh('roles');
    }

    private function admin(): User
    {
        $user=$this->account(['admin_capstone']);
        Sanctum::actingAs($user,['capstone:access']);
        $this->withHeader('X-Capstone-Role','admin');
        return $user;
    }

    public function test_admin_dosen_without_profile_can_open_lecturer_menu_and_profile_is_idempotent(): void
    {
        $user=$this->account(['admin_capstone','dosen']);
        $this->actingAs($user)->withSession(['session_version'=>0,'capstone.blade_role'=>'admin'])
            ->get('/capstone/dosen/titles')->assertOk()->assertSee('Manage Titles')->assertSessionHas('capstone.blade_role','dosen');
        $first=CapstoneActor::lecturer($user->fresh());
        $this->assertSame(explode('@',$user->email)[0],$first->employee_number);
        $this->assertSame($first->id,CapstoneActor::lecturer($user->fresh())->id);
        $this->assertSame(1,Lecturer::where('user_id',$user->id)->count());
        $this->get('/capstone/admin/users')->assertOk();
    }

    public function test_profile_repair_never_claims_another_lecturers_identity(): void
    {
        $owner=$this->account(['dosen']);
        Lecturer::create(['user_id'=>$owner->id,'employee_number'=>'COLLISION']);
        $user=$this->account(['admin_capstone','dosen']);
        $user->update(['sso_data'=>['employeeId'=>'COLLISION']]);
        try { CapstoneActor::lecturer($user);$this->fail('Expected an identity collision to be rejected.'); }
        catch (\Illuminate\Auth\Access\AuthorizationException $e) { $this->assertStringContainsString('NIP unik',$e->getMessage()); }
        $this->assertFalse(Lecturer::where('user_id',$user->id)->exists());
        $this->assertSame($owner->id,Lecturer::first()->user_id);
    }

    public function test_admin_without_dosen_role_still_cannot_open_dosen_pages(): void
    {
        $user=$this->account(['admin_capstone']);
        $this->actingAs($user)->withSession(['session_version'=>0])->getJson('/capstone/dosen/titles')->assertForbidden();
        $this->assertFalse(Lecturer::where('user_id',$user->id)->exists());
    }

    public function test_user_creation_saves_combined_roles_and_real_lecturer_identity(): void
    {
        $this->admin();
        $response=$this->postJson('/api/capstone/admin/user-management',['name'=>'New Lecturer','email'=>'lecturer@example.test','password'=>'test-password-123','roles'=>['admin','dosen'],'nip'=>'NIP-123456']);
        $response->assertCreated()->assertJsonPath('roles',['admin','dosen'])->assertJsonPath('nip','NIP-123456')->assertJsonMissingPath('password');
        $user=User::findOrFail($response->json('id'));
        $this->assertTrue(Hash::check('test-password-123',$user->password));
        $this->assertSame($user->id,Lecturer::where('employee_number','NIP-123456')->firstOrFail()->user_id);
    }

    public function test_users_role_filter_keeps_account_ids_and_all_role_badges(): void
    {
        $this->admin();$user=$this->account(['admin_capstone','dosen']);
        Lecturer::create(['user_id'=>$user->id,'employee_number'=>'NIP-FILTER']);
        $this->getJson('/api/capstone/admin/user-management?role=dosen&per_page=1')->assertOk()->assertJsonPath('data.0.id',$user->id)->assertJsonPath('data.0.roles',['admin','dosen'])->assertJsonPath('total',1);
    }

    public function test_editing_roles_preserves_unrelated_roles_and_blank_password(): void
    {
        $this->admin();$user=$this->account(['dosen']);Lecturer::create(['user_id'=>$user->id,'employee_number'=>'OLD-NIP']);
        $role=Role::create(['name'=>'reviewer','module'=>'banksoal','guard_name'=>'web']);$user->roles()->attach($role->id);$old=$user->password;
        $this->putJson('/api/capstone/admin/user-management/'.$user->id,['name'=>'Updated Lecturer','email'=>$user->email,'password'=>'','roles'=>['dosen','admin'],'nip'=>'NEW-NIP'])->assertOk()->assertJsonPath('nip','NEW-NIP');
        $this->assertSame($old,$user->fresh()->password);
        $this->assertTrue($user->fresh()->roles->contains('id',$role->id));
        $this->assertSame(1,Lecturer::where('user_id',$user->id)->count());
    }

    public function test_invalid_student_combinations_and_missing_academic_ids_are_rejected(): void
    {
        $this->admin();$base=['name'=>'Invalid Roles','email'=>'invalid@example.test','password'=>'test-password-123'];
        $this->postJson('/api/capstone/admin/user-management',$base+['roles'=>['mahasiswa','admin'],'nim'=>'12345678','cohort_year'=>2024])->assertUnprocessable()->assertJsonValidationErrors('roles');
        $this->postJson('/api/capstone/admin/user-management',$base+['roles'=>['dosen']])->assertUnprocessable()->assertJsonValidationErrors('nip');
        $this->assertDatabaseMissing('users',['email'=>'invalid@example.test']);
    }

    public function test_users_cannot_delete_self_or_modify_a_superadmin(): void
    {
        $admin=$this->admin();
        $role=Role::firstOrCreate(['name'=>'superadmin','guard_name'=>'web'],['module'=>'global']);
        $root=User::factory()->create();$root->roles()->attach($role->id);
        $this->deleteJson('/api/capstone/admin/user-management/'.$admin->id)->assertUnprocessable();
        $this->deleteJson('/api/capstone/admin/user-management/'.$root->id)->assertForbidden();
        $this->putJson('/api/capstone/admin/user-management/'.$root->id,['name'=>'Changed'])->assertForbidden();
        $this->assertFalse($root->fresh()->trashed());
    }

    public function test_dosen_without_admin_cannot_manage_users(): void
    {
        $user=$this->account(['dosen']);Lecturer::create(['user_id'=>$user->id,'employee_number'=>'DENY-NIP']);Sanctum::actingAs($user,['capstone:access']);
        $this->withHeader('X-Capstone-Role','dosen')->getJson('/api/capstone/admin/user-management')->assertForbidden();
        $this->postJson('/api/capstone/admin/user-management',[])->assertForbidden();
    }
}
