<?php

namespace App\Services;

use App\Models\BsAuditLog;
use App\Models\EoAuditLog;
use App\Models\MkAuditLog;
use Illuminate\Support\Facades\Cache;
use Modules\Capstone\Models\AuditLog as CapstoneAuditLog;

class ModuleAccessOverview
{
    public const MODELS = [
        'bank_soal' => BsAuditLog::class,
        'capstone' => CapstoneAuditLog::class,
        'eoffice' => EoAuditLog::class,
        'manajemen_mahasiswa' => MkAuditLog::class,
    ];

    public function forDashboard(): array
    {
        $today = today();

        return Cache::remember('sa:module_access:'.$today->toDateString(), 60, function () use ($today) {
            $start = $today->copy()->subDays(13);
            $result = [];
            foreach (self::MODELS as $module => $model) {
                // Read each application's existing audit trail, including logs not dual-written globally.
                $query = $model::query()->whereNotNull('user_id')
                    ->where('created_at', '>=', $start)
                    ->where('created_at', '<', $today->copy()->addDay());
                $records = (clone $query)
                    ->selectRaw('DATE(created_at) as date, COUNT(DISTINCT user_id) as users, COUNT(*) as events')
                    ->groupBy('date')->get()->keyBy('date');
                $totalUsers = (clone $query)->distinct()->count('user_id');
                $days = [];
                for ($i = 0; $i < 14; $i++) {
                    $date = $start->copy()->addDays($i);
                    $days[] = [
                        'date' => $date->toDateString(),
                        'label' => $date->translatedFormat('d M'),
                        'users' => (int) ($records->get($date->toDateString())?->users ?? 0),
                        'events' => (int) ($records->get($date->toDateString())?->events ?? 0),
                    ];
                }

                $result[$module] = [
                    'days' => $days,
                    'total_users' => $totalUsers,
                    'total_events' => array_sum(array_column($days, 'events')),
                    'today_users' => $days[13]['users'],
                    'peak_users' => max(array_column($days, 'users')),
                    'date_from' => $start->toDateString(),
                    'date_to' => $today->toDateString(),
                ];
            }

            return $result;
        });
    }
}
