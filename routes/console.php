<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('app:set-users-offline')->everyMinute();
Schedule::command('app:prune-notifications')->daily();
// Auto-approve blind review items yang sudah melewati deadline 2 hari
Schedule::command('banksoal:auto-approve-blind-review')->daily();
