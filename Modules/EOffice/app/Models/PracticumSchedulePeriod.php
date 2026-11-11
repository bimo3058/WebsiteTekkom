<?php
namespace Modules\EOffice\Models;
use Illuminate\Database\Eloquent\{Model,Concerns\HasUuids};
class PracticumSchedulePeriod extends Model
{
    use HasUuids;
    protected $table='eo_prak_schedule_periods';
    protected $guarded=[];
    protected $casts=['starts_on'=>'date','ends_on'=>'date','version'=>'integer'];
}
