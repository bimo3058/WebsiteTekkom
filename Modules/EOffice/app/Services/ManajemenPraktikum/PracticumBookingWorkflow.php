<?php
namespace Modules\EOffice\Services\ManajemenPraktikum;

use App\Models\User;
use Illuminate\Support\Facades\{DB,Validator,Log};
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Modules\EOffice\Models\{PracticumBookingRequest as Package,PracticumBookingMeeting as Meeting,PracticumSchedulePeriod as Period,Peminjaman,Praktikum,DaftarPraktikan,AsprakPraktikum};

class PracticumBookingWorkflow
{
    public function __construct(private PracticumSchedulePlanner $planner){}
    private function cerc(): void{abort_unless(auth()->user()?->hasAnyRole(['superadmin','koordinator_cerc']),403);}
    private function check(bool $ok,string $message): void{if(!$ok)throw ValidationException::withMessages(['jadwal'=>$message]);}
    private function locked(string $id,int $version): Package
    {
        $package=Package::lockForUpdate()->findOrFail($id);
        $this->check($package->version===$version,'Pengajuan telah berubah. Muat ulang sebelum melanjutkan.');return $package;
    }
    public function rows(Package $package): array
    {
        return $package->meetings()->get()->map(fn($m)=>array_merge($m->only(['id','praktikum_id','module_id','module_name','module_order','shift','starts_at','ends_at','room_id','participants','note','replaces_id','booking_id']),['date'=>$m->date->toDateString()]))->all();
    }
    private function event(Package $package,string $action,?string $reason=null): void
    {
        $history=$package->history??[];$history[]=['at'=>now()->toIso8601String(),'actor_id'=>auth()->id(),'actor'=>auth()->user()->name,'action'=>$action,'reason'=>$reason];
        $package->history=$history;$package->version++;$package->save();
        $label=match($action) {
            'draft_saved'=>'Penyimpanan draft jadwal',
            'submitted'=>'Pengajuan peminjaman ruang',
            'withdrawn'=>'Penarikan pengajuan ruang',
            'bookings_cancelled'=>'Pembatalan peminjaman ruang',
            default=>null,
        };
        if ($label) CercActivity::record($label, $package->code, null, array_values(array_filter([$label, $reason])));
    }
    public function saveDraft(string $id,int $version,array $payload): Package
    {
        $this->cerc();
        $data=Validator::make($payload,[
            'period_id'=>'nullable|uuid|exists:eo_prak_schedule_periods,id','reason'=>'nullable|string|max:2000','plans'=>'nullable|array',
            'plans.*.praktikum_id'=>'required|uuid','plans.*.week_start'=>'nullable|date_format:Y-m-d','plans.*.modules'=>'nullable|array|max:200','plans.*.modules.*'=>'string|max:64',
            'plans.*.slots'=>'nullable|array|max:100','plans.*.slots.*'=>'array:shift,weekday,room_id,starts_at,ends_at,note','plans.*.slots.*.shift'=>['required','string','max:50','regex:/^[1-9][0-9]*$/'],'plans.*.slots.*.weekday'=>'nullable|integer|min:1|max:7','plans.*.slots.*.room_id'=>'nullable|integer','plans.*.slots.*.starts_at'=>'nullable|string|max:8','plans.*.slots.*.ends_at'=>'nullable|string|max:8','plans.*.slots.*.note'=>'nullable|string|max:500',
            'overrides'=>'nullable|array|max:5000','overrides.*'=>'array:date,room_id,starts_at,ends_at,note','overrides.*.date'=>'nullable|date_format:Y-m-d','overrides.*.room_id'=>'nullable|integer','overrides.*.starts_at'=>'nullable|string|max:8','overrides.*.ends_at'=>'nullable|string|max:8','overrides.*.note'=>'nullable|string|max:500',
            'excluded_meetings'=>'nullable|array|max:5000','excluded_meetings.*'=>'required|string|max:200|distinct',
            'meetings'=>'nullable|array|max:5000','meetings.*.id'=>'required|uuid','meetings.*.date'=>'required|date_format:Y-m-d','meetings.*.room_id'=>'required|integer','meetings.*.starts_at'=>'required|string|max:8','meetings.*.ends_at'=>'required|string|max:8',
        ],['*.date_format'=>'Tanggal jadwal harus berupa tanggal yang valid.','*.required'=>'Lengkapi identitas periode, praktikum, dan pertemuan.','plans.*.slots.*.shift.regex'=>'Shift harus berupa angka bulat positif.','plans.*.slots.*.note.max'=>'Catatan maksimal 500 karakter.'])->validate();
        return DB::transaction(function()use($id,$version,$data){
            $package=Package::lockForUpdate()->find($id);
            if(($package?->kind??'new')==='new' && !empty($data['plans'])){
                $ids=array_unique(array_column($data['plans'],'praktikum_id'));$practicums=Praktikum::whereIn('id',$ids)->get();$periods=Period::all();$resolved=[];
                $this->check($practicums->count()===count($ids),'Praktikum tidak ditemukan. Muat ulang daftar praktikum aktif.');
                foreach($practicums as $practicum){
                    $this->check((bool)$practicum->is_active,$practicum->nama.': hanya praktikum aktif yang dapat ditambahkan.');
                    $period=$periods->first(fn($p)=>(string)$p->tahun_ajaran===(string)$practicum->getRawOriginal('tahun_ajaran') && strcasecmp($p->semester,$practicum->semester)===0);
                    $this->check($period!==null,$practicum->nama.': pengaturan jadwal belum lengkap. Admin Praktikum perlu melengkapi batas pelaksanaan semester.');$resolved[$period->id]=true;
                }
                $data['period_id']=array_key_first($resolved);
            }elseif($package){$data['period_id']=$package->period_id;}
            $this->check(filled($data['period_id']??null),'Pilih praktikum terlebih dahulu. Pengaturan jadwal akan mengikuti ketentuan Admin Praktikum secara otomatis.');
            if(!$package){$this->check($version===0,'Rancangan tidak ditemukan.');$package=Package::create(['id'=>$id,'code'=>'PRK-'.now()->format('Ymd').'-'.substr(str_replace('-','',$id),-12),'period_id'=>$data['period_id'],'requested_by'=>auth()->id()]);}
            else{$this->check($package->version===$version,'Rancangan telah berubah. Muat ulang halaman.');$this->check($package->status==='draft','Hanya rancangan yang belum dikirim yang dapat diubah. Tarik pengajuan menunggu atau buat rancangan baru untuk pengajuan yang sudah diputuskan.');}
            if($package->kind==='new'){
                $this->check(!$package->meetings()->whereNotNull('booking_id')->exists(),'Rancangan yang sudah memiliki booking tidak dapat ditimpa. Buat rancangan baru atau tarik seluruh pengajuan yang belum diputuskan.');
                $period=Period::findOrFail($data['period_id']);
                $plans=$data['plans']??[];$this->check(count(array_unique(array_column($plans,'praktikum_id')))===count($plans),'Praktikum tidak boleh dimasukkan dua kali dalam satu paket.');
                $generated=$this->planner->generate($period,$plans,$data['overrides']??[],$data['excluded_meetings']??[]);
                $package->meetings()->delete();
                foreach($generated['meetings'] as $row)$package->meetings()->create($row);
                $package->period_id=$period->id;$package->plan=['plans'=>$plans,'overrides'=>$data['overrides']??[],'excluded_meetings'=>$data['excluded_meetings']??[],'generation_errors'=>$generated['errors'],'notes'=>$generated['notes']];
            }else{
                $this->check((string)$package->period_id===(string)$data['period_id'],'Periode pengajuan perubahan tidak dapat diganti.');
                if($package->kind==='change'){
                    $rows=collect($data['meetings']??[]);$current=$package->meetings()->get()->keyBy('id');
                    $this->check($rows->count()===$current->count() && $rows->pluck('id')->unique()->count()===$current->count() && $rows->every(fn($r)=>$current->has($r['id'])),'Isi seluruh pertemuan yang akan diubah.');
                    foreach($rows as $row)$current[$row['id']]->update(collect($row)->only(['date','room_id','starts_at','ends_at'])->all());
                }
            }
            $package->reason=$data['reason']??null;$package->status='draft';$package->decision_reason=null;
            $package->fingerprint=$this->planner->fingerprint($package->period()->firstOrFail(),$this->rows($package));
            $this->event($package,'draft_saved');return $package->fresh();
        });
    }
    public function amendment(array $ids,string $kind,string $reason): Package
    {
        $this->cerc();
        $this->check($kind==='cancel','Reschedule sementara belum tersedia. Tunggu konfirmasi layanan Manajemen Ruang.');
        $this->check(count($ids)>0 && count($ids)<=5000,'Pilih pertemuan yang akan dibatalkan.');
        $this->check(filled(trim($reason)) && mb_strlen($reason)<=2000,'Isi alasan pembatalan, maksimal 2000 karakter.');
        return DB::transaction(function()use($ids,$reason){
            Package::whereIn('id',Meeting::whereIn('id',$ids)->pluck('request_id'))->orderBy('id')->lockForUpdate()->get();
            $rows=Meeting::with('request')->whereIn('id',array_unique($ids))->orderBy('id')->lockForUpdate()->get();
            $this->check($rows->count()===count(array_unique($ids)),'Pertemuan tidak ditemukan.');
            $packages=[];
            foreach($rows as $row){
                $this->check($row->date->copy()->setTimeFromTimeString($row->starts_at)->isFuture(),'Pertemuan yang sudah dimulai tidak dapat dibatalkan.');
                $this->check(in_array($row->state,['pending','approved'],true),'Pilih booking yang masih menunggu atau disetujui.');
                app(PracticumRoomBooking::class)->cancel($row,trim($reason));
                $row->update(['state'=>'cancelled']);$packages[$row->request_id]=$row->request;
            }
            foreach($packages as $p){
                $this->event($p,'bookings_cancelled',trim($reason));
                app(PracticumBookingStatus::class)->sync($p);
                $this->notify($p->fresh(),true);
            }
            return reset($packages)->fresh();
        });
    }

    public function targets(Package $package,bool $lock=false): array
    {
        $this->check($package->kind==='new','Reschedule sementara belum tersedia.');
        return [];
    }

    public function review(Package $package,bool $approval=false): array
    {
        $package->setRelation('period',$package->period()->firstOrFail());
        $rows=$this->rows($package);$errors=$package->plan['generation_errors']??[];
        if(!$rows)$errors[]='Rancangan belum memiliki pertemuan. Lengkapi rancangan.';
        if($package->kind!=='new')$errors[]='Reschedule ditunda sampai layanan Manajemen Ruang dikonfirmasi.';
        $inspection=$this->planner->inspect($package->period,$rows,[],$package->id,false,$approval);
        if($package->fingerprint && !hash_equals($package->fingerprint,$this->planner->fingerprint($package->period,$rows)))$errors[]='Periode, pembagian shift, modul, atau ketentuan ruang berubah. Simpan dan tinjau ulang rancangan.';
        return ['errors'=>array_values(array_unique(array_merge($errors,$inspection['errors']))),'warnings'=>$inspection['warnings'],'notes'=>$package->plan['notes']??[]];
    }

    public function submit(string $id,int $version): Package
    {
        $this->cerc();return DB::transaction(function()use($id,$version){
            $p=$this->locked($id,$version);
            $this->check($p->status==='draft' && $p->kind==='new','Hanya rancangan booking baru yang dapat dikirim.');
            $gateway=app(PracticumRoomBooking::class);$gateway->assertReady();
            $rows=$p->meetings()->with('praktikum')->get();
            Praktikum::whereIn('id',$rows->pluck('praktikum_id'))->orderBy('id')->lockForUpdate()->get();
            RoomBookingGuard::lockRooms($rows->pluck('room_id')->all());
            $report=$this->review($p);$this->check(!$report['errors'],implode(' ',$report['errors']));
            foreach($rows as $row){
                $this->check(!$row->booking_id,'Rancangan masih terhubung ke booking lama. Tarik pengajuan sebelum mengirim ulang.');
                $booking=$gateway->book($row,(int)$p->requested_by);
                $row->update(['booking_id'=>$booking->id,'state'=>'pending']);
            }
            $p->status='pending';$p->submitted_at=now();
            $this->event($p,'submitted');$this->notify($p,false);return $p;
        });
    }

    public function withdraw(string $id,int $version): Package
    {
        $this->cerc();return DB::transaction(function()use($id,$version){
            $p=$this->locked($id,$version);
            $this->check($p->status==='pending' && $p->kind==='new','Hanya pengajuan baru yang belum diputuskan dapat ditarik.');
            $rows=$p->meetings()->orderBy('id')->lockForUpdate()->get();
            foreach($rows as $row){
                $booking=$row->booking()->lockForUpdate()->first();
                $this->check($booking?->status==='menunggu','Sebagian booking sudah diputuskan. Pengajuan tidak dapat ditarik seluruhnya.');
                app(PracticumRoomBooking::class)->cancel($row,'Pengajuan ditarik menjadi rancangan oleh Koordinator CERC.');
            }
            foreach($rows as $row)$row->update(['booking_id'=>null,'state'=>'draft']);
            $p->status='draft';$p->submitted_at=null;$this->event($p,'withdrawn');return $p;
        });
    }

    public function decide(string $id,int $version,bool $approve,?string $reason): Package
    {
        abort(410,'Persetujuan dilakukan per booking melalui menu Persetujuan Manajemen Ruang.');
    }
    public function notify(Package $p,bool $published): void
    {
        $ids=User::whereHas('roles',fn($q)=>$q->whereIn('name',$p->status==='pending'?['admin_eoffice','superadmin']:['koordinator_cerc']))->pluck('id')->push($p->requested_by);
        if($published){$practicums=$p->meetings()->pluck('praktikum_id')->unique();$ids=$ids->merge(DaftarPraktikan::whereIn('praktikum_id',$practicums)->pluck('user_id'))->merge(AsprakPraktikum::whereIn('praktikum_id',$practicums)->pluck('user_id'))->merge(DB::table('eo_prak_praktikum_dosen')->whereIn('praktikum_id',$practicums)->pluck('dosen_id'));}
        $audiences = ['cerc' => collect([$p->requested_by])->filter()];
        if ($published) {
            $audiences['mahasiswa'] = DaftarPraktikan::whereIn('praktikum_id', $practicums)->pluck('user_id');
            $audiences['asprak'] = AsprakPraktikum::whereIn('praktikum_id', $practicums)->where('role', 'asprak')->pluck('user_id');
            $audiences['koor'] = DB::table('eo_prak_praktikum')->whereIn('id', $practicums)->pluck('koor_id')->filter();
            $audiences['dosen'] = DB::table('eo_prak_praktikum_dosen')->whereIn('praktikum_id', $practicums)->pluck('dosen_id');
        }
        DB::afterCommit(function()use($p,$audiences){foreach($audiences as $role=>$recipients)foreach($recipients->unique() as $id)try{app(\Modules\EOffice\Services\NotifikasiService::class)->kirim($id,'Pengajuan Jadwal Praktikum',$p->code.' · '.$p->status.'. Lihat detail pengajuan/jadwal praktikum.',null,$role);}catch(\Throwable){Log::warning('Notifikasi paket praktikum perlu dikirim ulang',['request_id'=>$p->id,'user_id'=>$id]);}});
    }
}
