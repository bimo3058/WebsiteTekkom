@props(['praktikum'=>null,'role','dashboard'=>false])
@if(($praktikum || ($dashboard && $role==='mahasiswa')) && \Illuminate\Support\Facades\Schema::hasTable('eo_prak_booking_meetings'))
@php
    $actor=auth()->user();$student=null;
    $allStudentPracticums=$dashboard && $role==='mahasiswa';
    $semesterSchedule=$allStudentPracticums && request('dashboard_schedule_range')==='all';
    $studentPracticumIds=$allStudentPracticums && $actor
        ? \Modules\EOffice\Models\DaftarPraktikan::where('user_id',$actor->id)->whereHas('praktikum',fn($q)=>$q->where('is_active',true))->pluck('praktikum_id')->unique()
        : collect();
    $allowed=$allStudentPracticums ? $studentPracticumIds->isNotEmpty() : ($actor?->hasRole('superadmin') || match($role){
        'koor'=>(string)$praktikum->koor_id===(string)$actor?->id,
        'dosen'=>$praktikum->dosens()->where('users.id',$actor?->id)->exists(),
        'asprak'=>\Modules\EOffice\Models\AsprakPraktikum::where('praktikum_id',$praktikum->id)->where('user_id',$actor?->id)->exists(),
        'mahasiswa'=>($student=\Modules\EOffice\Models\DaftarPraktikan::where('praktikum_id',$praktikum->id)->where('user_id',$actor?->id)->first())!==null,
        default=>false,
    });
    $scheduleQuery=\Modules\EOffice\Models\PracticumBookingMeeting::with(['room','praktikum'])->official()->orderBy('date')->orderBy('starts_at')->orderBy('id');
    if($allStudentPracticums) $scheduleQuery->whereIn('praktikum_id',$studentPracticumIds)->whereExists(function($query) use ($actor){
        $query->selectRaw('1')->from('eo_prak_daftar_praktikan as schedule_enrollment')
            ->where('schedule_enrollment.user_id',$actor?->id)
            ->whereColumn('schedule_enrollment.praktikum_id','eo_prak_booking_meetings.praktikum_id')
            ->whereColumn('schedule_enrollment.shift','eo_prak_booking_meetings.shift')
            ->whereNotNull('schedule_enrollment.shift')->where('schedule_enrollment.shift','!=','');
    });
    else $scheduleQuery->where('praktikum_id',$praktikum->id);
    if($role==='asprak' && !$actor?->hasRole('superadmin')){
        $membershipIds=\Modules\EOffice\Models\AsprakPraktikum::where('praktikum_id',$praktikum->id)->where('user_id',$actor->id)->pluck('id');
        $scheduleQuery->whereIn('module_id',\Illuminate\Support\Facades\DB::table('eo_prak_modul_asprak')->whereIn('asprak_id',$membershipIds)->pluck('modul_id')->map(fn($id)=>(string)$id));
    }
    if($dashboard && !$semesterSchedule){
        $scheduleQuery->whereDate('date','>=',today()->toDateString())->whereDate('date','<=',today()->addDays(6)->toDateString())
            ->where(fn($q)=>$q->whereDate('date','>',today()->toDateString())->orWhere('ends_at','>',now()->format('H:i:s')));
    }
    $scheduleSize=$dashboard?(int)request('dashboard_schedule_per_page',5):10;
    if($dashboard && !in_array($scheduleSize,[5,10,15],true))$scheduleSize=5;
    $scheduleRows=$allowed?$scheduleQuery->paginate($scheduleSize,['*'],$dashboard?'dashboard_schedule_page':'jadwal_page')->withQueryString():null;
    $scheduleRoute=match($role){
        'koor'=>'eoffice.manprak.koor.praktikan.index',
        'asprak'=>'eoffice.manprak.asprak.daftar-praktikan.index',
        'mahasiswa'=>'eoffice.manprak.mahasiswa.daftar-praktikan.index',
        default=>'eoffice.manprak.dosen.daftar-praktikan.index',
    };
    $allSchedulesUrl=$allStudentPracticums
        ? request()->fullUrlWithQuery(['dashboard_schedule_range'=>$semesterSchedule?'upcoming':'all','dashboard_schedule_page'=>1]).'#jadwal-praktikum'
        : route($scheduleRoute,['praktikum_id'=>$praktikum->id]).'#jadwal-praktikum';
    $pendingChanges=$scheduleRows?\Modules\EOffice\Models\PracticumBookingMeeting::whereIn('replaces_id',$scheduleRows->pluck('id'))->whereHas('request',fn($q)=>$q->where('status','pending'))->with('request')->get()->keyBy('replaces_id'):collect();
@endphp
@if($allowed)
<x-eoffice::manajemen-praktikum.ui.schedule-styles />
<section id="jadwal-praktikum" class="mp-card mp-settings-card">
    <div class="mp-card-header" style="gap:12px;flex-wrap:wrap"><div><h2 class="mp-card-title">{{ $dashboard && !$semesterSchedule?'Jadwal Praktikum Terdekat':'Jadwal Praktikum' }}</h2><p class="mp-settings-description">@if($allStudentPracticums){{ $semesterSchedule?'Jadwal disetujui sesuai shift Anda dari seluruh praktikum aktif yang Anda ikuti.':'Jadwal disetujui untuk tujuh hari ke depan sesuai shift Anda di setiap praktikum aktif.' }}@elseif($dashboard){{ $praktikum->nama }} · Jadwal disetujui untuk tujuh hari ke depan.@else{{ $role==='mahasiswa'?'Jadwal resmi seluruh modul dan shift pada praktikum ini.':'Jadwal modul yang telah disetujui admin ruang.' }} Booking yang dibatalkan tidak ditampilkan sebagai jadwal resmi.@endif</p></div>@if($dashboard)<a class="mp-btn secondary sm" href="{{ $allSchedulesUrl }}">{{ $semesterSchedule?'Lihat Jadwal Terdekat':'Lihat Semua Jadwal' }}</a>@endif</div>
    <div class="mp-data-scroll"><table class="mp-table mp-settings-table" style="min-width:{{ $allStudentPracticums?'900':'700' }}px"><thead><tr><th class="mp-th mp-settings-no">NO</th>@if($allStudentPracticums)<th class="mp-th">NAMA PRAKTIKUM</th>@endif<th class="mp-th">MODUL</th><th class="mp-th">SHIFT</th><th class="mp-th">TANGGAL</th><th class="mp-th">WAKTU</th><th class="mp-th">RUANG</th></tr></thead><tbody>
        @forelse($scheduleRows as $session)<tr class="mp-tr"><td class="mp-settings-no">{{ $scheduleRows->firstItem()+$loop->index }}</td>@if($allStudentPracticums)<td style="white-space:normal;overflow-wrap:anywhere;max-width:300px"><div class="mp-settings-name">{{ $session->praktikum->nama }}</div></td>@endif<td><div class="mp-settings-name">{{ $session->module_name }}</div>@if($pendingChanges->has($session->id))<span class="mp-badge warning sm">{{ $pendingChanges[$session->id]->request->kind==='cancel'?'Menunggu Pembatalan':'Menunggu Perubahan' }}</span>@endif</td><td style="text-align:center">{{ $session->shift }}</td><td>{{ $session->date->locale('id')->translatedFormat('l, d M Y') }}</td><td style="white-space:nowrap">{{ substr($session->starts_at,0,5) }}–{{ substr($session->ends_at,0,5) }}</td><td>{{ $session->room?->nama??'Ruang tidak tersedia' }}</td></tr>
        @empty<tr><td colspan="{{ $allStudentPracticums?7:6 }}" class="mp-settings-empty">{{ $dashboard && !$semesterSchedule?'Belum ada jadwal disetujui untuk tujuh hari ke depan.':'Belum ada jadwal yang disetujui.' }}</td></tr>@endforelse
    </tbody></table></div><x-eoffice::manajemen-praktikum.ui.pagination :paginator="$scheduleRows" :options="$dashboard?[5,10,15]:[10]" :perPageName="$dashboard?'dashboard_schedule_per_page':'per_page'" fragment="jadwal-praktikum" />
</section>
@endif
@endif
