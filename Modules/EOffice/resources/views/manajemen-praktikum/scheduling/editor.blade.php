<x-dynamic-component :component="$reviewer?'eoffice::manajemen-ruangan.layout':'eoffice::manajemen-praktikum.layout'" pageTitle="Rancangan dan Pengajuan Jadwal">
    <x-eoffice::manajemen-praktikum.ui.schedule-styles />
    <x-eoffice::manajemen-praktikum.ui.schedule-editor-script />
    @php
        $base=$reviewer?'eoffice.peminjaman.admin.praktikum.':'eoffice.manprak.cerc.scheduling.';
        $editable=!$reviewer && (!$package->exists || in_array($package->status,['draft'],true));
        $statuses=['draft'=>'Rancangan','pending'=>'Menunggu Persetujuan','approved'=>'Disetujui','rejected'=>'Ditolak','partial'=>'Sebagian Diputuskan','cancelled'=>'Dibatalkan'];$kinds=['new'=>'Peminjaman Baru','change'=>'Perubahan Jadwal','cancel'=>'Pembatalan'];
        $saved=json_decode(old('payload','null'),true)??($package->plan??[]);
        $roomOptions=$rooms->mapWithKeys(fn($r)=>[(string)$r->id=>$r->nama.' · '.$r->kapasitas.' orang'])->all();
        $rows=$meetings->map(fn($m)=>array_merge($m->only(['id','praktikum_id','module_id','module_name','shift','room_id','participants']),['date'=>$m->date->toDateString(),'starts_at'=>substr($m->starts_at,0,5),'ends_at'=>substr($m->ends_at,0,5),'room_name'=>$m->room?->nama,'practicum_name'=>$m->praktikum?->nama,'booking_status'=>$m->booking?->status??($m->state==='draft'?'rancangan':'referensi belum lengkap')]))->values()->all();
        $config=['calendarPositionKey'=>'practicum-calendar-'.$package->id,'kind'=>$package->kind,'periodId'=>$package->period_id??'','reason'=>$saved['reason']??$package->reason??'','catalog'=>$catalog,'periods'=>$periods->map(fn($p)=>['id'=>$p->id,'year'=>$p->tahun_ajaran,'semester'=>$p->semester,'start'=>$p->starts_on->toDateString(),'end'=>$p->ends_on->toDateString()])->values(),'rows'=>$rows,'saved'=>$saved,'plans'=>[]];
    @endphp
    <div x-data="practicumScheduleEditor(@js($config))" style="display:contents">
        <nav class="mp-schedule-breadcrumb" aria-label="Breadcrumb"><a href="{{ ($reviewer?route('eoffice.peminjaman.admin.persetujuan.index'):route($base.'index')) }}">Peminjaman Ruang</a><x-eoffice::manajemen-praktikum.ui.icon name="chevron-right" size="14" /><span>{{ $package->exists?'Detail Pengajuan':'Buat Rancangan' }}</span></nav>
        <div class="mp-page-header"><div><h1 class="mp-page-title">{{ $package->exists?'Detail Pengajuan':'Rancangan Jadwal Baru' }}</h1><p class="mp-page-sub">{{ $kinds[$package->kind] }}. Setiap booking pertemuan diputuskan oleh admin ruang.</p></div><div class="mp-schedule-actions"><x-eoffice::manajemen-praktikum.ui.schedule-status :status="$package->status" /><a class="mp-btn secondary md" href="{{ ($reviewer?route('eoffice.peminjaman.admin.persetujuan.index'):route($base.'index')) }}">Kembali</a></div></div>
        @if($package->exists)<p class="mp-schedule-summary">Kode Pengajuan: <strong>{{ $package->code }}</strong></p>@endif
        @if($editable && $package->kind==='new')<p class="mp-settings-description"><span x-text="plans.filter(p=>p.selected).length+' praktikum dalam pengajuan ini.'"></span> Pilih slot berikutnya pada kalender untuk menambahkan praktikum atau shift lain ke pengajuan yang sama.</p>@endif
        @include('eoffice::manajemen-praktikum.scheduling.messages')
        @if($package->decision_reason)<div class="mp-alert warning" role="status">Alasan keputusan: {{ $package->decision_reason }}</div>@endif
        @if(!$periods->count())<div class="mp-alert warning" role="status">Pengaturan jadwal belum lengkap. Admin Praktikum perlu melengkapi batas pelaksanaan semester agar rancangan dapat diajukan.</div>@endif
        @include('eoffice::manajemen-praktikum.scheduling.calendar')
        @if($editable && $package->kind==='new')
            @include('eoffice::manajemen-praktikum.scheduling.slot-dialog')
        @endif
        @if($editable)
        <form x-ref="draftForm" method="POST" action="{{ route('eoffice.manprak.cerc.scheduling.save',$package->id) }}" @submit="rememberCalendarPosition();if(submitting){$event.preventDefault()}else{submitting=true}">
            <input type="hidden" name="week" value="{{ $calendar['start']->toDateString() }}"><input type="hidden" name="calendar_room" value="{{ $calendar['selected_room'] }}">
            @csrf<input type="hidden" name="version" value="{{ $package->version }}"><input type="hidden" name="payload" :value="JSON.stringify(payload)">
            <div class="mp-schedule-actions" x-show="dirty" x-cloak><button class="mp-btn primary md" :disabled="submitting||!payload.period_id" type="submit">Simpan Perubahan</button></div>
        </form>
        @endif
        @if($package->exists)
            @if($report['errors'] || $report['warnings'] || $report['notes'])<section class="mp-card mp-settings-card"><div class="mp-card-header"><h2 class="mp-card-title">Hasil Pemeriksaan</h2><span class="mp-badge {{ count($report['errors'])?'error':'success' }} sm">{{ count($report['errors']) }} masalah</span></div><div class="mp-settings-body mp-schedule-report">
                @foreach(['errors'=>'Perlu diperbaiki','warnings'=>'Peringatan','notes'=>'Penyesuaian kalender'] as $key=>$label)@if($report[$key])<div><strong>{{ $label }}</strong><ul>@foreach($report[$key] as $message)<li>{{ $message }}</li>@endforeach</ul></div>@endif @endforeach
            </div></section>@endif
            <section class="mp-card mp-settings-card"><div class="mp-card-header"><div><h2 class="mp-card-title">Daftar Pertemuan</h2><p class="mp-settings-description">{{ $meetings->count() }} pertemuan. {{ $package->kind==='cancel'?'Booking yang dibatalkan langsung melepas ruang.':'Ruang hanya dipesan pada tanggal dan jam setiap pertemuan.' }}</p></div></div>
                <div class="mp-data-scroll"><table class="mp-table mp-settings-table" style="min-width:1000px"><thead><tr><th class="mp-th mp-settings-no">NO</th><th class="mp-th">PRAKTIKUM / MODUL</th><th class="mp-th">SHIFT</th><th class="mp-th">TANGGAL</th><th class="mp-th">RUANG</th><th class="mp-th">MULAI</th><th class="mp-th">SELESAI</th><th class="mp-th">STATUS</th></tr></thead><tbody>
                    <template x-for="(row,index) in rows" :key="row.id"><tr class="mp-tr"><td class="mp-settings-no" x-text="index+1"></td><td><div class="mp-settings-name" x-text="row.practicum_name"></div><div class="mp-settings-email" x-text="row.module_name"></div></td><td style="text-align:center" x-text="row.shift"></td>
                        @if($editable && $package->kind!=='cancel')<td><input type="date" class="mp-input" x-model="row.date" aria-label="Tanggal pertemuan"></td><td style="min-width:210px"><x-eoffice::manajemen-praktikum.ui.ta-select x-model="row.room_id" :options="$roomOptions" /></td><td><input class="mp-input mp-schedule-input" type="time" x-model="row.starts_at" aria-label="Jam mulai pertemuan"></td><td><input class="mp-input mp-schedule-input" type="time" x-model="row.ends_at" aria-label="Jam selesai pertemuan"></td>
                        @else<td x-text="row.date"></td><td x-text="@js($roomOptions)[row.room_id]||'Ruang tidak tersedia'"></td><td x-text="row.starts_at"></td><td x-text="row.ends_at"></td>@endif
                    <td x-text="row.booking_status"></td></tr></template>
                    @if($meetings->isEmpty())<tr><td colspan="8" class="mp-settings-empty">Belum ada pertemuan. Lengkapi dan simpan rancangan.</td></tr>@endif
                </tbody></table></div>
                <div class="mp-settings-footer"><p class="mp-settings-description">{{ $meetings->map(fn($m)=>$m->praktikum ? $m->praktikum->semester.' '.$m->praktikum->getRawOriginal('tahun_ajaran') : null)->filter()->unique()->implode(' ? ') ?: $package->period->semester.' '.$package->period->tahun_ajaran }} · {{ $statuses[$package->status] }}</p><div class="mp-schedule-actions">
                    @if(!$reviewer && $package->status==='draft')<form method="POST" action="{{ route($base.'submit',$package->id) }}" x-data="{confirmOpen:false,confirmed:false,submitting:false}" x-ref="actionForm" @submit="if(submitting){$event.preventDefault();return}if(!confirmed){$event.preventDefault();confirmOpen=true}else{submitting=true}">@csrf<input type="hidden" name="version" value="{{ $package->version }}"><button class="mp-btn primary md" :disabled="dirty||submitting||{{ count($report['errors'])?'true':'false' }}">Kirim Pengajuan</button><x-eoffice::manajemen-praktikum.ui.schedule-confirmation message="Kirim seluruh pertemuan ke admin ruang? Setiap pertemuan menunggu keputusan admin ruang." /></form>@endif
                    @if(!$reviewer && $package->status==='pending')<form method="POST" action="{{ route($base.'withdraw',$package->id) }}" x-data="{confirmOpen:false,confirmed:false,submitting:false}" x-ref="actionForm" @submit="if(submitting){$event.preventDefault();return}if(!confirmed){$event.preventDefault();confirmOpen=true}else{submitting=true}">@csrf<input type="hidden" name="version" value="{{ $package->version }}"><button class="mp-btn secondary md" :disabled="submitting">Tarik ke Rancangan</button><x-eoffice::manajemen-praktikum.ui.schedule-confirmation title="Tarik Pengajuan" message="Tarik pengajuan yang belum diputuskan menjadi rancangan? Jadwal resmi yang sudah ada tidak berubah." /></form>@endif
                </div></div>
            </section>

        @endif
    </div>
</x-dynamic-component>
