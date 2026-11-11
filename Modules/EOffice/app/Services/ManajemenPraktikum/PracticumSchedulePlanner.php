<?php
namespace Modules\EOffice\Services\ManajemenPraktikum;

use Illuminate\Support\Carbon;
use Modules\EOffice\Models\{Praktikum,Modul,DaftarPraktikan,PracticumSchedulePeriod,PracticumBookingMeeting,Peminjaman,Ruangan,Pengaturan,TanggalLibur};

class PracticumSchedulePlanner
{
    public function settings(): array
    {
        $settings=Pengaturan::pluck('value','key');
        return ['open'=>$settings['jam_buka']??'08:00','close'=>$settings['jam_tutup']??'16:00','weekends'=>filter_var($settings['buka_akhir_pekan']??false,FILTER_VALIDATE_BOOLEAN),'lead'=>(int)($settings['batas_h_min_booking']??0)];
    }

    public function generate(PracticumSchedulePeriod $period,array $plans,array $overrides=[],array $excludedMeetings=[]): array
    {
        $rows=[];$errors=[];$notes=[];$settings=$this->settings();
        $holidays=TanggalLibur::pluck('tanggal')->map(fn($d)=>Carbon::parse($d)->toDateString())->all();
        foreach($plans as $plan){
            $p=Praktikum::find($plan['praktikum_id']??'');
            if(!$p || !$p->is_active){$errors[]='Pilih praktikum yang masih aktif.';continue;}
            $period=PracticumSchedulePeriod::where('tahun_ajaran',(string)$p->getRawOriginal('tahun_ajaran'))->whereRaw('LOWER(semester) = ?',[mb_strtolower($p->semester)])->first();
            if(!$period){$errors[]=$p->nama.': pengaturan periode pelaksanaan belum tersedia.';continue;}
            $roster=DaftarPraktikan::where('praktikum_id',$p->id)->get(['id','shift','kelompok']);
            if($roster->isEmpty() || $roster->contains(fn($r)=>blank($r->shift)||blank($r->kelompok)) || $p->draft_pembagian!==null){$errors[]=$p->nama.': koor perlu menyelesaikan dan menyimpan pembagian kelompok serta shift.';continue;}
            if(!$p->koor_id){$errors[]=$p->nama.': koordinator praktikum belum ditunjuk.';continue;}
            $shifts=$roster->groupBy(fn($r)=>(string)$r->shift);$slots=collect($plan['slots']??[]);
            $modules=Modul::where('praktikum_id',$p->id)->orderBy('urutan')->orderBy('id')->get();
            if(isset($plan['modules']))$modules=$modules->filter(fn($m)=>in_array((string)$m->getRawOriginal('id'),array_map('strval',$plan['modules']),true));
            if($modules->isEmpty()){$errors[]=$p->nama.': pilih minimal satu modul yang telah tersedia.';continue;}
            $occupied=$this->occupiedMeetings()->where('praktikum_id',$p->id)->get()->mapWithKeys(fn($m)=>[(string)$m->module_id.'|'.$m->shift=>true]);
            if($slots->count()!==$shifts->count() || $slots->pluck('shift')->map(fn($s)=>(string)$s)->sort()->values()->all()!==$shifts->keys()->map(fn($s)=>(string)$s)->sort()->values()->all()){$errors[]=$p->nama.': atur tepat satu slot untuk setiap shift berisi praktikan.';continue;}
            $valid=true;$completeSlots=[];
            foreach($slots as $slot){
                // An existing booking belongs to one module/shift occurrence, not the whole shift.
                if($modules->every(fn($module)=>isset($occupied[(string)$module->getRawOriginal('id').'|'.$slot['shift']])))continue;
                if(!in_array((int)($slot['weekday']??0),range(1,7),true) || empty($slot['room_id']) || !$this->validTime($slot['starts_at']??'') || !$this->validTime($slot['ends_at']??'')){$valid=false;continue;}
                if(!$settings['weekends'] && (int)$slot['weekday']>5){$errors[]=$p->nama.': peminjaman akhir pekan tidak diizinkan pengaturan ruang.';$valid=false;continue;}
                $completeSlots[]=$slot;
            }
            if(!$valid)$errors[]=$p->nama.': lengkapi hari, ruangan, dan jam setiap shift.';
            $slots=collect($completeSlots);if($slots->isEmpty())continue;
            try{$week=Carbon::createFromFormat('!Y-m-d',(string)($plan['week_start']??''))->startOfWeek();}catch(\Throwable){$errors[]=$p->nama.': tanggal awal minggu tidak valid.';continue;}
            foreach($modules as $module){
                $skipped=0;
                while($slots->contains(fn($s)=>in_array($week->copy()->addDays((int)$s['weekday']-1)->toDateString(),$holidays,true))){
                    $week->addWeek();$skipped++;
                    if($week->gt($period->ends_on))break;
                }
                if($week->gt($period->ends_on)){$errors[]=$p->nama.': pergeseran libur melewati akhir periode. Sesuaikan rancangan.';break;}
                if($skipped)$notes[]=$p->nama.' · '.$module->nama.': seluruh shift digeser '.$skipped.' minggu karena hari libur.';
                foreach($slots as $slot){
                    if(isset($occupied[(string)$module->getRawOriginal('id').'|'.$slot['shift']]))continue;
                    $key=$p->id.'|'.$module->getRawOriginal('id').'|'.$slot['shift'];
                    $row=['praktikum_id'=>$p->id,'module_id'=>(string)$module->getRawOriginal('id'),'module_name'=>$module->nama,'module_order'=>$module->urutan,'shift'=>(string)$slot['shift'],'date'=>$week->copy()->addDays((int)$slot['weekday']-1)->toDateString(),'starts_at'=>$slot['starts_at'],'ends_at'=>$slot['ends_at'],'room_id'=>(int)$slot['room_id'],'participants'=>$shifts->get((string)$slot['shift'])->count()];
                    $row['note']=filled($slot['note']??null)?trim($slot['note']):null;
                    foreach(['date','starts_at','ends_at','room_id','note'] as $field)if(array_key_exists($field,$overrides[$key]??[]))$row[$field]=$overrides[$key][$field];
                    if(in_array($key,$excludedMeetings,true))continue;
                    $rows[]=$row;
                    if(count($rows)>5000){$errors[]='Paket terlalu besar. Bagi menjadi beberapa pengajuan.';break 3;}
                }
                $week->addWeek();
            }
        }
        return ['meetings'=>$rows,'errors'=>array_values(array_unique($errors)),'notes'=>$notes];
    }

    public function validTime(string $value): bool{return (bool)preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d(?::[0-5]\d)?$/D',$value);}

    public function inspect(PracticumSchedulePeriod $period,array $rows,array $exclude=[],?string $requestId=null,bool $cancel=false,bool $approval=false): array
    {
        $errors=[];$warnings=[];$settings=$this->settings();$rooms=Ruangan::all()->keyBy('id');$guard=app(RoomBookingGuard::class);$min=Carbon::today()->addDays($approval?0:$settings['lead'])->toDateString();$seen=[];
        foreach($rows as $i=>$r){
            $label=($r['module_name']??'Pertemuan').' · Shift '.($r['shift']??'?').' · '.($r['date']??'');
            try{$date=Carbon::createFromFormat('!Y-m-d',(string)$r['date']);}catch(\Throwable){$errors[]=$label.': tanggal tidak valid.';continue;}
            $dateString=$date->toDateString();
            if($cancel){if($dateString<Carbon::today()->toDateString())$errors[]=$label.': pertemuan sudah berlalu.';continue;}
            $p=Praktikum::find($r['praktikum_id']);
            if(!$p || !$p->is_active){$errors[]=$label.': praktikum tidak aktif.';continue;}
            if(!$this->validTime((string)$r['starts_at']) || !$this->validTime((string)$r['ends_at']) || substr($r['starts_at'],0,5)>=substr($r['ends_at'],0,5)){$errors[]=$label.': jam mulai/selesai tidak valid.';continue;}
            $rowPeriod=PracticumSchedulePeriod::where('tahun_ajaran',(string)$p->getRawOriginal('tahun_ajaran'))->whereRaw('LOWER(semester) = ?',[mb_strtolower($p->semester)])->first();
            if(!$rowPeriod || $dateString<$min || $date->lt($rowPeriod->starts_on) || $date->gt($rowPeriod->ends_on))$errors[]=$label.': tanggal di luar periode atau tidak memenuhi batas minimum pengajuan.';
            if($dateString===Carbon::today()->toDateString() && substr($r['starts_at'],0,5)<=now()->format('H:i'))$errors[]=$label.': jam pertemuan sudah dimulai atau berlalu.';
            if((!$settings['weekends'] && $date->isWeekend()) || TanggalLibur::whereDate('tanggal',$dateString)->exists())$errors[]=$label.': hari peminjaman tidak tersedia.';
            if(substr($r['starts_at'],0,5)<substr($settings['open'],0,5) || substr($r['ends_at'],0,5)>substr($settings['close'],0,5))$errors[]=$label.': di luar jam operasional ruang.';
            $room=$rooms->get($r['room_id']);
            $roster=DaftarPraktikan::where('praktikum_id',$p->id)->get(['id','shift','kelompok']);
            $count=$roster->filter(fn($s)=>(string)$s->shift===(string)$r['shift'])->count();
            if(!$count || $roster->contains(fn($s)=>blank($s->shift)||blank($s->kelompok)) || $p->draft_pembagian!==null)$errors[]=$label.': pembagian praktikan belum final atau shift kosong.';
            if(!$p->koor_id || !Modul::where('praktikum_id',$p->id)->whereKey($r['module_id'])->exists())$errors[]=$label.': koordinator/modul tidak tersedia.';
            if(!$room || !$room->is_active || (int)$room->kapasitas<$count)$errors[]=$label.': ruang tidak aktif atau kapasitas tidak mencukupi.';
            if($room && !in_array($room->kategori,['Kelas','Laboratorium'],true))$errors[]=$label.': praktikum hanya boleh menggunakan ruang Kelas atau Laboratorium.';
            $conflict=$guard->conflict((int)$r['room_id'],$dateString,$r['starts_at'],$r['ends_at'],$exclude);
            if($conflict)$errors[]=$label.': '.$conflict;
            $identity=$r['praktikum_id'].'|'.$r['module_id'].'|'.$r['shift'];
            if(isset($seen[$identity]))$errors[]=$label.': modul/shift tercantum lebih dari sekali.';$seen[$identity]=true;
            $duplicate=$this->occupiedMeetings($requestId)->where('praktikum_id',$r['praktikum_id'])->where('module_id',$r['module_id'])->where('shift',$r['shift'])->when($exclude,fn($q)=>$q->whereNotIn('booking_id',$exclude))->exists();
            if($duplicate)$errors[]=$label.': modul/shift sudah dipesan atau sedang diajukan. Gunakan pengajuan perubahan untuk jadwal resmi.';
            if(Peminjaman::where('ruangan_id',$r['room_id'])->whereDate('tanggal_pinjam',$dateString)->where('status','menunggu')->whereTime('jam_mulai','<',$r['ends_at'])->whereTime('jam_selesai','>',$r['starts_at'])->exists())$warnings[]=$label.': ada pengajuan pihak lain yang masih menunggu; slot belum dijamin.';
            if(PracticumBookingMeeting::where('room_id',$r['room_id'])->whereDate('date',$dateString)->whereTime('starts_at','<',$r['ends_at'])->whereTime('ends_at','>',$r['starts_at'])->whereHas('request',fn($q)=>$q->where('status','pending')->where('kind','!=','cancel')->when($requestId,fn($q)=>$q->where('id','!=',$requestId)))->exists())$warnings[]=$label.': ada paket praktikum lain yang masih menunggu pada slot ini; slot belum dijamin.';
            foreach(array_slice($rows,0,$i) as $other)if((int)$r['room_id']===(int)$other['room_id'] && $dateString===$other['date'] && $r['starts_at']<$other['ends_at'] && $r['ends_at']>$other['starts_at']){$errors[]=$label.': bentrok dengan pertemuan lain dalam paket ini.';break;}
        }
        return ['errors'=>array_values(array_unique($errors)),'warnings'=>array_values(array_unique($warnings))];
    }

    private function occupiedMeetings(?string $requestId=null)
    {
        return PracticumBookingMeeting::query()
            ->when($requestId,fn($q)=>$q->where('request_id','!=',$requestId))
            ->where(function($q){
                $q->where(fn($official)=>$official->official())
                    ->orWhere(function($pending){
                        $pending->where('state','pending')->whereHas('request',fn($q)=>$q->whereNotNull('submitted_at')->where('status','!=','draft'))
                            ->whereHas('booking',fn($q)=>$q->where('source','praktikum')->where('status','menunggu')
                                ->whereRaw('CAST(eo_mr_peminjamans.source_id AS TEXT) = CAST(eo_prak_booking_meetings.id AS TEXT)'));
                    });
            });
    }

    public function fingerprint(PracticumSchedulePeriod $period,array $rows): string
    {
        $ids=collect($rows)->pluck('praktikum_id')->unique()->sort()->values();
        $practicums=Praktikum::whereIn('id',$ids)->orderBy('id')->get(['id','is_active','koor_id','tahun_ajaran','semester','draft_pembagian'])->toArray();
        $modules=Modul::whereIn('praktikum_id',$ids)->orderBy('id')->get(['id','praktikum_id','urutan','nama'])->toArray();
        $students=DaftarPraktikan::whereIn('praktikum_id',$ids)->orderBy('id')->get(['id','praktikum_id','kelompok','shift'])->toArray();
        $snapshot=[$period->only(['id','starts_on','ends_on','version']),$practicums,$modules,$students,$this->settings(),TanggalLibur::orderBy('tanggal')->pluck('tanggal')->all()];
        $periods=$this->periodsForRows($rows);
        if($periods->count()>1)$snapshot[]=$periods->map(fn($p)=>$p->only(['id','starts_on','ends_on','version']))->values()->all();
        return hash('sha256',json_encode($snapshot));
    }

    public function periodsForRows(array $rows): \Illuminate\Support\Collection
    {
        $practicums=Praktikum::whereIn('id',collect($rows)->pluck('praktikum_id')->unique())->get(['tahun_ajaran','semester']);
        return PracticumSchedulePeriod::orderBy('id')->get()->filter(fn($period)=>$practicums->contains(fn($p)=>(string)$p->getRawOriginal('tahun_ajaran')===(string)$period->tahun_ajaran && strcasecmp($p->semester,$period->semester)===0));
    }
}
