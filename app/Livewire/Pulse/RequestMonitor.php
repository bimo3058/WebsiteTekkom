<?php

// ================================================================
// TARUH DI: app/Livewire/Pulse/RequestMonitor.php
// (bukan App\Pulse\Cards — supaya Livewire auto-discover)
// ================================================================

namespace App\Livewire\Pulse;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Laravel\Pulse\Livewire\Card;
use Livewire\Attributes\Lazy;

#[Lazy]
class RequestMonitor extends Card
{
    public function render(): \Illuminate\View\View
    {
        $requests = $this->getRequests();

        return View::make('livewire.pulse.request-monitor', [
            'requests' => $requests,
        ]);
    }

    protected function getRequests(): \Illuminate\Support\Collection
    {
        try {
            return DB::table('pulse_entries')
                ->where('type', 'request_monitor')
                ->orderByDesc('timestamp')
                ->limit(50)
                ->get()
                ->map(function ($entry) {
                    $data = json_decode($entry->key, true);
                    return (object) [
                        'method'   => $data['method']   ?? 'GET',
                        'path'     => $data['path']     ?? '/',
                        'status'   => $data['status']   ?? 200,
                        'duration' => $data['duration'] ?? 0,
                        'user'     => $data['user']     ?? 'Guest',
                        'ip'       => $data['ip']       ?? '-',
                        'happened' => \Illuminate\Support\Carbon::createFromTimestamp($entry->timestamp)->diffForHumans(),
                    ];
                });
        } catch (\Exception $e) {
            return collect();
        }
    }
}