<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // supaya Laravel bisa resolve saat bootstrap auth
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
        // Strict mode di development — deteksi N+1 dan lazy loading
        Model::shouldBeStrict(! app()->isProduction());

        // Log slow queries di production
        if (app()->isProduction()) {
            DB::whenQueryingForLongerThan(1000, function () {
                Log::warning('Slow query detected', [
                    'queries' => DB::getQueryLog(),
                ]);
            });
        }

        // Log query lambat di local
        if (app()->environment('local')) {
            DB::listen(function ($query) {
                if ($query->time > 100) { // turunkan threshold: 200ms → 100ms
                    Log::channel('daily')->warning('Slow query (>100ms)', [
                        'sql'      => $query->sql,
                        'bindings' => $query->bindings,
                        'time'     => $query->time . 'ms',
                    ]);
                }
            });
        }
    }
}