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
use Illuminate\Support\Facades\URL;
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
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Custom Provider untuk Caching User agar aplikasi lebih cepat
        Auth::resolved(function ($auth) {
            $auth->provider('cached-eloquent', function ($app, array $config) {
                return new class ($app['hash'], $config['model']) extends EloquentUserProvider {
                    public function retrieveById($identifier): ?Authenticatable
                    {
                        $cacheKey = "user:{$identifier}:data";
                        $cached = Cache::get($cacheKey);

                        if ($cached) {
                            $model = $this->createModel();
                            $user = $model->newFromBuilder($cached);

                            if (!isset($cached['password'])) {
                                return parent::retrieveById($identifier);
                            }

                            return $user;
                        }

                        $user = parent::retrieveById($identifier);
                        if ($user) {
                            Cache::put($cacheKey, $user->makeVisible(['password', 'remember_token'])
                                ->withoutRelations()->toArray(), now()->addHours(8));
                        }

                        return $user;
                    }
                };
            });
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 1. SETUP PAGINATION & AUTO-STATUS ONLINE/OFFLINE
        Paginator::useTailwind();

        // Update status online saat Login
        Event::listen(function (Login $event) {
            $event->user->recordLogin();
        });

        // Update status offline saat Logout
        Event::listen(function (Logout $event) {
            if ($event->user) {
                \App\Models\User::where('id', $event->user->id)->update([
                    'is_online' => DB::raw('false')
                ]);
            }
        });

        // 2. SISTEM KEAMANAN & MONITORING
        if (request()->header('X-Forwarded-Proto') === 'https') {
            URL::forceScheme('https');
        }

        // Ketat di mode development, santai di mode production
        Model::shouldBeStrict(!app()->isProduction());

        if (app()->isProduction()) {
            DB::whenQueryingForLongerThan(1000, function () {
                Log::warning('Slow query detected', [
                    'queries' => DB::getQueryLog(),
                ]);
            });
        }

        // View Composer untuk status import (khusus Superadmin)
        view()->composer(['superadmin.*'], function ($view) {
            if (Auth::check()) {
                $activeImport = \App\Models\ImportStatus::where('user_id', Auth::id())
                    ->whereIn('status', ['pending', 'processing'])
                    ->latest()
                    ->first();

                $view->with('activeImportId', $activeImport?->id);
            }
        });

        // 3. GATE & PERMISSION SYSTEM
        // Menghubungkan @can() di Blade dengan sistem hasPermissionTo di Model User
        Gate::before(function (User $user, string $ability) {
            if ($user->hasRole('superadmin')) {
                return true;
            }

            if (str_contains($ability, '.')) {
                return $user->hasPermissionTo($ability) ?: null;
            }

            return null;
        });

        Gate::define('viewPulse', function (User $user) {
            return $user->hasRole('superadmin');
        });

        // Logging Slow Query di Local
        if (app()->environment('local')) {
            DB::listen(function ($query) {
                if ($query->time > 100) {
                    Log::channel('daily')->warning('Slow query (>100ms)', [
                        'sql' => $query->sql,
                        'bindings' => $query->bindings,
                        'time' => $query->time . 'ms',
                    ]);
                }
            });
        }

        if (class_exists(Livewire::class)) {
            Livewire::component('pulse.request-monitor', RequestMonitor::class);
        }
    }
}