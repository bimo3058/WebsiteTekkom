<?php

namespace Modules\Capstone\Providers;

use DistortedFusion\BladeComponents\BladeComponents;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\Compilers\BladeCompiler;
use Modules\Capstone\Console\Commands\PurgeEmptyGroups;
use Modules\Capstone\Console\Commands\RepairDuplicateLeaders;
use Modules\Capstone\Console\Commands\SyncEofficeRooms;
use Modules\Capstone\Models\Bid;
use Modules\Capstone\Models\ExpoEvent;
use Modules\Capstone\Models\Group;
use Modules\Capstone\Models\GroupMember;
use Modules\Capstone\Models\SeminarSchedule;
use Modules\Capstone\Models\TaDefenseSchedule;
use Modules\Capstone\Observers\BidObserver;
use Modules\Capstone\Observers\ExpoEventObserver;
use Modules\Capstone\Observers\GroupMemberObserver;
use Modules\Capstone\Observers\GroupObserver;
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
use Modules\Capstone\Themes\CapstoneTheme;
use Modules\Capstone\View\Composers\CapstoneShellComposer;

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
        Group::observe(GroupObserver::class);
        GroupMember::observe(GroupMemberObserver::class);
        Bid::observe(BidObserver::class);
        SeminarSchedule::observe(SeminarScheduleObserver::class);
        TaDefenseSchedule::observe(TaDefenseScheduleObserver::class);
        ExpoEvent::observe(ExpoEventObserver::class);

        if ($this->app->runningInConsole()) {
            $this->commands([
                RepairDuplicateLeaders::class,
                PurgeEmptyGroups::class,
                SyncEofficeRooms::class,
            ]);
        }
        $this->loadViewsFrom(module_path('Capstone', 'resources/views'), 'capstone');
        Blade::anonymousComponentPath(module_path('Capstone', 'resources/views/components'), 'capstone');

        if (! $this->app->runningInConsole()) {
            $this->callAfterResolving(BladeCompiler::class, function (): void {
                BladeComponents::setDefaultTheme(CapstoneTheme::class);
            });
        }

        View::composer(
            ['capstone::layouts.*', 'capstone::components.*'],
            CapstoneShellComposer::class
        );
        $this->loadMigrationsFrom(module_path('Capstone', 'database/migrations'));
    }
}
