<?php
namespace Modules\EOffice\Models;
use Illuminate\Database\Eloquent\{Model,Concerns\HasUuids};
class PracticumBookingRequest extends Model
{
    use HasUuids;
    protected $table='eo_prak_booking_requests';
    protected $guarded=[];
    protected $attributes=['kind'=>'new','status'=>'draft','version'=>0,'history'=>'[]'];
    protected $casts=['plan'=>'array','history'=>'array','version'=>'integer','submitted_at'=>'datetime','decided_at'=>'datetime'];
    public function meetings(){return $this->hasMany(PracticumBookingMeeting::class,'request_id')->orderBy('date')->orderBy('starts_at')->orderBy('shift');}
    public function period(){return $this->belongsTo(PracticumSchedulePeriod::class,'period_id');}
    public function requester(){return $this->belongsTo(\App\Models\User::class,'requested_by')->withTrashed();}
}
