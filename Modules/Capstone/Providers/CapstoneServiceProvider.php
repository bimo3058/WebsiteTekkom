<?php

namespace Modules\Capstone\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Capstone\Services\BidService;
use Modules\Capstone\Services\GroupService;
use Modules\Capstone\Services\NotificationService;
use Modules\Capstone\Services\PeriodService;
use Modules\Capstone\Services\TitleService;

class CapstoneServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(BidService::class);
        $this->app->singleton(GroupService::class);
        $this->app->singleton(NotificationService::class);
        $this->app->singleton(PeriodService::class);
        $this->app->singleton(TitleService::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');
        $this->loadRoutesFrom(__DIR__ . '/routes/api.php');
        $this->loadRoutesFrom(__DIR__ . '/routes/web.php');
    }
}