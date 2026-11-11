<?php

namespace Modules\BankSoal\Models\Komprehensif;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class CheatLog extends Model
{
    protected $table = 'bs_cheat_logs';

    protected $fillable = [
        'kompre_session_id', 'event_type', 'description', 'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function session()
    {
        return $this->belongsTo(KompreSession::class, 'kompre_session_id');
    }

    /**
     * Label manusiawi untuk tipe pelanggaran.
     */
    public static function labelFor(string $eventType): string
    {
        return match ($eventType) {
            'tab_switch' => 'Pindah tab / minimize',
            'window_blur' => 'Berpindah aplikasi/jendela',
            'fullscreen_exit' => 'Keluar dari layar penuh',
            'fullscreen_denied' => 'Layar penuh ditolak / diblokir',
            'duplicate_tab' => 'Membuka tab ganda',
            default => Str::headline($eventType),
        };
    }

    /**
     * Ubah kumpulan log menjadi data siap tampil (kronologis).
     *
     * @param  Collection<int, CheatLog>  $logs
     * @return array<int, array{time: string, label: string, description: ?string}>
     */
    public static function detailsFrom(Collection $logs): array
    {
        return $logs
            ->sortBy(fn (CheatLog $log) => [
                $log->created_at?->timestamp ?? 0,
                $log->id ?? 0,
            ])
            ->map(fn (CheatLog $log) => [
                'time' => $log->created_at?->format('H:i:s') ?? '-',
                'label' => self::labelFor($log->event_type),
                'description' => $log->description,
            ])
            ->values()
            ->all();
    }

    public static function latestFrom(Collection $logs): ?array
    {
        $details = self::detailsFrom($logs);

        return $details === [] ? null : $details[array_key_last($details)];
    }
}
