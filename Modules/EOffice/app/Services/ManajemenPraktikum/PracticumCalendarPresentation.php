<?php

namespace Modules\EOffice\Services\ManajemenPraktikum;

use Illuminate\Support\Carbon;

/** Read-only calendar cells; submission and approval retain their own validation. */
class PracticumCalendarPresentation
{
    public function build(array $calendar): array
    {
        $settings=$calendar['settings'];
        $rooms=$calendar['rooms']->when($calendar['selected_room']!=='',fn($rooms)=>$rooms->where('id',$calendar['selected_room']))->values();
        $open=substr($settings['open'],0,5);$close=substr($settings['close'],0,5);
        $hours=range((int)substr($open,0,2),max((int)substr($open,0,2),(int)ceil(((int)substr($close,0,2)*60+(int)substr($close,3,2))/60)-1));
        $events=collect($calendar['events'])->groupBy(fn($event)=>$event['date'].'|'.$event['room_id']);
        $days=collect(range(0,6))->map(fn($i)=>$calendar['start']->copy()->addDays($i));$cells=[];
        foreach($days as $day)foreach($rooms as $room){
            $date=$day->toDateString();$column=[];
            foreach($hours as $hour){
                $start=max(sprintf('%02d:00',$hour),$open);$end=min(sprintf('%02d:00',$hour+1),$close);
                $items=$events->get($date.'|'.$room->id,collect())->filter(fn($event)=>$event['start']<$end && $event['end']>$start)->values()->all();
                $blocked=collect($items)->contains(fn($item)=>in_array($item['status'],['Disetujui','Internal','Menunggu'],true));
                $draft=collect($items)->contains('status','Rancangan');$pending=collect($items)->contains('status','Menunggu');
                $closed=match(true){
                    in_array($date,$calendar['holidays'],true)=>'Hari libur',
                    !$settings['weekends'] && $day->isWeekend()=>'Akhir pekan ditutup',
                    $date<Carbon::today()->toDateString() || ($day->isToday() && $start<=now()->format('H:i'))=>'Waktu telah berlalu',
                    $date<Carbon::today()->addDays($settings['lead'])->toDateString()=>'Belum memenuhi batas pengajuan',
                    $start>=$end=>'Di luar jam operasional',
                    default=>null,
                };
                $state=match(true){$draft && $blocked=>'conflict',collect($items)->contains('status','Disetujui')=>'occupied',collect($items)->contains('status','Internal')=>'internal',$closed!==null=>'closed',$pending=>'pending',$draft=>'draft',default=>'available'};
                $column[$hour]=['start'=>$start,'end'=>$end,'items'=>$items,'state'=>$state,'closed'=>$closed,'selectable'=>!$blocked && $closed===null,'rowspan'=>1,'skip'=>false];
            }
            // Merge occupied/closed cells just like the EOffice weekly timetable.
            foreach($hours as $index=>$hour){
                if($column[$hour]['skip'] || $column[$hour]['selectable'])continue;
                $signature=[$column[$hour]['state'],$column[$hour]['closed'],array_column($column[$hour]['items'],'id')];
                foreach(array_slice($hours,$index+1) as $next){
                    if($column[$next]['selectable'] || [$column[$next]['state'],$column[$next]['closed'],array_column($column[$next]['items'],'id')]!==$signature)break;
                    $column[$hour]['rowspan']++;$column[$next]['skip']=true;
                }
            }
            $cells[$date][$room->id]=$column;
        }
        return compact('rooms','days','hours','cells');
    }
}
