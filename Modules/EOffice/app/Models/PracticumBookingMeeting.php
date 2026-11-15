<?php
namespace Modules\EOffice\Models;
use Illuminate\Database\Eloquent\{Model,Concerns\HasUuids};
class PracticumBookingMeeting extends Model
{
    use HasUuids;
    protected $table='eo_prak_booking_meetings';
    protected $guarded=[];
    protected $attributes=['state'=>'draft','participants'=>0];
    protected $casts=['date'=>'date'];
    public function request(){return $this->belongsTo(PracticumBookingRequest::class,'request_id');}
    public function praktikum(){return $this->belongsTo(Praktikum::class);}
    public function room(){return $this->belongsTo(Ruangan::class,'room_id');}
    public function booking(){return $this->belongsTo(Peminjaman::class,'booking_id');}
    public function scopeOfficial($query)
    {
        return $query->where(function ($sessions) {
            $sessions->whereHas('booking', fn ($booking) => $booking
                ->where('status', 'disetujui')->where('source', 'praktikum')
                ->whereRaw('CAST(eo_mr_peminjamans.source_id AS TEXT) = CAST(eo_prak_booking_meetings.id AS TEXT)'))
                ->orWhere(function ($legacy) {
                    $legacy->where('state', 'approved')
                        ->whereHas('request', fn ($request) => $request->whereIn('status', ['approved', 'partial', 'pending'])->whereNotNull('decided_at')->whereNotNull('decided_by'))
                        ->whereHas('booking', fn ($booking) => $booking->where('status', 'disetujui')->where('source', 'praktikum')->whereNull('source_id')
                            ->whereColumn('eo_mr_peminjamans.ruangan_id', 'eo_prak_booking_meetings.room_id')
                            ->whereRaw('DATE(eo_mr_peminjamans.tanggal_pinjam) = DATE(eo_prak_booking_meetings.date)')
                            ->whereRaw('SUBSTR(CAST(eo_mr_peminjamans.jam_mulai AS TEXT), 1, 5) = SUBSTR(CAST(eo_prak_booking_meetings.starts_at AS TEXT), 1, 5)')
                            ->whereRaw('SUBSTR(CAST(eo_mr_peminjamans.jam_selesai AS TEXT), 1, 5) = SUBSTR(CAST(eo_prak_booking_meetings.ends_at AS TEXT), 1, 5)'));
                });
        });
    }
}
