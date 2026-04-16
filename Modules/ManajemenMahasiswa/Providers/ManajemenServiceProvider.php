<?php

namespace Modules\ManajemenMahasiswa\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\ManajemenMahasiswa\Services\AlumniService;
use Modules\ManajemenMahasiswa\Services\DashboardAnalitikService;
use Modules\ManajemenMahasiswa\Services\ForumService;
use Modules\ManajemenMahasiswa\Services\KegiatanService;
use Modules\ManajemenMahasiswa\Services\KemahasiswaanService;
use Modules\ManajemenMahasiswa\Services\PengurusHimaskomService;
use Modules\ManajemenMahasiswa\Services\PengumumanService;
use Modules\ManajemenMahasiswa\Services\RepoMulmedService;

class ManajemenServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Semua service di-bind sebagai singleton agar instance hanya dibuat sekali
        // per request cycle — efisien untuk traffic tinggi.
        $this->app->singleton(KemahasiswaanService::class);
        $this->app->singleton(AlumniService::class);
        $this->app->singleton(KegiatanService::class);
        $this->app->singleton(PengumumanService::class);
        $this->app->singleton(ForumService::class);
        $this->app->singleton(RepoMulmedService::class);
        $this->app->singleton(PengurusHimaskomService::class);
        $this->app->singleton(DashboardAnalitikService::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR . 'migrations');
        $this->loadRoutesFrom(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'routes' . DIRECTORY_SEPARATOR . 'api.php');
        $this->loadRoutesFrom(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'routes' . DIRECTORY_SEPARATOR . 'web.php');
    }
}