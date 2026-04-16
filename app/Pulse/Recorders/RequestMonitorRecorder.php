<?php

namespace App\Pulse\Recorders;

use Illuminate\Foundation\Application;
use Laravel\Pulse\Pulse;

class RequestMonitorRecorder
{
    public string $listen = 'Illuminate\Foundation\Http\Events\RequestHandled';

    /**
     * Pattern request yang DIABAIKAN (tidak direkam).
     * Hanya aktivitas user bermakna yang masuk.
     */
    protected array $ignore = [
        // ── Package internal / debug tools ──
        '_boost*',
        '_debugbar*',
        'telescope*',
        'horizon*',

        // ── Pulse & Livewire internal ──
        'pulse*',
        'livewire*',
        'livewire-d7c49144*',   // Livewire update endpoint

        // ── Static assets ──
        '*.js',
        '*.css',
        '*.map',
        '*.ico',
        '*.png',
        '*.jpg',
        '*.jpeg',
        '*.gif',
        '*.svg',
        '*.webp',
        '*.woff',
        '*.woff2',
        '*.ttf',
        '*.eot',

        // ── Health check / uptime monitoring ──
        'health*',
        'up',
        'ping',
    ];

    public function __construct(
        protected Pulse $pulse,
        protected Application $app,
    ) {}

    public function record(\Illuminate\Foundation\Http\Events\RequestHandled $event): void
    {
        $request  = $event->request;
        $response = $event->response;

        // Terapkan filter ignore
        foreach ($this->ignore as $pattern) {
            if ($request->is($pattern)) {
                return;
            }
        }

        // Hitung durasi request
        $startTime = defined('LARAVEL_START') ? LARAVEL_START : microtime(true);
        $duration  = (int) round((microtime(true) - $startTime) * 1000);

        // Ambil nama user — tampilkan role jika ada
        $user = $request->user();
        $userName = $user
            ? $user->name . ($user->roles?->first() ? ' (' . $user->roles->first()->name . ')' : '')
            : 'Guest';

        $this->pulse->record(
            type: 'request_monitor',
            key: json_encode([
                'method'   => $request->method(),
                'path'     => '/' . ltrim($request->path(), '/'),
                'status'   => $response->getStatusCode(),
                'duration' => $duration,
                'user'     => $userName,
                'ip'       => $request->ip(),
            ]),
            value: $duration,
        )->max()->count()->onlyRememberForever();
    }
}