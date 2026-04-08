<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use App\Livewire\Pulse\RequestMonitor;

// Tambahan untuk Pagination & Auth Events
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        Auth::resolved(function ($auth) {
            $auth->provider('cached-eloquent', function ($app, array $config) {
                return new class($app['hash'], $config['model']) extends EloquentUserProvider {
                    public function retrieveById($identifier): ?Authenticatable
                    {
                        $cacheKey = "user:{$identifier}:data";
                        $cached   = Cache::get($cacheKey);

                        if ($cached) {
                            $model = $this->createModel();
                            return $model->newFromBuilder($cached);
                        }

                        $user = parent::retrieveById($identifier);
                        if ($user) {
                            Cache::put($cacheKey, $user->withoutRelations()->toArray(), now()->addHours(8));
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
                \App\Models\User::where('id', $event->user->id)->update([
                    'is_online' => \Illuminate\Support\Facades\DB::raw('false')
                ]);
            }
        });


        // =====================================================================
        // 2. SISTEM KEAMANAN & MONITORING BAWAAN
        // =====================================================================
        if (request()->header('X-Forwarded-Proto') === 'https') {
            \Illuminate\Support\Facades\URL::forceScheme('https');
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
                $activeImport = \App\Models\ImportStatus::where('user_id', Auth::id())
                    ->whereIn('status', ['pending', 'processing'])
                    ->latest()
                    ->first();
                    
                $view->with('activeImportId', $activeImport?->id);
            }
        });


        // =====================================================================
        // 3. GATE & PERMISSION SYSTEM
        // FIX: Gate::before() — menghubungkan @can() / @cannot() di Blade
        // dengan sistem permission custom kita (hasPermissionTo).
        // =====================================================================
        Gate::before(function (User $user, string $ability) {
            // Superadmin bypass semua
            if ($user->hasRole('superadmin')) {
                return true;
            }

            // Hanya intercept permission format "module.action" (berisi titik)
            if (str_contains($ability, '.')) {
                return $user->hasPermissionTo($ability) ?: null;
            }

            return null; // Biarkan Gate definition lain yang handle
        });

        Gate::define('viewPulse', function (User $user) {
            return $user->hasRole('superadmin');
        });

        if (app()->environment('local')) {
            DB::listen(function ($query) {
                if ($query->time > 100) {
                    Log::channel('daily')->warning('Slow query (>100ms)', [
                        'sql'      => $query->sql,
                        'bindings' => $query->bindings,
                        'time'     => $query->time . 'ms',
                    ]);
                }
            });
        }
        
        Livewire::component('pulse.request-monitor', RequestMonitor::class);
    }
}