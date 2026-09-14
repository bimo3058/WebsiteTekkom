<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\SystemModule;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Modules\EOffice\Http\Controllers\KoordinatorController;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class EOfficeKpAccessTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Cache::put('module_data_eoffice', new SystemModule([
            'slug' => 'eoffice', 'is_active' => true,
        ]));
    }

    private function actor(array $roles): User
    {
        $user = new User(['name' => 'KP Test', 'email' => 'kp@example.test']);
        $user->id = 1;
        $user->setRelation('roles', new Collection(array_map(
            fn (string $role) => new Role(['name' => $role, 'guard_name' => 'web']),
            $roles,
        )));

        return $user;
    }

    public static function coordinatorRoles(): array
    {
        return [
            'admin eoffice' => ['admin_eoffice'],
            'superadmin' => ['superadmin'],
            'koordinator kp' => ['koor_kp'],
        ];
    }

    #[DataProvider('coordinatorRoles')]
    public function test_authorized_role_can_open_kp_coordinator_dashboard(string $role): void
    {
        // Keep the actual route and authorization middleware; isolate dashboard queries.
        $this->partialMock(KoordinatorController::class, function ($mock) {
            $mock->shouldReceive('dashboard')->once()->andReturn(response('Dashboard KP'));
        });

        $this->actingAs($this->actor([$role]))
            ->get(route('eoffice.kp.koordinator.dashboard'))
            ->assertOk()->assertSee('Dashboard KP');
    }

    public static function unrelatedRoles(): array
    {
        return [
            'mahasiswa' => ['mahasiswa'],
            'dosen' => ['dosen'],
            'koordinator praktikum' => ['koor_prak'],
        ];
    }

    #[DataProvider('unrelatedRoles')]
    public function test_unrelated_roles_cannot_access_coordinator_pages_or_actions(string $role): void
    {
        $this->actingAs($this->actor([$role]));
        $this->getJson(route('eoffice.kp.koordinator.dashboard'))->assertForbidden();
        $this->postJson(route('eoffice.kp.koordinator.pengaturan.store'), [
            'pendaftaran_kp_buka' => '1',
        ])->assertForbidden();
    }

    public static function menuRoles(): array
    {
        return [
            'admin' => [['admin_eoffice'], 'koordinator'],
            'superadmin' => [['superadmin'], 'koordinator'],
            'koordinator kp' => [['koor_kp'], 'koordinator'],
            'dosen koordinator kp' => [['dosen', 'koor_kp'], 'koordinator'],
            'dosen' => [['dosen'], 'dosen'],
            'mahasiswa koordinator praktikum' => [['mahasiswa', 'koor_prak'], 'mahasiswa'],
            'mahasiswa' => [['mahasiswa'], 'mahasiswa'],
        ];
    }

    #[DataProvider('menuRoles')]
    public function test_kp_menu_points_to_the_dashboard_for_the_users_role(array $roles, string $destination): void
    {
        $this->view('eoffice::dashboard._sidebar', [
            'user' => $this->actor($roles), 'currentRoute' => 'eoffice.dashboard',
            'iDashboard' => '', 'iPraktikum' => '', 'iKP' => '',
        ])->assertSee('href="'.route('eoffice.kp.'.$destination.'.dashboard').'"', false);
    }
}
