<?php

namespace App\Providers;

use App\Database\CachedPostgresConnection;
use App\Livewire\Pulse\RequestMonitor;
use App\Models\ImportStatus;
use App\Models\User;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
// Tambahan untuk Pagination & Auth Events
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Throwable;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        Connection::resolverFor('pgsql', function ($connection, $database, $prefix, $config) {
            return new CachedPostgresConnection($connection, $database, $prefix, $config);
        });

        Auth::resolved(function ($auth) {
            $auth->provider('cached-eloquent', function ($app, array $config) {
                return new class($app['hash'], $config['model']) extends EloquentUserProvider
                {
                    public function retrieveById($identifier): ?Authenticatable
                    {
                        $cacheKey = "user:{$identifier}:data";

                        try {
                            $cached = Cache::get($cacheKey);
                        } catch (Throwable) {
                            return parent::retrieveById($identifier);
                        }

                        if (is_array($cached)) {
                            $model = $this->createModel();
                            $keyName = $model->getKeyName();

                            if (isset($cached[$keyName])
                                && (string) $cached[$keyName] === (string) $identifier) {
                                $hasRememberToken = (bool) ($cached[User::AUTH_CACHE_HAS_REMEMBER_TOKEN] ?? false);
                                unset($cached[User::AUTH_CACHE_HAS_REMEMBER_TOKEN]);

                                $rememberTokenName = $model->getRememberTokenName();
                                if ($rememberTokenName !== '') {
                                    $cached[$rememberTokenName] = $hasRememberToken
                                        ? User::AUTH_CACHE_REMEMBER_TOKEN_PLACEHOLDER
                                        : null;
                                }

                                return $model->newFromBuilder($cached);
                            }
                        }

                        $user = parent::retrieveById($identifier);
                        if ($user) {
                            try {
                                Cache::put(
                                    $cacheKey,
                                    $user instanceof User
                                        ? $user->authCachePayload()
                                        : collect($user->getAttributes())
                                            ->except(['password', 'remember_token'])
                                            ->all(),
                                    max(1, (int) config('auth.cache.user_ttl_seconds', 28_800))
                                );
                            } catch (Throwable) {
                                // Authentication must keep working during a cache outage.
                            }
                        }

                        return $user;
                    }
                };
            });
        });
    }

    public function boot(): void
    {
        // =====================================================================
        // 1. SETUP PAGINATION & AUTO-STATUS ONLINE/OFFLINE
        // =====================================================================
        Paginator::useTailwind();

        // Saklar otomatis saat User Login
        Event::listen(function (Login $event) {
            $event->user->recordLogin();
        });

        // Saklar otomatis saat User Logout
        Event::listen(function (Logout $event) {
            if ($event->user) {
                // Gunakan Query Builder murni untuk menghindari cast PHP Object -> true
                User::where('id', $event->user->id)->update([
                    'is_online' => DB::raw('false'),
                ]);
            }
        });

        // =====================================================================
        // 2. SISTEM KEAMANAN & MONITORING BAWAAN
        // =====================================================================
        if (request()->header('X-Forwarded-Proto') === 'https') {
            URL::forceScheme('https');
        }

        Model::shouldBeStrict(! app()->isProduction());

        if (app()->isProduction()) {
            DB::whenQueryingForLongerThan(1000, function () {
                Log::warning('Slow query detected', [
                    'queries' => DB::getQueryLog(),
                ]);
            });
        }

        view()->composer(['superadmin.*'], function ($view) {
            if (Auth::check()) {
                $activeImport = ImportStatus::where('user_id', Auth::id())
                    ->whereIn('status', ['pending', 'processing'])
                    ->latest()
                    ->first();

                $view->with('activeImportId', $activeImport?->id);
            }
        });

        // =====================================================================
        // 3. GATE & PERMISSION SYSTEM
        // Superadmin bypass semua gate check. Pengecekan permission
        // module.action ditangani otomatis oleh Spatie via PermissionRegistrar.
        // =====================================================================
        Gate::before(function (User $user, string $ability) {
            if ($user->hasRole('superadmin')) {
                return true;
            }

            return null; // Biarkan Spatie dan Gate definition lain yang handle
        });

        Gate::define('viewPulse', function (User $user) {
            return $user->hasRole('superadmin');
        });

        if (app()->environment('local')) {
            DB::listen(function ($query) {
                if ($query->time > 100) {
                    Log::channel('daily')->warning('Slow query (>100ms)', [
                        'sql' => $query->sql,
                        'bindings' => $query->bindings,
                        'time' => $query->time.'ms',
                    ]);
                }
            });
        }

        Livewire::component('pulse.request-monitor', RequestMonitor::class);
    }
}
