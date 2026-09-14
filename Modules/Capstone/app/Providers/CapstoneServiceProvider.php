<?php

namespace Modules\Capstone\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Capstone\Services\BiddingService;
use Modules\Capstone\Services\ExpoEligibilityService;
use Modules\Capstone\Services\ExpoService;
use Modules\Capstone\Services\FinalizationService;
use Modules\Capstone\Services\GroupStateMachine;
use Modules\Capstone\Services\NotificationService;
use Modules\Capstone\Services\SchedulingService;

class CapstoneServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(module_path('Capstone', 'config/config.php'), 'capstone');
        $this->app->singleton(BiddingService::class);
        $this->app->singleton(ExpoEligibilityService::class);
        $this->app->singleton(ExpoService::class);
        $this->app->singleton(FinalizationService::class);
        $this->app->singleton(GroupStateMachine::class);
        $this->app->singleton(NotificationService::class);
        $this->app->singleton(SchedulingService::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(module_path('Capstone', 'database/migrations'));
    }
}
