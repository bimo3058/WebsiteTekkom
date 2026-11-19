<?php
namespace Modules\EOffice\Services\ManajemenPraktikum;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Modules\EOffice\Models\{Peminjaman,MrJadwalInternal,Ruangan};

class RoomBookingGuard
{
    public static function lockRooms(array $ids): void
    {
        Ruangan::whereIn('id',array_unique(array_filter($ids)))->orderBy('id')->lockForUpdate()->get();
    }
    public function conflict(int $room,string $date,string $start,string $end,array $exclude=[]): ?string
    {
        $start=\Illuminate\Support\Carbon::parse($start)->format('H:i:s');$end=\Illuminate\Support\Carbon::parse($end)->format('H:i:s');
        if(Peminjaman::where('ruangan_id',$room)->whereDate('tanggal_pinjam',$date)->where('status','disetujui')->whereNotIn('id',$exclude)->whereTime('jam_mulai','<',$end)->whereTime('jam_selesai','>',$start)->exists())return 'Bentrok dengan peminjaman yang sudah disetujui.';
        $day=\Illuminate\Support\Carbon::parse($date)->dayOfWeekIso;
        $internal=MrJadwalInternal::where('ruangan_id',$room)->whereTime('jam_mulai','<',$end)->whereTime('jam_selesai','>',$start)->where(function($q)use($date,$day){
            $q->where(fn($q)=>$q->where('tipe_jadwal','spesifik')->whereDate('tanggal_spesifik',$date))->orWhere(function($q)use($date,$day){$q->where('tipe_jadwal','rutin')->where('hari',$day)->where(fn($q)=>$q->whereNull('tgl_mulai_efektif')->orWhereDate('tgl_mulai_efektif','<=',$date))->where(fn($q)=>$q->whereNull('tgl_selesai_efektif')->orWhereDate('tgl_selesai_efektif','>=',$date));});
        })->exists();
        return $internal?'Bentrok dengan jadwal internal/akademik ruangan.':null;
    }
}
