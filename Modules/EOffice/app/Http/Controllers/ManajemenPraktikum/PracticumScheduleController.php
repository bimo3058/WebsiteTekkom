<?php
namespace Modules\EOffice\Http\Controllers\ManajemenPraktikum;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\{DB,Validator};
use Illuminate\Support\{Carbon,Str};
use Illuminate\Validation\ValidationException;
use Modules\EOffice\Models\{PracticumSchedulePeriod as Period,PracticumBookingRequest as Package,PracticumBookingMeeting as Meeting,Praktikum,Modul,DaftarPraktikan,Ruangan,Peminjaman,MrJadwalInternal};
use Modules\EOffice\Services\ManajemenPraktikum\{PracticumBookingWorkflow as Workflow,PracticumSchedulePlanner as Planner};

class PracticumScheduleController extends Controller
{
    public function availability(Request $request)
    {
        $data=$request->validate(['tanggal'=>'required|date_format:Y-m-d','jam_mulai'=>'required|date_format:H:i','jam_selesai'=>'required|date_format:H:i|after:jam_mulai','kapasitas'=>'nullable|integer|min:1']);
        return response()->json(['success'=>true,'data'=>app(\Modules\EOffice\Services\ManajemenPraktikum\PracticumRoomBooking::class)->availability($data['tanggal'],$data['jam_mulai'],$data['jam_selesai'],(int)($data['kapasitas']??0))]);
    }
    private function reviewer(Request $request): bool{return $request->routeIs('eoffice.peminjaman.admin.praktikum.*');}
    public function index(Request $request)
    {
        app(\Modules\EOffice\Services\ManajemenPraktikum\PracticumBookingStatus::class)->syncSubmitted();
        $reviewer=false;$tab=$request->input('tab','drafts');
        if(!in_array($tab,['drafts','requests','schedules'],true))$tab='drafts';
        if($reviewer && $tab==='drafts')$tab='requests';
        $request->validate(['q'=>'nullable|string|max:100']);
        $query=Package::with(['period','requester','meetings:id,request_id,praktikum_id,shift'])->withCount('meetings')->withMin('meetings','date')->withMax('meetings','date')->orderByDesc('created_at')->orderByDesc('id');
        if($request->filled('q'))$query->where(fn($q)=>$q->whereRaw('LOWER(code) LIKE ?',['%'.mb_strtolower($request->input('q')).'%'])->orWhereHas('meetings.praktikum',fn($p)=>$p->whereRaw('LOWER(nama) LIKE ?',['%'.mb_strtolower($request->input('q')).'%'])));
        if($tab==='drafts')$query->where('status','draft');
        elseif($tab==='requests')$query->whereNotNull('submitted_at')->where('status','!=','draft');
        if($request->filled('status') && in_array($request->status,['draft','pending','approved','rejected','partial','cancelled'],true))$query->where('status',$request->status);
        $packages=$query->paginate(10,['*'],'page')->withQueryString();
        $practicumIds=$packages->getCollection()->flatMap(fn($p)=>collect($p->plan['plans']??[])->pluck('praktikum_id')->merge($p->meetings->pluck('praktikum_id')))->filter()->unique();
        $catalog=Praktikum::withTrashed()->whereIn('id',$practicumIds)->get(['id','nama','tahun_ajaran','semester'])->keyBy('id');
        foreach($packages as $package){
            $ids=collect($package->plan['plans']??[])->pluck('praktikum_id')->merge($package->meetings->pluck('praktikum_id'))->filter()->unique();
            $practicums=$ids->map(function($id)use($package,$catalog){
                $practicum=$catalog->get($id);$sessions=$package->meetings->where('praktikum_id',$id);
                return ['id'=>$id,'name'=>$practicum?->nama??'Praktikum tidak tersedia','shifts'=>$sessions->pluck('shift')->filter(fn($s)=>filled($s))->unique()->count(),'sessions'=>$sessions->count(),
                    'period'=>$practicum?$practicum->semester.' '.$practicum->getRawOriginal('tahun_ajaran'):null];
            })->sortBy('name',SORT_NATURAL|SORT_FLAG_CASE)->values();
            $package->setAttribute('schedule_summary',['practicums'=>$practicums->all(),'count'=>$practicums->count(),'shifts'=>$practicums->sum('shifts'),'sessions'=>$package->meetings_count,'periods'=>$practicums->pluck('period')->filter()->unique()->values()->all()]);
        }
        $schedules=Meeting::with(['praktikum','room','request'])->official()->orderBy('date')->orderBy('starts_at')->paginate(20,['*'],'schedule_page')->withQueryString();
        $calendar=$this->calendar($request);
        return view('eoffice::manajemen-praktikum.scheduling.index',compact('reviewer','tab','packages','schedules','calendar'));
    }
    public function create(Request $request)
    {
        $seed=$request->validate(['slot_date'=>'nullable|required_with:slot_room,slot_start,slot_end|date_format:Y-m-d','slot_room'=>['nullable','required_with:slot_date','integer',\Illuminate\Validation\Rule::exists('eo_mr_ruangans','id')->where('is_active',true)->whereIn('kategori',['Kelas','Laboratorium'])],'slot_start'=>'nullable|required_with:slot_date|date_format:H:i','slot_end'=>'nullable|required_with:slot_date|date_format:H:i|after:slot_start']);
        $package=new Package(['id'=>(string)Str::uuid(),'version'=>0,'period_id'=>null,'requested_by'=>auth()->id()]);
        if(!empty($seed['slot_date'])){
            $matches=Period::whereDate('starts_on','<=',$seed['slot_date'])->whereDate('ends_on','>=',$seed['slot_date'])->get();
            if($matches->count()===1)$package->period_id=$matches->first()->id;
            $request->merge(['week'=>$seed['slot_date'],'calendar_room'=>(string)$seed['slot_room']]);
        }
        return $this->editor($request,$package,$seed);
    }
    public function show(Request $request,string $id){$package=Package::with('period')->findOrFail($id);$package=app(\Modules\EOffice\Services\ManajemenPraktikum\PracticumBookingStatus::class)->sync($package);return $this->editor($request,$package);}
    private function editor(Request $request,Package $package,array $seed=[])
    {
        $reviewer=false;$periods=Period::orderByDesc('starts_on')->get();
        $rooms=app(\Modules\EOffice\Services\ManajemenPraktikum\PracticumRoomBooking::class)->rooms()->orderBy('nama')->get();
        $catalog=Praktikum::where('is_active',true)->orderBy('nama')->get()->map(function($p){
            $shifts=DaftarPraktikan::where('praktikum_id',$p->id)->get(['shift'])->groupBy(fn($s)=>(string)$s->shift)->map(fn($list,$shift)=>['shift'=>(string)$shift,'participants'=>$list->count()])->values();
            return ['id'=>$p->id,'name'=>$p->nama,'year'=>$p->getRawOriginal('tahun_ajaran'),'semester'=>$p->semester,'shifts'=>$shifts,'modules'=>Modul::where('praktikum_id',$p->id)->orderBy('urutan')->orderBy('id')->get()->map(fn($m)=>['id'=>(string)$m->getRawOriginal('id'),'name'=>$m->nama])->values()];
        })->values();
        $meetings=$package->exists?$package->meetings()->with(['room','praktikum','booking'])->get():collect();
        // Older incomplete drafts stored their plan without any generated meetings.
        if($meetings->isEmpty() && $package->exists && $package->kind==='new' && in_array($package->status,['draft','rejected'],true)){
            $generated=app(Planner::class)->generate($package->period,$package->plan['plans']??[],$package->plan['overrides']??[],$package->plan['excluded_meetings']??[]);
            $meetings=Meeting::hydrate(array_map(fn($row)=>array_merge($row,['id'=>(string)Str::uuid()]),$generated['meetings']))->load(['room','praktikum']);
        }
        $report=$package->exists && $package->status==='draft'?app(Workflow::class)->review($package,$package->status==='pending'):['errors'=>[],'warnings'=>[],'notes'=>[]];
        $calendar=$this->calendar($request);
        $calendarSeed=empty($seed['slot_date'])?null:['date'=>$seed['slot_date'],'weekday'=>Carbon::parse($seed['slot_date'])->dayOfWeekIso,'roomId'=>(string)$seed['slot_room'],'roomName'=>$rooms->firstWhere('id',$seed['slot_room'])?->nama,'start'=>$seed['slot_start'],'end'=>$seed['slot_end']];
        if(!$reviewer && in_array($package->status,['draft','rejected'],true))foreach($meetings as $meeting)if($meeting->date->betweenIncluded($calendar['start'],$calendar['end']))$calendar['events'][]=['id'=>'draft-'.$meeting->id,'meeting_id'=>(string)$meeting->id,'room_id'=>(string)$meeting->room_id,'date'=>$meeting->date->toDateString(),'start'=>substr($meeting->starts_at,0,5),'end'=>substr($meeting->ends_at,0,5),'room'=>$meeting->room?->nama??'Ruangan','title'=>($meeting->praktikum?->nama??'Praktikum').' · '.$meeting->module_name.' · Shift '.$meeting->shift,'status'=>'Rancangan','shift'=>(string)$meeting->shift];
        return view('eoffice::manajemen-praktikum.scheduling.editor',compact('reviewer','package','periods','rooms','catalog','meetings','report','calendar','calendarSeed'));
    }
    public function save(Request $request,Workflow $workflow,string $id)
    {
        abort_unless(Str::isUuid($id),404);$request->validate(['version'=>'required|integer|min:0','payload'=>'required|json','week'=>'nullable|date_format:Y-m-d','calendar_room'=>['nullable','integer',\Illuminate\Validation\Rule::exists('eo_mr_ruangans','id')->where('is_active',true)->whereIn('kategori',['Kelas','Laboratorium'])]]);
        $package=$workflow->saveDraft($id,$request->integer('version'),json_decode($request->payload,true)??[]);
        return redirect()->route('eoffice.manprak.cerc.scheduling.show',array_merge(['id'=>$package->id],$request->only(['week','calendar_room'])))->withFragment('kalender-ruang')->with('success','Rancangan disimpan. Anda dapat memilih slot untuk praktikum atau shift lainnya.');
    }
    public function submit(Request $request,Workflow $workflow,string $id)
    {
        $request->validate(['version'=>'required|integer|min:0']);$workflow->submit($id,$request->integer('version'));
        return back()->with('success','Pengajuan dikirim. Admin ruang memutuskan setiap booking pertemuan secara terpisah.');
    }
    public function withdraw(Request $request,Workflow $workflow,string $id)
    {
        $request->validate(['version'=>'required|integer|min:0']);$workflow->withdraw($id,$request->integer('version'));
        return back()->with('success','Pengajuan ditarik kembali menjadi rancangan. Booking resmi yang sudah ada tetap berlaku.');
    }
    public function amendment(Request $request,Workflow $workflow)
    {
        $data=$request->validate(['meetings'=>'required|array|min:1|max:5000','meetings.*'=>'required|uuid|distinct','kind'=>'required|in:cancel','reason'=>'required|string|max:2000']);
        $p=$workflow->amendment($data['meetings'],$data['kind'],$data['reason']);
        return redirect()->route('eoffice.manprak.cerc.scheduling.show',$p->id)->with('success','Booking pertemuan terpilih dibatalkan dan ruang dilepas.');
    }
    public function decide(Request $request,Workflow $workflow,string $id)
    {
        $request->validate(['version'=>'required|integer|min:0','decision'=>'required|in:approve,reject','reason'=>'nullable|string|max:2000']);
        $workflow->decide($id,$request->integer('version'),$request->decision==='approve',$request->input('reason'));
        return back()->with('success','Keputusan paket berhasil disimpan.');
    }
    public function periods(){return view('eoffice::manajemen-praktikum.scheduling.periods',['periods'=>Period::orderByDesc('starts_on')->get()]);}
    public function savePeriod(Request $request,?string $id=null)
    {
        $data=$request->validate(['tahun_ajaran'=>'required|regex:/^\d{4}\/\d{4}$/','semester'=>'required|in:Ganjil,Genap','starts_on'=>'required|date_format:Y-m-d','ends_on'=>'required|date_format:Y-m-d|after:starts_on','version'=>'required|integer|min:0']);
        if((int)substr($data['tahun_ajaran'],5)!==(int)substr($data['tahun_ajaran'],0,4)+1 || Carbon::parse($data['starts_on'])->diffInDays(Carbon::parse($data['ends_on']))>366)throw ValidationException::withMessages(['periode'=>'Tahun ajaran harus berurutan dan periode maksimal 366 hari.']);
        DB::transaction(function()use($id,$data){
            $period=$id?Period::lockForUpdate()->findOrFail($id):new Period(['version'=>0]);
            if($period->exists && (int)$period->version!==(int)$data['version'])throw ValidationException::withMessages(['periode'=>'Periode telah berubah. Muat ulang halaman.']);
            if(Period::where('tahun_ajaran',$data['tahun_ajaran'])->where('semester',$data['semester'])->when($id,fn($q)=>$q->where('id','!=',$id))->exists())throw ValidationException::withMessages(['periode'=>'Periode untuk tahun ajaran dan semester ini sudah ada.']);
            if($period->exists && Package::where('period_id',$period->id)->exists() && ($period->tahun_ajaran!==$data['tahun_ajaran'] || $period->semester!==$data['semester']))throw ValidationException::withMessages(['periode'=>'Identitas periode yang sudah digunakan tidak dapat diganti.']);
            $period->fill(collect($data)->except('version')->all());$period->version++;$period->save();
            \App\Services\AuditLogger::log('eoffice','UPDATE','Pengaturan periode pelaksanaan praktikum',$period,null,$period->only(['tahun_ajaran','semester','starts_on','ends_on']),auth()->id());
        });
        return back()->with('success','Periode disimpan. Pengajuan yang menunggu perlu ditinjau ulang jika batas periode berubah; booking resmi tidak digeser otomatis.');
    }
    public function calendar(Request $request): array
    {
        $data=Validator::make($request->only(['week','calendar_room']),['week'=>'nullable|date_format:Y-m-d','calendar_room'=>['nullable','integer',\Illuminate\Validation\Rule::exists('eo_mr_ruangans','id')->where('is_active',true)->whereIn('kategori',['Kelas','Laboratorium'])]])->validate();
        $start=isset($data['week'])?Carbon::parse($data['week'])->startOfWeek():Carbon::today()->startOfWeek();$end=$start->copy()->addDays(6);$events=[];
        foreach(Peminjaman::with('ruangan')->whereBetween('tanggal_pinjam',[$start->toDateString(),$end->toDateString()])->whereIn('status',['disetujui','menunggu'])->orderBy('jam_mulai')->get() as $booking)$events[]=['id'=>'booking-'.$booking->id,'room_id'=>(string)$booking->ruangan_id,'date'=>$booking->tanggal_pinjam->toDateString(),'start'=>substr($booking->jam_mulai,0,5),'end'=>substr($booking->jam_selesai,0,5),'room'=>$booking->ruangan?->nama??'Ruang tidak tersedia','title'=>$booking->tujuan,'status'=>$booking->status==='disetujui'?'Disetujui':'Menunggu'];
        foreach(Meeting::with(['room','praktikum'])->whereBetween('date',[$start->toDateString(),$end->toDateString()])->whereNull('booking_id')->whereHas('request',fn($q)=>$q->where('status','pending')->where('kind','!=','cancel'))->get() as $meeting)$events[]=['id'=>'meeting-'.$meeting->id,'room_id'=>(string)$meeting->room_id,'date'=>$meeting->date->toDateString(),'start'=>substr($meeting->starts_at,0,5),'end'=>substr($meeting->ends_at,0,5),'room'=>$meeting->room?->nama??'Ruang tidak tersedia','title'=>($meeting->praktikum?->nama??'Praktikum').' · '.$meeting->module_name.' · Shift '.$meeting->shift,'status'=>'Menunggu'];
        foreach(MrJadwalInternal::with('ruangan')->get() as $internal)for($day=$start->copy();$day->lte($end);$day->addDay()){
            $date=$day->toDateString();$a=$internal->getAttributes();
            if(($a['tipe_jadwal']==='spesifik' && substr((string)($a['tanggal_spesifik']??''),0,10)===$date) || ($a['tipe_jadwal']==='rutin' && (int)$a['hari']===$day->dayOfWeekIso && (empty($a['tgl_mulai_efektif'])||$a['tgl_mulai_efektif']<=$date) && (empty($a['tgl_selesai_efektif'])||$a['tgl_selesai_efektif']>=$date)))$events[]=['id'=>'internal-'.$internal->id.'-'.$date,'room_id'=>(string)$internal->ruangan_id,'date'=>$date,'start'=>substr($internal->jam_mulai,0,5),'end'=>substr($internal->jam_selesai,0,5),'room'=>$internal->ruangan?->nama??'Ruangan','title'=>$a['keterangan']??$a['mata_kuliah']??'Jadwal internal','status'=>'Internal'];
        }
        $settings=app(Planner::class)->settings();
        $holidays=\Modules\EOffice\Models\TanggalLibur::whereBetween('tanggal',[$start->toDateString(),$end->toDateString()])->pluck('tanggal')->map(fn($date)=>Carbon::parse($date)->toDateString())->all();
        $calendarRooms=app(\Modules\EOffice\Services\ManajemenPraktikum\PracticumRoomBooking::class)->rooms()->orderBy('nama')->get();
        return ['start'=>$start,'end'=>$end,'events'=>collect($events)->sortBy('start')->values()->all(),'settings'=>$settings,'holidays'=>$holidays,'rooms'=>$calendarRooms,'selected_room'=>(string)($data['calendar_room']??'')];
    }
}
