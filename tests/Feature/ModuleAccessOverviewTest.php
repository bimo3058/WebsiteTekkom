<?php

namespace Tests\Feature;

use App\Services\ModuleAccessOverview;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ModuleAccessOverviewTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.connections.module_access_test' => ['driver' => 'sqlite', 'database' => ':memory:']]);
        DB::setDefaultConnection('module_access_test');
        $this->travelTo(now()->setDate(2026, 9, 14)->setTime(12, 0));

        foreach (ModuleAccessOverview::MODELS as $model) {
            Schema::create((new $model)->getTable(), function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('action');
                $table->timestamp('created_at');
            });
        }
    }

    protected function tearDown(): void
    {
        $this->travelBack();
        parent::tearDown();
    }

    private function activity(string $module, ?int $userId, string $date, string $action = 'UPDATE'): void
    {
        $model = ModuleAccessOverview::MODELS[$module];
        DB::table((new $model)->getTable())->insert([
            'user_id' => $userId, 'action' => $action, 'created_at' => $date,
        ]);
    }

    public static function modules(): array
    {
        return array_map(fn ($module) => [$module], array_keys(ModuleAccessOverview::MODELS));
    }

    #[DataProvider('modules')]
    public function test_existing_module_logs_count_users_once_per_day_and_once_per_period(string $module): void
    {
        $this->activity($module, 1, '2026-09-01 00:00:00', 'LOGIN');
        $this->activity($module, 1, '2026-09-14 08:00:00', 'VIEW');
        $this->activity($module, 1, '2026-09-14 09:00:00', 'UPDATE');
        $this->activity($module, 2, '2026-09-14 10:00:00', 'CREATE');
        $this->activity($module, null, '2026-09-14 10:30:00');
        $this->activity($module, 3, '2026-08-31 23:59:59');
        $this->activity($module, 4, '2026-09-15 00:00:00');

        $all = (new ModuleAccessOverview)->forDashboard();
        $data = $all[$module];
        $this->assertCount(14, $data['days']);
        $this->assertSame('2026-09-01', $data['date_from']);
        $this->assertSame('2026-09-14', $data['date_to']);
        $this->assertSame(2, $data['total_users']);
        $this->assertSame(4, $data['total_events']);
        $this->assertSame(2, $data['today_users']);
        $this->assertSame(2, $data['peak_users']);
        $this->assertSame(1, $data['days'][0]['users']);
        $this->assertSame(0, $data['days'][1]['users']);
        $this->assertSame(3, $data['days'][13]['events']);
        foreach ($all as $key => $other) {
            if ($key !== $module) {
                $this->assertSame(0, $other['total_users']);
            }
        }
    }

    public function test_empty_modules_render_zero_values_without_fake_activity(): void
    {
        $access = (new ModuleAccessOverview)->forDashboard();
        $view = $this->view('superadmin.dashboard._modules', [
            'moduleAccess' => $access,
            'modules' => collect(array_keys(ModuleAccessOverview::MODELS))->mapWithKeys(fn ($key) => [
                $key => ['slug' => $key, 'name' => $key, 'is_active' => true],
            ])->all(),
        ]);
        $view->assertSee('Aktivitas pengguna')->assertSee('pengguna unik')
            ->assertSee('Belum ada aktivitas tercatat dalam 14 hari ini.');
        $this->assertSame(4, substr_count((string) $view, 'class="module-access"'));
        $this->assertSame(56, substr_count((string) $view, 'class="module-access-column"'));
    }

    public function test_cached_graph_refreshes_after_one_minute_and_rolls_over_at_midnight(): void
    {
        $service = new ModuleAccessOverview;
        $this->assertSame(0, $service->forDashboard()['eoffice']['total_users']);
        $this->activity('eoffice', 1, '2026-09-14 12:00:00');
        $this->assertSame(0, $service->forDashboard()['eoffice']['total_users']);
        $this->travel(61)->seconds();
        $this->assertSame(1, $service->forDashboard()['eoffice']['total_users']);
        $this->travelTo(now()->setTime(23, 59, 59));
        Cache::forget('sa:module_access:2026-09-14');
        $service->forDashboard();
        $this->travel(2)->seconds();
        $next = $service->forDashboard()['eoffice'];
        $this->assertSame('2026-09-02', $next['date_from']);
        $this->assertSame('2026-09-15', $next['date_to']);
        $this->assertSame(0, $next['today_users']);
        $this->assertSame(1, $next['total_users']);
    }
}
