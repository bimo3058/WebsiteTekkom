<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

class MobileNavigationTest extends TestCase
{
    public function test_guests_do_not_receive_authenticated_navigation(): void
    {
        $this->assertStringNotContainsString('id="mobile-navigation"', Blade::render('<x-mobile-navigation />'));
    }

    public function test_module_navigation_uses_existing_dashboard_routes_and_renders_once(): void
    {
        $user = new User(['name' => 'Mahasiswa Uji']);
        $user->id = 1;
        $user->setRelation('roles', collect([new Role(['name' => 'mahasiswa'])]));
        $this->actingAs($user);

        foreach ([
            'eoffice.manprak.mahasiswa.tugas.index' => 'eoffice.manprak.dashboard',
            'eoffice.kp.mahasiswa.dashboard' => 'eoffice.dashboard',
            'banksoal.dashboard' => 'banksoal.dashboard',
            'komprehensif.mahasiswa.riwayat' => 'banksoal.dashboard',
            'capstone.dashboard' => 'capstone.dashboard',
            'manajemenmahasiswa.dashboard' => 'manajemenmahasiswa.dashboard',
            'profile.edit' => 'dashboard',
        ] as $current => $home) {
            $request = Request::create('/preview');
            $route = (new Route('GET', 'preview', fn () => null))->name($current);
            $request->setRouteResolver(fn () => $route);
            $this->app->instance('request', $request);

            $html = Blade::render('<x-mobile-navigation /><x-mobile-navigation />');
            $this->assertSame(1, substr_count($html, 'id="mobile-navigation"'), $current);
            $this->assertStringContainsString('href="'.route($home).'"', $html);
            $this->assertStringNotContainsString('Dashboard superadmin', $html);
            $this->assertStringContainsString('aria-controls="mobile-account-menu"', $html);
        }
    }

    public function test_superadmin_account_menu_keeps_post_logout_and_profile_access(): void
    {
        $user = new User(['name' => 'Admin Uji']);
        $user->id = 1;
        $user->setRelation('roles', collect([new Role(['name' => 'superadmin'])]));
        $this->actingAs($user);

        $html = Blade::render('<x-mobile-navigation />');
        $this->assertStringContainsString('Dashboard superadmin', $html);
        $this->assertStringContainsString('href="'.route('profile.edit').'"', $html);
        $this->assertStringContainsString('method="POST" action="'.route('logout').'"', $html);
        $this->assertStringContainsString('name="_token"', $html);
    }
}
