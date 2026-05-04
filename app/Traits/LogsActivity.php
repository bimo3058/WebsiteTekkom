<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

/**
 * Trait LogsActivity
 *
 * Attach ke model mana saja yang butuh audit trail di ACTIVITY_LOG.
 * Secara otomatis mencatat created/updated/deleted events.
 *
 * Cara pakai:
 *   use App\Traits\LogsActivity;
 *   protected static array $logAttributes = ['nama', 'email', 'status'];
 */
trait LogsActivity
{
    /**
     * Boot trait — register model event listeners.
     */
    protected static function bootLogsActivity(): void
    {
        static::created(function ($model) {
            $model->logActivity('create', null, $model->getLogAttributes());
        });

        static::updated(function ($model) {
            // Hanya log field yang ada di $logAttributes
            $dirty    = $model->getDirty();
            $tracked  = static::getTrackedAttributes();
            $relevant = array_intersect_key($dirty, array_flip($tracked));

            if (! empty($relevant)) {
                $oldValues = array_intersect_key($model->getOriginal(), array_flip($tracked));
                $model->logActivity('update', $oldValues, $relevant);
            }
        });

        static::deleted(function ($model) {
            // Soft delete → log 'delete', hard delete → log 'force_delete'
            $action = method_exists($model, 'trashed') && $model->trashed()
                ? 'delete'
                : 'force_delete';

            $model->logActivity($action, $model->getLogAttributes(), null);
        });
    }

    /**
     * Tulis 1 baris ke activity_logs.
     */
    protected function logActivity(string $action, ?array $oldValues, ?array $newValues): void
    {
        try {
            // Guard: kalau tidak ada auth (misal CLI/seeder), skip
            $penggunaId = Auth::id();
            if (! $penggunaId) {
                return;
            }

            ActivityLog::create([
                'pengguna_id' => $penggunaId,
                'action'      => $action,
                'model'       => class_basename($this),
                'model_id'    => $this->getKey(),
                'old_values'  => $oldValues,
                'new_values'  => $newValues,
                'ip_address'  => Request::ip(),
                'user_agent'  => Request::header('User-Agent'),
            ]);
        } catch (\Throwable $e) {
            // Jangan crash app hanya karena gagal log
            \Illuminate\Support\Facades\Log::error('LogsActivity failed: ' . $e->getMessage());
        }
    }

    /**
     * Ambil nilai field yang di-track untuk saat ini.
     */
    protected function getLogAttributes(): array
    {
        $tracked = static::getTrackedAttributes();
        return array_intersect_key($this->getAttributes(), array_flip($tracked));
    }

    /**
     * Daftar attribute yang di-log. Override via $logAttributes di model.
     */
    protected static function getTrackedAttributes(): array
    {
        return static::$logAttributes ?? [];
    }
}
