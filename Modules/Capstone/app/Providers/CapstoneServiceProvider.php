<?php

namespace Modules\Capstone\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Capstone\Models\ExpoEvent;
use Modules\Capstone\Models\SeminarSchedule;
use Modules\Capstone\Models\TaDefenseSchedule;
use Modules\Capstone\Observers\ExpoEventObserver;
use Modules\Capstone\Observers\SeminarScheduleObserver;
use Modules\Capstone\Observers\TaDefenseScheduleObserver;
use Modules\Capstone\Services\BiddingService;
use Modules\Capstone\Services\EofficeAvailabilityService;
use Modules\Capstone\Services\EofficeBookingService;
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
        $this->app->singleton(EofficeAvailabilityService::class);
        $this->app->singleton(EofficeBookingService::class);
    }

    public function boot(): void
    {
        SeminarSchedule::observe(SeminarScheduleObserver::class);
        TaDefenseSchedule::observe(TaDefenseScheduleObserver::class);
        ExpoEvent::observe(ExpoEventObserver::class);

        if ($this->app->runningInConsole()) {
            $this->commands([
                \Modules\Capstone\Console\Commands\RepairDuplicateLeaders::class,
                \Modules\Capstone\Console\Commands\PurgeEmptyGroups::class,
                \Modules\Capstone\Console\Commands\SyncEofficeRooms::class,
            ]);
        }
        $this->loadViewsFrom(module_path('Capstone', 'resources/views'), 'capstone');
        \Illuminate\Support\Facades\Blade::anonymousComponentPath(module_path('Capstone', 'resources/views/components'), 'capstone');
        $this->loadMigrationsFrom(module_path('Capstone', 'database/migrations'));
    }
}
