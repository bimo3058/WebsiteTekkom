<?php
namespace Modules\EOffice\Models;

use App\Models\EoAuditLog;
use Illuminate\Database\Eloquent\SoftDeletes;

/** CERC-specific soft deletes leave the shared audit log available for auditing. */
class CercCoordinatorHistory extends EoAuditLog
{
    use SoftDeletes;

    public const DELETED_AT='cerc_deleted_at';

    public function scopeActivities($query)
    {
        return $query->where(function ($query) {
            $query->where('action', 'CERC_ACTIVITY')->orWhere(function ($query) {
                $query->where('action', 'UPDATE')->where('description', 'Pergantian Koordinator CERC');
            });
        });
    }
}
