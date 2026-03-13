<?php

namespace Modules\BankSoal\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\BankSoal\Services\KompreService;
use Modules\BankSoal\Services\MataKuliahService;
use Modules\BankSoal\Services\PertanyaanService;
use Modules\BankSoal\Services\RpsService;

class BankSoalServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(MataKuliahService::class);
        $this->app->singleton(PertanyaanService::class);
        $this->app->singleton(RpsService::class);
        $this->app->singleton(KompreService::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');
        $this->loadRoutesFrom(__DIR__ . '/routes/api.php');
        $this->loadRoutesFrom(__DIR__ . '/routes/web.php');
    }
}