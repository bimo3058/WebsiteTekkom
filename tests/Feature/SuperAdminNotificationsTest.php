<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Role;
use App\Models\User;
use App\Services\SuperAdminNotifications;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class SuperAdminNotificationsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.connections.notification_test' => ['driver' => 'sqlite', 'database' => ':memory:']]);
        DB::setDefaultConnection('notification_test');
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->timestamps();
            $table->softDeletes();
        });
        (require database_path('migrations/2026_09_14_000001_add_notification_preferences_to_users.php'))->up();
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('module');
            $table->string('action');
            $table->string('description');
            $table->timestamp('created_at');
        });
        Schema::create('import_statuses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('filename');
            $table->string('status');
            $table->timestamps();
        });
    }

    private function actor(string $role = 'superadmin'): User
    {
        $user = User::withoutEvents(fn () => User::create(['name' => 'Admin Test', 'email' => uniqid().'@example.test']));
        $user->setRelation('roles', new Collection([new Role(['name' => $role, 'guard_name' => 'web'])]));

        return $user;
    }

    private function log(string $module, string $action, int $age = 0): AuditLog
    {
        return AuditLog::create(['module' => $module, 'action' => $action, 'description' => $module.' activity', 'created_at' => now()->subDays($age)]);
    }

    public function test_preferences_are_persisted_for_current_user_and_auth_cache_is_invalidated(): void
    {
        $user = $this->actor();
        $other = $this->actor();
        $user->cacheUserData();
        $preferences = ['authentication' => false, 'user_management' => true, 'module_activity' => false, 'imports' => true];

        $this->actingAs($user)->patch(route('profile.notifications.update'), ['notifications' => $preferences, 'user_id' => $other->id])
            ->assertRedirect(route('profile.edit', ['tab' => 'notifikasi']))
            ->assertSessionHas('status', 'notifications-updated');
        $this->assertSame($preferences, $user->fresh()->notification_preferences);
        $this->assertNull($other->fresh()->notification_preferences);
        $this->assertNull(Cache::get('user:'.$user->id.':data'));
    }

    public function test_feed_filters_categories_and_only_includes_the_current_admins_imports(): void
    {
        $user = $this->actor();
        $user->forceFill(['notification_preferences' => ['authentication' => false, 'module_activity' => false]])->saveQuietly();
        $this->log('auth', 'LOGIN');
        $this->log('capstone', 'CREATE');
        $wanted = $this->log('user_management', 'UPDATE');
        $this->log('user_management', 'DELETE', 8);
        foreach ([[$user->id, 'completed', 'mine.csv'], [999, 'failed', 'private.csv'], [$user->id, 'processing', 'pending.csv']] as [$id, $status, $filename]) {
            DB::table('import_statuses')->insert(['user_id' => $id, 'filename' => $filename, 'status' => $status, 'created_at' => now(), 'updated_at' => now()]);
        }

        $this->actingAs($user)->getJson(route('superadmin.notifications.index'))->assertOk()
            ->assertJsonCount(2, 'notifications')->assertJsonFragment(['id' => 'audit-'.$wanted->id])
            ->assertJsonFragment(['description' => 'mine.csv'])->assertJsonMissing(['description' => 'private.csv']);
        $this->assertSame(4, AuditLog::count());
    }

    public function test_default_feed_is_limited_to_ten_newest_events_and_excludes_non_login_auth_actions(): void
    {
        $user = $this->actor();
        for ($i = 0; $i < 12; $i++) {
            $this->log('auth', 'LOGIN');
        }
        $excluded = $this->log('auth', 'PASSWORD_RESET');
        $response = $this->actingAs($user)->getJson(route('superadmin.notifications.index'))->assertOk()->assertJsonCount(10, 'notifications');
        $this->assertSame('audit-12', $response->json('notifications.0.id'));
        $response->assertJsonMissing(['id' => 'audit-'.$excluded->id]);
    }

    public function test_all_categories_can_be_disabled(): void
    {
        $user = $this->actor();
        $preferences = array_fill_keys(array_keys(SuperAdminNotifications::OPTIONS), false);
        $this->actingAs($user)->patch(route('profile.notifications.update'), ['notifications' => $preferences])->assertSessionHasNoErrors();
        $this->log('auth', 'LOGIN');
        $this->getJson(route('superadmin.notifications.index'))->assertOk()->assertExactJson(['notifications' => []]);
    }

    public function test_invalid_or_unknown_options_are_rejected(): void
    {
        $user = $this->actor();
        $preferences = array_fill_keys(array_keys(SuperAdminNotifications::OPTIONS), true);
        $preferences['authentication'] = 'invalid';
        $preferences['email'] = true;
        $this->actingAs($user)->patchJson(route('profile.notifications.update'), ['notifications' => $preferences])
            ->assertUnprocessable()->assertJsonValidationErrors(['notifications', 'notifications.authentication']);
        $this->assertNull($user->fresh()->notification_preferences);
    }

    public function test_non_superadmins_and_guests_cannot_read_or_change_superadmin_notifications(): void
    {
        $this->getJson(route('superadmin.notifications.index'))->assertUnauthorized();
        $user = $this->actor('mahasiswa');
        $this->actingAs($user)->getJson(route('superadmin.notifications.index'))->assertForbidden();
        $this->patchJson(route('profile.notifications.update'), ['notifications' => array_fill_keys(array_keys(SuperAdminNotifications::OPTIONS), true)])->assertForbidden();
    }

    public function test_settings_render_real_options_and_saved_toggle_values(): void
    {
        $user = $this->actor();
        $user->setAttribute('notification_preferences', ['authentication' => false]);
        $view = $this->view('profile.partials.settings-panel-notifikasi', ['user' => $user, 'errors' => new \Illuminate\Support\ViewErrorBag]);
        $view->assertSee('Login dan logout pengguna')->assertSee('Hasil impor pengguna')->assertDontSee('Transaction')->assertDontSee('Payment Error');
        $document = new \DOMDocument;
        @$document->loadHTML((string) $view);
        $this->assertFalse($document->getElementById('notification-authentication')->hasAttribute('checked'));
        $this->assertTrue($document->getElementById('notification-imports')->hasAttribute('checked'));
    }
}
